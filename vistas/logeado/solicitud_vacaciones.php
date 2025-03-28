<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>

<script>
    //Funcionalidad al cargar la pagina
    $(document).ready(function() {
        $('#editDateF').on('change', function() {
            $('#editDateF_hidden').val($(this).val());
        });

        $('#editDateL').on('change', function() {
            $('#editDateL_hidden').val($(this).val());
        });
        // O al cargar la información, asegurarte de que el campo hidden tenga el mismo valor.

        $('#editDateC, #editDays, #editTurno').on('change', calculateEndDate);
        $('#userTable2').datagrid({
            singleSelect: true,
            onSelect: function(index, row) {
                $('input[name="labelRV"]').val(row.request_vacation_id);
                $('input[name="labelDays"]').val(row.days);
                $('input[name="labelDateR"]').val(convertToDateInputFormat(row.request_date));
                $('input[name="labelDateC"]').val(convertToDateInputFormat(row.start_date));
                $('input[name="labelDateF"]').val(convertToDateInputFormat(row.finish_date));
                $('select[name="labelTurno"]').val(row.work_shift, 10);

                if(row.estado==='CREADA' || row.estado==='PROCESO')
                {
                    $('#editButton').attr('hidden', false);
                    if(row.estado==='PROCESO'){
                        $('#genButton').attr('hidden', false)
                    }
                }else{
                    $('#editButton').attr('hidden', true);
                }
            },
            onUnselect: function (index, row) {

                $('input[name="labelDays"]').prop("disabled", true);
                $('input[name="labelDateR"]').prop("disabled", true);
                $('input[name="labelDateC"]').prop("disabled", true);
                $('select[name="labelTurno"]').prop("disabled", true);
                $('#editarUsuario').prop("hidden", true);
                $('#genButton').attr('hidden', true)
            }
        });

        $('#genButton').on('click', function() {
            // Recoger todos los datos
            var datos = {
                id: $('input[name="labelRV"]').val(),
                nombre_empleado: $('input[name="labelName"]').val(),
                fecha_solicitud: $('input[name="labelDateR"]').val(),
                no_empleado: $('input[name="labelEmployeeNumberId"]').val(),
                departamento: $('input[name="labelDepartment"]').val(),
                fecha_ingreso: $('input[name="labelHireDate"]').val(),
                dias_solicitados: $('input[name="labelDays"]').val(),
                fecha_desde: $('input[name="labelDateC"]').val(),
                fecha_hasta: $('input[name="labelDateF"]').val(),
                dias_disponibles: $('input[name="labelVacationDaysIn"]').val(),
                fecha_regreso: $('input[name="labelDateL"]').val(),
                tiempo_servicio: calcularTiempoServicio($('input[name="labelHireDate"]').val()),
                dias_corresponden: calcularDiasCorrespondientes($('input[name="labelHireDate"]').val())
            };

            console.log(datos);

            // Validar que todos los campos estén llenos
            var camposVacios = Object.keys(datos).filter(key => !datos[key]);
            if (camposVacios.length > 0) {
                alert('Por favor, complete todos los campos antes de generar el PDF');
                return;
            }

            // Enviar datos por POST
            $.ajax({
                url: 'index.php?c=pdf&m=generarPDF',
                method: 'POST',
                data: datos,
                xhrFields: {
                    responseType: 'blob'
                },
                success: function(response) {
                    // Crear un enlace temporal para descargar el PDF
                    var blob = new Blob([response], {type: 'application/pdf'});
                    var link = document.createElement('a');
                    link.href = window.URL.createObjectURL(blob);
                    link.download = 'solicitud_vacaciones.pdf';
                    link.click();
                },
                error: function(xhr, status, error) {
                    // Ocultar loader
                    ocultarLoader();

                    console.error('Error generando PDF:', error);
                    alert('Hubo un error al generar el PDF');
                }
            });
        });

        // Funciones de cálculo
        function calcularTiempoServicio(fechaIngreso) {
            var fechaInicio = moment(fechaIngreso, 'DD/MM/YYYY');
            var fechaActual = moment();

            var años = fechaActual.diff(fechaInicio, 'years');
            var meses = fechaActual.diff(fechaInicio.add(años, 'years'), 'months');

            return `${años} años ${meses} meses`;
        }

        function calcularDiasCorrespondientes(fechaIngreso) {
            var fechaInicio = moment(fechaIngreso, 'DD/MM/YYYY');
            var fechaActual = moment();

            var años = fechaActual.diff(fechaInicio, 'years');
            
            // Primer año 14 días, cada año adicional 2 días más
            var diasCorrespondientes = 14 + (Math.max(0, años - 1) * 2);

            return diasCorrespondientes;
        }

        $('#userTable').datagrid({
            onSelect: function(index, row) {
                // Filtrar la segunda tabla (userTable2) mostrando solo los registros con el mismo employee_number
                var employee_number = row.employee_number_id;
                let diasSolicitados;
                $.ajax({
                    url: 'index.php?c=vacaciones&m=consultarSolicitudes',
                    type: 'GET',
                    dataType: 'json',
                    data: { employee_number: employee_number },
                    cache: false,
                    success: function(respuesta) {
                        if (respuesta.error === true) {
                            $.messager.alert('Error', respuesta.msg, 'error');
                        } else {
                            $('#userTable2').datagrid('loadData', respuesta.registros);
                            diasSolicitados=respuesta.count;
                            let fechaStr = row.hire_date;
                            let [fecha, hora] = fechaStr.split(" ");
                            let [dia, mes, anio] = fecha.split("/");
                            let fechaISO = `${anio}-${mes}-${dia}T${hora}`;
                            let fechaInicial = new Date(fechaISO);

                            let fechaActual = new Date(); // Fecha de hoy

                            // Normalizar ambas fechas al inicio del día (00:00:00)
                            fechaInicial.setHours(0, 0, 0, 0);
                            fechaActual.setHours(0, 0, 0, 0);

                            // Calcular la diferencia en años
                            let diferenciaAnios = fechaActual.getFullYear() - fechaInicial.getFullYear();

                            // Ajustar si aún no ha pasado la fecha exacta en el año actual
                            if (
                                fechaActual.getMonth() < fechaInicial.getMonth() ||
                                (fechaActual.getMonth() === fechaInicial.getMonth() && fechaActual.getDate() < fechaInicial.getDate())
                            ) {
                                diferenciaAnios--;
                            }

                            // Calcular días de vacaciones
                            let diasVacaciones = diferenciaAnios >= 1 ? 14 + (diferenciaAnios - 1) * 2 : 0;

                            // Calcular los días disponibles después de restar los días solicitados
                            let diasDisponibles = diasVacaciones - diasSolicitados;

                            // Mostrar los días disponibles en la etiqueta correspondiente
                            $('h3[name="vacationDays"]').text(diasDisponibles > 0 ? `${diasDisponibles} días` : "0 días disponibles");
                            $('input[name="labelVacationDaysIn"]').val(diasDisponibles);

                            $('#divVacTable').prop('hidden', diasVacaciones <= 0);
                            $('#solicitarVac').prop('hidden', diasVacaciones <= 0);
                            $('#editFormVac').prop('hidden', diasVacaciones <= 0);
                        }
                    }
                });

                // Obtener la fecha de contratación de la fila seleccionada
                // Obtener la fecha de contratación en formato DD/MM/YYYY HH:MM:SS
                
                $('input[name="labelEmployeeNumberId"]').val(row.employee_number_id);
                $('input[name="labelName"]').val(row.name);
                $('input[name="labelHireDate"]').val(row.hire_date);
                $('select[name="labelGenre"]').val(row.genre_wname);
                $('select[name="labelRole"]').val(row.role_wname);
                $('input[name="labelDepartment"]').val(row.department);
                $('input[name="labelSupervisor"]').val(row.supervisor);
            },
            onUnselect: function(index, row) {
                $('h3[name="vacationDays"]').text("");
                $('input[name="labelDays"]').prop("disabled", true);
                $('input[name="labelDateR"]').prop("disabled", true);
                $('input[name="labelDateC"]').prop("disabled", true);
                $('select[name="labelTurno"]').prop("disabled", true);
                $('input[name="labelRV"]').val('');
                $('input[name="labelDays"]').val('');
                $('input[name="labelDateR"]').val('');
                $('input[name="labelDateC"]').val('');
                $('input[name="labelDateF"]').val('');
                $('select[name="labelTurno"]').prop('');
                $('#editarUsuario').prop("hidden", true);
                $('#genButton').attr('hidden', true)
                $('#divVacTable').prop('hidden');
                // Deshabilitar botones cuando se deselecciona la fila
                if (row.estado != 'BAJA') {
                    $('#editButton').linkbutton('enable');
                    $('#bajaButton').linkbutton('enable');
                } else {
                    $('#editButton').linkbutton('disable');
                    $('#bajaButton').linkbutton('enable');
                }
            }
        });

        $.ajax({
            url: 'index.php?c=empleados&m=consultarEmpleados',
            type: 'POST',
            dataType: 'json',
            cache: false,
            success: function(respuesta) {
                if (respuesta.error === true) {
                    $.messager.alert('Error', respuesta.msg, 'error');
                } else {
                    $('#userTable').datagrid('loadData', respuesta.registros);
                    $('#divVacTable').attr('hidden', true);
                    $('#solicitarVac').attr('hidden', true);
                    $('#editButton').attr('hidden', true);

                }
            }
        });

        $.ajax({
            url: 'index.php?c=empleados&m=consultarDepartamentos',
            type: 'POST',
            dataType: 'json',
            cache: false,
            success: function(respuesta) {
                if (respuesta.error) {
                    $.messager.alert('Error', respuesta.msg, 'error');
                } else {
                    // Vaciar el select y agregar opción por defecto
                    var select = $('#editRole');
                    select.empty();
                    select.append('<option value=""></option>');
                    // Iterar el arreglo y agregar cada opción
                    $.each(respuesta.departamentos, function(index, departamento) {
                        select.append('<option value="' + departamento.role_id + '">' + departamento.name + '</option>');
                    });
                }
            },
            error: function(xhr, status, error) {
                console.error("AJAX Error:", error);
            }
        });
    });

    function calculateEndDate() {
        let startDateInput = $('#editDateC');
        let daysInput = $('#editDays');
        let endDateInput = $('#editDateF');
        let backDateInput = $('#editDateL');
        let turnoInput = $('#editTurno');

        let startDate = moment(startDateInput.val(), 'YYYY-MM-DD');
        let days = parseInt(daysInput.val());
        let turno = turnoInput.val();

        if (!startDate.isValid() || isNaN(days)) {
            return; 
        }

        // Function to check if a date is a working day (Monday to Saturday)
        function isWorkingDay(date) {
            return date.day() !== 0; // 0 is Sunday
        }

        // Calculate end date precisely, counting the start date as day 1
        function calculatePreciseEndDate(startDate, totalWorkingDays) {
            let currentDate = moment(startDate);
            let workingDaysCount = 1; // Start counting from the first day

            while (workingDaysCount < totalWorkingDays) {
                currentDate.add(1, 'days');
                if (isWorkingDay(currentDate)) {
                    workingDaysCount++;
                }
            }

            return currentDate;
        }

        // Calculate end date
        let endDate = calculatePreciseEndDate(startDate, days);
        endDateInput.val(endDate.format('YYYY-MM-DD'));

        // Calculate back to work date
        let backDate = moment(endDate).add(1, 'days');
        
        // Adjust back to work date
        while (!isWorkingDay(backDate)) {
            backDate.add(1, 'days');
        }

        // Special rule: if back date is Saturday and turno is vespertino, move to Monday
        if (turno === '1' && backDate.day() === 6) {
            backDate.add(2, 'days'); // Move to Monday
        }

        backDateInput.val(backDate.format('YYYY-MM-DD'));
    }

    function editUser() {
        $('input[name="labelDays"]').prop("disabled", false);
        $('input[name="labelDateR"]').prop("disabled", false);
        $('input[name="labelDateC"]').prop("disabled", false);
        $('select[name="labelTurno"]').prop("disabled", false);
        $('#editarUsuario').prop("hidden", false);
        $('#genButton').attr('hidden', true)
    }    

    function convertToDateInputFormat(dateString) {
        if (!dateString) return ''; // Evita errores si el valor es null o vacío

        let parts = dateString.split(' '); // Divide fecha y hora
        let dateParts = parts[0].split('/'); // Divide DD/MM/YYYY

        let day = dateParts[0];
        let month = dateParts[1];
        let year = dateParts[2];

        return `${year}-${month}-${day}`; // Formato YYYY-MM-DD
    }
    
    function updateForm() {
        var row = $('#userTable2').datagrid('getSelected');
        if (!row) {
            $.messager.alert('Error', 'Por favor selecciona un usuario para actualizar.', 'error');
            return;
        }

        var index = $('#userTable2').datagrid('getRowIndex', row);

        // Se comparan todos los campos relevantes
        if (
            convertToDateInputFormat(row.request_date) == $('#editDateR').val() &&
            convertToDateInputFormat(row.start_date) === $('#editDateC').val() &&
            convertToDateInputFormat(row.finish_date) === $('#editDateF').val() &&
            row.days === $('#editDays').val() &&
            row.work_shift === $('#editTurno')
        ) {
            $.messager.alert('Info', 'No se detecta ningún cambio.', 'info');
            return;
        }

        var userData = $('#editFormVac').serialize();
        var userId = row.request_vacation_id;
        userData += '&id=' + userId;

        $.ajax({
            url: 'index.php?c=vacaciones&m=editarSolicitud',
            type: 'POST',
            dataType: 'json',
            data: userData,
            cache: false,
            success: function (respuesta) {
                $.messager.progress('close');
                if (respuesta.error === true) {
                    $.messager.alert('Error', respuesta.msg, 'error');
                } else {
                    if (respuesta.cambio) {
                        // Actualiza la fila en la tabla con los nuevos datos
                        var updatedData = {
                            
                        };

                        $('#userTable2').datagrid('updateRow', {
                            index: index,
                            row: updatedData
                        });

                        $('#userDialog').dialog('close');
                        $.messager.alert('Se realizó la petición', '¡El usuario fue actualizado correctamente!', 'info');
                        $('input[name="labelDays"]').prop("disabled", true);
                        $('input[name="labelDateR"]').prop("disabled", true);
                        $('input[name="labelDateC"]').prop("disabled", true);
                        $('select[name="labelTurno"]').prop("disabled", true);
                        $('#editarUsuario').prop("hidden", true);
                        $('#genButton').attr('hidden', false)
                    } else {
                        $.messager.alert('Error', respuesta.msg, 'error');
                    }
                }
            },
            error: function () {
                $.messager.alert('Error', 'Hubo un problema al procesar la solicitud. Intenta nuevamente.', 'error');
            }
        });
    }

    function crearSolicitud(){
        var rows = $('#userTable2').datagrid('getRows');
        var existeCreada = rows.some(row => row.estado === 'CREADA' || row.estado === 'PROCESO'); // Verificar si existe una fila con estado 'CREADA'

        if (existeCreada) {
            $.messager.alert('Advertencia', 'Ya existe una solicitud en proceso. Debe finalizarla antes de crear otra.', 'warning');
            return; // Detener la ejecución si ya hay una solicitud creada
        }
        $.messager.confirm('Confirmación', 'Se creara la solicitud de vacaciones para el empleado' + ' ¿Está seguro?', function (r) {
            if (r) {
                var employee_number = $('#editID').val();
                console.log(employee_number);
                $.messager.progress({
                    title: 'Procesando...',
                    msg: 'Por favor espere mientras se crea la solicitud.'
                 });
                 $.ajax({
                    url: 'index.php?c=vacaciones&m=crearSolicitud',
                    type: 'POST',
                    dataType: 'json',
                    data: { employee_number: employee_number },
                    cache: false,
                    success: function (respuesta) {
                        $.messager.progress('close');
                        if (respuesta.error === true) {
                        $.messager.alert('Error', respuesta.msg, 'error');
                        } else {
                            if (respuesta.creado) {
                                var newRow = {
                                    request_vacation_id: respuesta.id['lastInsertId'],
                                    estado: 'CREADA',
                                    employee_name: $('#editName').val()
                                }

                                $('#userTable2').datagrid('insertRow', {
                                    index: 0,
                                    row: newRow
                                });
                                $.messager.alert('Se realizo la petición', 'Se creo la nueva solicitud!', 'info');
                            } else {
                                $.messager.alert('Error', respuesta.msg, 'error');
                            }
                        }
                    }
                });
            }
        });
    }

    //COLORES DE CELDAS
    $(function() {
        var dg = $('#userTable');

        // Definir el estilo dinámico en base a la columna 'estado'
        var estadoCol = dg.datagrid('getColumnOption', 'estado');
        estadoCol.styler = function(value, row, index) {
            if (value === 'ACTIVO') {
                return 'background-color: green; color: white; font-weight: bold;';
            } else if (value === 'BAJA') {
                return 'background-color: red; color: white; font-weight: bold;';
            } else {
                return '';
            }
        };

        // Refrescar las filas para aplicar los estilos
        dg.datagrid('reload');
    });

    $(function() {
        var dg = $('#userTable2');

        // Definir el estilo dinámico en base a la columna 'estado'
        var estadoCol = dg.datagrid('getColumnOption', 'estado');
        estadoCol.styler = function(value, row, index) {
            if (value === 'CREADA') {
                return 'background-color: black; color: white; font-weight: bold;';
            } else if (value === 'PROCESO') {
                return 'background-color: orange; color: white; font-weight: bold;';
            } else if (value === 'APROVADA') {
                return 'background-color: green; color: white; font-weight: bold;';
            } else if (value === 'RECHAZADA') {
                return 'background-color: red; color: white; font-weight: bold;';
            }else {
                return '';
            }
        };

        // Refrescar las filas para aplicar los estilos
        dg.datagrid('reload');
    });
</script>

<body>
    <div class="container-fluid d-flex p-5" style="height: 700px">
        <div class="container">
            <div class="row">
                <div class="col-md-6"> <!-- Primera sección (70%) con margen -->
                    <!--TABLA DE LOS USUARIOS-->
                    <table id="userTable" class="easyui-datagrid" title="Empleados"
                        style="width:100%;height:100%" data-options="singleSelect:true"
                        toolbar="#toolbar">
                        <thead>
                            <tr>
                                <th data-options="field:'employee_number_id',width:50">#</th>
                                <th data-options="field:'name',width:200">Nombre</th>
                                <th data-options="field:'estado',width:100">Estado</th>
                                <th data-options="field:'department',width:175">Departamento</th>
                                <th data-options="field:'role_name',width:200">Cargo</th>
                            </tr>
                        </thead>
                    </table>
                </div>

                <div class="col-md-6">
                    <!-- Segunda sección (30%) -->
                    <form id="editForm" action="post">
                        <div class="container" style="width: 600px;">
                            <div class="row">
                                <h2 class="col-sm-2">Detalles</h2>
                            </div>
                            <div class="input-group mb-2 w-25">
                                <span class="input-group-text" id="basic-addon1">#</span>
                                <input type="text" class="form-control" name="labelEmployeeNumberId" id="editID" aria-describedby="basic-addon1" readonly disabled>
                            </div>
                            <input type="text" class="form-control w-75 mb-2" id="editName" name="labelName" disabled>
                            <div class="row mb-2">
                                <div class="col-sm-4">
                                    <p class="mb-0">Fecha de ingreso:</p>
                                    <input type="text" class="form-control" id="editHireDate" name="labelHireDate" readonly disabled>
                                </div>
                                <div class="col-sm-3">
                                    <p class="mb-0">Genero:</p>
                                    <select id="editGenre" name="labelGenre" class="form-control" disabled>
                                        <option value=""></option>
                                        <option value="0">Femenino</option>
                                        <option value="1">Masculino</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col">
                                    <p class="mb-0">Puesto del empleado:</p>
                                    <select id="editRole" name="labelRole" class="form-control" onchange="consultarDatosExtra()" disabled>
                                    </select>
                                </div>
                                <div class="col">
                                    <p class="mb-0">Departamento:</p>
                                    <input type="text" class="form-control" id="editDepartment" name="labelDepartment" disabled>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="mb-2">
                                    <p class="mb-0">Supervisor:</p>
                                    <input type="text" class="form-control" id="editSupervisor" name="labelSupervisor" disabled>
                                </div>
                                <div class="col-sm-4 mb-4">
                                    <p class="mb-0" id="titleVac">Días de vacaciones:</p>
                                    <h3 name="vacationDays" class="mt-0"></h3>
                                </div>
                                <div class="col-sm-2 mt-4">
                                    <button type="button" id="solicitarVac" class="btn btn-warning" onclick="crearSolicitud()">Solicitar</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="col-md-6" id="divVacTable">
                    <table id="userTable2" class="easyui-datagrid" title="Solicitudes"
                        style="width:100%;height:300px" 
                        data-options="singleSelect:true, queryParams:{employee_number:''}"
                        >
                        <thead>
                            <tr>
                                <th data-options="field:'request_vacation_id',width:50" hidden>#</th>
                                <th data-options="field:'start_date',width:50" hidden>#</th>
                                <th data-options="field:'finish_date',width:50" hidden>#</th>
                                <th data-options="field:'work_shift',width:50" hidden>#</th>
                                <th data-options="field:'employee_number',width:50" hidden>#</th>
                                <th data-options="field:'employee_name',width:250">Nombre</th>
                                <th data-options="field:'estado',width:100">Estado</th>
                                <th data-options="field:'days',width:120">Dias solicitados</th>
                                <th data-options="field:'request_date',width:150">Fecha de solicitud</th>
                            </tr>
                        </thead>
                    </table>
                </div>
                <div class="col-md-6">
                    <form id="editFormVac" action="post" hidden>
                        <div class="container" style="width: 600px;">
                            <div class="row">
                                <h4 class="col-sm-8">Detalles de la solicitud</h4>
                                <a href="javascript:void(0)" class="easyui-linkbutton col-sm-3" iconCls="icon-edit" plain="true"
                                onclick="editUser()" id="editButton">Editar solicitud</a>
                            </div>
                            <div class="row mb-2">
                                <div class="col-sm-4">
                                    <p class="mb-0">Numero de solicitud</p>
                                    <input type="text" class="form-control" name="labelRV" readonly disabled>
                                </div>
                                <div class="col-sm-4">
                                    <p class="mb-0">Dias solicitados</p>
                                    <input type="text" class="form-control" id="editDays" name="labelDays" disabled>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-sm-6">
                                    <p class="mb-0">Fecha de solicitud</p>
                                    <input type="date" class="form-control w-75 mb-2" id="editDateR" name="labelDateR" disabled>
                                </div>
                                <div class="col-sm-4">
                                    <p class="mb-0">Turno del empleado:</p>
                                    <select id="editTurno" name="labelTurno" class="form-control" disabled>
                                        <option value="0">Matutino</option>
                                        <option value="1">Vespertino</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-sm-6">
                                    <p class="mb-0">Fecha de comienzo</p>
                                    <input type="date" class="form-control w-75 mb-2" id="editDateC" name="labelDateC" disabled>
                                </div>
                                <div class="col-sm-6">
                                    <p class="mb-0">Fecha de fin</p>
                                    <input type="date" class="form-control w-75 mb-2" id="editDateF" name="labelDateF" readonly>
                                </div>
                                <div class="col-sm-6">
                                    <p class="mb-0">Retoma labores:</p>
                                    <input type="date" class="form-control w-75 mb-2" id="editDateL" name="labelDateL" readonly>
                                </div>
                                <input type="text" class="form-control w-75 mb-2" name="labelVacationDaysIn" id="vacationDaysIn" hidden>
                            </div>
                            <div class="row">
                                <div class="col-sm-4">
                                    <button type="button" class="btn btn-warning w-50 " id="editarUsuario"
                                    onclick="updateForm()" hidden>Editar</button>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                    <button type="button" class="btn btn-warning" id="genButton" hidden>Generar solicitud</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>