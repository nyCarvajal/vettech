@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-6xl space-y-6">
    <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h1 class="text-2xl font-semibold text-slate-900">Configuración de Clínica</h1>
                <p class="text-sm text-slate-500">Administra la información legal, operativa y de branding para tus imprimibles.</p>
            </div>
            <span class="rounded-full bg-indigo-50 px-4 py-1 text-xs font-semibold text-indigo-600">Solo Administradores</span>
        </div>
    </div>

    @if (session('status'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
            {{ session('status') }}
        </div>
    @endif

    <div x-data="{ tab: 'general' }" class="grid gap-6 lg:grid-cols-[280px_1fr]">
        <div class="space-y-2">
            @php
                $tabs = [
                    'general' => 'Datos generales',
                    'modules' => 'Módulos del plan',
                    'branding' => 'Branding',
                    'billing' => 'Facturación y pagos',
                    'dian' => 'DIAN',
                ];
                $featureDefaults = \App\Models\Clinica::featureDefaults();
                $featureOptions = [
                    'agenda' => [
                        'label' => 'Agenda',
                        'description' => 'Citas, reservas y calendario.',
                    ],
                    'facturacion_pos' => [
                        'label' => 'Facturación POS',
                        'description' => 'Cobros rápidos y emisión de facturas.',
                    ],
                    'tutores' => [
                        'label' => 'Tutores',
                        'description' => 'Gestión de propietarios de mascotas.',
                    ],
                    'pacientes' => [
                        'label' => 'Pacientes',
                        'description' => 'Registro y seguimiento clínico.',
                    ],
                    'dispensacion' => [
                        'label' => 'Dispensario',
                        'description' => 'Entrega y control de medicamentos.',
                    ],
                    'hospitalizacion' => [
                        'label' => 'Hospitalización 24/7',
                        'description' => 'Manejo de estancias y monitoreo.',
                    ],
                    'cirugia' => [
                        'label' => 'Cirugía y procedimientos',
                        'description' => 'Registro y seguimiento de cirugías.',
                    ],
                    'belleza' => [
                        'label' => 'Peluquería',
                        'description' => 'Servicios de grooming y estética.',
                    ],
                    'consentimientos' => [
                        'label' => 'Consentimientos',
                        'description' => 'Documentos clínicos firmados.',
                    ],
                    'plantillas_consentimientos' => [
                        'label' => 'Plantillas de consentimientos',
                        'description' => 'Modelos reutilizables de formularios.',
                    ],
                    'arqueo_caja' => [
                        'label' => 'Arqueo de caja',
                        'description' => 'Cierres y consolidación de caja.',
                    ],
                    'reportes_basicos' => [
                        'label' => 'Reportes básicos',
                        'description' => 'Indicadores rápidos del negocio.',
                    ],
                    'reportes_avanzados' => [
                        'label' => 'Reportes avanzados',
                        'description' => 'Reportes detallados y analíticos.',
                    ],
                    'config_clinica' => [
                        'label' => 'Configuración de clínica',
                        'description' => 'Acceso al panel de ajustes.',
                    ],
                ];
            @endphp
            @foreach ($tabs as $key => $label)
                <button
                    type="button"
                    class="flex w-full items-center justify-between rounded-xl border px-4 py-3 text-left text-sm font-semibold transition"
                    :class="tab === '{{ $key }}' ? 'border-indigo-500 bg-indigo-50 text-indigo-700' : 'border-slate-200 bg-white text-slate-600 hover:bg-slate-50'"
                    @click="tab = '{{ $key }}'"
                >
                    <span>{{ $label }}</span>
                    <span class="text-xs" :class="tab === '{{ $key }}' ? 'text-indigo-600' : 'text-slate-400'">→</span>
                </button>
            @endforeach
        </div>

        <div class="space-y-6">
            <form method="POST" action="{{ route('settings.clinica.update') }}" class="space-y-6 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                @csrf
                @method('PUT')

                <div x-show="tab === 'general'" x-cloak class="space-y-6">
                    <div class="grid gap-5 md:grid-cols-2">
                        <div>
                            <label class="text-sm font-medium text-slate-700">Nombre de la clínica</label>
                            <input type="text" name="name" value="{{ old('name', $clinica->name ?? $clinica->nombre) }}" class="mt-2 w-full rounded-xl border-slate-200 text-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                            @error('name')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="text-sm font-medium text-slate-700">NIT</label>
                            <input type="text" name="nit" value="{{ old('nit', $clinica->nit) }}" class="mt-2 w-full rounded-xl border-slate-200 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label class="text-sm font-medium text-slate-700">Dígito de verificación (DV)</label>
                            <input type="text" name="dv" value="{{ old('dv', $clinica->dv) }}" class="mt-2 w-full rounded-xl border-slate-200 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label class="text-sm font-medium text-slate-700">Régimen</label>
                            <input type="text" name="regimen" value="{{ old('regimen', $clinica->regimen) }}" class="mt-2 w-full rounded-xl border-slate-200 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                        <div class="flex items-center gap-3 rounded-xl border border-slate-200 p-4">
                            <input id="responsable_iva" type="checkbox" name="responsable_iva" value="1" class="h-4 w-4 rounded border-slate-300 text-indigo-600" @checked(old('responsable_iva', $clinica->responsable_iva))>
                            <label for="responsable_iva" class="text-sm text-slate-700">Responsable de IVA</label>
                        </div>
                    </div>

                    <div class="grid gap-5 md:grid-cols-2">
                        <div>
                            <label class="text-sm font-medium text-slate-700">Correo electrónico</label>
                            <input type="email" name="email" value="{{ old('email', $clinica->email) }}" class="mt-2 w-full rounded-xl border-slate-200 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label class="text-sm font-medium text-slate-700">Teléfono</label>
                            <input type="text" name="phone" value="{{ old('phone', $clinica->phone) }}" class="mt-2 w-full rounded-xl border-slate-200 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label class="text-sm font-medium text-slate-700">Dirección</label>
                            <input type="text" name="address" value="{{ old('address', $clinica->address ?? $clinica->direccion) }}" class="mt-2 w-full rounded-xl border-slate-200 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label class="text-sm font-medium text-slate-700">Ciudad</label>
                            <input type="text" name="city" value="{{ old('city', $clinica->city) }}" class="mt-2 w-full rounded-xl border-slate-200 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label class="text-sm font-medium text-slate-700">Departamento / Estado</label>
                            <input type="text" name="department" value="{{ old('department', $clinica->department) }}" class="mt-2 w-full rounded-xl border-slate-200 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label class="text-sm font-medium text-slate-700">País</label>
                            <input type="text" name="country" value="{{ old('country', $clinica->country ?? 'CO') }}" class="mt-2 w-full rounded-xl border-slate-200 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label class="text-sm font-medium text-slate-700">Sitio web</label>
                            <input type="text" name="website" value="{{ old('website', $clinica->website) }}" class="mt-2 w-full rounded-xl border-slate-200 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label class="text-sm font-medium text-slate-700">Zona horaria</label>
                            <input type="text" name="timezone" value="{{ old('timezone', $clinica->timezone ?? 'America/Bogota') }}" class="mt-2 w-full rounded-xl border-slate-200 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label class="text-sm font-medium text-slate-700">Moneda</label>
                            <input type="text" name="currency" value="{{ old('currency', $clinica->currency ?? 'COP') }}" class="mt-2 w-full rounded-xl border-slate-200 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                    </div>
                </div>

                <div x-show="tab === 'modules'" x-cloak class="space-y-6">
                    <div class="rounded-xl border border-indigo-100 bg-indigo-50 p-4 text-sm text-indigo-700">
                        Define qué módulos están disponibles para la clínica según su plan contratado.
                    </div>
                    <div class="grid gap-4 md:grid-cols-2">
                        @foreach ($featureOptions as $key => $feature)
                            <label class="flex h-full cursor-pointer items-start gap-4 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm transition hover:border-indigo-200">
                                <span class="mt-1 inline-flex h-10 w-10 items-center justify-center rounded-xl bg-indigo-50 text-indigo-600">
                                    <iconify-icon icon="solar:widget-4-line"></iconify-icon>
                                </span>
                                <span class="flex-1 space-y-1">
                                    <span class="block text-sm font-semibold text-slate-800">{{ $feature['label'] }}</span>
                                    <span class="block text-xs text-slate-500">{{ $feature['description'] }}</span>
                                </span>
                                <span class="flex items-center">
                                    <input type="hidden" name="features[{{ $key }}]" value="0">
                                    <input
                                        type="checkbox"
                                        name="features[{{ $key }}]"
                                        value="1"
                                        class="h-5 w-5 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500"
                                        @checked(old("features.{$key}", $clinica->featureEnabled($key, $featureDefaults[$key])))
                                    >
                                </span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <div x-show="tab === 'branding'" x-cloak class="space-y-6">
                    <div class="rounded-xl border border-dashed border-slate-200 bg-slate-50 p-5">
                        <p class="text-sm text-slate-600">El logo se usa en todos los imprimibles y reportes PDF.</p>
                    </div>
                    <div class="grid gap-5 md:grid-cols-2">
                        <div>
                            <label class="text-sm font-medium text-slate-700">Color primario</label>
                            <input type="text" name="primary_color" value="{{ old('primary_color', $clinica->primary_color) }}" placeholder="#6D28D9" class="mt-2 w-full rounded-xl border-slate-200 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label class="text-sm font-medium text-slate-700">Color secundario</label>
                            <input type="text" name="secondary_color" value="{{ old('secondary_color', $clinica->secondary_color) }}" placeholder="#0EA5E9" class="mt-2 w-full rounded-xl border-slate-200 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                        <div class="md:col-span-2">
                            <label class="text-sm font-medium text-slate-700">Nota de encabezado</label>
                            <textarea name="header_note" rows="3" class="mt-2 w-full rounded-xl border-slate-200 text-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('header_note', $clinica->header_note) }}</textarea>
                        </div>
                        <div class="md:col-span-2">
                            <label class="text-sm font-medium text-slate-700">Nota de pie de página</label>
                            <textarea name="footer_note" rows="3" class="mt-2 w-full rounded-xl border-slate-200 text-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('footer_note', $clinica->footer_note) }}</textarea>
                        </div>
                    </div>
                </div>

                <div x-show="tab === 'billing'" x-cloak class="space-y-6">
                    <div class="grid gap-5 md:grid-cols-2">
                        <div class="md:col-span-2">
                            <label class="text-sm font-medium text-slate-700">Términos de pago</label>
                            <textarea name="payment_terms" rows="3" class="mt-2 w-full rounded-xl border-slate-200 text-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('payment_terms', $clinica->payment_terms) }}</textarea>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-slate-700">Vencimiento (días)</label>
                            <input type="number" name="payment_due_days" value="{{ old('payment_due_days', $clinica->payment_due_days) }}" class="mt-2 w-full rounded-xl border-slate-200 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label class="text-sm font-medium text-slate-700">Prefijo de factura</label>
                            <input type="text" name="invoice_prefix" value="{{ old('invoice_prefix', $clinica->invoice_prefix) }}" class="mt-2 w-full rounded-xl border-slate-200 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label class="text-sm font-medium text-slate-700">IVA por defecto (%)</label>
                            <input type="number" step="0.001" name="default_tax_rate" value="{{ old('default_tax_rate', $clinica->default_tax_rate) }}" class="mt-2 w-full rounded-xl border-slate-200 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label class="text-sm font-medium text-slate-700">WhatsApp</label>
                            <input type="text" name="whatsapp_number" value="{{ old('whatsapp_number', $clinica->whatsapp_number) }}" class="mt-2 w-full rounded-xl border-slate-200 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                        <div class="md:col-span-2">
                            <label class="text-sm font-medium text-slate-700">Datos bancarios</label>
                            <textarea name="bank_account_info" rows="3" class="mt-2 w-full rounded-xl border-slate-200 text-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('bank_account_info', $clinica->bank_account_info) }}</textarea>
                        </div>
                        <div class="md:col-span-2">
                            <label class="text-sm font-medium text-slate-700">Leyenda legal para facturas</label>
                            <textarea name="invoice_footer_legal" rows="3" class="mt-2 w-full rounded-xl border-slate-200 text-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('invoice_footer_legal', $clinica->invoice_footer_legal) }}</textarea>
                        </div>
                    </div>
                </div>

                <div x-show="tab === 'dian'" x-cloak class="space-y-6">
                    <div class="rounded-xl border border-slate-200 bg-slate-50 p-4 text-sm text-slate-600">
                        Configuración preparatoria para integración DIAN. Desactiva si aún no aplica.
                    </div>
                    <div class="flex items-center gap-3 rounded-xl border border-slate-200 p-4">
                        <input id="dian_enabled" type="checkbox" name="dian_enabled" value="1" class="h-4 w-4 rounded border-slate-300 text-indigo-600" @checked(old('dian_enabled', $clinica->dian_enabled))>
                        <label for="dian_enabled" class="text-sm text-slate-700">Habilitar DIAN</label>
                    </div>
                    <div class="grid gap-5 md:grid-cols-2">
                        <div>
                            <label class="text-sm font-medium text-slate-700">Software ID</label>
                            <input type="text" name="dian_software_id" value="{{ old('dian_software_id', $clinica->dian_software_id) }}" class="mt-2 w-full rounded-xl border-slate-200 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label class="text-sm font-medium text-slate-700">Software PIN</label>
                            <input type="text" name="dian_software_pin" value="{{ old('dian_software_pin', $clinica->dian_software_pin) }}" class="mt-2 w-full rounded-xl border-slate-200 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label class="text-sm font-medium text-slate-700">Test set ID</label>
                            <input type="text" name="dian_test_set_id" value="{{ old('dian_test_set_id', $clinica->dian_test_set_id) }}" class="mt-2 w-full rounded-xl border-slate-200 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label class="text-sm font-medium text-slate-700">Prefijo resolución</label>
                            <input type="text" name="dian_resolution_prefix" value="{{ old('dian_resolution_prefix', $clinica->dian_resolution_prefix) }}" class="mt-2 w-full rounded-xl border-slate-200 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label class="text-sm font-medium text-slate-700">Número resolución</label>
                            <input type="text" name="dian_resolution_number" value="{{ old('dian_resolution_number', $clinica->dian_resolution_number) }}" class="mt-2 w-full rounded-xl border-slate-200 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label class="text-sm font-medium text-slate-700">Desde</label>
                            <input type="number" name="dian_resolution_from" value="{{ old('dian_resolution_from', $clinica->dian_resolution_from) }}" class="mt-2 w-full rounded-xl border-slate-200 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label class="text-sm font-medium text-slate-700">Hasta</label>
                            <input type="number" name="dian_resolution_to" value="{{ old('dian_resolution_to', $clinica->dian_resolution_to) }}" class="mt-2 w-full rounded-xl border-slate-200 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label class="text-sm font-medium text-slate-700">Fecha resolución</label>
                            <input type="date" name="dian_resolution_date" value="{{ old('dian_resolution_date', optional($clinica->dian_resolution_date)->format('Y-m-d')) }}" class="mt-2 w-full rounded-xl border-slate-200 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                    </div>
                </div>

                <div class="flex flex-wrap items-center justify-between gap-3 border-t border-slate-200 pt-6">
                    <p class="text-xs text-slate-400">Los cambios se reflejarán en facturas, recibos y reportes PDF.</p>
                    <button type="submit" class="rounded-xl bg-indigo-600 px-6 py-2 text-sm font-semibold text-white shadow hover:bg-indigo-500">Guardar cambios</button>
                </div>
            </form>

            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm" x-data="{ preview: null }">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-lg font-semibold text-slate-900">Logo de la clínica</h2>
                        <p class="text-sm text-slate-500">PNG, JPG o WEBP. Máximo 2MB. Recomendado 600x200.</p>
                    </div>
                </div>
                <div class="mt-6 grid gap-6 lg:grid-cols-[220px_1fr]">
                    <div class="rounded-xl border border-dashed border-slate-200 bg-slate-50 p-4 text-center">
                        @php
                            $logoUrl = $clinica->logo_path ? asset('storage/' . $clinica->logo_path) : asset('images/logo-dark.png');
                        @endphp
                        <img :src="preview || '{{ $logoUrl }}'" alt="Logo clínica" class="mx-auto h-20 w-auto object-contain">
                    </div>
                    <div class="space-y-4">
                        <form method="POST" action="{{ route('settings.clinica.logo.store') }}" enctype="multipart/form-data" class="space-y-4">
                            @csrf
                            <input
                                type="file"
                                name="logo"
                                accept="image/png,image/jpeg,image/webp"
                                @change="
                                    const file = $event.target.files[0];
                                    if (!file) { preview = null; return; }
                                    const reader = new FileReader();
                                    reader.onload = (e) => preview = e.target.result;
                                    reader.readAsDataURL(file);
                                "
                                class="block w-full text-sm text-slate-600 file:mr-4 file:rounded-xl file:border-0 file:bg-indigo-50 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-indigo-700 hover:file:bg-indigo-100"
                            >
                            @error('logo')
                                <p class="text-xs text-red-600">{{ $message }}</p>
                            @enderror
                            <button type="submit" class="rounded-xl bg-slate-900 px-5 py-2 text-sm font-semibold text-white hover:bg-slate-800">Subir logo</button>
                        </form>
                        @if ($clinica->logo_path)
                            <form method="POST" action="{{ route('settings.clinica.logo.destroy') }}">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="rounded-xl border border-rose-200 px-4 py-2 text-sm font-semibold text-rose-600 hover:bg-rose-50">Quitar logo</button>
                            </form>
                        @endif
                        <p class="text-xs text-slate-400">Asegúrate de ejecutar <code>php artisan storage:link</code> para servir los archivos públicos.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
