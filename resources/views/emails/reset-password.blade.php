<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Restablecer Contraseña - DHP Visitors System</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #2563eb;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 8px 8px 0 0;
        }
        .content {
            background-color: #f8f9fa;
            padding: 30px;
            border: 1px solid #dee2e6;
        }
        .button {
            display: inline-block;
            padding: 12px 24px;
            background-color: #2563eb;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            font-weight: bold;
            margin: 20px 0;
        }
        .footer {
            background-color: #6c757d;
            color: white;
            padding: 15px;
            text-align: center;
            font-size: 14px;
            border-radius: 0 0 8px 8px;
        }
        .warning {
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            color: #856404;
            padding: 15px;
            border-radius: 4px;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>DHP Visitors System</h1>
        <p>Solicitud de Restablecimiento de Contraseña</p>
    </div>
    
    <div class="content">
        <h2>¡Hola!</h2>
        
        <p>Has recibido este correo porque hemos recibido una solicitud de restablecimiento de contraseña para tu cuenta en el sistema DHP Visitors.</p>
        
        <p style="text-align: center;">
            <a href="{{ $actionUrl }}" class="button">Restablecer Contraseña</a>
        </p>
        
        <div class="warning">
            <strong>⚠️ Importante:</strong>
            <ul>
                <li>Este enlace expirará en {{ $count }} minutos</li>
                <li>Si no solicitaste este restablecimiento, puedes ignorar este correo</li>
                <li>Tu contraseña no será cambiada hasta que accedas al enlace y crees una nueva</li>
            </ul>
        </div>
        
        <p>Si tienes problemas haciendo clic en el botón "Restablecer Contraseña", copia y pega la siguiente URL en tu navegador web:</p>
        <p style="word-break: break-all; color: #666; font-size: 14px;">{{ $actionUrl }}</p>
        
        <p><strong>Equipo DHP Visitors System</strong></p>
    </div>
    
    <div class="footer">
        <p>Este es un correo automático, por favor no respondas a este mensaje.</p>
        <p>&copy; {{ date('Y') }} DHP Visitors System. Todos los derechos reservados.</p>
    </div>
</body>
</html>