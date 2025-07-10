<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\CvBank;
use App\Services\EmailService;
use App\Http\Controllers\FrontendPasswordController;

class TestPostulanteEmailCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:postulante-email {email}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test email sending for postulantes';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $email = $this->argument('email');
        
        try {
            // Buscar el postulante por email
            $postulante = CvBank::where('email', $email)->first();
            
            if (!$postulante) {
                $this->error("Postulante no encontrado con email: {$email}");
                return 1;
            }
            
            $this->info("Postulante encontrado: {$postulante->names}");
            $this->info("Código: {$postulante->code}");
            $this->info("DNI: {$postulante->identification_number}");
            
            // Generar URL de reset
            $passwordResetController = new FrontendPasswordController();
            $resetUrl = $passwordResetController->generateResetUrl($email);
            
            if (!$resetUrl) {
                $this->error('No se pudo generar la URL de reset');
                return 1;
            }
            
            $this->info("URL de reset generada: {$resetUrl}");
            
            // Enviar correo
            $emailService = new EmailService();
            $result = $emailService->sendWelcomeEmailPostulante($postulante, $resetUrl);
            
            if ($result) {
                $this->info('✅ Correo enviado exitosamente a: ' . $email);
                $this->info('📧 URL de cambio de contraseña: ' . $resetUrl);
            } else {
                $this->error('❌ Error al enviar el correo');
            }
            
            return 0;
            
        } catch (\Exception $e) {
            $this->error('Error: ' . $e->getMessage());
            return 1;
        }
    }
} 