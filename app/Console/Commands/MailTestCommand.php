<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\Mailer\Exception\TransportException;
use Throwable;

class MailTestCommand extends Command
{
    protected $signature = 'mail:test {email? : Recipient (defaults to MAIL_FROM_ADDRESS)}';

    protected $description = 'Send a test email to verify SMTP settings';

    public function handle(): int
    {
        $to = $this->argument('email') ?: config('mail.from.address');

        if (! $to) {
            $this->error('No recipient. Set MAIL_FROM_ADDRESS in .env or pass an email argument.');

            return self::FAILURE;
        }

        $this->info('Mailer: '.config('mail.default'));
        $this->info('Host: '.config('mail.mailers.smtp.host'));
        $this->info('Port: '.config('mail.mailers.smtp.port'));
        $this->info('Username: '.config('mail.mailers.smtp.username'));
        $this->info('Sending test to: '.$to);

        try {
            Mail::raw('SMTP test from Estandarte Dental Clinic — if you received this, mail is configured correctly.', function ($message) use ($to) {
                $message->to($to)->subject('Dental System — SMTP Test');
            });
        } catch (TransportException $e) {
            $this->error('SMTP failed: '.$e->getMessage());
            $this->newLine();
            $this->warn('Gmail requires an App Password (16 characters), not your normal Gmail password.');
            $this->line('1. Enable 2-Step Verification: https://myaccount.google.com/security');
            $this->line('2. Create App Password: https://myaccount.google.com/apppasswords');
            $this->line('3. Put the 16-char password in .env as MAIL_PASSWORD (no spaces)');
            $this->line('4. Run: php artisan config:clear');

            return self::FAILURE;
        } catch (Throwable $e) {
            $this->error('Failed: '.$e->getMessage());

            return self::FAILURE;
        }

        $this->info('Test email sent successfully.');

        return self::SUCCESS;
    }
}
