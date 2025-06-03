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

        <x-validation-errors class="mb-4" />

        <form method="POST" action="{{ route('password.update') }}">
            @csrf

            <input type="hidden" name="token" value="{{ $request->route('token') }}">

            <div class="block">
                <x-label for="email" value="{{ __('Email') }}" />
                <x-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email', $request->email)"
                    required autofocus autocomplete="username" />
            </div>

            <div class="mt-4">
                <x-label for="password" value="{{ __('Password') }}" />
                <x-input id="password" class="block mt-1 w-full" type="password" name="password" required
                    autocomplete="new-password" />
            </div>

            <div class="mt-4">
                <x-label for="password_confirmation" value="{{ __('Confirm Password') }}" />
                <x-input id="password_confirmation" class="block mt-1 w-full" type="password"
                    name="password_confirmation" required autocomplete="new-password" />
            </div>

            <div class="flex items-center justify-end mt-4">
                <x-button>
                    {{ __('Reset Password') }}
                </x-button>
            </div>
        </form>
    </x-authentication-card>
</x-guest-layout>
