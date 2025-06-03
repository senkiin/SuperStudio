<x-guest-layout>
    <x-authentication-card>
        <x-slot name="logo">
            @php
                // No uses la declaración 'use' aquí.
                // Utiliza el namespace completo para la fachada Storage.
                $logoUrl = \Illuminate\Support\Facades\Storage::disk('logos')->temporaryUrl(
                    'SuperLogo.png',
                    now()->addMinutes(30), // Asegúrate de que la función now() esté disponible o usa Carbon\Carbon::now()
                );
            @endphp
            {{-- Etiqueta img corregida y completada --}}
            <img src="{{ $logoUrl }}" alt="Logo de la empresa" class="h-48 w-auto" />
        </x-slot>

        <div class="mb-4 text-sm text-gray-600">
            {{ __('This is a secure area of the application. Please confirm your password before continuing.') }}
        </div>

        <x-validation-errors class="mb-4" />

        <form method="POST" action="{{ route('password.confirm') }}">
            @csrf

            <div>
                <x-label for="password" value="{{ __('Password') }}" />
                <x-input id="password" class="block mt-1 w-full" type="password" name="password" required
                    autocomplete="current-password" autofocus />
            </div>

            <div class="flex justify-end mt-4">
                <x-button class="ms-4">
                    {{ __('Confirm') }}
                </x-button>
            </div>
        </form>
    </x-authentication-card>
</x-guest-layout>
