@extends('layouts.vertical')

@section('content')
    <div class="mx-auto max-w-3xl">
        <div class="rounded-2xl border border-rose-100 bg-white p-8 shadow-sm">
            <div class="flex flex-col gap-4">
                <span class="inline-flex h-12 w-12 items-center justify-center rounded-full bg-rose-50 text-rose-500">
                    <iconify-icon icon="solar:shield-warning-line" class="text-2xl"></iconify-icon>
                </span>
                <div class="space-y-2">
                    <h1 class="text-2xl font-semibold text-slate-900">Módulo no disponible en tu plan</h1>
                    <p class="text-sm text-slate-600">
                        Este módulo no está activo para tu clínica. Si necesitas acceso, puedes solicitar una
                        actualización de plan.
                    </p>
                </div>
                <div class="flex flex-wrap gap-3">
                    <a
                        href="{{ route('settings.clinica.edit') }}"
                        class="rounded-xl bg-indigo-600 px-5 py-2 text-sm font-semibold text-white hover:bg-indigo-500"
                    >
                        Actualizar plan
                    </a>
                    <a
                        href="{{ route('dashboard') }}"
                        class="rounded-xl border border-slate-200 px-5 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50"
                    >
                        Volver al dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
