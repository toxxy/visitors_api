<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmación de Visita - Novolex</title>
    <style>
        @media print {
            body { margin: 0; }
            .no-print { display: none; }
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            line-height: 1.6;
            color: #374151;
            background-color: #f9fafb;
            margin: 0;
            padding: 20px;
        }
        
        .container {
            max-width: 1024px;
            margin: 0 auto;
            background-color: white;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }
        
        .header {
            background-color: white;
            padding: 32px;
            border-bottom: 2px solid #e5e7eb;
        }
        
        .logo-section {
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 24px;
        }
        
        .logo {
            height: 96px;
            width: auto;
        }
        
        .title-section {
            text-align: center;
        }
        
        .title {
            font-size: 30px;
            font-weight: bold;
            color: #1f2937;
            margin: 8px 0;
        }
        
        .subtitle {
            color: #6b7280;
        }
        
        .content {
            padding: 32px;
        }
        
        .grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 32px;
        }
        
        @media (max-width: 768px) {
            .grid {
                grid-template-columns: 1fr;
            }
        }
        
        .left-section {
            
        }
        
        .right-section {
            
        }
        
        .visitor-section {
            background-color: #dbeafe;
            padding: 24px;
            border-radius: 8px;
            margin-bottom: 24px;
        }
        
        .visitor-title {
            font-size: 24px;
            font-weight: bold;
            color: #1e40af;
            margin-bottom: 16px;
        }
        
        .visitor-name {
            font-size: 30px;
            font-weight: bold;
            color: #1f2937;
            margin-bottom: 8px;
            text-transform: uppercase;
        }
        
        .visitor-company {
            font-size: 18px;
            color: #4b5563;
            margin-bottom: 8px;
        }
        
        .visitor-contact {
            color: #4b5563;
        }
        
        .info-item {
            margin-bottom: 16px;
            border-left: 4px solid #4f46e5;
            padding-left: 16px;
        }
        
        .info-label {
            font-weight: 600;
            color: #374151;
        }
        
        .info-value {
            font-size: 18px;
        }
        
        .info-value-large {
            font-size: 24px;
            font-weight: bold;
            color: #4f46e5;
        }
        
        .info-subvalue {
            color: #6b7280;
        }
        
        .right-section {
            text-align: center;
        }
        
        .qr-section {
            background-color: #f9fafb;
            padding: 24px;
            border-radius: 8px;
            margin-bottom: 24px;
        }
        
        .qr-title {
            font-size: 20px;
            font-weight: bold;
            color: #1f2937;
            margin-bottom: 16px;
        }
        
        .qr-description {
            font-size: 14px;
            color: #6b7280;
            margin-bottom: 16px;
        }
        
        .qr-id {
            font-size: 12px;
            color: #9ca3af;
        }
        
        .instructions {
            background-color: #fef3c7;
            border: 1px solid #fcd34d;
            border-radius: 8px;
            padding: 16px;
            margin-top: 24px;
        }
        
        .instructions-title {
            font-weight: 600;
            color: #92400e;
            margin-bottom: 8px;
        }
        
        .instructions-list {
            font-size: 14px;
            color: #78350f;
            margin: 0;
            padding-left: 16px;
            text-align: left;
        }
        
        .instructions-list li {
            margin-bottom: 4px;
        }
        
        .footer {
            background-color: #f3f4f6;
            padding: 24px;
            text-align: center;
            border-top: 1px solid #e5e7eb;
        }
        
        .footer-text {
            font-size: 12px;
            color: #9ca3af;
            margin-top: 8px;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header con logo -->
        <div class="header">
            <div class="logo-section">
                <div style="text-align: center; margin-bottom: 20px;">
                    <div style="font-size: 36px; font-weight: bold; color: #1f2937; margin-bottom: 8px;">NOVOLEX</div>
                    <div style="font-size: 14px; color: #6b7280;">Sistema de Control de Visitas</div>
                </div>
            </div>
            <div class="title-section">
                <h1 class="title">CONFIRMACIÓN DE VISITA</h1>
                <p class="subtitle">Su visita ha sido programada exitosamente</p>
            </div>
        </div>

        <!-- Contenido principal -->
        <div class="content">
            <div class="grid">
                <!-- Información de la visita (Columna izquierda) -->
                <div class="left-section">
                    <div class="visitor-section">
                        <h2 class="visitor-title">VISITANTE</h2>
                        <div class="visitor-name">{{ $visit->visitor_name }}</div>
                        @if($visit->company)
                        <div class="visitor-company">{{ $visit->company }}</div>
                        @endif
                        @if($visit->visitor_email)
                        <div class="visitor-contact">📧 {{ $visit->visitor_email }}</div>
                        @endif
                        @if($visit->visitor_phone)
                        <div class="visitor-contact">📞 {{ $visit->visitor_phone }}</div>
                        @endif
                    </div>

                    <div style="margin-top: 24px;">
                        <div class="info-item">
                            <div class="info-label">ID de Visita</div>
                            <div class="info-value-large">#{{ $visit->id }}</div>
                        </div>

                        <div class="info-item">
                            <div class="info-label">Fecha y Hora</div>
                            <div class="info-value">{{ \Carbon\Carbon::parse($visit->scheduled_at)->format('l, d \d\e F \d\e Y') }}</div>
                            <div class="info-value">{{ \Carbon\Carbon::parse($visit->scheduled_at)->format('h:i A') }}</div>
                        </div>

                        <div class="info-item">
                            <div class="info-label">Sitio</div>
                            <div class="info-value">{{ $visit->site->name }}</div>
                            @if($visit->site->location)
                            <div class="info-subvalue">{{ $visit->site->location }}</div>
                            @endif
                        </div>

                        <div class="info-item">
                            <div class="info-label">Departamento</div>
                            <div class="info-value">{{ $visit->department->name }}</div>
                        </div>

                        <div class="info-item">
                            <div class="info-label">Propósito</div>
                            <div class="info-value">{{ $visit->purpose }}</div>
                        </div>

                        @if($visit->notes)
                        <div class="info-item">
                            <div class="info-label">Notas</div>
                            <div class="info-value">{{ $visit->notes }}</div>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- QR Code y instrucciones (Columna derecha) -->
                <div class="right-section">
                    <div class="qr-section">
                        <h3 class="qr-title">Código QR de Verificación</h3>
                        <div style="text-align: center; margin: 16px 0;">
                            <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=VISIT-{{ $visit->id }}" 
                                 alt="QR Code" 
                                 style="border: 2px solid #e5e7eb; border-radius: 4px; max-width: 200px; height: auto;">
                        </div>
                        <p class="qr-description">Presenta este código QR al llegar a las instalaciones</p>
                        <div class="qr-id">ID: VISIT-{{ $visit->id }}</div>
                    </div>

                    <div class="instructions">
                        <h4 class="instructions-title">📋 Instrucciones</h4>
                        <ul class="instructions-list">
                            <li>Llega 10 minutos antes de tu cita</li>
                            <li>Presenta una identificación válida</li>
                            <li>Muestra este código QR en recepción</li>
                            <li>Mantén este documento contigo durante la visita</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p class="footer-text">Documento generado el {{ now()->format('d/m/Y \a \l\a\s H:i') }}</p>
        </div>
    </div>
</body>
</html>