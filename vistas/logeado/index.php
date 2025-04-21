<body>
  <div class="container py-5">
    <div class="container-xxl flex-grow-1 container-p-y">
      <div class="row">
        <div class="col-lg-12 mb-4 order-0">
          <div class="card">
            <div class="d-flex align-items-end row">
              <div class="col-sm-7">
                <div class="card-body">
                  <h5 class="card-title text-primary">Bienvenido <?= $_SESSION['usuario'] ?>!</h5>
                  <p class="mb-4">
                    Revisa las solicitudes de vacaciones
                  </p>

                  <a href="javascript:;" class="btn btn-sm btn-outline-primary">Comenzar</a>
                </div>
              </div>

            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="row">
  <!-- Estadísticas de Solicitudes -->
  <div class="col-md-6 col-lg-4 col-xl-4 order-0 mb-4">
    <div class="card h-100">
      <div class="card-header d-flex align-items-center justify-content-between pb-0">
        <div class="card-title mb-0">
          <h5 class="m-0 me-2">Estadísticas de Solicitudes</h5>
          <small class="text-muted">Total: 42.82k solicitudes</small>
        </div>
        <div class="dropdown">
          <button
            class="btn p-0"
            type="button"
            id="estadisticasSolicitudes"
            data-bs-toggle="dropdown"
            aria-haspopup="true"
            aria-expanded="false">
            <i class="bx bx-dots-vertical-rounded"></i>
          </button>
          <div class="dropdown-menu dropdown-menu-end" aria-labelledby="estadisticasSolicitudes">
            <a class="dropdown-item" href="javascript:void(0);">Seleccionar todo</a>
            <a class="dropdown-item" href="javascript:void(0);">Actualizar</a>
            <a class="dropdown-item" href="javascript:void(0);">Compartir</a>
          </div>
        </div>
      </div>
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <div class="d-flex flex-column align-items-center gap-1">
            <h2 class="mb-2">8,258</h2>
            <span>Solicitudes Totales</span>
          </div>
          <div id="orderStatisticsChart"></div>
        </div>
        <ul class="p-0 m-0">
          <li class="d-flex mb-4 pb-1">
            <div class="avatar flex-shrink-0 me-3">
              <span class="avatar-initial rounded bg-label-primary"><i class="bx bx-log-in"></i></span>
            </div>
            <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
              <div class="me-2">
                <h6 class="mb-0">Ingreso</h6>
                <small class="text-muted">Nuevas contrataciones</small>
              </div>
              <div class="user-progress">
                <small class="fw-semibold">3.2k</small>
              </div>
            </div>
          </li>
          <li class="d-flex mb-4 pb-1">
            <div class="avatar flex-shrink-0 me-3">
              <span class="avatar-initial rounded bg-label-danger"><i class="bx bx-log-out"></i></span>
            </div>
            <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
              <div class="me-2">
                <h6 class="mb-0">Salida</h6>
                <small class="text-muted">Retiros y desvinculaciones</small>
              </div>
              <div class="user-progress">
                <small class="fw-semibold">1.5k</small>
              </div>
            </div>
          </li>
          <li class="d-flex mb-4 pb-1">
            <div class="avatar flex-shrink-0 me-3">
              <span class="avatar-initial rounded bg-label-warning"><i class="bx bx-calendar"></i></span>
            </div>
            <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
              <div class="me-2">
                <h6 class="mb-0">Vacaciones</h6>
                <small class="text-muted">Solicitudes aprobadas</small>
              </div>
              <div class="user-progress">
                <small class="fw-semibold">2.3k</small>
              </div>
            </div>
          </li>
          <li class="d-flex">
            <div class="avatar flex-shrink-0 me-3">
              <span class="avatar-initial rounded bg-label-info"><i class="bx bx-time-five"></i></span>
            </div>
            <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
              <div class="me-2">
                <h6 class="mb-0">Permisos</h6>
                <small class="text-muted">Justificados y no justificados</small>
              </div>
              <div class="user-progress">
                <small class="fw-semibold">1.2k</small>
              </div>
            </div>
          </li>
        </ul>
      </div>
    </div>
  </div>
  <!--/ Estadísticas de Solicitudes -->

  <!-- Visión General -->
  <div class="col-md-6 col-lg-4 order-1 mb-4">
    <div class="card h-100">
      <div class="card-header">
        <ul class="nav nav-pills" role="tablist">
          <li class="nav-item">
            <button
              type="button"
              class="nav-link active"
              role="tab"
              data-bs-toggle="tab"
              data-bs-target="#navs-tabs-line-card-ingresos"
              aria-controls="navs-tabs-line-card-ingresos"
              aria-selected="true">
              Ingresos
            </button>
          </li>
          <li class="nav-item">
            <button type="button" class="nav-link" role="tab">Salidas</button>
          </li>
          <li class="nav-item">
            <button type="button" class="nav-link" role="tab">Balance</button>
          </li>
        </ul>
      </div>
      <div class="card-body px-0">
        <div class="tab-content p-0">
          <div class="tab-pane fade show active" id="navs-tabs-line-card-ingresos" role="tabpanel">
            <div class="d-flex p-4 pt-3">
              <div class="avatar flex-shrink-0 me-3">
                <img src="assets/img/icons/unicons/wallet.png" alt="Usuario" />
              </div>
              <div>
                <small class="text-muted d-block">Total Registrado</small>
                <div class="d-flex align-items-center">
                  <h6 class="mb-0 me-1">4,591</h6>
                  <small class="text-success fw-semibold">
                    <i class="bx bx-chevron-up"></i>
                    42.9%
                  </small>
                </div>
              </div>
            </div>
            <div id="incomeChart"></div>
            <div class="d-flex justify-content-center pt-4 gap-2">
              <div class="flex-shrink-0">
                <div id="expensesOfWeek"></div>
              </div>
              <div>
                <p class="mb-n1 mt-1">Solicitudes esta semana</p>
                <small class="text-muted">+120 más que la semana pasada</small>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!--/ Visión General -->

  <!-- Transacciones -->
  <div class="col-md-6 col-lg-4 order-2 mb-4">
    <div class="card h-100">
      <div class="card-header d-flex align-items-center justify-content-between">
        <h5 class="card-title m-0 me-2">Historial de Solicitudes</h5>
        <div class="dropdown">
          <button
            class="btn p-0"
            type="button"
            id="historialSolicitudes"
            data-bs-toggle="dropdown"
            aria-haspopup="true"
            aria-expanded="false">
            <i class="bx bx-dots-vertical-rounded"></i>
          </button>
          <div class="dropdown-menu dropdown-menu-end" aria-labelledby="historialSolicitudes">
            <a class="dropdown-item" href="javascript:void(0);">Últimos 28 días</a>
            <a class="dropdown-item" href="javascript:void(0);">Último mes</a>
            <a class="dropdown-item" href="javascript:void(0);">Último año</a>
          </div>
        </div>
      </div>
      <div class="card-body">
        <ul class="p-0 m-0">
          <li class="d-flex mb-4 pb-1">
            <div class="avatar flex-shrink-0 me-3">
              <img src="assets/img/icons/unicons/cc-warning.png" alt="Ingreso" class="rounded" />
            </div>
            <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
              <div class="me-2">
                <small class="text-muted d-block mb-1">Ingreso</small>
                <h6 class="mb-0">Juan Pérez</h6>
              </div>
              <div class="user-progress d-flex align-items-center gap-1">
                <h6 class="mb-0">Aprobado</h6>
              </div>
            </div>
          </li>
          <li class="d-flex mb-4 pb-1">
            <div class="avatar flex-shrink-0 me-3">
              <img src="assets/img/icons/unicons/cc-warning.png" alt="Salida" class="rounded" />
            </div>
            <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
              <div class="me-2">
                <small class="text-muted d-block mb-1">Salida</small>
                <h6 class="mb-0">Ana Torres</h6>
              </div>
              <div class="user-progress d-flex align-items-center gap-1">
                <h6 class="mb-0">Pendiente</h6>
              </div>
            </div>
          </li>
          <li class="d-flex mb-4 pb-1">
            <div class="avatar flex-shrink-0 me-3">
              <img src="assets/img/icons/unicons/cc-warning.png" alt="Vacaciones" class="rounded" />
            </div>
            <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
              <div class="me-2">
                <small class="text-muted d-block mb-1">Vacaciones</small>
                <h6 class="mb-0">Luis García</h6>
              </div>
              <div class="user-progress d-flex align-items-center gap-1">
                <h6 class="mb-0">Aprobado</h6>
              </div>
            </div>
          </li>
          <li class="d-flex">
            <div class="avatar flex-shrink-0 me-3">
              <img src="assets/img/icons/unicons/cc-warning.png" alt="Permiso" class="rounded" />
            </div>
            <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
              <div class="me-2">
                <small class="text-muted d-block mb-1">Permiso</small>
                <h6 class="mb-0">Marta López</h6>
              </div>
              <div class="user-progress d-flex align-items-center gap-1">
                <h6 class="mb-0">Rechazado</h6>
              </div>
            </div>
          </li>
        </ul>
      </div>
    </div>
  </div>
  <!--/ Transacciones -->
</div>

  </div>
  <!-- / Content -->
