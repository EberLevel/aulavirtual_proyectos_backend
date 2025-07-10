<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class TestLoginCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:login {email} {password}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test login functionality with role and password change validation';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $email = $this->argument('email');
        $password = $this->argument('password');
        
        $this->info("Testing login for: {$email}");
        
        try {
            // Buscar el usuario
            $user = DB::table('users')->where('email', $email)->first();
            
            if (!$user) {
                $this->error("Usuario no encontrado con email: {$email}");
                return 1;
            }
            
            $this->info("Usuario encontrado: {$user->name} {$user->lastname}");
            $this->info("Rol ID: {$user->rol_id}");
            $this->info("Estado password_changed: " . ($user->password_changed ? 'true' : 'false'));
            
            // Verificar contraseña
            if (Hash::check($password, $user->password)) {
                $this->info("Contraseña correcta");
                
                // Verificar si es alumno (rol_id = 12) y si ha cambiado la contraseña
                if ($user->rol_id == 12 && !$user->password_changed) {
                    $this->warn("⚠️  El alumno debe cambiar su contraseña inicial");
                    $this->info("Respuesta esperada del login: require_password_change = true");
                    return 0;
                } else if ($user->rol_id == 12 && $user->password_changed) {
                    $this->info("✅ Alumno puede acceder normalmente (ya cambió contraseña)");
                    return 0;
                } else {
                    $this->info("✅ Usuario no es alumno, puede acceder sin restricción de contraseña");
                    return 0;
                }
            } else {
                $this->error("❌ Contraseña incorrecta");
                return 1;
            }
            
        } catch (\Exception $e) {
            $this->error('Error: ' . $e->getMessage());
            return 1;
        }
    }
} 