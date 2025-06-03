@php
    use Illuminate\Support\Facades\Storage;
    // URL temporal S3 para tu logo
    $logoUrl = Storage::disk('logos')->temporaryUrl(
        'SuperLogo.png',
        now()->addMinutes(30)
    );
@endphp

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Nueva Solicitud de Cita – Fotovalera</title>
</head>
<body style="margin:0;padding:0;font-family:Arial,sans-serif;background:#f4f4f4;">

  <table width="100%" cellpadding="0" cellspacing="0" style="background:#f4f4f4;padding:50px 0 0;">
    <tr>
      <td align="center">

        {{-- Logo “flotando” sobre el contenedor --}}
        <table cellpadding="0" cellspacing="0" style="margin-bottom:-40px;">
          <tr>
            <td align="center">
              <img src="{{ $logoUrl }}"
                   alt="Fotovalera"
                   style="max-width:150px; height:auto; display:block;">
            </td>
          </tr>
        </table>

        {{-- Contenedor blanco principal --}}
        <table width="600" cellpadding="0" cellspacing="0"
               style="background:#ffffff;border-radius:8px;overflow:hidden;box-shadow:0 2px 8px rgba(0,0,0,0.1);">

          {{-- Espacio superior para que no choque con el logo --}}
          <tr>
            <td style="height:40px; line-height:40px; font-size:0;">&nbsp;</td>
          </tr>

          {{-- CUERPO --}}
          <tr>
            <td style="padding:30px;color:#333;">
              <h1 style="margin-top:0;font-size:24px;">Nueva Solicitud de Cita (SuperAppointment) Recibida</h1>
              <p>Se ha recibido una nueva solicitud de cita con los siguientes detalles:</p>
              <ul style="line-height:1.6;margin:0 0 20px 0;padding-left:20px;">
                <li><strong>ID de Cita:</strong> {{ $appointmentId }}</li>
                <li><strong>Nombre Cliente:</strong> {{ $clientName }}</li>
                <li><strong>Email Cliente:</strong>
                  <a href="mailto:{{ $clientEmail }}">{{ $clientEmail }}</a>
                </li>
                <li><strong>Teléfono Cliente:</strong> {{ $clientPhone }}</li>
                <li><strong>Servicio Principal Solicitado:</strong> {{ $primaryServiceName }}</li>
                @if(!empty($additionalServices) && $additionalServices !== 'Ninguno')
                  <li><strong>Servicios Adicionales Solicitados:</strong> {{ $additionalServices }}</li>
                @endif
                <li><strong>Fecha y Hora Solicitada:</strong> {{ $appointmentDateTime }}</li>
                @if($appointmentNotes)
                  <li><strong>Notas Adicionales del Cliente:</strong> {{ $appointmentNotes }}</li>
                @endif
              </ul>
              <p style="text-align:center;margin:30px 0;">
                <a href="{{ route('admin.dashboard') }}"
                   style="background:#2d3748;color:#fff;padding:12px 24px;border-radius:4px;text-decoration:none;display:inline-block;">
                  Ir al Panel de Admin
                </a>
              </p>
            </td>
          </tr>

          {{-- FOOTER --}}
          <tr>
            <td style="padding:20px;text-align:center;font-size:12px;color:#777;background:#fafafa;">
              © {{ date('Y') }} Fotovalera. Todos los derechos reservados.<br>
              El equipo de Fotovalera
            </td>
          </tr>
        </table>

      </td>
    </tr>
  </table>

</body>
</html>
