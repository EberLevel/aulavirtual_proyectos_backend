<?php

// Script para configurar las variables de entorno de correo
// Ejecutar este script antes de probar el envío de correos

// Configuración de correo SMTP - Zoho
putenv('MAIL_MAILER=smtp');
putenv('MAIL_HOST=smtp.zoho.com');
putenv('MAIL_PORT=465');
putenv('MAIL_USERNAME=info@dentalteammedellin.com');
putenv('MAIL_PASSWORD=aqwEh3z@');
putenv('MAIL_ENCRYPTION=ssl');
putenv('MAIL_FROM_ADDRESS=info@dentalteammedellin.com');
putenv('MAIL_FROM_NAME=Plataforma Educativa');

echo "Variables de entorno de correo configuradas.\n";
echo "MAIL_HOST: " . getenv('MAIL_HOST') . "\n";
echo "MAIL_PORT: " . getenv('MAIL_PORT') . "\n";
echo "MAIL_USERNAME: " . getenv('MAIL_USERNAME') . "\n";
echo "MAIL_ENCRYPTION: " . getenv('MAIL_ENCRYPTION') . "\n";
echo "MAIL_FROM_ADDRESS: " . getenv('MAIL_FROM_ADDRESS') . "\n"; 