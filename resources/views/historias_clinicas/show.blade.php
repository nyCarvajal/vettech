@extends('layouts.app')

@section('content')
@php
    $patientName = trim(($historia->paciente->nombres ?? '') . ' ' . ($historia->paciente->apellidos ?? '')) ?: 'Sin paciente';
    $tutor = $historia->paciente?->owner;
@endphp
<div class="space-y-6">
    <div class="rounded-3xl bg-gradient-to-r from-mint-500 via-mint-600 to-emerald-500 p-6 shadow-lg text-white">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div class="space-y-1">
                <p class="text-xs uppercase tracking-[0.2em] text-white/80">Historia clínica</p>
                <h1 class="text-3xl font-bold">#{{ $historia->id }} • {{ $patientName }}</h1>
                <p class="text-white/90">Tutor: {{ $tutor->name ?? 'Sin tutor' }}</p>
            </div>
            <div class="flex flex-wrap items-center gap-2 text-sm">
                <a
                    href="{{ route('historias-clinicas.edit', $historia) }}"
                    class="inline-flex items-center gap-2 rounded-full px-4 py-2 font-semibold text-white shadow-sm transition"
                    style="background: linear-gradient(120deg, #c084fc, #5eead4);"
                >Editar</a>
                <a
                    href="{{ route('historias-clinicas.recetarios.create', $historia) }}"
                    class="inline-flex items-center gap-2 rounded-full px-4 py-2 font-semibold text-white shadow-sm transition hover:brightness-110"
                    style="background: linear-gradient(120deg, #a78bfa, #34d399);"
                >Agregar recetario</a>
                <a
                    href="{{ route('historias-clinicas.remisiones.create', $historia) }}"
                    class="inline-flex items-center gap-2 rounded-full px-4 py-2 font-semibold text-white shadow-sm transition hover:brightness-110"
                    style="background: linear-gradient(120deg, #c084fc, #22c55e);"
                >Nueva remisión</a>
                <a
                    href="{{ route('historias-clinicas.pdf', $historia) }}"
                    class="inline-flex items-center gap-2 rounded-full px-4 py-2 font-semibold text-white shadow-sm transition hover:brightness-110"
                    style="background: linear-gradient(120deg, #e9d5ff, #99f6e4); color: #0f172a;"
                >Imprimir PDF</a>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="rounded-2xl border border-green-200 bg-green-50 px-4 py-3 text-green-800 shadow-sm">{{ session('success') }}</div>
    @endif

    <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
        <div class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-wider text-mint-700">Paciente</p>
            <h2 class="text-lg font-semibold text-gray-900">{{ $patientName }}</h2>
            <p class="text-sm text-gray-600">{{ $historia->paciente->especie ?? 'Especie no definida' }}</p>
        </div>
        <div class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-wider text-mint-700">Tutor</p>
            <h2 class="text-lg font-semibold text-gray-900">{{ $tutor->name ?? 'Sin tutor' }}</h2>
            <p class="text-sm text-gray-600">Tel: {{ $tutor->phone ?: 'N/D' }} · WhatsApp: {{ $tutor->whatsapp ?: 'N/D' }}</p>
        </div>
        <div class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm col-span-full">
            <p class="text-xs font-semibold uppercase tracking-wider text-mint-700">Actividad</p>
            <div class="mt-3 flex flex-wrap gap-2 text-sm font-semibold text-gray-700">
                <span class="rounded-full bg-mint-50 px-4 py-2 text-mint-700">{{ $historia->paraclinicos->count() }} paraclínicos</span>
                <span class="rounded-full bg-amber-50 px-4 py-2 text-amber-700">{{ $historia->diagnosticos->count() }} diagnósticos</span>
                <span class="rounded-full bg-blue-50 px-4 py-2 text-blue-700">{{ $prescriptions->count() }} recetarios</span>
            </div>
        </div>
    </div>

    <div class="rounded-2xl border border-gray-100 bg-white p-6 shadow-sm">
        <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between pb-4">
            <div>
                <h3 class="text-lg font-semibold text-gray-900">Adjuntos</h3>
                <p class="text-sm text-gray-600">Imágenes, PDFs y videos vinculados a la historia clínica.</p>
            </div>
            <button
                type="button"
                id="open-attachment-modal"
                class="inline-flex items-center gap-2 rounded-full px-4 py-2 font-semibold text-white shadow-sm transition hover:brightness-110"
                style="background: linear-gradient(120deg, #c084fc, #5eead4);"
            >
                Subir adjunto
            </button>
        </div>

        @if($errors->any())
            <div class="mb-3 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-rose-800">
                {{ $errors->first() }}
            </div>
        @endif

        <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3" id="attachments-grid">
            @forelse($attachments as $attachment)
                <div class="rounded-xl border border-gray-100 bg-white p-4 shadow-sm flex flex-col gap-3">
                    <div class="aspect-video rounded-lg bg-gray-50 flex items-center justify-center overflow-hidden">
                        @if($attachment->file_type === 'image')
                            <img src="{{ $attachment->cloudinary_secure_url }}" alt="{{ $attachment->titulo }}" class="h-full w-full object-cover">
                        @elseif($attachment->file_type === 'pdf')
                            <div class="flex flex-col items-center text-gray-500">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M7 3h8l4 4v14H7z"/><path d="M7 3v4h4"/></svg>
                                <span class="text-sm font-semibold">PDF</span>
                            </div>
                        @else
                            <div class="flex flex-col items-center text-gray-500">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M4 17v-7l8-4 8 4v7l-8 4z"/><path d="M10 14v-4l4 2z"/></svg>
                                <span class="text-sm font-semibold">Video</span>
                            </div>
                        @endif
                    </div>

                    @php
                        $attachmentExtension = $attachment->cloudinary_format
                            ?? ($attachment->file_type === 'pdf' ? 'pdf' : $attachment->file_type);
                        $downloadFilename = $attachment->titulo_limpio . ($attachmentExtension ? '.' . $attachmentExtension : '');
                        $downloadUrl = $attachment->cloudinary_secure_url;
                        $viewUrl = $attachment->cloudinary_secure_url;

                        if ($attachment->file_type === 'pdf') {
                            $downloadUrl = \Illuminate\Support\Str::replaceFirst(
                                '/upload/',
                                '/upload/fl_attachment:' . $attachment->titulo_limpio . '/',
                                $attachment->cloudinary_secure_url
                            );
                            $viewUrl = \Illuminate\Support\Str::replaceFirst(
                                '/upload/',
                                '/upload/fl_inline/',
                                $attachment->cloudinary_secure_url
                            );
                        }
                    @endphp
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                        <div>
                            <p class="font-semibold text-gray-900">{{ $attachment->titulo_limpio }}</p>
                            <p class="text-xs text-gray-500">{{ strtoupper($attachment->file_type) }} • {{ number_format($attachment->size_bytes / 1024 / 1024, 2) }} MB</p>
                            <p class="text-xs text-gray-400">{{ $attachment->created_at?->format('d/m/Y H:i') }}</p>
                        </div>
                        <div class="flex flex-wrap gap-2 text-xs font-semibold">
                            @if($attachment->file_type === 'image')
                                <button type="button" class="rounded-full border border-emerald-200 px-3 py-1 text-emerald-700 hover:bg-emerald-50 transition" onclick="window.open('{{ $attachment->cloudinary_secure_url }}','_blank')">Ver</button>
                            @elseif($attachment->file_type === 'pdf')
                                <a class="rounded-full border border-blue-200 px-3 py-1 text-blue-700 hover:bg-blue-50 transition" href="{{ $viewUrl }}" target="_blank" rel="noopener">Ver</a>
                            @else
                                <a class="rounded-full border border-indigo-200 px-3 py-1 text-indigo-700 hover:bg-indigo-50 transition" href="{{ $attachment->cloudinary_secure_url }}" target="_blank" rel="noopener">Reproducir</a>
                            @endif
                            <a class="rounded-full border border-gray-200 px-3 py-1 text-gray-700 hover:bg-gray-50 transition" href="{{ $downloadUrl }}" download="{{ $downloadFilename }}">Descargar</a>
                            <form method="POST" action="{{ route('historias-clinicas.adjuntos.destroy', $attachment) }}">
                                @csrf
                                @method('DELETE')
                                <button class="rounded-full border border-rose-200 px-3 py-1 text-rose-700 hover:bg-rose-50 transition" type="submit" onclick="return confirm('¿Eliminar adjunto?')">Eliminar</button>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <div class="md:col-span-2 lg:col-span-3 flex items-center justify-center rounded-xl border border-dashed border-gray-200 bg-gray-50 p-6 text-gray-500">
                    No hay adjuntos registrados aún.
                </div>
            @endforelse
        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-3">
        <div class="lg:col-span-3">
            <div class="rounded-2xl border border-gray-100 bg-white p-6 shadow-sm">
                <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between pb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Motivo y antecedentes</h3>
                    <span class="w-full rounded-full bg-mint-50 px-3 py-1 text-center text-xs font-semibold uppercase tracking-wide text-mint-700 md:w-auto">Consulta</span>
                </div>
                <div class="grid gap-4 text-sm text-gray-700 md:grid-cols-2">
                    <p class="text-base"><strong class="text-gray-900">Motivo de consulta:</strong> {{ $historia->motivo_consulta ?: 'Sin registrar' }}</p>
                    <p class="text-base md:col-span-2"><strong class="text-gray-900">Antecedentes / Enfermedad actual:</strong> {{ $historia->enfermedad_actual ?: 'Sin registrar' }}</p>
                    <p class="text-base"><strong class="text-gray-900">Antecedentes farmacológicos:</strong> {{ $historia->antecedentes_farmacologicos ?: 'N/D' }}</p>
                    <p class="text-base"><strong class="text-gray-900">Antecedentes patológicos:</strong> {{ $historia->antecedentes_patologicos ?: 'N/D' }}</p>
                </div>
            </div>
        </div>

        <div class="space-y-4 lg:col-span-2">
            <div class="rounded-2xl border border-gray-100 bg-white p-6 shadow-sm">
                <div class="flex items-center justify-between pb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Paraclínicos solicitados</h3>
                    <span class="rounded-full bg-blue-50 px-3 py-1 text-xs font-semibold uppercase tracking-wide text-blue-700">Exámenes</span>
                </div>
                <div class="divide-y divide-gray-100">
                    @forelse($historia->paraclinicos as $paraclinico)
                        <div class="flex items-start justify-between gap-3 py-3">
                            <div>
                                <p class="font-semibold text-gray-900">{{ $paraclinico->nombre }}</p>
                                <p class="text-sm text-gray-600">{{ $paraclinico->resultado ?: 'Pendiente' }}</p>
                            </div>
                        </div>
                    @empty
                        <p class="py-2 text-sm text-gray-500">No hay paraclínicos agregados.</p>
                    @endforelse
                </div>
            </div>

            <div class="rounded-2xl border border-gray-100 bg-white p-6 shadow-sm">
                <div class="flex items-center justify-between pb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Diagnósticos</h3>
                    <span class="rounded-full bg-amber-50 px-3 py-1 text-xs font-semibold uppercase tracking-wide text-amber-700">Clínica</span>
                </div>
                <div class="divide-y divide-gray-100">
                    @forelse($historia->diagnosticos as $diagnostico)
                        <div class="py-3">
                            <p class="font-semibold text-gray-900">{{ $diagnostico->descripcion }}</p>
                            <p class="text-sm text-gray-600">{{ $diagnostico->codigo ?: 'Sin código' }} · {{ $diagnostico->confirmado ? 'Confirmado' : 'Presuntivo' }}</p>
                        </div>
                    @empty
                        <p class="py-2 text-sm text-gray-500">No hay diagnósticos registrados.</p>
                    @endforelse
                </div>
            </div>

            <div class="rounded-2xl border border-gray-100 bg-white p-6 shadow-sm">
                <div class="flex items-center justify-between pb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Plan y análisis</h3>
                    <span class="rounded-full bg-purple-50 px-3 py-1 text-xs font-semibold uppercase tracking-wide text-purple-700">Seguimiento</span>
                </div>
                <div class="space-y-4 text-sm text-gray-700">
                    <div>
                        <p class="font-semibold text-gray-900">Análisis</p>
                        <p class="mt-1 text-gray-600">{{ $historia->analisis ?: 'Sin registrar' }}</p>
                    </div>
                    <div>
                        <p class="font-semibold text-gray-900">Plan procedimientos</p>
                        <p class="mt-1 text-gray-600">{{ $historia->plan_procedimientos ?: 'Sin registrar' }}</p>
                    </div>
                    <div>
                        <p class="font-semibold text-gray-900">Plan medicamentos</p>
                        <p class="mt-1 text-gray-600">{{ $historia->plan_medicamentos ?: 'Sin registrar' }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="space-y-4">
            <div class="rounded-2xl border border-gray-100 bg-white p-6 shadow-sm">
                <div class="flex items-center justify-between pb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Tutor</h3>
                    <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold uppercase tracking-wide text-slate-700">Contacto</span>
                </div>
                <div class="space-y-2 text-sm text-gray-700">
                    <p class="font-semibold text-gray-900">{{ $tutor->name ?? 'Sin tutor' }}</p>
                    <p class="text-gray-600">Tel: {{ $tutor->phone ?: 'N/D' }}</p>
                    <p class="text-gray-600">WhatsApp: {{ $tutor->whatsapp ?: 'N/D' }}</p>
                    <p class="text-gray-600">Correo: {{ $tutor->email ?: 'N/D' }}</p>
                </div>
            </div>

            <div class="rounded-2xl border border-gray-100 bg-white p-6 shadow-sm">
                <div class="flex items-center justify-between pb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Recetarios</h3>
                    <span class="rounded-full bg-mint-50 px-3 py-1 text-xs font-semibold uppercase tracking-wide text-mint-700">Tratamientos</span>
                </div>
                <div class="divide-y divide-gray-100">
                    @forelse($prescriptions as $prescription)
                        <div class="py-3 space-y-2">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <p class="font-semibold text-gray-900">Recetario #{{ $prescription->id }}</p>
                                    <p class="text-sm text-gray-600">{{ optional($prescription->professional)->name }}</p>
                                </div>
                                <div class="flex items-center gap-2 text-xs font-semibold">
                                    <a class="rounded-full border border-blue-200 px-3 py-1 text-blue-700 hover:bg-blue-50 transition" href="{{ route('historias-clinicas.recetarios.print', $prescription) }}">PDF</a>
                                    <form method="post" action="{{ route('historias-clinicas.recetarios.facturar', $prescription) }}">
                                        @csrf
                                        <button class="rounded-full border border-emerald-200 px-3 py-1 text-emerald-700 hover:bg-emerald-50 transition" type="submit">Facturar</button>
                                    </form>
                                </div>
                            </div>
                            <ul class="space-y-1 text-sm text-gray-600">
                                @foreach($prescription->items as $item)
                                    <li class="flex items-start justify-between gap-2">
                                        <span>{{ $item->is_manual ? $item->manual_name : optional($item->product)->name }} ({{ $item->qty_requested }})</span>
                                        @if($item->is_manual)
                                            <span class="rounded-full bg-gray-100 px-3 py-0.5 text-xs font-semibold text-gray-700">No facturable</span>
                                        @endif
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @empty
                        <p class="py-2 text-sm text-gray-500">Sin recetarios aún.</p>
                    @endforelse
                </div>
            </div>

            <div class="rounded-2xl border border-gray-100 bg-white p-6 shadow-sm">
                <div class="flex items-center justify-between pb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Remisiones de exámenes</h3>
                    <span class="rounded-full bg-indigo-50 px-3 py-1 text-xs font-semibold uppercase tracking-wide text-indigo-700">Exámenes</span>
                </div>
                <div class="divide-y divide-gray-100">
                    @forelse($referrals as $referral)
                        <div class="flex items-center justify-between py-3">
                            <div>
                                <p class="font-semibold text-gray-900">Remisión #{{ $referral->id }}</p>
                                <p class="text-sm text-gray-600">{{ $referral->created_at?->format('d/m/Y') }}</p>
                            </div>
                            <a class="rounded-full border border-blue-200 px-3 py-1 text-blue-700 hover:bg-blue-50 transition" href="{{ route('historias-clinicas.remisiones.print', $referral) }}">PDF</a>
                        </div>
                    @empty
                        <p class="py-2 text-sm text-gray-500">Sin remisiones registradas.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

<div id="attachment-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/40 px-4">
    <div class="w-full max-w-xl rounded-2xl bg-white p-6 shadow-xl">
        <div class="flex items-start justify-between">
            <div>
                <h3 class="text-lg font-semibold text-gray-900">Nuevo adjunto</h3>
                <p class="text-sm text-gray-600">Formatos permitidos: JPG, PNG, WEBP, PDF, MP4, WEBM, MOV. Máx 10MB.</p>
            </div>
            <button type="button" id="close-attachment-modal" class="text-gray-500 hover:text-gray-800">✕</button>
        </div>

        <form method="POST" action="{{ route('historias-clinicas.adjuntos.store', $historia) }}" enctype="multipart/form-data" class="mt-4 space-y-4" id="attachment-form">
            @csrf
            <div>
                <label class="block text-sm font-semibold text-gray-800">Título del adjunto</label>
                <input type="text" name="titulo" id="attachment-title" value="{{ old('titulo') }}" required minlength="3" maxlength="60" class="mt-1 w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-emerald-500 focus:outline-none" placeholder="Ej: radiografia">
                <p id="sanitized-preview" class="mt-1 text-xs text-gray-500">Título limpio: —</p>
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-800">Archivos</label>
                <input type="file" name="files[]" id="attachment-files" accept=".jpg,.jpeg,.png,.webp,.pdf,.mp4,.webm,.mov" multiple required class="mt-1 w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-emerald-500 focus:outline-none" />
                <p class="mt-1 text-xs text-gray-500">Tamaño máximo por archivo: 10MB.</p>
                <div id="file-errors" class="mt-2 text-xs text-rose-600"></div>
            </div>

            <div class="flex items-center justify-end gap-2">
                <button type="button" class="rounded-full border border-gray-200 px-4 py-2 text-gray-700 hover:bg-gray-50" id="cancel-attachment">Cancelar</button>
                <button type="submit" class="rounded-full bg-emerald-600 px-4 py-2 text-white font-semibold shadow-sm hover:bg-emerald-500">Subir</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    const modal = document.getElementById('attachment-modal');
    const openBtn = document.getElementById('open-attachment-modal');
    const closeBtn = document.getElementById('close-attachment-modal');
    const cancelBtn = document.getElementById('cancel-attachment');
    const titleInput = document.getElementById('attachment-title');
    const preview = document.getElementById('sanitized-preview');
    const fileInput = document.getElementById('attachment-files');
    const fileErrors = document.getElementById('file-errors');
    const maxSizeBytes = 10 * 1024 * 1024;

    const sanitizeTitle = (value) => {
        const ascii = value.normalize('NFD').replace(/[^\x00-\x7F]/g, '');
        const cleaned = ascii.replace(/[^A-Za-z0-9_\-\s]+/g, '').trim().replace(/\s+/g, ' ');
        const slug = cleaned
            .toLowerCase()
            .replace(/\s+/g, '-')
            .replace(/-+/g, '-');
        return slug;
    };

    const validateFiles = () => {
        fileErrors.textContent = '';
        const files = Array.from(fileInput.files || []);
        const allowed = ['image/jpeg','image/png','image/webp','application/pdf','video/mp4','video/webm','video/quicktime'];
        for (const file of files) {
            if (file.size > maxSizeBytes) {
                fileErrors.textContent = `"${file.name}" supera los 10MB.`;
                return false;
            }
            if (!allowed.includes(file.type)) {
                fileErrors.textContent = `"${file.name}" tiene un tipo no permitido.`;
                return false;
            }
        }
        return true;
    };

    const toggleModal = (show) => {
        modal.classList.toggle('hidden', !show);
        modal.classList.toggle('flex', show);
    };

    openBtn?.addEventListener('click', () => toggleModal(true));
    closeBtn?.addEventListener('click', () => toggleModal(false));
    cancelBtn?.addEventListener('click', () => toggleModal(false));

    titleInput?.addEventListener('input', (event) => {
        const clean = sanitizeTitle(event.target.value || '');
        preview.textContent = clean.length ? `Título limpio: ${clean}` : 'Título limpio: —';
    });

    fileInput?.addEventListener('change', validateFiles);

    document.getElementById('attachment-form')?.addEventListener('submit', (event) => {
        if (!validateFiles()) {
            event.preventDefault();
        }
        const clean = sanitizeTitle(titleInput.value || '');
        if (clean.length < 3) {
            event.preventDefault();
            fileErrors.textContent = 'El título no es válido. Usa letras, números, guion y guion bajo.';
        }
    });
</script>
@endpush
@endsection
