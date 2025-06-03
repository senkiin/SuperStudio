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
  <title>Confirmación de tu Solicitud de Cita – Fotovalera</title>
</head>
<body style="margin:0;padding:0;font-family:Arial,sans-serif;background:#f4f4f4;">
  <table width="100%" cellpadding="0" cellspacing="0" style="background:#f4f4f4;padding:30px 0;">
    <tr>
      <td align="center">
        <table width="600" cellpadding="0" cellspacing="0" style="background:#ffffff;border-radius:8px;overflow:hidden;">

          {{-- HEADER --}}
          <tr>
            <td style="padding:20px;text-align:center;background:#ffffff;">
              <a href="{{ url('/') }}">
                <img src="{{ $logoUrl }}" alt="Fotovalera" style="max-width:200px;height:auto;">
              </a>
            </td>
          </tr>

          {{-- CUERPO --}}
          <tr>
            <td style="padding:30px;color:#333;">
              <h1 style="margin-top:0;">¡Tu Solicitud de Cita ha sido Recibida!</h1>
              <p>Hola <strong>{{ $appointmentName }}</strong>,</p>
              <p>Hemos recibido tu solicitud de cita en Fotovalera. Aquí tienes los detalles:</p>
              <ul style="line-height:1.6;">
                <li><strong>Servicio Principal:</strong> {{ $primaryServiceName }}</li>
                @if(!empty($additionalServices) && $additionalServices !== 'Ninguno')
                  <li><strong>Servicios Adicionales:</strong> {{ $additionalServices }}</li>
                @endif
                <li><strong>Fecha y Hora:</strong> {{ $appointmentDateTime }}</li>
                @if($appointmentNotes)
                  <li><strong>Notas Adicionales:</strong> {{ $appointmentNotes }}</li>
                @endif
              </ul>
              <p style="text-align:center;margin:30px 0;">
                <a href="{{ url('/contacto') }}"
                   style="background:#2d3748;color:#fff;padding:12px 24px;border-radius:4px;text-decoration:none;">
                  Contactar al Equipo
                </a>
              </p>
              <p>Nos pondremos en contacto contigo a la brevedad para confirmar la disponibilidad y los detalles finales de tu cita.</p>
              <p>¡Gracias por elegirnos!</p>
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
