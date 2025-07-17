<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class EmailService
{
    /**
     * Enviar correo de bienvenida al alumno
     */
    public function sendWelcomeEmail($alumno, $resetUrl)
    {
        try {
            $to = $alumno->email;
            $subject = 'Bienvenido a la Plataforma Educativa';
            
            $message = $this->buildWelcomeEmailContent($alumno, $resetUrl);
            
            // Usar Laravel Mail en lugar de la función mail() de PHP
            Mail::html($message, function($message) use ($to, $subject) {
                $message->to($to)
                        ->subject($subject)
                        ->from(env('MAIL_FROM_ADDRESS', 'noreply@example.com'), 
                               env('MAIL_FROM_NAME', 'Plataforma Educativa'));
            });
            
            Log::info('Correo de bienvenida enviado exitosamente a: ' . $to);
            return true;
            
        } catch (\Exception $e) {
            Log::error('Excepción al enviar correo: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Construir el contenido del correo
     */
    private function buildWelcomeEmailContent($alumno, $resetUrl)
    {
        $html = '
        <!DOCTYPE html>
        <html lang="es">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Bienvenido a la Plataforma Educativa</title>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px; }
                .container { background-color: #ffffff; padding: 30px; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
                .header { text-align: center; margin-bottom: 30px; padding-bottom: 20px; border-bottom: 2px solid #007bff; }
                .header h1 { color: #007bff; margin: 0; font-size: 28px; }
                .content { margin-bottom: 30px; }
                .welcome-text { font-size: 18px; margin-bottom: 20px; color: #555; }
                .student-info { background-color: #f8f9fa; padding: 20px; border-radius: 8px; margin: 20px 0; border-left: 4px solid #007bff; }
                .student-info h3 { margin-top: 0; color: #007bff; }
                .info-row { margin: 10px 0; }
                .info-label { font-weight: bold; color: #555; }
                .btn { display: inline-block; padding: 12px 24px; background-color: #007bff; color: #ffffff; text-decoration: none; border-radius: 5px; font-weight: bold; margin: 20px 0; text-align: center; }
                .warning { background-color: #fff3cd; border: 1px solid #ffeaa7; color: #856404; padding: 15px; border-radius: 5px; margin: 20px 0; }
                .footer { text-align: center; margin-top: 30px; padding-top: 20px; border-top: 1px solid #ddd; color: #666; font-size: 14px; }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="header">
                    <h1>🎓 Bienvenido a la Plataforma Educativa</h1>
                </div>
                
                <div class="content">
                    <div class="welcome-text">
                        <p>¡Hola <strong>' . $alumno->nombres . ' ' . $alumno->apellidos . '</strong>!</p>
                        <p>Te damos la bienvenida a nuestra plataforma educativa. Tu cuenta ha sido creada exitosamente y ya puedes acceder a todos los recursos disponibles.</p>
                    </div>
                    
                    <div class="student-info">
                        <h3>📋 Información de tu cuenta:</h3>
                        <div class="info-row">
                            <span class="info-label">Código de estudiante:</span> ' . $alumno->codigo . '
                        </div>
                        <div class="info-row">
                            <span class="info-label">Email:</span> ' . $alumno->email . '
                        </div>
                        <div class="info-row">
                            <span class="info-label">Carrera:</span> ' . ($alumno->carrera_nombre ?? 'Por asignar') . '
                        </div>
                        <div class="info-row">
                            <span class="info-label">Ciclo:</span> ' . ($alumno->ciclo_nombre ?? 'Por asignar') . '
                        </div>
                    </div>
                    
                    <div class="warning">
                        <strong>🔐 Importante:</strong> Por seguridad, te recomendamos cambiar tu contraseña al acceder por primera vez a la plataforma.
                    </div>
                    
                    <div style="text-align: center;">
                        <a href="' . $resetUrl . '" class="btn">
                            🔑 Cambiar Contraseña
                        </a>
                    </div>
                    
                    <p style="text-align: center; margin-top: 20px;">
                        <strong>¿No puedes hacer clic en el botón?</strong><br>
                        Copia y pega este enlace en tu navegador:<br>
                        <a href="' . $resetUrl . '" style="color: #007bff; word-break: break-all;">' . $resetUrl . '</a>
                    </p>
                </div>
                
                <div class="footer">
                    <p>Este es un correo automático, por favor no respondas a este mensaje.</p>
                    <p>Si tienes alguna pregunta, contacta con el soporte técnico.</p>
                    <p>&copy; ' . date('Y') . ' Plataforma Educativa. Todos los derechos reservados.</p>
                </div>
            </div>
        </body>
        </html>';
        
        return $html;
    }

    /**
     * Enviar correo de bienvenida al docente
     */
    public function sendWelcomeEmailDocente($docente, $resetUrl)
    {
        try {
            $to = $docente->email;
            $subject = 'Bienvenido como Docente a la Plataforma Educativa';
            
            $message = $this->buildWelcomeEmailDocenteContent($docente, $resetUrl);
            
            Mail::html($message, function($message) use ($to, $subject) {
                $message->to($to)
                        ->subject($subject)
                        ->from(env('MAIL_FROM_ADDRESS', 'noreply@example.com'), 
                               env('MAIL_FROM_NAME', 'Plataforma Educativa'));
            });
            
            Log::info('Correo de bienvenida enviado exitosamente a docente: ' . $to);
            return true;
            
        } catch (\Exception $e) {
            Log::error('Excepción al enviar correo a docente: ' . $e->getMessage());
            return false;
        }
    }

    private function buildWelcomeEmailDocenteContent($docente, $resetUrl)
    {
        $html = '
        <!DOCTYPE html>
        <html lang="es">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Bienvenido como Docente</title>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px; }
                .container { background-color: #f9f9f9; padding: 30px; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.08); }
                .header { text-align: center; margin-bottom: 30px; padding-bottom: 20px; border-bottom: 2px solid #28a745; }
                .header h1 { color: #28a745; margin: 0; font-size: 28px; }
                .content { margin-bottom: 30px; }
                .welcome-text { font-size: 18px; margin-bottom: 20px; color: #444; }
                .info-box { background-color: #e9f7ef; padding: 20px; border-radius: 8px; margin: 20px 0; border-left: 4px solid #28a745; }
                .info-box h3 { margin-top: 0; color: #28a745; }
                .info-row { margin: 10px 0; }
                .info-label { font-weight: bold; color: #555; }
                .btn { display: inline-block; padding: 12px 24px; background-color: #28a745; color: #ffffff; text-decoration: none; border-radius: 5px; font-weight: bold; margin: 20px 0; text-align: center; }
                .footer { text-align: center; margin-top: 30px; padding-top: 20px; border-top: 1px solid #ddd; color: #666; font-size: 14px; }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="header">
                    <h1>👨‍🏫 ¡Bienvenido, Docente!</h1>
                </div>
                <div class="content">
                    <div class="welcome-text">
                        <p>Estimado(a) <strong>' . $docente->nombres . '</strong>,</p>
                        <p>Nos complace darte la bienvenida al equipo docente de nuestra Plataforma Educativa. Tu experiencia y dedicación serán clave para el aprendizaje de nuestros estudiantes.</p>
                        <p>Tu cuenta ha sido creada exitosamente. Para comenzar, por favor activa tu acceso cambiando tu contraseña inicial.</p>
                    </div>
                    <div class="info-box">
                        <h3>📝 Detalles de tu cuenta:</h3>
                        <div class="info-row">
                            <span class="info-label">Código de docente:</span> ' . $docente->codigo . '
                        </div>
                        <div class="info-row">
                            <span class="info-label">Email:</span> ' . $docente->email . '
                        </div>
                        <div class="info-row">
                            <span class="info-label">Profesión:</span> ' . ($docente->profesion ?? 'Por asignar') . '
                        </div>
                    </div>
                    <div style="text-align: center;">
                        <a href="' . $resetUrl . '" class="btn">
                            Cambiar Contraseña y Activar Cuenta
                        </a>
                    </div>
                    <p style="text-align: center; margin-top: 20px;">
                        <strong>¿No puedes hacer clic en el botón?</strong><br>
                        Copia y pega este enlace en tu navegador:<br>
                        <a href="' . $resetUrl . '" style="color: #28a745; word-break: break-all;">' . $resetUrl . '</a>
                    </p>
                </div>
                <div class="footer">
                    <p>Gracias por formar parte de nuestro equipo docente.</p>
                    <p>Si tienes dudas, contacta con soporte académico.</p>
                    <p>&copy; ' . date('Y') . ' Plataforma Educativa.</p>
                </div>
            </div>
        </body>
        </html>';
        return $html;
    }

    /**
     * Enviar correo de bienvenida al postulante
     */
    public function sendWelcomeEmailPostulante($postulante, $resetUrl)
    {
        try {
            $to = $postulante->email;
            $subject = 'Bienvenido como Postulante a la Plataforma';
            
            $message = $this->buildWelcomeEmailPostulanteContent($postulante, $resetUrl);
            
            Mail::html($message, function($message) use ($to, $subject) {
                $message->to($to)
                        ->subject($subject)
                        ->from(env('MAIL_FROM_ADDRESS', 'noreply@example.com'), 
                               env('MAIL_FROM_NAME', 'Plataforma Educativa'));
            });
            
            Log::info('Correo de bienvenida enviado exitosamente a postulante: ' . $to);
            return true;
            
        } catch (\Exception $e) {
            Log::error('Excepción al enviar correo a postulante: ' . $e->getMessage());
            return false;
        }
    }

    private function buildWelcomeEmailPostulanteContent($postulante, $resetUrl)
    {
        $html = '
        <!DOCTYPE html>
        <html lang="es">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Bienvenido como Postulante</title>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px; }
                .container { background-color: #f8f9fa; padding: 30px; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
                .header { text-align: center; margin-bottom: 30px; padding-bottom: 20px; border-bottom: 2px solid #6f42c1; }
                .header h1 { color: #6f42c1; margin: 0; font-size: 28px; }
                .content { margin-bottom: 30px; }
                .welcome-text { font-size: 18px; margin-bottom: 20px; color: #444; }
                .info-box { background-color: #f3e5f5; padding: 20px; border-radius: 8px; margin: 20px 0; border-left: 4px solid #6f42c1; }
                .info-box h3 { margin-top: 0; color: #6f42c1; }
                .info-row { margin: 10px 0; }
                .info-label { font-weight: bold; color: #555; }
                .btn { display: inline-block; padding: 12px 24px; background-color: #6f42c1; color: #ffffff; text-decoration: none; border-radius: 5px; font-weight: bold; margin: 20px 0; text-align: center; }
                .warning { background-color: #fff3cd; border: 1px solid #ffeaa7; color: #856404; padding: 15px; border-radius: 5px; margin: 20px 0; }
                .footer { text-align: center; margin-top: 30px; padding-top: 20px; border-top: 1px solid #ddd; color: #666; font-size: 14px; }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="header">
                    <h1>📋 ¡Bienvenido, Postulante!</h1>
                </div>
                <div class="content">
                    <div class="welcome-text">
                        <p>Estimado(a) <strong>' . $postulante->names . '</strong>,</p>
                        <p>Te damos la bienvenida a nuestra plataforma de postulaciones. Tu registro ha sido completado exitosamente y ya puedes acceder a todas las oportunidades disponibles.</p>
                        <p>Para comenzar a utilizar la plataforma, por favor activa tu cuenta cambiando tu contraseña inicial.</p>
                    </div>
                    <div class="info-box">
                        <h3>📝 Información de tu cuenta:</h3>
                        <div class="info-row">
                            <span class="info-label">Código de postulante:</span> ' . $postulante->code . '
                        </div>
                        <div class="info-row">
                            <span class="info-label">Email:</span> ' . $postulante->email . '
                        </div>
                        <div class="info-row">
                            <span class="info-label">DNI:</span> ' . $postulante->identification_number . '
                        </div>
                        <div class="info-row">
                            <span class="info-label">Estado:</span> ' . ($postulante->estado_actual_id ? 'Activo' : 'Pendiente') . '
                        </div>
                    </div>
                    <div class="warning">
                        <strong>🔐 Importante:</strong> Por seguridad, debes cambiar tu contraseña al acceder por primera vez a la plataforma.
                    </div>
                    <div style="text-align: center;">
                        <a href="' . $resetUrl . '" class="btn">
                            🔑 Cambiar Contraseña y Activar Cuenta
                        </a>
                    </div>
                    <p style="text-align: center; margin-top: 20px;">
                        <strong>¿No puedes hacer clic en el botón?</strong><br>
                        Copia y pega este enlace en tu navegador:<br>
                        <a href="' . $resetUrl . '" style="color: #6f42c1; word-break: break-all;">' . $resetUrl . '</a>
                    </p>
                </div>
                <div class="footer">
                    <p>Gracias por registrarte en nuestra plataforma de postulaciones.</p>
                    <p>Si tienes dudas, contacta con soporte técnico.</p>
                    <p>&copy; ' . date('Y') . ' Plataforma Educativa.</p>
                </div>
            </div>
        </body>
        </html>';
        return $html;
    } 

    /**
     * Enviar correo de recuperación de contraseña
     */
    public function sendPasswordResetEmail($user, $resetUrl)
    {
        try {
            $to = $user->email;
            $subject = 'Recuperación de Contraseña - Plataforma Educativa';
            
            $message = $this->buildPasswordResetEmailContent($user, $resetUrl);
            
            Mail::html($message, function($message) use ($to, $subject) {
                $message->to($to)
                        ->subject($subject)
                        ->from(env('MAIL_FROM_ADDRESS', 'noreply@example.com'), 
                               env('MAIL_FROM_NAME', 'Plataforma Educativa'));
            });
            
            Log::info('Correo de recuperación de contraseña enviado exitosamente a: ' . $to);
            return true;
            
        } catch (\Exception $e) {
            Log::error('Error al enviar correo de recuperación: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Construir el contenido del correo de recuperación de contraseña
     */
    private function buildPasswordResetEmailContent($user, $resetUrl)
    {
        $userName = $user->name . ' ' . $user->lastname;
        
        $html = '
        <!DOCTYPE html>
        <html lang="es">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Recuperación de Contraseña</title>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px; }
                .container { background-color: #ffffff; padding: 30px; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
                .header { text-align: center; margin-bottom: 30px; padding-bottom: 20px; border-bottom: 2px solid #dc3545; }
                .header h1 { color: #dc3545; margin: 0; font-size: 28px; }
                .content { margin-bottom: 30px; }
                .welcome-text { font-size: 18px; margin-bottom: 20px; color: #555; }
                .info-box { background-color: #f8f9fa; padding: 20px; border-radius: 8px; margin: 20px 0; border-left: 4px solid #dc3545; }
                .info-box h3 { margin-top: 0; color: #dc3545; }
                .btn { display: inline-block; padding: 12px 24px; background-color: #dc3545; color: #ffffff; text-decoration: none; border-radius: 5px; font-weight: bold; margin: 20px 0; text-align: center; }
                .warning { background-color: #fff3cd; border: 1px solid #ffeaa7; color: #856404; padding: 15px; border-radius: 5px; margin: 20px 0; }
                .footer { text-align: center; margin-top: 30px; padding-top: 20px; border-top: 1px solid #ddd; color: #666; font-size: 14px; }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="header">
                    <h1>🔐 Recuperación de Contraseña</h1>
                </div>
                
                <div class="content">
                    <div class="welcome-text">
                        <p>¡Hola <strong>' . $userName . '</strong>!</p>
                        <p>Has solicitado recuperar tu contraseña en la Plataforma Educativa. Haz clic en el botón de abajo para crear una nueva contraseña.</p>
                    </div>
                    
                    <div class="info-box">
                        <h3>📧 Información de la solicitud:</h3>
                        <p><strong>Email:</strong> ' . $user->email . '</p>
                        <p><strong>Fecha de solicitud:</strong> ' . date('d/m/Y H:i:s') . '</p>
                        <p><strong>IP de solicitud:</strong> ' . ($_SERVER['REMOTE_ADDR'] ?? 'No disponible') . '</p>
                    </div>
                    
                    <div class="warning">
                        <strong>⚠️ Importante:</strong> Este enlace es válido por 24 horas. Si no solicitaste este cambio, puedes ignorar este correo.
                    </div>
                    
                    <div style="text-align: center;">
                        <a href="' . $resetUrl . '" class="btn">
                            🔑 Cambiar Contraseña
                        </a>
                    </div>
                    
                    <p style="text-align: center; margin-top: 20px;">
                        <strong>¿No puedes hacer clic en el botón?</strong><br>
                        Copia y pega este enlace en tu navegador:<br>
                        <a href="' . $resetUrl . '" style="color: #dc3545; word-break: break-all;">' . $resetUrl . '</a>
                    </p>
                </div>
                
                <div class="footer">
                    <p>Este es un correo automático, por favor no respondas a este mensaje.</p>
                    <p>Si tienes alguna pregunta, contacta con el soporte técnico.</p>
                    <p>&copy; ' . date('Y') . ' Plataforma Educativa. Todos los derechos reservados.</p>
                </div>
            </div>
        </body>
        </html>';
        
        return $html;
    }
} 