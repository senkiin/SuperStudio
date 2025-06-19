<div class="p-4">
    <h3 class="text-lg font-medium text-gray-900 mb-4">Selecciona un Video de la Galería</h3>
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 max-h-96 overflow-y-auto">
        @forelse($videos as $video)
            <div class="cursor-pointer border-2 border-transparent hover:border-indigo-500 rounded-lg p-2"
                 wire:click="selectVideo('{{ $video->url }}')">
                <img src="{{ $video->thumbnail_url ?? 'https://via.placeholder.com/150' }}" alt="{{ $video->title }}" class="w-full h-24 object-cover rounded">
                <p class="text-sm mt-2 truncate">{{ $video->title }}</p>
            </div>
        @empty
            <p class="col-span-full text-gray-500">No hay videos en la galería.</p>
        @endforelse
    </div>
</div>
