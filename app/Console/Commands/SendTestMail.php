<?php

namespace App\Console\Commands;

use App\Mail\TestMail;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Throwable;

class SendTestMail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-test-mail {email : Recipient email address}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send a test email using the current SMTP configuration';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $email = (string) $this->argument('email');

        try {
            Mail::to($email)->send(new TestMail);
        } catch (Throwable $exception) {
            $this->error('Mail test failed: '.$exception->getMessage());

            return self::FAILURE;
        }

        $this->info("Mail test sent to {$email}.");

        return self::SUCCESS;
    }
}
