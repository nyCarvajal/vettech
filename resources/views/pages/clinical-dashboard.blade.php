@extends('layouts.base')

@section('css')
    <style>
        .clinical-dashboard {
            min-height: 100vh;
            display: grid;
            grid-template-columns: 280px 1fr;
            background: #f5f7fb;
            color: #1d2a3b;
            font-family: 'Play', sans-serif;
        }

        .clinical-sidebar {
            background: linear-gradient(180deg, #182848 0%, #4b6cb7 100%);
            color: #fff;
            padding: 32px 24px;
            display: flex;
            flex-direction: column;
            gap: 28px;
            box-shadow: 6px 0 20px rgba(0, 0, 0, 0.1);
        }

        .clinical-sidebar .doctor-card {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .clinical-sidebar .doctor-avatar {
            width: 64px;
            height: 64px;
            border-radius: 16px;
            background: linear-gradient(135deg, #9cecfb 0%, #65c7f7 50%, #0052d4 100%);
            color: #0b1c38;
            font-weight: 700;
            font-size: 22px;
            display: grid;
            place-items: center;
            box-shadow: 0 10px 24px rgba(0, 0, 0, 0.15);
        }

        .clinical-sidebar .doctor-meta {
            line-height: 1.3;
        }

        .clinical-sidebar .doctor-meta h3 {
            margin: 0;
            font-size: 18px;
            font-weight: 700;
        }

        .clinical-sidebar .doctor-meta p {
            margin: 0;
            color: rgba(255, 255, 255, 0.82);
            font-size: 14px;
        }

        .clinical-sidebar nav {
            display: grid;
            gap: 8px;
        }

        .clinical-sidebar nav a {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 12px;
            border-radius: 10px;
            color: rgba(255, 255, 255, 0.9);
            text-decoration: none;
            transition: background 0.2s ease, transform 0.15s ease;
            font-weight: 600;
        }

        .clinical-sidebar nav a:hover,
        .clinical-sidebar nav a.active {
            background: rgba(255, 255, 255, 0.12);
            transform: translateX(4px);
        }

        .clinical-sidebar .quick-action {
            background: #ffe66d;
            border: none;
            color: #1d2a3b;
            font-weight: 700;
            padding: 12px 14px;
            border-radius: 12px;
            box-shadow: 0 12px 25px rgba(0, 0, 0, 0.15);
            transition: transform 0.15s ease, box-shadow 0.2s ease;
        }

        .clinical-sidebar .quick-action:hover {
            transform: translateY(-2px);
            box-shadow: 0 16px 30px rgba(0, 0, 0, 0.18);
        }

        .clinical-main {
            padding: 28px 36px;
            display: flex;
            flex-direction: column;
            gap: 24px;
        }

        .clinical-topbar {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .clinical-search {
            flex: 1;
            position: relative;
        }

        .clinical-search input {
            width: 100%;
            padding: 14px 16px 14px 48px;
            border-radius: 14px;
            border: 1px solid #dbe1f1;
            background: #fff;
            font-weight: 600;
            color: #1d2a3b;
            box-shadow: 0 8px 20px rgba(45, 70, 115, 0.06);
        }

        .clinical-search svg {
            position: absolute;
            top: 50%;
            left: 18px;
            transform: translateY(-50%);
            color: #7c8aa5;
        }

        .topbar-actions {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .ghost-button {
            border: 1px dashed #4b6cb7;
            color: #1d2a3b;
            background: #e9efff;
            border-radius: 12px;
            padding: 12px 14px;
            font-weight: 700;
        }

        .icon-button {
            width: 46px;
            height: 46px;
            border-radius: 12px;
            background: #fff;
            border: 1px solid #dbe1f1;
            display: grid;
            place-items: center;
            position: relative;
            box-shadow: 0 10px 22px rgba(45, 70, 115, 0.08);
        }

        .icon-button .badge {
            position: absolute;
            top: -4px;
            right: -4px;
            background: #ef476f;
            color: #fff;
            border-radius: 999px;
            padding: 3px 7px;
            font-size: 11px;
            font-weight: 800;
        }

        .content-grid {
            display: grid;
            grid-template-columns: 1.35fr 1fr;
            gap: 18px;
        }

        .panel {
            background: #fff;
            border-radius: 18px;
            padding: 18px;
            box-shadow: 0 16px 40px rgba(24, 40, 72, 0.08);
            border: 1px solid #e4e9f5;
        }

        .panel h4 {
            margin-bottom: 12px;
            font-weight: 800;
            color: #152036;
        }

        .pill {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 12px;
            border-radius: 999px;
            background: #f1f5ff;
            color: #2d4379;
            font-weight: 700;
        }

        .agenda-item,
        .patient-item,
        .task-item,
        .alert-item {
            display: flex;
            gap: 12px;
            padding: 12px;
            border-radius: 14px;
            border: 1px dashed #e0e6f5;
            margin-bottom: 10px;
            background: #fdfdff;
        }

        .agenda-item strong {
            color: #14213d;
        }

        .patient-item .status {
            font-weight: 700;
            padding: 6px 10px;
            border-radius: 10px;
            font-size: 12px;
        }

        .status-ok { background: #def7ec; color: #1b7a53; }
        .status-wait { background: #fff7e6; color: #a26b1f; }
        .status-risk { background: #fde8e8; color: #a80f38; }

        .task-list,
        .alert-list,
        .chronic-summary {
            display: grid;
            gap: 10px;
        }

        .chronic-summary .summary-card {
            padding: 12px;
            border-radius: 12px;
            border: 1px solid #e0e6f5;
            background: linear-gradient(135deg, #f9fbff 0%, #eef3ff 100%);
        }

        .chronic-summary .summary-card h5 {
            margin-bottom: 6px;
            font-weight: 800;
        }

        .tag {
            display: inline-block;
            padding: 6px 10px;
            border-radius: 12px;
            background: #edf2ff;
            color: #2e3f70;
            font-weight: 700;
            margin-right: 8px;
            margin-top: 4px;
        }
    </style>
@endsection

@section('content')
    <div class="clinical-dashboard">
        <aside class="clinical-sidebar">
            <div class="doctor-card">
                <div class="doctor-avatar">DR</div>
                <div class="doctor-meta">
                    <h3>Dr. Camilo Herrera</h3>
                    <p>Medicina Interna · ID 1029384</p>
                </div>
            </div>
            <nav aria-label="Menú principal">
                <a href="#" class="active">🏠 Dashboard</a>
                <a href="#">🗓️ Agenda</a>
                <a href="#">👥 Pacientes</a>
                <a href="#">💬 Mensajes</a>
                <a href="#">🧪 Resultados</a>
                <a href="#">⚙️ Configuración</a>
            </nav>
            <button type="button" class="quick-action">Nueva nota / Nueva orden / Nueva receta</button>
        </aside>

        <main class="clinical-main">
            <header class="clinical-topbar">
                <div class="clinical-search">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-search" viewBox="0 0 16 16">
                        <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85zm-5.242.656a5 5 0 1 1 0-10 5 5 0 0 1 0 10z"/>
                    </svg>
                    <input type="search" placeholder="Buscar paciente, historia o documento" aria-label="Buscador global">
                </div>
                <div class="topbar-actions">
                    <button type="button" class="ghost-button">Nueva nota / Nueva orden / Nueva receta</button>
                    <button type="button" class="icon-button" aria-label="Notificaciones">
                        🔔
                        <span class="badge">3</span>
                    </button>
                </div>
            </header>

            <div class="content-grid">
                <section class="panel">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h4>Agenda</h4>
                        <span class="pill">Hoy · 8 citas</span>
                    </div>
                    <div class="agenda-item">
                        <div>
                            <div class="fw-bold">08:30 · Control hipertensión</div>
                            <div class="text-muted">Paciente: María López · Consultorio 3</div>
                        </div>
                        <span class="status status-ok ms-auto">Confirmada</span>
                    </div>
                    <div class="agenda-item">
                        <div>
                            <div class="fw-bold">10:00 · Seguimiento diabetes</div>
                            <div class="text-muted">Paciente: Jorge Páez · Teleconsulta</div>
                        </div>
                        <span class="status status-wait ms-auto">En sala</span>
                    </div>
                    <div class="agenda-item mb-0">
                        <div>
                            <div class="fw-bold">12:00 · Resultados laboratorio</div>
                            <div class="text-muted">Paciente: Ana Suárez · Consultorio 1</div>
                        </div>
                        <span class="status status-risk ms-auto">Prioridad</span>
                    </div>
                </section>

                <section class="panel">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h4>Alertas clínicas</h4>
                        <span class="pill">3 nuevas</span>
                    </div>
                    <div class="alert-list">
                        <div class="alert-item">
                            <div>
                                <strong>Resultado crítico</strong>
                                <div class="text-muted">Potasio alto · Paciente: Luis M.</div>
                            </div>
                            <span class="status status-risk ms-auto">Revisar hoy</span>
                        </div>
                        <div class="alert-item">
                            <div>
                                <strong>Vacunación pendiente</strong>
                                <div class="text-muted">Influenza · Paciente: Gabriela T.</div>
                            </div>
                            <span class="status status-wait ms-auto">Programar</span>
                        </div>
                        <div class="alert-item mb-0">
                            <div>
                                <strong>Renovar receta</strong>
                                <div class="text-muted">Hipotiroidismo · Paciente: Pablo R.</div>
                            </div>
                            <span class="status status-ok ms-auto">Listo para firmar</span>
                        </div>
                    </div>
                </section>

                <section class="panel">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h4>Pacientes del día</h4>
                        <span class="pill">5 en sala</span>
                    </div>
                    <div class="patient-item">
                        <div>
                            <div class="fw-bold">María López</div>
                            <div class="text-muted">Hipertensión · Último control hace 3 meses</div>
                        </div>
                        <span class="status status-ok ms-auto">En consulta</span>
                    </div>
                    <div class="patient-item">
                        <div>
                            <div class="fw-bold">Jorge Páez</div>
                            <div class="text-muted">Diabetes tipo 2 · Ajuste de insulina</div>
                        </div>
                        <span class="status status-wait ms-auto">En sala</span>
                    </div>
                    <div class="patient-item mb-0">
                        <div>
                            <div class="fw-bold">Ana Suárez</div>
                            <div class="text-muted">Resultados recientes · ECG + Laboratorios</div>
                        </div>
                        <span class="status status-risk ms-auto">Prioridad</span>
                    </div>
                </section>

                <section class="panel">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h4>Tareas clínicas</h4>
                        <span class="pill">4 pendientes</span>
                    </div>
                    <div class="task-list">
                        <div class="task-item">
                            <div>
                                <strong>Firmar notas</strong>
                                <div class="text-muted">3 borradores listos</div>
                            </div>
                            <span class="status status-ok ms-auto">2 min</span>
                        </div>
                        <div class="task-item">
                            <div>
                                <strong>Solicitar imagen</strong>
                                <div class="text-muted">RM cervical · Paciente: Laura G.</div>
                            </div>
                            <span class="status status-wait ms-auto">Hoy</span>
                        </div>
                        <div class="task-item mb-0">
                            <div>
                                <strong>Actualizar tratamientos</strong>
                                <div class="text-muted">Pacientes crónicos</div>
                            </div>
                            <span class="status status-wait ms-auto">Esta semana</span>
                        </div>
                    </div>
                </section>

                <section class="panel">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h4>Resumen crónicos</h4>
                        <span class="pill">12 en seguimiento</span>
                    </div>
                    <div class="chronic-summary">
                        <div class="summary-card">
                            <h5>Hipertensión</h5>
                            <div class="text-muted mb-1">Pacientes fuera de meta: 3</div>
                            <span class="tag">Controles próximos</span>
                            <span class="tag">Ajustar medicación</span>
                        </div>
                        <div class="summary-card">
                            <h5>Diabetes</h5>
                            <div class="text-muted mb-1">Pendientes HbA1c: 2</div>
                            <span class="tag">Educación</span>
                            <span class="tag">Revisión de dieta</span>
                        </div>
                        <div class="summary-card mb-0">
                            <h5>EPOC / Asma</h5>
                            <div class="text-muted mb-1">Plan de acción actualizado</div>
                            <span class="tag">Inhaladores</span>
                            <span class="tag">Vacunas</span>
                        </div>
                    </div>
                </section>
            </div>
        </main>
    </div>
@endsection
