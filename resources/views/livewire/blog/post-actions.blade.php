<div class="mt-12 border-t pt-8">
    <div class="flex items-center space-x-4 mb-8">
        <button wire:click="toggleLike" class="flex items-center space-x-2 text-gray-500 hover:text-red-500 transition-colors">
            <svg class="w-8 h-8 {{ $isLikedByUser ? 'text-red-500 fill-current' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 016.364 0L12 7.758l1.318-1.44a4.5 4.5 0 116.364 6.364L12 20.06 4.318 12.682a4.5 4.5 0 010-6.364z"></path>
            </svg>
            <span class="font-semibold">{{ $likesCount }} {{ Str::plural('Like', $likesCount) }}</span>
        </button>
    </div>

    <h3 class="text-2xl font-bold mb-4">Comentarios</h3>
    @auth
        <form wire:submit.prevent="addComment" class="mb-6">
            <textarea wire:model.defer="comment" class="w-full border-gray-300 bg-black rounded-md shadow-sm" rows="3" placeholder="Escribe tu comentario..."></textarea>
            @error('comment') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            <div class="text-right mt-2">
                <x-button type="submit">Publicar Comentario</x-button>
            </div>
        </form>
    @else
        <p class="mb-6 text-gray-600">Debes <a href="{{ route('login') }}" class="text-indigo-600 hover:underline">iniciar sesión</a> para dejar un comentario.</p>
    @endauth

    <div class="space-y-6">
        @forelse($post->comments as $comment)
            <div class="flex space-x-4">
                <img src="{{ $comment->user->profile_photo_url }}" alt="{{ $comment->user->name }}" class="w-12 h-12 rounded-full">
                <div>
                    <p class="font-semibold">{{ $comment->user->name }} <span class="text-gray-400 text-sm font-normal">{{ $comment->created_at->diffForHumans() }}</span></p>
                    <p class="text-gray-700">{{ $comment->content }}</p>
                </div>
            </div>
        @empty
            <p class="text-gray-500">Sé el primero en comentar.</p>
        @endforelse
    </div>
</div>
