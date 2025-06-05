@php
    use Illuminate\Support\Facades\Storage;

    // URL temporal S3 para tu logo
    // Asegúrate de que el disco 'logos' esté configurado correctamente en config/filesystems.php
    // y que el archivo 'SuperLogo.png' exista en ese disco.
    try {
        $logoUrl = Storage::disk('logos')->temporaryUrl(
            'SuperLogo.png',
            now()->addMinutes(30)
        );
    } catch (\Exception $e) {
        \Log::error('Error al generar S3 temporaryUrl para el logo en email: ' . $e->getMessage());
        $logoUrl = null; // O asset('images/default-logo.png') si tienes uno
    }
@endphp

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Nueva Solicitud de Cita – Fotovalera</title>
  {{-- El bloque <style> que estaba aquí ha sido eliminado según tu instrucción --}}
</head>
<body style="margin:0;padding:0;font-family:Arial,sans-serif;background:#f4f4f4;">
  <table width="100%" cellpadding="0" cellspacing="0" style="background:#f4f4f4;padding:30px 0;">
    <tr>
      <td align="center">
        <table width="600" cellpadding="0" cellspacing="0" style="background:#ffffff;border-radius:8px;overflow:hidden;{{-- Nota: Algunos estilos inline permanecen del original --}}">

          {{-- HEADER --}}
          <tr>
            <td style="padding:20px;text-align:center;background:#ffffff;">
              <a href="{{ url('/') }}" target="_blank">
                @if($logoUrl)
                    <img src="{{ $logoUrl }}" alt="Fotovalera" style="max-width:200px;height:auto;">
                @else
                    <h2 style="margin:0;color:#2d3748;">Fotovalera</h2> {{-- Fallback si no hay logo --}}
                @endif
              </a>
            </td>
          </tr>

          {{-- CUERPO --}}
          <tr>
            <td style="padding:30px;color:#333;">
              <h1 style="margin-top:0;font-size:24px;color:#2d3748;font-weight:bold;{{-- Nota: Algunos estilos inline permanecen del original --}}">Nueva Solicitud de Contacto Recibida</h1>
              <p style="margin: 10px 0 20px;{{-- Nota: Algunos estilos inline permanecen del original --}}">Hola Admin,</p>
              <p style="margin: 10px 0 20px;{{-- Nota: Algunos estilos inline permanecen del original --}}">Has recibido una nueva solicitud a través del formulario de contacto de la web "bolita de Contáctanos". Aquí están los detalles:</p>
              <ul style="line-height:1.6;list-style-type:none;padding-left:0;margin:20px 0;{{-- Nota: Algunos estilos inline permanecen del original --}}">
                <li style="padding:10px 0;border-bottom:1px dashed #dddddd;{{-- Nota: Algunos estilos inline permanecen del original --}}"><strong>Nombre del Solicitante:</strong> {{ $name }}</li>
                <li style="padding:10px 0;border-bottom:1px dashed #dddddd;{{-- Nota: Algunos estilos inline permanecen del original --}}"><strong>Email del Solicitante:</strong> <a href="mailto:{{ $email }}" style="color:#3683d6;text-decoration:none;">{{ $email }}</a></li>
                <li style="padding:10px 0;border-bottom:1px dashed #dddddd;{{-- Nota: Algunos estilos inline permanecen del original --}}"><strong>Motivo de la Cita:</strong> {{ $category }}</li>
                    <li><strong>Teléfono del Solicitante:</strong> {{ $phone }}</li> {{-- Mostrar teléfono --}}

                @if($description)
                  <li style="padding:10px 0;border-bottom:none;{{-- Nota: Algunos estilos inline permanecen del original --}}"><strong>Mensaje Adicional:</strong></li>
              </ul>
              <div style="background-color:#f9f9f9;border:1px solid #eeeeee;padding:15px;border-radius:4px;margin-top:10px;margin-bottom:25px;{{-- Nota: Algunos estilos inline permanecen del original --}}">
                 <p style="margin:0;white-space: pre-wrap;word-wrap:break-word;{{-- Nota: Algunos estilos inline permanecen del original --}}">{{ $description }}</p>
              </div>
                @else
              </ul> {{-- Cierra el UL si no hay descripción --}}
                @endif

              <p style="text-align:center;margin:30px 0;{{-- Nota: Algunos estilos inline permanecen del original --}}">
                <a href="mailto:{{ $email }}"
                   style="background:#2d3748;color:#fff;padding:12px 24px;border-radius:4px;text-decoration:none;font-weight:bold;display:inline-block;{{-- Nota: Algunos estilos inline permanecen del original --}}">
                  Responder al Usuario
                </a>
              </p>
              <p style="margin: 10px 0 20px;{{-- Nota: Algunos estilos inline permanecen del original --}}">Por favor, da seguimiento a esta solicitud a la brevedad.</p>
            </td>
          </tr>

          {{-- FOOTER --}}
          <tr>
            <td style="padding:20px;text-align:center;font-size:12px;color:#777;background:#fafafa;border-top: 1px solid #eeeeee;{{-- Nota: Algunos estilos inline permanecen del original --}}">
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
