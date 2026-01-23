@php
    $featureDefaults = \App\Models\Clinica::featureDefaults();
    $featureEnabled = function (string $key) use ($clinica, $featureDefaults): bool {
        if ($clinica) {
            return $clinica->featureEnabled($key, $featureDefaults[$key] ?? true);
        }

        return $featureDefaults[$key] ?? true;
    };

    $showServicios = $featureEnabled('dispensacion')
        || $featureEnabled('hospitalizacion')
        || $featureEnabled('belleza')
        || $featureEnabled('consentimientos')
        || $featureEnabled('plantillas_consentimientos');

    $showCajaReportes = $featureEnabled('arqueo_caja')
        || $featureEnabled('reportes_basicos')
        || $featureEnabled('reportes_avanzados');
@endphp

<div class="app-sidebar">
     <div class="scrollbar" data-simplebar>
          <ul class="navbar-nav" id="navbar-nav">
               <li class="menu-title">OPERACIÓN</li>

               <li class="nav-item">
                    <a class="nav-link" href="{{ route('dashboard') }}">
                         <span class="nav-icon">
                              <iconify-icon icon="mingcute:home-3-line"></iconify-icon>
                         </span>
                         <span class="nav-text">Dashboard</span>
                    </a>
               </li>

               @if ($featureEnabled('agenda'))
                   <li class="nav-item">
                        <a class="nav-link" href="{{ route('agenda.index') }}">
                             <span class="nav-icon">
                                  <i class="bx bx-calendar-alt"></i>
                             </span>
                             <span class="nav-text">Agenda</span>
                        </a>
                   </li>
               @endif

               @if ($featureEnabled('facturacion_pos'))
                   <li class="nav-item">
                        <a class="nav-link" href="{{ route('invoices.pos') }}">
                             <span class="nav-icon">
                                  <i class="bx bx-receipt"></i>
                             </span>
                             <span class="nav-text">Facturación POS</span>
                        </a>
                   </li>
               @endif

               @if ($featureEnabled('tutores'))
                   <li class="nav-item">
                        <a class="nav-link" href="{{ route('owners.index') }}">
                             <span class="nav-icon">
                                  <iconify-icon icon="mingcute:user-3-line"></iconify-icon>
                             </span>
                             <span class="nav-text">Tutores</span>
                        </a>
                   </li>
               @endif

               @if ($featureEnabled('pacientes'))
                   <li class="nav-item">
                        <a class="nav-link" href="{{ route('patients.index') }}">
                             <span class="nav-icon">
                                  <iconify-icon icon="solar:heart-pulse-2-line"></iconify-icon>
                             </span>
                             <span class="nav-text">Pacientes</span>
                        </a>
                   </li>
               @endif

               @if ($showServicios)
                   <li class="menu-title">SERVICIOS / CLÍNICA</li>

                   @if ($featureEnabled('dispensacion'))
                       <li class="nav-item">
                            <a class="nav-link" href="{{ route('dispensations.index') }}">
                                 <span class="nav-icon">
                                      <iconify-icon icon="solar:pill-line"></iconify-icon>
                                 </span>
                                 <span class="nav-text">Dispensario</span>
                            </a>
                       </li>
                   @endif

                   @if ($featureEnabled('hospitalizacion'))
                       <li class="nav-item">
                            <a class="nav-link" href="{{ route('hospital.index') }}">
                                 <span class="nav-icon">
                                      <iconify-icon icon="solar:hospital-line"></iconify-icon>
                                 </span>
                                 <span class="nav-text">Hospitalización 24/7</span>
                            </a>
                       </li>
                   @endif

                   @if ($featureEnabled('belleza'))
                       <li class="nav-item">
                            <a class="nav-link" href="{{ route('groomings.index') }}">
                                 <span class="nav-icon">
                                      <iconify-icon icon="solar:scissors-line"></iconify-icon>
                                 </span>
                                 <span class="nav-text">Peluquería</span>
                            </a>
                       </li>
                   @endif

                   @if ($featureEnabled('consentimientos'))
                       <li class="nav-item">
                            <a class="nav-link" href="{{ route('consents.index') }}">
                                 <span class="nav-icon">
                                      <iconify-icon icon="solar:document-add-line"></iconify-icon>
                                 </span>
                                 <span class="nav-text">Consentimientos</span>
                            </a>
                       </li>
                   @endif

                   @if ($featureEnabled('plantillas_consentimientos'))
                       <li class="nav-item">
                            <a class="nav-link" href="{{ route('consent-templates.index') }}">
                                 <span class="nav-icon">
                                      <iconify-icon icon="solar:documents-line"></iconify-icon>
                                 </span>
                                 <span class="nav-text">Plantillas de consentimientos</span>
                            </a>
                       </li>
                   @endif
               @endif

               @if ($showCajaReportes)
                   <li class="menu-title">CAJA Y REPORTES</li>

                   @if ($featureEnabled('arqueo_caja'))
                       <li class="nav-item">
                            <a class="nav-link" href="{{ route('cash.closures.create') }}">
                                 <span class="nav-icon">
                                      <iconify-icon icon="solar:wallet-money-line"></iconify-icon>
                                 </span>
                                 <span class="nav-text">Arqueo de caja</span>
                            </a>
                       </li>
                   @endif

                   @if ($featureEnabled('reportes_basicos'))
                       <li class="nav-item">
                            <a class="nav-link" href="{{ route('reports.quick') }}">
                                 <span class="nav-icon">
                                      <i class="bx bx-line-chart"></i>
                                 </span>
                                 <span class="nav-text">Reportes básicos</span>
                            </a>
                       </li>
                   @endif

                   @if ($featureEnabled('reportes_avanzados'))
                       <li class="nav-item">
                            <a class="nav-link" href="{{ route('reports.home') }}">
                                 <span class="nav-icon">
                                      <iconify-icon icon="solar:chart-2-line"></iconify-icon>
                                 </span>
                                 <span class="nav-text">Reportes avanzados</span>
                            </a>
                       </li>
                   @endif
               @endif

               @if ($featureEnabled('config_clinica'))
                   <li class="menu-title">CONFIGURACIÓN</li>
                   <li class="nav-item">
                        <a class="nav-link" href="{{ route('settings.clinica.edit') }}">
                             <span class="nav-icon">
                                  <i class='bx bx-cog'></i>
                             </span>
                             <span class="nav-text">Configuración de clínicas</span>
                        </a>
                   </li>
               @endif
          </ul>
     </div>
</div>
