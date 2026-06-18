<?php

namespace App\Support;

use App\Models\Lead;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Collection;
use Symfony\Component\HttpFoundation\StreamedResponse;

class LeadCsvExport
{
    public function downloadFromQuery(Builder $query, ?string $filename = null): StreamedResponse
    {
        $records = (clone $query)
            ->with(['company', 'site', 'form', 'assignee'])
            ->get();

        return $this->download($records, $filename);
    }

    public function downloadFromRecords(EloquentCollection | Collection $records, ?string $filename = null): StreamedResponse
    {
        $records = $records instanceof EloquentCollection
            ? $records
            : new EloquentCollection($records->all());

        $records->loadMissing(['company', 'site', 'form', 'assignee']);

        return $this->download($records, $filename);
    }

    public function download(EloquentCollection $records, ?string $filename = null): StreamedResponse
    {
        $filename ??= 'leads-' . now()->format('Y-m-d-His') . '.csv';
        $extraKeys = $this->extraKeys($records);
        $columns = $this->columns($extraKeys);

        return response()->streamDownload(function () use ($records, $columns): void {
            $output = fopen('php://output', 'w');

            if ($output === false) {
                return;
            }

            fwrite($output, "\xEF\xBB\xBF");
            fputcsv($output, array_values($columns));

            foreach ($records as $lead) {
                fputcsv($output, $this->row($lead, array_keys($columns)));
            }

            fclose($output);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    /**
     * @return array<string, string>
     */
    private function columns(Collection $extraKeys): array
    {
        return [
            'id' => 'ID',
            'company' => __('admin.fields.company'),
            'site' => __('admin.fields.site'),
            'form' => __('admin.fields.form'),
            'form_key' => __('admin.fields.form_key'),
            'form_name' => __('admin.fields.form'),
            'form_type' => __('admin.fields.type'),
            'name' => __('admin.fields.name'),
            'email' => __('admin.fields.email'),
            'phone' => __('admin.fields.phone'),
            'message' => __('admin.fields.message'),
            'page_url' => __('admin.fields.page'),
            'status' => __('admin.fields.status'),
            'assignee' => __('admin.fields.assigned_to'),
            'assigned_at' => __('admin.fields.assigned_at'),
            'last_contacted_at' => __('admin.fields.last_contacted_at'),
            ...$extraKeys->mapWithKeys(fn (string $key): array => [
                "extra.{$key}" => LeadFieldFormatter::humanizeKey($key),
            ])->all(),
            'ip_address' => __('admin.fields.ip_address'),
            'user_agent' => __('admin.fields.user_agent'),
            'created_at' => __('admin.fields.submitted_at'),
            'updated_at' => __('admin.fields.updated_at'),
        ];
    }

    private function row(Lead $lead, array $columns): array
    {
        $extra = LeadFieldFormatter::flatten($lead->raw_data ?? []);

        return collect($columns)
            ->map(fn (string $column): string => $this->sanitizeCell(match ($column) {
                'id' => $lead->id,
                'company' => $lead->company?->name,
                'site' => $lead->site?->name,
                'form' => $lead->form?->name,
                'form_key' => $lead->form_key,
                'form_name' => $lead->form_name,
                'form_type' => $lead->form_type,
                'name' => $lead->name,
                'email' => $lead->email,
                'phone' => $lead->phone,
                'message' => $lead->message,
                'page_url' => $lead->page_url,
                'status' => __("admin.statuses.{$lead->status}"),
                'assignee' => $lead->assignee?->name,
                'assigned_at' => $lead->assigned_at?->format('Y-m-d H:i:s'),
                'last_contacted_at' => $lead->last_contacted_at?->format('Y-m-d H:i:s'),
                'ip_address' => $lead->ip_address,
                'user_agent' => $lead->user_agent,
                'created_at' => $lead->created_at?->format('Y-m-d H:i:s'),
                'updated_at' => $lead->updated_at?->format('Y-m-d H:i:s'),
                default => str_starts_with($column, 'extra.')
                    ? ($extra[substr($column, 6)] ?? null)
                    : null,
            }))
            ->all();
    }

    private function extraKeys(EloquentCollection $records): Collection
    {
        return $records
            ->flatMap(fn (Lead $lead): array => array_keys(LeadFieldFormatter::flatten($lead->raw_data ?? [])))
            ->filter()
            ->unique()
            ->sort(fn (string $first, string $second): int => strnatcasecmp($first, $second))
            ->values();
    }

    private function sanitizeCell(mixed $value): string
    {
        if (is_array($value)) {
            $value = json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }

        if (is_bool($value)) {
            $value = $value ? 'true' : 'false';
        }

        $value = trim((string) $value);

        if (preg_match('/^[=+\-@\t\r]/', $value) === 1) {
            return "'{$value}";
        }

        return $value;
    }
}
