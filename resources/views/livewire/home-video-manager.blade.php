<div>
    {{-- Mensaje de éxito --}}
    @if (session()->has('message'))
        <div style="padding: 10px; margin-bottom: 15px; background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; border-radius: 4px;">
            {{ session('message') }}
        </div>
    @endif

    {{-- Formulario de subida --}}
    <form wire:submit.prevent="save">
        <div>
            <label for="videoUpload">Subir nuevo video:</label>
            <input type="file" id="videoUpload" wire:model="video">

            {{-- Indicador de carga --}}
            <div wire:loading wire:target="video" style="color: blue; margin-top: 5px;">
                Subiendo video...
            </div>

            {{-- Errores de validación --}}
            @error('video') <span style="color: red; display: block; margin-top: 5px;">{{ $message }}</span> @enderror
        </div>

        <button type="submit" style="margin-top: 10px;">Guardar Video</button>
    </form>

    <hr style="margin: 20px 0;">

    {{-- Mostrar video actual y opción de borrar --}}
    @if ($videoPath)
        <div>
            <h3>Video Actual:</h3>
            <video width="400" controls>
                {{-- Asegúrate de haber ejecutado php artisan storage:link --}}
                <source src="{{ Storage::url($videoPath) }}" type="video/mp4">
                Tu navegador no soporta la etiqueta de video.
            </video>

            <button wire:click="deleteVideo"
                    wire:confirm="¿Estás seguro de que quieres eliminar este video?"
                    style="display: block; margin-top: 10px; background-color: #f44336; color: white; border: none; padding: 8px 15px; cursor: pointer;">
                Eliminar Video
            </button>
             {{-- Indicador de carga para borrado --}}
             <div wire:loading wire:target="deleteVideo" style="color: red; margin-top: 5px;">
                Eliminando...
            </div>
        </div>
    @else
        <p>No hay ningún video subido actualmente.</p>
    @endif
</div>
