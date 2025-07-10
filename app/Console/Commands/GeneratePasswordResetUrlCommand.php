<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class GeneratePasswordResetUrlCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:reset-url {email}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate password reset URL for testing';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $email = $this->argument('email');
        
        try {
            $user = DB::table('users')->where('email', $email)->first();
            
            if (!$user) {
                $this->error("Usuario no encontrado con email: {$email}");
                return 1;
            }
            
            $this->info("Usuario encontrado: {$user->name} {$user->lastname}");
            
            // Generar token único
            $token = Str::random(60);
            
            // Guardar token en la base de datos
            DB::table('password_resets')->updateOrInsert(
                ['email' => $email],
                [
                    'email' => $email,
                    'token' => $token,
                    'created_at' => Carbon::now()
                ]
            );
            
            // Generar URL de reset
            $baseUrl = env('FRONTEND_URL', 'http://localhost:4200');
            $resetUrl = $baseUrl . '/change-password?token=' . $token;
            
            $this->info("=== URL DE CAMBIO DE CONTRASEÑA ===");
            $this->info("URL: {$resetUrl}");
            $this->info("Token: {$token}");
            $this->info("Frontend URL configurada: {$baseUrl}");
            $this->info("=====================================");
            
            return 0;
            
        } catch (\Exception $e) {
            $this->error('Error: ' . $e->getMessage());
            return 1;
        }
    }
} 