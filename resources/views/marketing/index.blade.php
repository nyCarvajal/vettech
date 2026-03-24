@extends('layouts.app')

@section('content')
    <div class="mx-auto max-w-7xl space-y-6">
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                <div class="space-y-2">
                    <span class="inline-flex rounded-full bg-fuchsia-50 px-3 py-1 text-xs font-semibold uppercase tracking-[0.2em] text-fuchsia-700">
                        Marketing
                    </span>
                    <div>
                        <h1 class="text-2xl font-semibold text-slate-900">Campañas de reactivación</h1>
                        <p class="text-sm text-slate-500">
                            Se inspeccionó el proyecto y las campañas usan <strong>{{ $inspectedTables['consultation'] }}</strong> para consultas y
                            <strong>{{ $inspectedTables['grooming'] }}</strong> para peluquería.
                        </p>
                    </div>
                </div>
                <div class="grid gap-3 sm:grid-cols-2">
                    @foreach ($campaignTypes as $key => $label)
                        @php
                            $active = $campaignType === $key;
                        @endphp
                        <a
                            href="{{ route('marketing.index', array_merge(request()->query(), ['campaign_type' => $key])) }}"
                            class="rounded-2xl border px-4 py-3 text-left shadow-sm transition {{ $active ? 'border-fuchsia-300 bg-fuchsia-50 text-fuchsia-800' : 'border-slate-200 bg-white text-slate-700 hover:border-fuchsia-200' }}"
                        >
                            <p class="text-xs font-semibold uppercase tracking-[0.2em] {{ $active ? 'text-fuchsia-700' : 'text-slate-400' }}">
                                {{ $label }}
                            </p>
                            <p class="mt-2 text-2xl font-semibold">{{ $counts[$key] ?? 0 }}</p>
                            <p class="text-sm {{ $active ? 'text-fuchsia-700' : 'text-slate-500' }}">pacientes inactivos</p>
                        </a>
                    @endforeach
                </div>
            </div>
        </div>

        @if (session('status'))
            <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                {{ session('status') }}
            </div>
        @endif

        @if (session('error'))
            <div class="rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">
                {{ session('error') }}
            </div>
        @endif

        <div class="grid gap-6 xl:grid-cols-[1.5fr_1fr]">
            <div class="space-y-6">
                <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                    <div class="mb-4">
                        <h2 class="text-lg font-semibold text-slate-900">Filtros</h2>
                        <p class="text-sm text-slate-500">Busca pacientes inactivos y decide si quieres incluir los que nunca han asistido.</p>
                    </div>

                    <form method="GET" class="grid gap-4 md:grid-cols-4">
                        <input type="hidden" name="campaign_type" value="{{ $campaignType }}">
                        <div class="md:col-span-2">
                            <label class="text-sm font-medium text-slate-700">Buscar</label>
                            <input
                                type="text"
                                name="q"
                                value="{{ $filters['q'] }}"
                                placeholder="Paciente, tutor o teléfono"
                                class="mt-2 w-full rounded-xl border-slate-200 text-sm focus:border-fuchsia-500 focus:ring-fuchsia-500"
                            >
                        </div>
                        <div class="flex items-end">
                            <label class="flex w-full items-center gap-3 rounded-xl border border-slate-200 px-4 py-3 text-sm text-slate-700">
                                <input type="hidden" name="include_never" value="0">
                                <input type="checkbox" name="include_never" value="1" class="h-4 w-4 rounded border-slate-300 text-fuchsia-600 focus:ring-fuchsia-500" @checked($filters['include_never'])>
                                Incluir pacientes sin visitas
                            </label>
                        </div>
                        <div class="flex items-end gap-3">
                            <button type="submit" class="inline-flex w-full items-center justify-center rounded-xl bg-fuchsia-600 px-4 py-3 text-sm font-semibold text-white shadow hover:bg-fuchsia-500">
                                Aplicar filtros
                            </button>
                        </div>
                    </form>
                </div>

                <form method="POST" action="{{ route('marketing.send') }}" class="space-y-6">
                    @csrf
                    <input type="hidden" name="campaign_type" value="{{ $campaignType }}">

                    <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                        <div class="mb-4 flex flex-col gap-2 lg:flex-row lg:items-center lg:justify-between">
                            <div>
                                <h2 class="text-lg font-semibold text-slate-900">Listado de pacientes inactivos</h2>
                                <p class="text-sm text-slate-500">
                                    Solo se muestran pacientes activos, con tutor principal sin duplicados y con teléfono o WhatsApp disponible.
                                </p>
                            </div>
                            <div class="rounded-xl bg-slate-50 px-4 py-2 text-sm text-slate-600">
                                {{ $recipients->total() }} resultado(s)
                            </div>
                        </div>

                        <div class="overflow-x-auto rounded-2xl border border-slate-200">
                            <table class="min-w-full divide-y divide-slate-200 text-sm">
                                <thead class="bg-slate-50 text-left text-slate-500">
                                    <tr>
                                        <th class="px-4 py-3">
                                            <input type="checkbox" id="marketing-select-all" class="h-4 w-4 rounded border-slate-300 text-fuchsia-600 focus:ring-fuchsia-500">
                                        </th>
                                        <th class="px-4 py-3 font-medium">Paciente</th>
                                        <th class="px-4 py-3 font-medium">Tutor</th>
                                        <th class="px-4 py-3 font-medium">Teléfono</th>
                                        <th class="px-4 py-3 font-medium">Última consulta</th>
                                        <th class="px-4 py-3 font-medium">Última peluquería</th>
                                        <th class="px-4 py-3 font-medium">Días sin asistir</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 bg-white">
                                    @forelse ($recipients as $recipient)
                                        @php
                                            $lastConsultation = $recipient->last_consultation_at ? \Carbon\Carbon::parse($recipient->last_consultation_at)->format('d/m/Y') : 'Nunca';
                                            $lastGrooming = $recipient->last_grooming_at ? \Carbon\Carbon::parse($recipient->last_grooming_at)->format('d/m/Y') : 'Nunca';
                                        @endphp
                                        <tr class="hover:bg-slate-50">
                                            <td class="px-4 py-3 align-top">
                                                <input
                                                    type="checkbox"
                                                    name="patient_ids[]"
                                                    value="{{ $recipient->patient_id }}"
                                                    class="marketing-recipient-checkbox h-4 w-4 rounded border-slate-300 text-fuchsia-600 focus:ring-fuchsia-500"
                                                    data-owner-name="{{ e($recipient->owner_name) }}"
                                                    data-patient-name="{{ e($recipient->patient_name) }}"
                                                    data-last-consultation-date="{{ $lastConsultation }}"
                                                    data-last-grooming-date="{{ $lastGrooming }}"
                                                    data-clinic-name="{{ e($clinicName) }}"
                                                >
                                            </td>
                                            <td class="px-4 py-3 align-top">
                                                <p class="font-semibold text-slate-800">{{ $recipient->patient_name }}</p>
                                                <p class="text-xs text-slate-500">ID paciente: {{ $recipient->patient_id }}</p>
                                            </td>
                                            <td class="px-4 py-3 align-top">
                                                <p class="font-medium text-slate-700">{{ $recipient->owner_name }}</p>
                                                <p class="text-xs text-slate-500">Tutor principal</p>
                                            </td>
                                            <td class="px-4 py-3 align-top text-slate-700">
                                                {{ $recipient->contact_phone ?: 'Sin contacto' }}
                                            </td>
                                            <td class="px-4 py-3 align-top text-slate-700">{{ $lastConsultation }}</td>
                                            <td class="px-4 py-3 align-top text-slate-700">{{ $lastGrooming }}</td>
                                            <td class="px-4 py-3 align-top">
                                                @if ($recipient->days_since_last_visit !== null)
                                                    <span class="inline-flex rounded-full bg-amber-50 px-3 py-1 text-xs font-semibold text-amber-700">
                                                        {{ $recipient->days_since_last_visit }} días
                                                    </span>
                                                @else
                                                    <span class="inline-flex rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600">Nunca</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="px-4 py-8 text-center text-sm text-slate-500">
                                                No hay pacientes que cumplan los filtros para esta campaña.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        @error('patient_ids')
                            <p class="mt-3 text-sm text-rose-600">{{ $message }}</p>
                        @enderror
                        @error('patient_ids.*')
                            <p class="mt-3 text-sm text-rose-600">{{ $message }}</p>
                        @enderror

                        <div class="mt-4">{{ $recipients->links() }}</div>
                    </div>

                    <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                        <div class="mb-4 flex flex-col gap-2 lg:flex-row lg:items-center lg:justify-between">
                            <div>
                                <h2 class="text-lg font-semibold text-slate-900">Mensaje y vista previa</h2>
                                <p class="text-sm text-slate-500">Usa placeholders para personalizar cada envío y deja listo el módulo para campañas futuras.</p>
                            </div>
                            <div class="rounded-xl bg-slate-50 px-4 py-2 text-xs text-slate-600">
                                <strong>Placeholders:</strong> {{ implode(', ', $placeholders) }}
                            </div>
                        </div>

                        <div class="grid gap-6 xl:grid-cols-2">
                            <div>
                                <label class="text-sm font-medium text-slate-700">Plantilla del mensaje</label>
                                <textarea
                                    id="message_template"
                                    name="message_template"
                                    rows="9"
                                    class="mt-2 w-full rounded-2xl border-slate-200 text-sm focus:border-fuchsia-500 focus:ring-fuchsia-500"
                                >{{ old('message_template', $messageTemplate) }}</textarea>
                                @error('message_template')
                                    <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="rounded-2xl border border-dashed border-slate-200 bg-slate-50 p-5">
                                <div class="flex items-center justify-between gap-3">
                                    <div>
                                        <h3 class="text-sm font-semibold text-slate-800">Vista previa</h3>
                                        <p class="text-xs text-slate-500">Se actualiza con el primer paciente seleccionado.</p>
                                    </div>
                                    <span id="selected-count" class="rounded-full bg-fuchsia-100 px-3 py-1 text-xs font-semibold text-fuchsia-700">0 seleccionados</span>
                                </div>
                                <pre id="marketing-preview" class="mt-4 whitespace-pre-wrap rounded-2xl bg-white p-4 text-sm text-slate-700 shadow-inner">{{ $previewMessage }}</pre>
                            </div>
                        </div>

                        <div class="mt-6 flex flex-wrap items-center justify-between gap-3 border-t border-slate-200 pt-6">
                            <p class="text-xs text-slate-400">
                                Si la integración de WhatsApp no está configurada, los envíos se registran como stub en el historial para no perder la trazabilidad.
                            </p>
                            <button type="submit" class="inline-flex items-center justify-center rounded-xl bg-slate-900 px-6 py-3 text-sm font-semibold text-white shadow hover:bg-slate-800">
                                Enviar campaña seleccionada
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <div class="space-y-6">
                <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                    <h2 class="text-lg font-semibold text-slate-900">Reglas aplicadas</h2>
                    <ul class="mt-4 space-y-3 text-sm text-slate-600">
                        <li class="rounded-xl bg-slate-50 px-4 py-3">Consultas: últimas atenciones tomadas desde <strong>historias_clinicas.created_at</strong>.</li>
                        <li class="rounded-xl bg-slate-50 px-4 py-3">Peluquería: últimas atenciones tomadas desde <strong>groomings.finished_at / scheduled_at</strong> con estado <strong>finalizado</strong>.</li>
                        <li class="rounded-xl bg-slate-50 px-4 py-3">Se excluyen duplicados usando el tutor principal de <strong>patient_owner</strong> y como respaldo <strong>pacientes.owner_id</strong>.</li>
                        <li class="rounded-xl bg-slate-50 px-4 py-3">Solo se incluyen <strong>pacientes activos</strong> y tutores con <strong>teléfono o WhatsApp</strong>.</li>
                    </ul>
                </div>

                <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                    <div class="mb-4">
                        <h2 class="text-lg font-semibold text-slate-900">Historial reciente</h2>
                        <p class="text-sm text-slate-500">Últimos envíos registrados en marketing_campaign_logs.</p>
                    </div>
                    <div class="space-y-3">
                        @forelse ($recentLogs as $log)
                            <div class="rounded-2xl border border-slate-200 p-4">
                                <div class="flex items-start justify-between gap-3">
                                    <div>
                                        <p class="text-sm font-semibold text-slate-800">{{ optional($log->patient)->display_name ?? 'Paciente eliminado' }}</p>
                                        <p class="text-xs text-slate-500">{{ optional($log->owner)->name ?? 'Tutor eliminado' }} · {{ $campaignTypes[$log->campaign_type] ?? $log->campaign_type }}</p>
                                    </div>
                                    <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $log->status === 'sent' ? 'bg-emerald-50 text-emerald-700' : ($log->status === 'stubbed' ? 'bg-amber-50 text-amber-700' : 'bg-rose-50 text-rose-700') }}">
                                        {{ strtoupper($log->status) }}
                                    </span>
                                </div>
                                <p class="mt-3 text-xs text-slate-500">{{ optional($log->sent_at)->format('d/m/Y H:i') ?: 'Sin fecha' }} · {{ optional($log->creator)->name ?? 'Sistema' }}</p>
                                @if ($log->response)
                                    <p class="mt-2 text-sm text-slate-600">{{ $log->response }}</p>
                                @endif
                            </div>
                        @empty
                            <div class="rounded-2xl border border-dashed border-slate-200 px-4 py-8 text-center text-sm text-slate-500">
                                Aún no hay campañas registradas.
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const selectAll = document.getElementById('marketing-select-all');
            const checkboxes = Array.from(document.querySelectorAll('.marketing-recipient-checkbox'));
            const templateField = document.getElementById('message_template');
            const preview = document.getElementById('marketing-preview');
            const selectedCount = document.getElementById('selected-count');

            const defaultContext = {
                owner_name: 'Tutor ejemplo',
                patient_name: 'Paciente ejemplo',
                last_consultation_date: 'Nunca',
                last_grooming_date: 'Nunca',
                clinic_name: @json($clinicName),
            };

            const selectedItems = () => checkboxes.filter((checkbox) => checkbox.checked);

            const previewContext = () => {
                const [firstSelected] = selectedItems();

                if (!firstSelected) {
                    return defaultContext;
                }

                return {
                    owner_name: firstSelected.dataset.ownerName,
                    patient_name: firstSelected.dataset.patientName,
                    last_consultation_date: firstSelected.dataset.lastConsultationDate,
                    last_grooming_date: firstSelected.dataset.lastGroomingDate,
                    clinic_name: firstSelected.dataset.clinicName,
                };
            };

            const renderPreview = () => {
                const context = previewContext();
                let message = templateField.value;

                Object.entries(context).forEach(([key, value]) => {
                    message = message.replaceAll(`{${key}}`, value || '');
                });

                preview.textContent = message;
                selectedCount.textContent = `${selectedItems().length} seleccionados`;
            };

            if (selectAll) {
                selectAll.addEventListener('change', () => {
                    checkboxes.forEach((checkbox) => {
                        checkbox.checked = selectAll.checked;
                    });

                    renderPreview();
                });
            }

            checkboxes.forEach((checkbox) => {
                checkbox.addEventListener('change', () => {
                    if (!checkbox.checked && selectAll) {
                        selectAll.checked = false;
                    }

                    if (selectAll && checkboxes.length > 0 && checkboxes.every((item) => item.checked)) {
                        selectAll.checked = true;
                    }

                    renderPreview();
                });
            });

            templateField.addEventListener('input', renderPreview);
            renderPreview();
        });
    </script>
@endpush
