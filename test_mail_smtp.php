<?php
/**
 * Script de prueba SMTP para producción.
 *
 * Uso:
 *   1. Subir este archivo a la raíz del proyecto Laravel en cPanel
 *      (la misma carpeta donde está artisan, composer.json, .env).
 *   2. Cambiar el valor de DESTINO por un correo real al que tengas acceso.
 *   3. Ejecutar desde la Terminal de cPanel:
 *          php test_mail_smtp.php
 *   4. Borrar este archivo después de la prueba.
 */

// >>> CAMBIA ESTO POR UN CORREO REAL AL QUE TENGAS ACCESO <<<
$DESTINO = 'carjavalo1@hotmail.com';

require __DIR__.'/vendor/autoload.php';
$app = require __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== Prueba de envío SMTP ===\n";
echo "Mailer:    ".config('mail.default')."\n";
echo "Host:      ".config('mail.mailers.smtp.host').":".config('mail.mailers.smtp.port')."\n";
echo "Encrypt:   ".config('mail.mailers.smtp.encryption')."\n";
echo "Username:  ".config('mail.mailers.smtp.username')."\n";
echo "From:      ".config('mail.from.address')."\n";
echo "Destino:   {$DESTINO}\n";
echo "----------------------------\n";

if ($DESTINO === 'carjavalo1@hotmail.com') {
    echo "ERROR: edita la variable \$DESTINO en este archivo antes de ejecutar.\n";
    exit(1);
}

// 1) Test de conectividad TCP al puerto SMTP
$host = config('mail.mailers.smtp.host');
$port = (int) config('mail.mailers.smtp.port');
echo "Probando conexión TCP a {$host}:{$port} ... ";
$err_no = 0; $err_str = '';
$sock = @fsockopen($host, $port, $err_no, $err_str, 8);
if ($sock) {
    echo "OK\n";
    fclose($sock);
} else {
    echo "FALLA ({$err_no}) {$err_str}\n";
    echo "==> El hosting probablemente bloquea el puerto saliente.\n";
}

// 2) Envío real
echo "Enviando correo ... ";
try {
    Mail::raw(
        'Prueba SMTP citas HUV - '.now()->toDateTimeString(),
        function ($m) use ($DESTINO) {
            $m->to($DESTINO)->subject('Test SMTP citas HUV');
        }
    );
    echo "OK: correo enviado.\n";
    echo "Revisa la bandeja (y SPAM) de {$DESTINO}.\n";
} catch (\Throwable $e) {
    echo "ERROR\n";
    echo "Mensaje: ".$e->getMessage()."\n";
    echo "Tipo:    ".get_class($e)."\n";
    echo "Archivo: ".$e->getFile().":".$e->getLine()."\n";
}

echo "----------------------------\n";
echo "Recuerda BORRAR este archivo después de la prueba.\n";
