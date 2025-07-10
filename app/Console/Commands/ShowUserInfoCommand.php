<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ShowUserInfoCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'show:user {email}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Show user information';

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
            
            $this->info("=== INFORMACIÓN DEL USUARIO ===");
            $this->info("ID: {$user->id}");
            $this->info("Nombre: {$user->name} {$user->lastname}");
            $this->info("Email: {$user->email}");
            $this->info("DNI: {$user->dni}");
            $this->info("Rol ID: {$user->rol_id}");
            $this->info("Password Changed: " . ($user->password_changed ? 'true' : 'false'));
            $this->info("Password Changed At: " . ($user->password_changed_at ?? 'null'));
            $this->info("Created At: {$user->created_at}");
            $this->info("Updated At: {$user->updated_at}");
            
            return 0;
            
        } catch (\Exception $e) {
            $this->error('Error: ' . $e->getMessage());
            return 1;
        }
    }
} 