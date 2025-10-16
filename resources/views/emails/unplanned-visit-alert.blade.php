<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alerta persona sin cita previa</title>
    <style>
        body { font-family: Arial, Helvetica, sans-serif; color: #111827; }
        .container { max-width: 640px; margin: 0 auto; padding: 24px; background: #ffffff; }
        .header { text-align: center; margin-bottom: 16px; }
        .badge { display: inline-block; padding: 6px 10px; font-size: 12px; border-radius: 9999px; background: #FDE68A; color: #92400E; font-weight: 600; }
        .title { font-size: 20px; font-weight: 700; margin: 8px 0 16px; }
        .section { background: #F9FAFB; border: 1px solid #E5E7EB; border-radius: 8px; padding: 16px; margin-bottom: 12px; }
        .row { display: flex; justify-content: space-between; margin-bottom: 8px; }
        .label { color: #6B7280; font-size: 12px; }
        .value { font-size: 14px; font-weight: 600; }
        .footer { font-size: 12px; color: #6B7280; margin-top: 16px; }
    </style>
    </head>
<body>
    <div class="container">
        <div class="header">
            <img src="{{ asset('images/logos/Novolex.png') }}" alt="Novolex" height="48" style="height:48px" />
            <div class="badge">Alerta persona sin cita previa</div>
            <div class="title">{{ $visit->site->name ?? 'Sitio' }} — {{ $visit->scheduled_at?->timezone(config('app.timezone'))->format('d/m/Y H:i') }}</div>
        </div>

        <div class="section">
            <div class="row"><div class="label">Visitante</div><div class="value">{{ $visit->visitor_name }}</div></div>
            @if($visit->visitor_email)
                <div class="row"><div class="label">Correo</div><div class="value">{{ $visit->visitor_email }}</div></div>
            @endif
            @if($visit->visitor_phone)
                <div class="row"><div class="label">Teléfono</div><div class="value">{{ $visit->visitor_phone }}</div></div>
            @endif
            @if($visit->company)
                <div class="row"><div class="label">Empresa</div><div class="value">{{ $visit->company }}</div></div>
            @endif
        </div>

        <div class="section">
            <div class="row"><div class="label">Sitio</div><div class="value">{{ $visit->site->name ?? 'N/A' }}</div></div>
            <div class="row"><div class="label">Tipo</div><div class="value">No programada</div></div>
            @if($visit->visit_to)
                <div class="row"><div class="label">Visita a</div><div class="value">{{ $visit->visit_to }}</div></div>
            @endif
            <div class="row"><div class="label">Propósito</div><div class="value">{{ $visit->purpose }}</div></div>
            <div class="row"><div class="label">Hora de registro</div><div class="value">{{ $visit->scheduled_at?->timezone(config('app.timezone'))->format('d/m/Y H:i') }}</div></div>
            @if($visit->notes)
                <div class="row" style="display:block"><div class="label">Notas</div><div class="value" style="font-weight:400">{{ $visit->notes }}</div></div>
            @endif
        </div>

        <div class="footer">
            Este correo se envió automáticamente a los usuarios suscritos a alertas de visitas no programadas del sitio correspondiente.
        </div>
    </div>
</body>
</html>
