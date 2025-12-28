@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card border-0 shadow-sm mb-4 overflow-hidden" style="background: linear-gradient(135deg, #f5f3ff 0%, #e0f7f1 100%);">
        <div class="card-body d-flex flex-wrap justify-content-between align-items-center gap-3">
            <div class="d-flex align-items-start gap-3">
                <div class="rounded-4 bg-white shadow-sm p-3 d-flex align-items-center justify-content-center" style="width: 56px; height: 56px;">
                    <i class="ri-hearts-line" style="font-size: 26px; color: #8b5cf6;"></i>
                </div>
                <div>
                    <p class="text-uppercase text-muted mb-1" style="letter-spacing: .08em;">Panel de pacientes</p>
                    <h1 class="h3 mb-1">Pacientes</h1>
                    <p class="text-muted mb-0">Explora pacientes por especie, raza y tutor con un toque de color.</p>
                </div>
            </div>
            <div class="d-flex align-items-center gap-3">
                <div class="bg-white rounded-4 shadow-sm px-3 py-2 text-center">
                    <p class="text-muted text-uppercase mb-1" style="letter-spacing: .06em; font-size: 12px;">Total</p>
                    <div class="d-flex align-items-center gap-2">
                        <i class="ri-paw-line" style="color: #10b981;"></i>
                        <span class="fw-semibold" style="font-size: 18px;">{{ $patients->total() }}</span>
                    </div>
                </div>
                <a href="{{ route('patients.create') }}" class="btn btn-primary d-flex align-items-center gap-2" style="background: linear-gradient(135deg, #8b5cf6 0%, #10b981 100%); border: none;">
                    <i class="ri-add-circle-line"></i>
                    Nuevo paciente
                </a>
            </div>
        </div>
    </div>

    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">
            <div class="d-flex align-items-center gap-2 mb-3">
                <span class="badge rounded-pill px-3 py-2 text-white" style="background-color: #8b5cf6;"><i class="ri-filter-3-line"></i> Filtros activos</span>
                <span class="badge rounded-pill px-3 py-2" style="background-color: #d1fae5; color: #0f766e;"><i class="ri-magic-line"></i> Especies &amp; razas</span>
                <span class="badge rounded-pill px-3 py-2" style="background-color: #ede9fe; color: #6d28d9;"><i class="ri-user-heart-line"></i> Tutores</span>
            </div>

            <form method="get" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label text-muted">Nombre</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light" style="color: #8b5cf6;"><i class="ri-search-line"></i></span>
                        <input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="Buscar paciente">
                    </div>
                </div>
                <div class="col-md-3">
                    <label class="form-label text-muted">Especie</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light" style="color: #10b981;"><i class="ri-leaf-line"></i></span>
                        <select name="species_id" class="form-select">
                            <option value="">Todas</option>
                            @foreach($species as $item)
                                <option value="{{ $item->id }}" @selected(request('species_id') == $item->id)>{{ $item->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <label class="form-label text-muted">Raza</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light" style="color: #8b5cf6;"><i class="ri-star-smile-line"></i></span>
                        <select name="breed_id" class="form-select">
                            <option value="">Todas</option>
                            @foreach($breeds as $breed)
                                <option value="{{ $breed->id }}" @selected(request('breed_id') == $breed->id)>{{ $breed->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <label class="form-label text-muted">Tutor</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light" style="color: #10b981;"><i class="ri-user-heart-line"></i></span>
                        <select name="owner_id" class="form-select">
                            <option value="">Todos</option>
                            @foreach($owners as $owner)
                                <option value="{{ $owner->id }}" @selected(request('owner_id') == $owner->id)>{{ $owner->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-12 d-flex gap-2">
                    <button class="btn text-white" type="submit" style="background: linear-gradient(135deg, #8b5cf6 0%, #10b981 100%); border: none;">
                        <i class="ri-magic-line"></i> Aplicar filtros
                    </button>
                    <a href="{{ route('patients.index') }}" class="btn btn-link text-decoration-none">
                        <i class="ri-refresh-line"></i> Limpiar
                    </a>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm border-0">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Paciente</th>
                        <th>Especie / Raza</th>
                        <th>Tutor</th>
                        <th>Peso</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                @forelse($patients as $patient)
                    <tr>
                        <td class="fw-semibold">
                            <div class="d-flex align-items-center gap-3">
                                <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 44px; height: 44px; background: linear-gradient(135deg, #ede9fe, #d1fae5);">
                                    <i class="ri-shield-cross-line" style="color: #6d28d9;"></i>
                                </div>
                                <div>
                                    <div class="fw-semibold">{{ $patient->display_name }}</div>
                                    <div class="text-muted small d-flex align-items-center gap-2">
                                        <span class="badge" style="background-color: #ede9fe; color: #6d28d9;">{{ strtoupper($patient->sexo ?? '-') }}</span>
                                        @if($patient->edad)
                                            <span class="badge" style="background-color: #d1fae5; color: #0f766e;"><i class="ri-hourglass-2-line"></i> {{ $patient->edad }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="text-muted">
                            <div class="d-flex flex-column gap-1">
                                <span class="fw-semibold d-inline-flex align-items-center gap-2" style="color: #6d28d9;"><i class="ri-leaf-line"></i> {{ optional($patient->species)->name }}</span>
                                <span class="d-inline-flex align-items-center gap-2" style="color: #0f766e;"><i class="ri-star-smile-line"></i> {{ optional($patient->breed)->name }}</span>
                            </div>
                        </td>
                        <td>
                            <div class="d-flex flex-column gap-1">
                                <span class="fw-semibold d-inline-flex align-items-center gap-2"><i class="ri-user-heart-line" style="color: #8b5cf6;"></i> {{ optional($patient->owner)->name }}</span>
                                @if(optional($patient->owner)->document)
                                    <span class="text-muted small d-inline-flex align-items-center gap-2"><i class="ri-id-card-line" style="color: #6d28d9;"></i>{{ optional($patient->owner)->document_type }} {{ optional($patient->owner)->document }}</span>
                                @endif
                                @if(optional($patient->owner)->whatsapp)
                                    <span class="small d-inline-flex align-items-center gap-2" style="color: #0f766e;"><i class="ri-whatsapp-line"></i>{{ optional($patient->owner)->whatsapp }}</span>
                                @endif
                            </div>
                        </td>
                        <td>
                            <span class="badge rounded-pill px-3 py-2" style="background-color: #d1fae5; color: #0f766e;">
                                <i class="ri-scales-3-line"></i> {{ $patient->peso_formateado ?? 'N/D' }}
                            </span>
                        </td>
                        <td class="text-end"><a href="{{ route('patients.show', $patient) }}" class="btn btn-sm btn-outline-primary d-inline-flex align-items-center gap-1"><i class="ri-eye-line"></i> Ver perfil</a></td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center text-muted py-4">No hay pacientes registrados.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer bg-white">{{ $patients->links() }}</div>
    </div>
</div>
@endsection
