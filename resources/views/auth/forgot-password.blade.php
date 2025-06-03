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
            {{ __('Forgot your password? No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.') }}
        </div>

        @session('status')
            <div class="mb-4 font-medium text-sm text-green-600">
                {{ $value }}
            </div>
        @endsession

        <x-validation-errors class="mb-4" />

        <form method="POST" action="{{ route('password.email') }}">
            @csrf

            <div class="block">
                <x-label for="email" value="{{ __('Email') }}" />
                <x-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')"
                    required autofocus autocomplete="username" />
            </div>

            <div class="flex items-center justify-end mt-4">
                <x-button>
                    {{ __('Email Password Reset Link') }}
                </x-button>
            </div>
        </form>
    </x-authentication-card>
</x-guest-layout>
