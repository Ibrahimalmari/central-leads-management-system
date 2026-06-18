<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\NewLeadMail;
use App\Models\ApiSubmissionLog;
use App\Models\Lead;
use App\Models\Site;
use App\Models\SystemSetting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;

class LeadController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $apiKey = $request->bearerToken() ?: $request->header('X-API-Key');

        if (! $apiKey) {
            $this->logSubmission($request, null, null, 'failed', 401, __('api.missing_api_key'));

            return response()->json([
                'success' => false,
                'message' => __('api.missing_api_key'),
            ], 401);
        }

        $site = Site::query()
            ->with('company')
            ->where('api_key', Site::hashApiKey($apiKey))
            ->first();

        if (! $site) {
            $this->logSubmission($request, null, null, 'failed', 401, __('api.invalid_api_key'));

            return response()->json([
                'success' => false,
                'message' => __('api.invalid_api_key'),
            ], 401);
        }

        if ($site->status !== 'active') {
            $this->logSubmission($request, $site, null, 'failed', 403, __('api.site_inactive'));

            return response()->json([
                'success' => false,
                'message' => __('api.site_inactive'),
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'form_key' => ['nullable', 'string', 'max:255'],
            'form_name' => ['nullable', 'string', 'max:255'],
            'form_type' => ['nullable', 'string', 'max:255'],
            'name' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:255'],
            'message' => ['nullable', 'string'],
            'page_url' => ['nullable', 'url', 'max:2048'],
            'raw_data' => ['nullable', 'array'],
        ]);

        if ($validator->fails()) {
            $this->logSubmission($request, $site, null, 'failed', 422, __('api.validation_failed'));

            return response()->json([
                'success' => false,
                'message' => __('api.validation_failed'),
                'errors' => $validator->errors(),
            ], 422);
        }

        $data = $validator->validated();

        $form = null;

        if (! empty($data['form_key'])) {
            $form = $site->forms()
                ->where('form_key', $data['form_key'])
                ->where('status', 'active')
                ->first();
        }

        $lead = Lead::create([
            'company_id' => $site->company_id,
            'site_id' => $site->id,
            'form_id' => $form?->id,
            'form_key' => $data['form_key'] ?? $form?->form_key,
            'form_name' => $form?->name ?? ($data['form_name'] ?? null),
            'form_type' => $form?->type ?? ($data['form_type'] ?? null),
            'name' => $data['name'] ?? null,
            'email' => $data['email'] ?? null,
            'phone' => $data['phone'] ?? null,
            'message' => $data['message'] ?? null,
            'page_url' => $data['page_url'] ?? null,
            'status' => 'new',
            'raw_data' => $data['raw_data'] ?? $request->all(),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        $lead->activities()->create([
            'type' => 'created',
            'title' => __('admin.activities.lead_created_from_api'),
            'changes' => [
                'source' => 'api',
                'form_key' => $lead->form_key,
            ],
        ]);

        $this->logSubmission($request, $site, $lead, 'success', 201, __('api.lead_created'));
        $this->notifyNewLead($lead);

        return response()->json([
            'success' => true,
            'message' => __('api.lead_created'),
            'lead_id' => $lead->id,
        ], 201);
    }

    private function logSubmission(Request $request, ?Site $site, ?Lead $lead, string $status, int $httpStatus, string $message): void
    {
        if (! Schema::hasTable('api_submission_logs')) {
            return;
        }

        ApiSubmissionLog::create([
            'site_id' => $site?->id,
            'lead_id' => $lead?->id,
            'status' => $status,
            'http_status' => $httpStatus,
            'message' => $message,
            'form_key' => $request->input('form_key'),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'payload' => $request->except(['password', 'token']),
        ]);
    }

    private function notifyNewLead(Lead $lead): void
    {
        $settings = SystemSetting::current();
        $emails = collect($settings->notification_emails ?? [])
            ->map(fn ($item) => is_array($item) ? ($item['email'] ?? null) : $item)
            ->filter()
            ->unique()
            ->values();

        if (! $settings->notify_new_leads || $emails->isEmpty()) {
            return;
        }

        foreach ($emails as $email) {
            Mail::to($email)->send(new NewLeadMail($lead));
        }
    }
}
