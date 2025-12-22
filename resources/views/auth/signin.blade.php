@extends('layouts.guest')

@section('content')
<div class="w-full max-w-xl">
    <div class="bg-white border border-[#E5E7EB] shadow-lg shadow-slate-200/40 rounded-2xl p-8 sm:p-10 space-y-8">
        <div class="text-center space-y-3">
            <div class="mx-auto h-14 w-14 rounded-2xl bg-[#ECFDF5] flex items-center justify-center text-[#10B981] font-semibold text-lg shadow-sm">
                VT
            </div>
            <div class="space-y-1">
                <h1 class="text-2xl font-semibold text-[#111827]">{{ config('app.name', 'VetTech') }}</h1>
                <p class="text-sm text-[#374151]">Inicia sesión para continuar con tu experiencia clínica.</p>
            </div>
        </div>

        <form method="POST" action="{{ route('signin.store') }}" class="space-y-6">
            @csrf
            <div class="space-y-2">
                <label for="email" class="block text-sm font-medium text-[#111827]">Correo</label>
                <input
                    id="email"
                    type="email"
                    name="email"
                    value="{{ old('email') }}"
                    required
                    autocomplete="email"
                    class="w-full rounded-xl border border-[#E5E7EB] bg-white px-4 py-3 text-[#111827] placeholder:text-[#9CA3AF] focus:border-[#10B981] focus:ring-2 focus:ring-[#10B981] focus:outline-none transition"
                    placeholder="correo@ejemplo.com"
                >
                @error('email')
                    <p class="text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="space-y-2">
                <div class="flex items-center justify-between">
                    <label for="password" class="block text-sm font-medium text-[#111827]">Contraseña</label>
                    <button
                        type="button"
                        class="text-xs font-medium text-[#10B981] hover:text-[#0f9a70] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#10B981] rounded-lg px-2 py-1"
                        data-toggle-password
                        data-target="password"
                        data-show-text="Ver contraseña"
                        data-hide-text="Ocultar"
                        aria-label="Ver contraseña"
                    >
                        Ver contraseña
                    </button>
                </div>
                <input
                    id="password"
                    type="password"
                    name="password"
                    required
                    autocomplete="current-password"
                    class="w-full rounded-xl border border-[#E5E7EB] bg-white px-4 py-3 text-[#111827] placeholder:text-[#9CA3AF] focus:border-[#10B981] focus:ring-2 focus:ring-[#10B981] focus:outline-none transition"
                    placeholder="••••••••"
                >
                @error('password')
                    <p class="text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 text-sm">
                <label for="remember" class="inline-flex items-center gap-2 text-[#374151]">
                    <input
                        id="remember"
                        type="checkbox"
                        name="remember"
                        {{ old('remember') ? 'checked' : '' }}
                        class="h-4 w-4 rounded border-[#E5E7EB] text-[#10B981] focus:ring-[#10B981]"
                    >
                    <span>Recuérdame</span>
                </label>
                <a href="#" class="text-[#10B981] font-medium hover:text-[#0f9a70]">Olvidé mi contraseña</a>
            </div>

            <div class="space-y-3">
                <button
                    type="submit"
                    class="w-full rounded-xl bg-[#10B981] px-4 py-3 text-white font-semibold shadow-sm hover:bg-[#0f9a70] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#10B981] transition"
                >
                    Iniciar sesión
                </button>
                <p class="text-center text-sm text-[#374151]">
                    ¿Aún no tienes cuenta?
                    <a href="/signup" class="font-semibold text-[#10B981] hover:text-[#0f9a70]">Crear cuenta</a>
                </p>
            </div>
        </form>
    </div>
</div>
@endsection
