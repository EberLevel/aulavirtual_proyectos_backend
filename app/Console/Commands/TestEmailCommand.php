<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class TestEmailCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:test {email}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test email sending functionality';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // Configurar variables de entorno de correo
        $this->setupEmailEnvironment();
        
        $email = $this->argument('email');
        
        $this->info("Testing email sending to: {$email}");
        
        try {
            $html = '
            <!DOCTYPE html>
            <html>
            <head>
                <title>Test Email</title>
            </head>
            <body>
                <h1>Test Email</h1>
                <p>This is a test email from the Laravel/Lumen application.</p>
                <p>If you receive this email, the mail configuration is working correctly.</p>
                <p>Sent at: ' . date('Y-m-d H:i:s') . '</p>
            </body>
            </html>';
            
            Mail::html($html, function($message) use ($email) {
                $message->to($email)
                        ->subject('Test Email - Plataforma Educativa')
                        ->from(env('MAIL_FROM_ADDRESS', 'noreply@example.com'), 
                               env('MAIL_FROM_NAME', 'Plataforma Educativa'));
            });
            
            $this->info('Email sent successfully!');
            Log::info("Test email sent successfully to: {$email}");
            return 0;
            
        } catch (\Exception $e) {
            $this->error('Failed to send email: ' . $e->getMessage());
            Log::error('Test email failed: ' . $e->getMessage());
            return 1;
        }
    }
    
    /**
     * Configurar las variables de entorno de correo
     */
    private function setupEmailEnvironment()
    {
        // Configuración de correo SMTP - Zoho
        putenv('MAIL_MAILER=smtp');
        putenv('MAIL_HOST=smtp.zoho.com');
        putenv('MAIL_PORT=465');
        putenv('MAIL_USERNAME=info@dentalteammedellin.com');
        putenv('MAIL_PASSWORD=aqwEh3z@');
        putenv('MAIL_ENCRYPTION=ssl');
        putenv('MAIL_FROM_ADDRESS=info@dentalteammedellin.com');
        putenv('MAIL_FROM_NAME=Plataforma Educativa');
        
        $this->info('Email environment variables configured.');
    }
} 