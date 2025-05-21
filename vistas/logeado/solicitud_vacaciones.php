<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>

<script>
    //Funcionalidad al cargar la pagina
    $(document).ready(function () {

        $('#switchCheckDefault').on('change', function () {
            if ($(this).is(':checked')) {
                $('#editFormVac').prop('hidden', true);
            } else {
                $('#editFormVac').prop('hidden', true);
            }
        });

        $('#subirContsIngreso').hide();
        $('#subirContsSalida').hide();
        $('#subirContsVacaciones').hide();
        $('#doc_formato_vac').hide();
        $('#doc_formato').hide();


        $('#ingresoTable').datagrid({
            singleSelect: true,
            onSelect: function (index, row) {
                $('input[name="labelR"]').val(row.request_id);
                $('input[name="labelDateRequest"]').val(convertToDateInputFormat(row.request_date));
                $('input[name="labelDateRequired"]').val(convertToDateTimeLocal(row.required_date));
                $('#editFormSI').prop('hidden', false);
                $('#genButtonI').attr('hidden', true);
                $('#editarSI').attr('hidden', true);
                if (row.url_doc != null) {
                    $('#doc_formato').show();
                } else {
                    $('#doc_formato').hide();
                }
                $('#doc_formato').attr('src', row.url_doc);
                $('#doc_formato_vac').attr('src', '');
                if (row.estado === 'CREADA' || row.estado === 'PROCESO') {
                    $('#editButtonSal').attr('hidden', false);
                    $('#subirContsIngreso').hide();
                    if (row.estado != 'PROCESO') {
                        $('#genButtonI').attr('hidden', true);
                        $('#genButtonS').attr('hidden', false);
                        $('#cambiarEstadoI').linkbutton('disabled');
                    } else {
                        $('#genButtonI').attr('hidden', false);
                        $('#genButtonS').attr('hidden', true);
                        $('#cambiarEstadoI').linkbutton('enable');
                    }
                } else {
                    $('#subirContsIngreso').show();
                    $('#editButtonSal').attr('hidden', true);
                    $('#cambiarEstadoI').linkbutton('disable');
                }
            },
            onUnselect: function (index, row) {
                if (row.estado === 'CREADA' || row.estado === 'PROCESO') {
                    $('#editButtonSal').attr('hidden', false);
                    if (row.estado === 'PROCESO') {
                        $('#genButtonI').attr('hidden', false);
                        $('#genButtonS').attr('hidden', true);
                        $('#cambiarEstadoI').linkbutton('enable');
                    } else {
                        $('#cambiarEstadoI').linkbutton('disable');
                        $('#genButtonI').attr('hidden', true);
                        $('#genButtonS').attr('hidden', true);
                    }
                } else {
                    $('#editButtonSal').attr('hidden', true);
                    $('#cambiarEstadoI').linkbutton('disable');
                }

                $('input[name="labelDateRequired"]').prop("disabled", true);
                $('input[name="labelDateRequest"]').prop("disabled", true);
                $('#editarSI').prop("hidden", true);
                $('#genButton').attr('hidden', true)
            }
        });

        $('#salidaTable').datagrid({
            singleSelect: true,
            onSelect: function (index, row) {
                $('input[name="labelR"]').val(row.request_id);
                $('input[name="labelDateRequest"]').val(convertToDateInputFormat(row.request_date));
                $('input[name="labelDateRequired"]').val(convertToDateTimeLocal(row.required_date));
                $('#editFormSI').prop('hidden', false);
                $('#editarSI').attr('hidden', true);
                $('#doc_formato').attr('src', row.url_doc);
                $('#doc_formato_vac').attr('src', '');
                if (row.estado === 'CREADA' || row.estado === 'PROCESO') {
                    $('#editButtonSal').attr('hidden', false);
                    $('#subirContsSalida').hide();
                    if (row.estado === 'PROCESO') {
                        $('#genButtonI').attr('hidden', true);
                        $('#genButtonS').attr('hidden', false);
                        $('#cambiarEstadoR').linkbutton('enable');
                    } else {
                        $('#cambiarEstadoR').linkbutton('disable');
                    }
                } else {
                    $('#subirContsSalida').show();
                    $('#editButtonSal').attr('hidden', true);
                    $('#cambiarEstadoR').linkbutton('disable');
                }
            },
            onUnselect: function (index, row) {
                if (row.estado === 'CREADA' || row.estado === 'PROCESO') {
                    $('#editButtonSal').attr('hidden', false);
                    if (row.estado === 'PROCESO') {
                        $('#genButtonI').attr('hidden', true);
                        $('#genButtonS').attr('hidden', false);
                        $('#cambiarEstadoR').linkbutton('enable');
                    }
                } else {
                    $('#editButtonSal').attr('hidden', true);
                    $('#cambiarEstadoR').linkbutton('disable');
                }

                $('input[name="labelDateRequired"]').prop("disabled", true);
                $('input[name="labelDateRequest"]').prop("disabled", true);
                $('#editarSI').prop("hidden", true);
                $('#genButton').attr('hidden', true);
                $('#editSI').prop('hidden', true);
                $('#genButtonS').attr('hidden', true);
                $('#genButtonI').attr('hidden', true);
            }
        });

        $('#userTable2').datagrid({
            singleSelect: true,
            onSelect: function (index, row) {
                $('input[name="labelRV"]').val(row.request_vacation_id);
                $('input[name="labelDays"]').val(row.days);
                $('input[name="labelDateR"]').val(convertToDateInputFormat(row.request_date));
                $('input[name="labelDateC"]').val(convertToDateInputFormat(row.start_date));
                $('input[name="labelDateF"]').val(convertToDateInputFormat(row.finish_date));
                $('input[name="labelDateL"]').val(convertToDateInputFormat(row.back_date));
                $('select[name="labelTurno"]').val(row.work_shift, 10);
                $('#editFormVac').prop('hidden', false);
                $('#genButton').attr('hidden', true);
                $('#doc_formato').attr('src', '');
                if (row.url_doc != null) {
                    $('#doc_formato_vac').show();
                } else {
                    $('#doc_formato_vac').hide();
                }
                $('#doc_formato_vac').attr('src', row.url_doc);
                if (row.estado === 'CREADA' || row.estado === 'PROCESO') {
                    $('#subirContsVacaciones').hide();
                    $('#editButton').attr('hidden', false);
                    if (row.estado === 'PROCESO') {
                        $('#genButton').attr('hidden', false);
                        $('#bajaButton').linkbutton('enable');
                    } else {
                        $('#bajaButton').linkbutton('disable');
                    }
                } else {
                    $('#subirContsVacaciones').show();
                    $('#editButton').attr('hidden', true);
                    $('#bajaButton').linkbutton('disable');
                }
                //Activar Select año adelantado

                var rowUser = $('#userTable').datagrid('getSelected');
                var hire_date = rowUser ? rowUser.hire_date : null;

                if (hire_date) {
                    const partes = hire_date.split('/');
                    if (partes.length === 3) {
                        const hireDay = parseInt(partes[0], 10);
                        const hireMonth = parseInt(partes[1], 10);

                        const hoy = new Date();
                        const thisYear = hoy.getFullYear();
                        const hireThisYear = new Date(thisYear, hireMonth - 1, hireDay);

                        let year_i, year;

                        if (hoy >= hireThisYear) {
                            // Ya pasó el día y mes del hire_date: periodo 2026-2027
                            year_i = thisYear + 1;
                            year = thisYear + 2;
                        } else {
                            // Aún no llega el día y mes del hire_date: periodo 2025-2026
                            year_i = thisYear;
                            year = thisYear + 1;
                        }

                        const periodo = `${year_i}-${year}`;
                        document.getElementById("anioSiguiente").textContent = periodo;
                        // Activar switch solo si coincide con el periodo adelantado
                        if (parseInt(row.year_i) == year_i && parseInt(row.year) == year) {
                            $('#switchCheckDefault').prop('checked', true);
                        } else {
                            $('#switchCheckDefault').prop('checked', false);
                        }
                    }
                }

                cargarDias();
            },
            onUnselect: function (index, row) {
                if (row.estado === 'CREADA' || row.estado === 'PROCESO') {
                    $('#editButton').attr('hidden', false);
                    if (row.estado === 'PROCESO') {
                        $('#genButton').attr('hidden', false);
                        $('#bajaButton').linkbutton('enable');
                    }
                } else {
                    $('#editButton').attr('hidden', true);
                    $('#bajaButton').linkbutton('disable');
                }

                $('input[name="labelDays"]').prop("disabled", true);
                $('input[name="labelDateR"]').prop("disabled", true);
                $('input[name="labelDateC"]').prop("disabled", true);
                $('select[name="labelTurno"]').prop("disabled", true);
                $('#editarSalida').prop("hidden", true);
                $('#editFormSI').prop('hidden', true);
                $('#editSI').prop('hidden', true);
                $('#genButtonS').attr('hidden', true);
                $('#genButtonI').attr('hidden', true);
            }
        });

        $('#genButton').on('click', function () {
            var fecha_solicitud = separarFecha($('input[name="labelDateR"]').val());
            var fecha_ingreso = separarFechaFormato($('input[name="labelHireDate"]').val());
            var fecha_desde = separarFecha($('input[name="labelDateC"]').val());
            var fecha_hasta = separarFecha($('input[name="labelDateF"]').val());
            var fecha_regreso = separarFecha($('input[name="labelDateL"]').val());

            // Recoger todos los datos
            var datos = {
                id: $('input[name="labelEmployeeNumberId"]').val(),
                nombre_empleado: $('input[name="labelName"]').val(),
                fecha_solicitud_dia: fecha_solicitud.dia,
                fecha_solicitud_mes: fecha_solicitud.mes,
                fecha_solicitud_anio: fecha_solicitud.anio,

                no_empleado: $('input[name="labelEmployeeNumberId"]').val(),
                departamento: $('input[name="labelDepartment"]').val(),
                fecha_ingreso_dia: fecha_ingreso.dia,
                fecha_ingreso_mes: fecha_ingreso.mes,
                fecha_ingreso_anio: fecha_ingreso.anio,

                dias_solicitados: $('input[name="labelDays"]').val(),
                fecha_desde_dia: fecha_desde.dia,
                fecha_desde_mes: fecha_desde.mes,
                fecha_desde_anio: fecha_desde.anio,

                fecha_hasta_dia: fecha_hasta.dia,
                fecha_hasta_mes: fecha_hasta.mes,
                fecha_hasta_anio: fecha_hasta.anio,

                dias_disponibles: $('input[name="labelVacationDaysIn"]').val(),

                fecha_regreso_dia: fecha_regreso.dia,
                fecha_regreso_mes: fecha_regreso.mes,
                fecha_regreso_anio: fecha_regreso.anio,
                tiempo_servicio_anio: calcularTiempoServicioAnio($('input[name="labelHireDate"]').val()),
                tiempo_servicio_mes: calcularTiempoServicioMes($('input[name="labelHireDate"]').val()),
                dias_corresponden: calcularDiasCorrespondientes($('input[name="labelHireDate"]').val())
            };

            // Validar que todos los campos estén llenos
            var camposVacios = Object.keys(datos).filter(key => !datos[key]);

            if (camposVacios.length > 0) {
                alert('Por favor, complete todos los campos antes de generar el PDF');
                return;
            }

            $.messager.progress({
                title: 'Procesando...',
                msg: 'Por favor espere mientras se crea el documento.'
            });

            // Enviar datos por POST
            $.ajax({
                url: 'index.php?c=pdf&m=generarPDF',
                method: 'POST',
                data: datos,
                xhrFields: {
                    responseType: 'blob'
                },
                success: function (response) {
                    $.messager.progress('close');

                    // Crear un enlace temporal para descargar el PDF
                    var blob = new Blob([response], {
                        type: 'application/pdf'
                    });
                    var link = document.createElement('a');
                    link.href = window.URL.createObjectURL(blob);
                    link.download = 'solicitud_vacaciones.pdf';
                    link.click();
                },
                error: function (xhr, status, error) {
                    // Ocultar loader
                    ocultarLoader();

                    console.error('Error generando PDF:', error);
                    alert('Hubo un error al generar el PDF');
                }
            });
        });

        $('#genButtonS').on('click', function () {
            var row = $('#salidaTable').datagrid('getSelected');
            var type = row.type_request;
            // Recoger todos los datos
            var datos = {
                nombre_empleado: $('input[name="labelName"]').val(),
                fecha_solicitud: $('input[name="labelDateRequest"]').val(),
                no_empleado: $('input[name="labelEmployeeNumberId"]').val(),
                departamento: $('input[name="labelDepartment"]').val(),
                fecha_solicitada: $('input[name="labelDateRequired"]').val()
            };

            // Validar que todos los campos estén llenos
            var camposVacios = Object.keys(datos).filter(key => !datos[key]);
            if (camposVacios.length > 0) {
                alert('Por favor, complete todos los campos antes de generar el PDF');
                return;
            }

            // Enviar datos por POST

            $.messager.progress({
                title: 'Procesando...',
                msg: 'Por favor espere mientras se crea el documento.'
            });
            $.ajax({
                url: 'index.php?c=pdf&m=generarPDFS',
                method: 'POST',
                data: datos,
                xhrFields: {
                    responseType: 'blob'
                },
                success: function (response) {
                    $.messager.progress('close');
                    // Crear un enlace temporal para descargar el PDF
                    var blob = new Blob([response], {
                        type: 'application/pdf'
                    });
                    var link = document.createElement('a');
                    link.href = window.URL.createObjectURL(blob);
                    link.download = 'solicitud_S.pdf';
                    link.click();
                },
                error: function (xhr, status, error) {
                    // Ocultar loader
                    ocultarLoader();

                    console.error('Error generando PDF:', error);
                    alert('Hubo un error al generar el PDF');
                }
            });

        });

        $('#genButtonI').on('click', function () {
            var row = $('#ingresoTable').datagrid('getSelected');
            var type = row.type_request;
            // Recoger todos los datos
            var datos = {
                nombre_empleado: $('input[name="labelName"]').val(),
                fecha_solicitud: $('input[name="labelDateRequest"]').val(),
                no_empleado: $('input[name="labelEmployeeNumberId"]').val(),
                departamento: $('input[name="labelDepartment"]').val(),
                fecha_solicitada: $('input[name="labelDateRequired"]').val()
            };

            // Validar que todos los campos estén llenos
            var camposVacios = Object.keys(datos).filter(key => !datos[key]);
            if (camposVacios.length > 0) {
                alert('Por favor, complete todos los campos antes de generar el PDF');
                return;
            }

            $.messager.progress({
                title: 'Procesando...',
                msg: 'Por favor espere mientras se crea el documento.'
            });

            // Enviar datos por POST
            $.ajax({
                url: 'index.php?c=pdf&m=generarPDFI',
                method: 'POST',
                data: datos,
                xhrFields: {
                    responseType: 'blob'
                },
                success: function (response) {
                    $.messager.progress('close');

                    // Crear un enlace temporal para descargar el PDF
                    var blob = new Blob([response], {
                        type: 'application/pdf'
                    });
                    var link = document.createElement('a');
                    link.href = window.URL.createObjectURL(blob);
                    link.download = 'solicitud_I.pdf';
                    link.click();
                },
                error: function (xhr, status, error) {
                    // Ocultar loader
                    ocultarLoader();

                    console.error('Error generando PDF:', error);
                    alert('Hubo un error al generar el PDF');
                }
            });


        });
        //Funciones de Fecha
        function separarFecha(fechaStr) {
            if (!fechaStr) return { dia: '', mes: '', anio: '' };
            var partes = fechaStr.split('-');
            return {
                anio: partes[0],
                mes: partes[1],
                dia: partes[2]
            };
        }

        function separarFechaFormato(fechaStr) {
            if (!fechaStr) return { dia: '', mes: '', anio: '' };
            var soloFecha = fechaStr.split(' ')[0];
            var partes = soloFecha.split('/');
            return {
                dia: partes[0],
                mes: partes[1],
                anio: partes[2]
            };
        }

        // Funciones de cálculo
        function calcularTiempoServicioAnio(fechaIngreso) {
            var fechaInicio = moment(fechaIngreso, 'DD/MM/YYYY');
            var fechaActual = moment();
            var años = fechaActual.diff(fechaInicio.add(años, 'years'), 'years');

            return `${años}`;
        }
        function calcularTiempoServicioMes(fechaIngreso) {
            var fechaInicio = moment(fechaIngreso, 'DD/MM/YYYY');
            var fechaActual = moment();
            var años = fechaActual.diff(fechaInicio.add(años, 'years'), 'years');
            var meses = fechaActual.diff(fechaInicio.add(años, 'years'), 'months');

            return `${meses}`;
        }
        function calcularDiasCorrespondientes(fechaIngreso) {
            let fechaStr = fechaIngreso;
            let [fecha, hora] = fechaStr.split(" ");
            let [dia, mes, anio] = fecha.split("/");
            let fechaISO = `${anio}-${mes}-${dia}`;
            let fechaInicial = new Date(fechaISO + 'T00:00:00');
            var switchElement = document.getElementById("switchCheckDefault");
            let fechaActual;
            if (switchElement.checked) {
                let anioSiguiente = new Date().getFullYear() + 1;
                fechaActual = new Date(`${anioSiguiente}-01-01T00:00:00`);
            } else {
                fechaActual = new Date();
            }
            fechaInicial.setHours(0, 0, 0, 0);
            fechaActual.setHours(0, 0, 0, 0);

            // Calcular la diferencia en meses
            let diferenciaMeses =
                (fechaActual.getFullYear() - fechaInicial.getFullYear()) * 12 +
                (fechaActual.getMonth() - fechaInicial.getMonth());

            if (fechaActual.getDate() < fechaInicial.getDate()) {
                diferenciaMeses--; // No cuenta el mes incompleto
            }

            // Calcular días de vacaciones según las reglas
            let diasCorrespondientes = 0;

            if (diferenciaMeses >= 6) {
                let diferenciaAnios = Math.floor(diferenciaMeses / 12);

                if (diferenciaAnios >= 1 && diferenciaAnios <= 6) {
                    diasCorrespondientes = 12 + (diferenciaAnios - 1) * 2;
                } else if (diferenciaAnios > 6) {
                    diasCorrespondientes = 22;
                    let aniosExtra = diferenciaAnios - 6;
                    diasCorrespondientes += Math.floor(aniosExtra / 5) * 2;
                } else {
                    diasCorrespondientes = 12;
                }
            }

            return diasCorrespondientes;
        }

        $('#userTable').datagrid({
            onSelect: function (index, row) {
                $('#editFormVac').prop('hidden', true);
                const checkbox = document.getElementById("switchCheckDefault");
                checkbox.checked = false;
                // Filtrar la segunda tabla (userTable2) mostrando solo los registros con el mismo employee_number
                var row = $('#userTable').datagrid('getSelected');
                var employee_number = row.employee_number_id;
                tabS(employee_number, null);
                tabI(employee_number, null);
                tabV(employee_number);

                var hire_date = row.hire_date; // formato dd/mm/yyyy

                if (hire_date) {
                    const partes = hire_date.split('/');
                    if (partes.length === 3) {
                        const hireDay = parseInt(partes[0], 10);
                        const hireMonth = parseInt(partes[1], 10);
                        const currentDate = new Date();
                        const currentYear = currentDate.getFullYear();

                        // Crear fecha del aniversario este año
                        const aniversario = new Date(currentYear, hireMonth - 1, hireDay);

                        let inicio, fin;

                        if (currentDate >= aniversario) {
                            inicio = currentYear + 1;
                            fin = currentYear + 2;
                        } else {
                            inicio = currentYear;
                            fin = currentYear + 1;
                        }

                        const anioSiguiente = inicio + "-" + fin;
                        document.getElementById("anioSiguiente").textContent = anioSiguiente;
                    }
                }
                function tabV(employee_number) {
                    let diasSolicitados;
                    $.ajax({
                        url: 'index.php?c=vacaciones&m=consultarSolicitudes',
                        type: 'GET',
                        dataType: 'json',
                        data: {
                            employee_number: employee_number
                        },
                        cache: false,
                        success: function (respuesta) {
                            if (respuesta.error === true) {
                                $.messager.alert('Error', respuesta.msg, 'error');
                            } else {
                                $('#switch_anio_entrante').hide();
                                $('#userTable2').datagrid('loadData', respuesta.registros);
                                $('#userTable2').datagrid('enableFilter');
                                diasSolicitados = respuesta.count;
                                let fechaStr = row.hire_date;
                                let [fecha, hora] = fechaStr.split(" ");
                                let [dia, mes, anio] = fecha.split("/");
                                let fechaISO = `${anio}-${mes}-${dia}`;
                                let fechaInicial = new Date(fechaISO + 'T00:00:00');

                                let fechaActual = new Date(); // Fecha de hoy

                                // Normalizar ambas fechas al inicio del día (00:00:00)
                                fechaInicial.setHours(0, 0, 0, 0);
                                fechaActual.setHours(0, 0, 0, 0);
                                // Calcular la diferencia en meses

                                let diferenciaMeses =
                                    (fechaActual.getFullYear() - fechaInicial.getFullYear()) * 12 +
                                    (fechaActual.getMonth() - fechaInicial.getMonth());

                                if (fechaActual.getDate() < fechaInicial.getDate()) {
                                    diferenciaMeses--; // No cuenta el mes incompleto
                                }
                                // Calcular días de vacaciones según las reglas:
                                let diasVacaciones = 0;


                                if (diferenciaMeses >= 6) {
                                    // Calcular los años redondeando hacia abajo
                                    let diferenciaAnios = Math.floor(diferenciaMeses / 12);

                                    if (diferenciaAnios >= 1 && diferenciaAnios <= 6) {
                                        diasVacaciones = 12 + (diferenciaAnios - 1) * 2;
                                    } else if (diferenciaAnios > 6) {
                                        diasVacaciones = 22; // Hasta 6 años
                                        let aniosExtra = diferenciaAnios - 6;
                                        diasVacaciones += Math.floor(aniosExtra / 5) * 2;
                                    } else {
                                        // Aún no ha cumplido el primer año pero ya pasaron 8 meses
                                        diasVacaciones = 12;
                                    }
                                }

                                // Calcular los días disponibles después de restar los días solicitados
                                let diasDisponibles = diasVacaciones - diasSolicitados;

                                if (diasDisponibles == 0 && diferenciaMeses >= 16) {
                                    $('#switch_anio_entrante').show();
                                }
                                // Mostrar los días disponibles en la etiqueta correspondiente
                                $('h4[name="vacationDays"]').text(diasDisponibles > 0 ? `${diasDisponibles} días` : "0 días");
                                $('input[name="labelVacationDaysIn"]').val(diasDisponibles);

                                if (diasDisponibles == 0) {
                                    $('#solicitarVac').linkbutton('disable');
                                } else {
                                    $('#solicitarVac').attr('hidden', false);
                                    $('#solicitarVac').linkbutton('enable');
                                }

                            }
                        }
                    });
                }

                // Obtener la fecha de contratación de la fila seleccionada
                // Obtener la fecha de contratación en formato DD/MM/YYYY HH:MM:SS

                $('input[name="labelEmployeeNumberId"]').val(row.employee_number_id);
                $('input[name="labelName"]').val(row.name);
                $('input[name="labelHireDate"]').val(row.hire_date);
                $('select[name="labelGenre"]').val(row.genre_wname);
                $('select[name="labelRole"]').val(row.role_wname);
                $('input[name="labelDepartment"]').val(row.department);
                $('input[name="labelSupervisor"]').val(row.supervisor);
                $('#editForm').prop('hidden', false);
                $('#tt').prop('hidden', false);
                bajaButton
            },
            onUnselect: function (index, row) {
                $('#editFormVac').prop('hidden', true);
                $('h4[name="vacationDays"]').text("");
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
                $('#genButton').attr('hidden', true);
                $('#editFormVac').prop('hidden', true);
                $('#bajaButton').linkbutton('disable');
                $('#editForm').prop('hidden', true);
                $('#tt').prop('hidden', true);
                $('#editFormSI').prop('hidden', true);
                $('#editarSI').prop('hidden', true);
                $('#genButtonS').attr('hidden', true);
                $('#genButtonI').attr('hidden', true);
                $('input[name="labelDateRequired"]').prop("disabled", true);
                $('input[name="labelDateRequest"]').prop("disabled", true);
                $('#cambiarEstadoI').linkbutton('disable');
                $('#subirContsIngreso').hide();
                $('#subirContsSalida').hide();
                $('#subirContsVacaciones').hide();
            }
        });

        $.ajax({
            url: 'index.php?c=empleados&m=consultarEmpleadosSolicitud',
            type: 'POST',
            dataType: 'json',
            cache: false,
            success: function (respuesta) {
                if (respuesta.error === true) {
                    $.messager.alert('Error', respuesta.msg, 'error');
                } else {
                    $('#userTable').datagrid('loadData', respuesta.registros);
                    $('#userTable').datagrid('enableFilter');
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
            success: function (respuesta) {
                if (respuesta.error) {
                    $.messager.alert('Error', respuesta.msg, 'error');
                } else {
                    // Vaciar el 
                    // select y agregar opción por defecto
                    var select = $('#editRole');
                    select.empty();
                    select.append('<option value=""></option>');
                    // Iterar el arreglo y agregar cada opción
                    $.each(respuesta.departamentos, function (index, departamento) {
                        select.append('<option value="' + departamento.role_id + '">' + departamento.name + '</option>');
                    });
                }
            },
            error: function (xhr, status, error) {
                console.error("AJAX Error:", error);
            }
        });
    });

    function tabV(employee_number, idTab) {
        var row = $('#userTable').datagrid('getSelected');
        var employee_number = row.employee_number_id;
        var switchElement = document.getElementById("switchCheckDefault");
        let diasSolicitados;
        $.ajax({
            url: 'index.php?c=vacaciones&m=consultarSolicitudes',
            type: 'GET',
            dataType: 'json',
            data: {
                employee_number: employee_number,
                preev_year: switchElement.checked
            },
            cache: false,
            success: function (respuesta) {
                if (respuesta.error === true) {
                    $.messager.alert('Error', respuesta.msg, 'error');
                } else {
                    $('#switch_anio_entrante').hide();
                    $('#userTable2').datagrid('loadData', respuesta.registros);
                    $('#userTable2').datagrid('enableFilter');
                    diasSolicitados = respuesta.count;
                    let fechaStr = row.hire_date;
                    let [fecha, hora] = fechaStr.split(" ");
                    let [dia, mes, anio] = fecha.split("/");
                    let fechaISO = `${anio}-${mes}-${dia}`;
                    let fechaInicial = new Date(fechaISO + 'T00:00:00');
                    let fechaActual;
                    if (switchElement.checked) {
                        let anioSiguiente = new Date().getFullYear() + 1;
                        fechaActual = new Date(`${anioSiguiente}-01-01T00:00:00`);
                    } else {
                        fechaActual = new Date(); // Fecha actual real
                    }

                    if (idTab != null) {
                        $('#userTable2').datagrid('selectRow', idTab);
                    }

                    // Normalizar ambas fechas al inicio del día (00:00:00)
                    fechaActual.setHours(0, 0, 0, 0);
                    // Calcular la diferencia en meses

                    let diferenciaMeses =
                        (fechaActual.getFullYear() - fechaInicial.getFullYear()) * 12 +
                        (fechaActual.getMonth() - fechaInicial.getMonth());

                    if (fechaActual.getDate() < fechaInicial.getDate()) {
                        diferenciaMeses--; // No cuenta el mes incompleto
                    }
                    // Calcular días de vacaciones según las reglas:
                    let diasVacaciones = 0;

                    if (diferenciaMeses >= 6) {
                        // Calcular los años redondeando hacia abajo
                        let diferenciaAnios = Math.floor(diferenciaMeses / 12);

                        if (diferenciaAnios >= 1 && diferenciaAnios <= 6) {
                            diasVacaciones = 12 + (diferenciaAnios - 1) * 2;
                        } else if (diferenciaAnios > 6) {
                            diasVacaciones = 22; // Hasta 6 años
                            let aniosExtra = diferenciaAnios - 6;
                            diasVacaciones += Math.floor(aniosExtra / 5) * 2;
                        } else {
                            // Aún no ha cumplido el primer año pero ya pasaron 8 meses
                            diasVacaciones = 12;
                        }
                    }

                    // Calcular los días disponibles después de restar los días solicitados
                    let diasDisponibles = diasVacaciones - diasSolicitados;

                    if (diasDisponibles == 0 && diferenciaMeses >= 16) {
                        $('#switch_anio_entrante').show();
                    }
                    // Mostrar los días disponibles en la etiqueta correspondiente

                    if (switchElement.checked) {
                        $('#switch_anio_entrante').show();
                        $('h4[name="vacationDays"]').text(diasDisponibles > 0 ? `${diasDisponibles} días` : "0 días");
                    } else {
                        $('h4[name="vacationDays"]').text(diasDisponibles > 0 ? `${diasDisponibles} días` : "0 días");
                    }

                    $('input[name="labelVacationDaysIn"]').val(diasDisponibles);

                    if (diasDisponibles == 0) {
                        $('#solicitarVac').linkbutton('disable');
                    } else {
                        $('#solicitarVac').attr('hidden', false);
                        $('#solicitarVac').linkbutton('enable');
                    }
                }
            }
        });
    }

    function tabS(employee_number, idTab) {
        var type_request = 0;
        if (idTab != null) {
            if (idTab == 0) {
                var rowSolicitud = $('#ingresoTable').datagrid('getSelected');
                var indexSolitud = $('#ingresoTable').datagrid('getRowIndex', rowSolicitud);
            } else {
                var rowSolicitud = $('#salidaTable').datagrid('getSelected');
                var indexSolitud = $('#salidaTable').datagrid('getRowIndex', rowSolicitud);
            }
        }
        $.ajax({
            url: 'index.php?c=vacaciones&m=consultarSolicitudesSI',
            type: 'GET',
            dataType: 'json',
            data: {
                employee_number: employee_number,
                type_request: type_request
            },
            cache: false,
            success: function (respuesta) {
                if (respuesta.error === true) {
                    $.messager.alert('Error', respuesta.msg, 'error');
                } else {
                    $('#salidaTable').datagrid('loadData', respuesta.registros);
                    $('#salidaTable').datagrid('enableFilter');
                    if (idTab != null) {
                        if (idTab == 0) {
                            $('#ingresoTable').datagrid('selectRow', indexSolitud);
                            $('#genButtonI').attr('hidden', false);
                        } else {
                            $('#salidaTable').datagrid('selectRow', indexSolitud);
                            $('#genButtonS').attr('hidden', false);
                        }
                    }
                }
            }
        });
    }

    function tabI(employee_number, idTab) {
        var type_request = 1;
        if (idTab != null) {
            if (idTab == 0) {
                var rowSolicitud = $('#ingresoTable').datagrid('getSelected');
                var indexSolitud = $('#ingresoTable').datagrid('getRowIndex', rowSolicitud);
            } else {
                var rowSolicitud = $('#salidaTable').datagrid('getSelected');
                var indexSolitud = $('#salidaTable').datagrid('getRowIndex', rowSolicitud);
            }
        }
        $.ajax({
            url: 'index.php?c=vacaciones&m=consultarSolicitudesSI',
            type: 'GET',
            dataType: 'json',
            data: {
                employee_number: employee_number,
                type_request: type_request
            },
            cache: false,
            success: function (respuesta) {
                if (respuesta.error === true) {
                    $.messager.alert('Error', respuesta.msg, 'error');
                } else {
                    $('#ingresoTable').datagrid('loadData', respuesta.registros);
                    $('#ingresoTable').datagrid('enableFilter');
                    if (idTab != null) {
                        if (idTab == 0) {
                            $('#ingresoTable').datagrid('selectRow', indexSolitud);
                            $('#genButtonI').attr('hidden', false);
                        } else {
                            $('#salidaTable').datagrid('selectRow', indexSolitud);
                            $('#genButtonS').attr('hidden', false);
                        }
                    }
                }
            }
        });
    }

    function newUser() {
        $('#crearUsuario').show();
        $('#editarUsuario').hide();
        $('#userDialog').dialog('open').dialog('setTitle', 'Nuevo Usuario');
        $('#userForm').form('clear');
    }

    function cambiarEstadoSI() {
        $('#cambiarEstadoSButton').show();
        $('#cambiarEstadoButton').hide();

        $('#userDialogEstado').dialog('open').dialog('setTitle', 'Cambiar Estado');
        $('#userForm').form('clear');
    }

    function cambiarEstado() {
        $('#cambiarEstadoButton').show();
        $('#cambiarEstadoSButton').hide();
        $('#userDialogEstado').dialog('open').dialog('setTitle', 'Cambiar Estado');
        $('#userForm').form('clear');
    }

    function cambiarEstadoFunc() {
        var row = $('#userTable').datagrid('getSelected');
        employee_number = row.employee_number;
        var row2 = $('#userTable2').datagrid('getSelected');
        var index2 = $('#userTable2').datagrid('getRowIndex', row);

        var index = $('#estadoSelect').val();
        if (index == '2') {
            cambio = 'APROBADA';
            estado = 2;
        } else {
            cambio = 'RECHAZADA';
            estado = 3;
        }
        $.messager.confirm('Confirmación', 'Se cambiara el estado a ' + cambio + ' del usuario ' + row.name + ' ¿Está seguro?', function (r) {
            if (r) {
                $.ajax({
                    url: 'index.php?c=vacaciones&m=cambiarEstado',
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        id: row2.request_vacation_id,
                        estado: estado
                    },
                    cache: false,
                    success: function (respuesta) {
                        if (respuesta.error === true) {
                            $.messager.alert('Error', respuesta.msg, 'error');
                        } else {
                            tabV(employee_number, null);
                            if (respuesta.cambio) {
                                $('#genButton').attr('hidden', true)
                                $('#genButtonI').attr('hidden', true);
                                $('#genButtonS').attr('hidden', true);
                                $('#bajaButton').linkbutton('disable');
                                $('#editButton').linkbutton('disable');

                                $('#userDialogEstado').dialog('close');
                                $('#cambiarEstadoR').linkbutton('disable');
                                $.messager.alert('Se realizó la petición', '¡El usuario cambio su estado a ' + cambio + '!', 'info');
                            } else {
                                $.messager.alert('Error', respuesta.msg, 'error');
                            }
                        }
                    }
                });
            }
        });
    }

    function cambiarEstadoSIFunc() {
        var row = $('#userTable').datagrid('getSelected');
        var employee_number = row.employee_number_id;
        var index2 = $('#editR').val();

        var index = $('#estadoSelect').val();
        if (index == '2') {
            cambio = 'APROBADA';
            estado = 2;
        } else {
            cambio = 'RECHAZADA';
            estado = 3;
        }

        $.messager.confirm('Confirmación', 'Se cambiara el estado a ' + cambio + ' del usuario ' + row.name + ' ¿Está seguro?', function (r) {
            if (r) {
                $.ajax({
                    url: 'index.php?c=vacaciones&m=cambiarEstadoSI',
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        id: index2,
                        estado: estado
                    },
                    cache: false,
                    success: function (respuesta) {
                        if (respuesta.error === true) {
                            $.messager.alert('Error', respuesta.msg, 'error');
                        } else {
                            if (respuesta.cambio) {
                                tabS(employee_number, null);
                                tabI(employee_number, null);
                                $('#genButton').attr('hidden', true)
                                $('#genButtonI').attr('hidden', true);
                                $('#genButtonS').attr('hidden', true);
                                $('#editFormSI').prop('hidden', true);
                                $('#userDialogEstado').dialog('close');
                                $('#cambiarEstadoI').linkbutton('disable');
                                $('#cambiarEstadoR').linkbutton('disable');
                                $.messager.alert('Se realizó la petición', '¡El usuario cambio su estado a ' + cambio + '!', 'info');
                            } else {
                                $.messager.alert('Error', respuesta.msg, 'error');
                            }
                        }
                    }
                });
            }
        });
    }

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

        const feriadosPorAnio = {
            2025: [
                '2025-01-01', '2025-02-03', '2025-03-17', '2025-05-01',
                '2025-09-16', '2025-11-17', '2025-12-25', '2026-01-01'
            ],
        };

        function isWorkingDay(date) {
            const year = date.year();
            const feriados = feriadosPorAnio[year] || [];

            // For turno 1 or 2, Saturday is not a working day
            if ((turno === '1' || turno === '2') && date.day() === 6) {
                return false;
            }

            return date.day() !== 0 && !feriados.includes(date.format('YYYY-MM-DD'));
        }

        function calculatePreciseEndDate(startDate, totalWorkingDays) {
            let currentDate = moment(startDate);
            let workingDaysCount = isWorkingDay(currentDate) ? 1 : 0;

            while (workingDaysCount < totalWorkingDays) {
                currentDate.add(1, 'days');
                if (isWorkingDay(currentDate)) {
                    workingDaysCount++;
                }
            }
            return currentDate;
        }

        let endDate = calculatePreciseEndDate(startDate, days);
        endDateInput.val(endDate.format('YYYY-MM-DD'));

        // Calculate back date (reincorporación)
        function isValidBackDate(date, turno) {
            const year = date.year();
            const feriados = feriadosPorAnio[year] || [];

            // Sábado (6) y domingo (0) no válidos para turno 1 o 2
            if ((turno === '1' || turno === '2') && (date.day() === 0 || date.day() === 6)) {
                return false;
            }

            return !feriados.includes(date.format('YYYY-MM-DD'));
        }

        let backDate = moment(endDate).add(1, 'days');
        while (!isValidBackDate(backDate, turno)) {
            backDate.add(1, 'days');
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

    function editSI() {
        $('input[name="labelDateRequired"]').prop("disabled", false);
        $('input[name="labelDateRequest"]').prop("disabled", false);
        $('#editarSI').prop("hidden", false);
        $('#genButtonS').attr('hidden', true)
        $('#genButtonI').attr('hidden', true)
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

    function convertToDateTimeLocal(dateString) {
        if (!dateString) return '';

        const [datePart, timePart] = dateString.split(' ');
        const [day, month, year] = datePart.split('/');

        // Asegura que el timePart tenga formato HH:MM:SS
        const [hour, minute, second] = timePart.split(':');

        // Retorna en formato compatible con datetime-local: YYYY-MM-DDTHH:MM:SS
        return `${year}-${month}-${day}T${hour}:${minute}:${second}`;
    }

    function updateFormSI() {
        //Obtener los datos de la tabla
        var row = $('#userTable').datagrid('getSelected');
        var employee_number = row.employee_number_id;

        //Obtener datos de tabulador
        var selectedTab = $('#tt').tabs('getSelected');
        var indexTab = $('#tt').tabs('getTabIndex', selectedTab);


        var index = $('#editR').val();

        var userId = index;
        var userData = {
            id: userId,
            labelDateRequired: $('#editDateRequired').val()
        };

        $.ajax({
            url: 'index.php?c=vacaciones&m=editarSolicitudSI',
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
                        tabS(employee_number, indexTab);
                        tabI(employee_number, indexTab);

                        $('#userDialog').dialog('close');
                        $.messager.alert('Se realizó la petición', '¡Se actualizo la constancia!', 'info');
                        $('input[name="labelDateRequired"]').prop("disabled", true);
                        $('input[name="labelDateRequest"]').prop("disabled", true);
                        $('#editarSI').prop("hidden", true);

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

    function updateForm() {
        var row = $('#userTable2').datagrid('getSelected');

        if (!row) {
            $.messager.alert('Error', 'Por favor seleccionar la solicitud para actualizar.', 'error');
            return;
        }
        employee_number = row.employee_number;
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
        let diasDisponibles = parseInt($('h4[name="vacationDays"]').text());

        // Obtener el valor ingresado por el usuario
        let diasEditados = parseInt($('#editDays').val());

        // Comparar
        if (diasEditados > diasDisponibles) {
            $.messager.alert('Error', 'No puedes solicitar más días de los disponibles.', 'info');
            return;
        }

        var userId = row.request_vacation_id;
        var userData = {
            id: userId,
            labelDays: $('#editDays').val(),
            labelDateR: $('#editDateR').val(),
            labelTurno: $('#editTurno').val(),
            labelDateC: $('#editDateC').val(),
            labelDateF: $('#editDateF').val(),
            labelDateL: $('#editDateL').val()
        };

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
                        tabV(employee_number, index);
                        // Actualiza la fila en la tabla con los nuevos datos
                        $('#userDialog').dialog('close');
                        $.messager.alert('Se realizó la petición', '¡La solicitud fue actualizada correctamente!', 'info');
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

    function crearSolicitudI() {
        var rows = $('#ingresoTable').datagrid('getRows');
        var existeCreada = rows.some(row => row.estado === 'CREADA' || row.estado === 'PROCESO'); // Verificar si existe una fila con estado 'CREADA'
        var type_request = 1;
        if (existeCreada) {
            $.messager.alert('Advertencia', 'Ya existe una solicitud en proceso. Debe finalizarla antes de crear otra.', 'warning');
            return; // Detener la ejecución si ya hay una solicitud creada
        }
        $.messager.confirm('Confirmación', 'Se creara la solicitud de vacaciones para el empleado' + ' ¿Está seguro?', function (r) {
            if (r) {
                var employee_number = $('#editID').val();
                $.messager.progress({
                    title: 'Procesando...',
                    msg: 'Por favor espere mientras se crea la solicitud.'
                });
                $.ajax({
                    url: 'index.php?c=vacaciones&m=crearSolicitudSI',
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        employee_number: employee_number,
                        type_request: type_request
                    },
                    cache: false,
                    success: function (respuesta) {
                        $.messager.progress('close');
                        if (respuesta.error === true) {
                            $.messager.alert('Error', respuesta.msg, 'error');
                        } else {
                            if (respuesta.creado) {
                                var row = $('#userTable').datagrid('getSelected');
                                var employee_number = row.employee_number_id;
                                tabS(employee_number, null);
                                tabI(employee_number, null);

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

    function crearSolicitudS() {
        var rows = $('#salidaTable').datagrid('getRows');
        var existeCreada = rows.some(row => row.estado === 'CREADA' || row.estado === 'PROCESO'); // Verificar si existe una fila con estado 'CREADA'
        var type_request = 0;
        if (existeCreada) {
            $.messager.alert('Advertencia', 'Ya existe una solicitud en proceso. Debe finalizarla antes de crear otra.', 'warning');
            return; // Detener la ejecución si ya hay una solicitud creada
        }
        $.messager.confirm('Confirmación', 'Se creara la solicitud de vacaciones para el empleado' + ' ¿Está seguro?', function (r) {
            if (r) {
                var employee_number = $('#editID').val();
                $.messager.progress({
                    title: 'Procesando...',
                    msg: 'Por favor espere mientras se crea la solicitud.'
                });
                $.ajax({
                    url: 'index.php?c=vacaciones&m=crearSolicitudSI',
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        employee_number: employee_number,
                        type_request: type_request
                    },
                    cache: false,
                    success: function (respuesta) {
                        $.messager.progress('close');
                        if (respuesta.error === true) {
                            $.messager.alert('Error', respuesta.msg, 'error');
                        } else {
                            if (respuesta.creado) {
                                var row = $('#userTable').datagrid('getSelected');
                                var employee_number = row.employee_number_id;
                                tabS(employee_number, null);
                                tabI(employee_number, null);
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

    function crearSolicitud() {
        var rows = $('#userTable2').datagrid('getRows');
        var switchElement = document.getElementById("switchCheckDefault");
        var existeCreada = rows.some(row => row.estado === 'CREADA' || row.estado === 'PROCESO'); // Verificar si existe una fila con estado 'CREADA'

        if (existeCreada) {
            $.messager.alert('Advertencia', 'Ya existe una solicitud en proceso. Debe finalizarla antes de crear otra.', 'warning');
            return; // Detener la ejecución si ya hay una solicitud creada
        }
        $.messager.confirm('Confirmación', 'Se creara la solicitud de vacaciones para el empleado' + ' ¿Está seguro?', function (r) {
            if (r) {
                var employee_number = $('#editID').val();
                var hire_date = $('#editHireDate').val();
                $.messager.progress({
                    title: 'Procesando...',
                    msg: 'Por favor espere mientras se crea la solicitud.'
                });
                $.ajax({
                    url: 'index.php?c=vacaciones&m=crearSolicitud',
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        employee_number: employee_number,
                        preev_year: switchElement.checked,
                        hire_date: hire_date
                    },
                    cache: false,
                    success: function (respuesta) {
                        $.messager.progress('close');
                        if (respuesta.error === true) {
                            $.messager.alert('Error', respuesta.msg, 'error');
                        } else {
                            if (respuesta.creado) {
                                var newRow = {
                                    request_vacation_id: respuesta.id,
                                    estado: 'CREADA',
                                    employee_name: $('#editName').val(),
                                    year: respuesta.year,
                                    year_i: respuesta.year_i
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
    $(function () {
        var dg = $('#userTable');

        // Definir el estilo dinámico en base a la columna 'estado'
        var estadoCol = dg.datagrid('getColumnOption', 'estado');
        estadoCol.styler = function (value, row, index) {
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

    $(function () {
        var dg = $('#userTable2');

        // Definir el estilo dinámico en base a la columna 'estado'
        var estadoCol = dg.datagrid('getColumnOption', 'estado');
        estadoCol.styler = function (value, row, index) {
            if (value === 'CREADA') {
                return 'background-color: black; color: white; font-weight: bold;';
            } else if (value === 'PROCESO') {
                return 'background-color: orange; color: white; font-weight: bold;';
            } else if (value === 'APROBADA') {
                return 'background-color: green; color: white; font-weight: bold;';
            } else if (value === 'RECHAZADA') {
                return 'background-color: red; color: white; font-weight: bold;';
            } else {
                return '';
            }
        };

        // Refrescar las filas para aplicar los estilos
        dg.datagrid('reload');

        var dg = $('#salidaTable');

        // Definir el estilo dinámico en base a la columna 'estado'
        var estadoCol = dg.datagrid('getColumnOption', 'estado');
        estadoCol.styler = function (value, row, index) {
            if (value === 'CREADA') {
                return 'background-color: black; color: white; font-weight: bold;';
            } else if (value === 'PROCESO') {
                return 'background-color: orange; color: white; font-weight: bold;';
            } else if (value === 'APROBADA') {
                return 'background-color: green; color: white; font-weight: bold;';
            } else if (value === 'RECHAZADA') {
                return 'background-color: red; color: white; font-weight: bold;';
            } else {
                return '';
            }
        };

        // Refrescar las filas para aplicar los estilos
        dg.datagrid('reload');

        var dg = $('#ingresoTable');

        // Definir el estilo dinámico en base a la columna 'estado'
        var estadoCol = dg.datagrid('getColumnOption', 'estado');
        estadoCol.styler = function (value, row, index) {
            if (value === 'CREADA') {
                return 'background-color: black; color: white; font-weight: bold;';
            } else if (value === 'PROCESO') {
                return 'background-color: orange; color: white; font-weight: bold;';
            } else if (value === 'APROBADA') {
                return 'background-color: green; color: white; font-weight: bold;';
            } else if (value === 'RECHAZADA') {
                return 'background-color: red; color: white; font-weight: bold;';
            } else {
                return '';
            }
        };

        // Refrescar las filas para aplicar los estilos
        dg.datagrid('reload');
    });

    function onTabSelect(title, index) {
        //Cerrar Modales
        var row = $('#userTable').datagrid('getSelected');
        if (!row) {
            return;
        } else {
            var employee_number = row.employee_number_id;
            $('#subirContsIngreso').hide();
            $('#subirContsSalida').hide();
            $('#subirContsVacaciones').hide();

            $('#bajaButton').linkbutton('disable');
            $('#cambiarEstadoI').linkbutton('disable');
            $('#cambiarEstadoR').linkbutton('disable');
            switch (index) {
                case 0:
                    tabI(employee_number, null);
                    $('#editFormSI').prop('hidden', true);
                    $('#editFormVac').prop('hidden', true);
                    $('input[name="labelDateRequired"]').prop("disabled", true);
                    $('input[name="labelDateRequest"]').prop("disabled", true);
                    $('#genButton').attr('hidden', true)
                    $('#genButtonI').attr('hidden', true);
                    $('#genButtonS').attr('hidden', true);
                    $('#userDialogEstado').dialog('close');
                    $('#userDialog').dialog('close');
                    break;
                case 1:
                    tabS(employee_number, null);
                    $('#editFormSI').prop('hidden', true);
                    $('#editFormVac').prop('hidden', true);
                    $('input[name="labelDateRequired"]').prop("disabled", true);
                    $('input[name="labelDateRequest"]').prop("disabled", true);
                    $('#genButton').attr('hidden', true)
                    $('#genButtonI').attr('hidden', true);
                    $('#genButtonS').attr('hidden', true);
                    $('#userDialogEstado').dialog('close');
                    $('#userDialog').dialog('close');
                    break;
                case 2:
                    tabV(employee_number, null);
                    $('#editFormSI').prop('hidden', true);
                    $('#editFormVac').prop('hidden', true);
                    $('input[name="labelDateRequired"]').prop("disabled", true);
                    $('input[name="labelDateRequest"]').prop("disabled", true);
                    $('#genButton').attr('hidden', true)
                    $('#genButtonI').attr('hidden', true);
                    $('#genButtonS').attr('hidden', true);
                    $('#userDialogEstado').dialog('close');
                    $('#userDialog').dialog('close');
                    break;
                default:
                    break;
            }
        }
    }

    function handleFileUpload(file, opcion) {
        var id = 0;
        if (opcion == 0) {
            var row = $('#ingresoTable').datagrid('getSelected');
            id = row.request_id;
        } else if (opcion == 1) {
            var row = $('#salidaTable').datagrid('getSelected');
            id = row.request_id;
        } else {
            var row = $('#userTable2').datagrid('getSelected');
            id = row.request_vacation_id;
        }

        if (!file) return;

        if (file.type !== "application/pdf") {
            $.messager.alert('Error', 'Solo se permiten archivos PDF.');
            return;
        }

        let formData = new FormData();
        formData.append("archivo", file);
        formData.append("opcion", opcion);
        formData.append("id", id);

        $.ajax({
            url: 'index.php?c=vacaciones&m=subirConstancia', // Cambia esto por tu endpoint
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {
                var responseData = JSON.parse(response);
                if (opcion == 2) {
                    $('#doc_formato_vac').show();
                    $('#doc_formato_vac').attr('src', responseData.url + '?t=' + new Date().getTime());
                } else {
                    $('#doc_formato').show();
                    $('#doc_formato').attr('src', responseData.url + '?t=' + new Date().getTime());
                }
                $.messager.alert('Éxito', 'Archivo subido correctamente');
            },
            error: function (xhr) {
                $.messager.alert('Error', 'Hubo un problema al subir el archivo');
            }
        });
    }

    function mostrarVacaciones(indexTab) {
        const switchElement = document.getElementById("switchCheckDefault");
        const row = $('#userTable').datagrid('getSelected');
        const employee_number = row.employee_number_id;

        tabV(employee_number, function () {
            $('#userTable2').datagrid('selectRow', indexTab);
        });

        $('#solicitarVac').removeAttr('hidden');

        if (switchElement.checked) {
            $('#solicitarVac').linkbutton('enable');
            $('#tt').tabs('select', 'Vacaciones');
        } else {
            $('#solicitarVac').linkbutton('disable');
        }
    }

    function cargarDias() {
        var row = $('#userTable').datagrid('getSelected');
        var employee_number = row.employee_number_id;
        var switchElement = document.getElementById("switchCheckDefault");
        let diasSolicitados;
        $.ajax({
            url: 'index.php?c=vacaciones&m=consultarSolicitudes',
            type: 'GET',
            dataType: 'json',
            data: {
                employee_number: employee_number,
                preev_year: switchElement.checked
            },
            cache: false,
            success: function (respuesta) {
                if (respuesta.error === true) {
                    $.messager.alert('Error', respuesta.msg, 'error');
                } else {
                    diasSolicitados = respuesta.count;
                    let fechaStr = row.hire_date;
                    let [fecha, hora] = fechaStr.split(" ");
                    let [dia, mes, anio] = fecha.split("/");
                    let fechaISO = `${anio}-${mes}-${dia}`;
                    let fechaInicial = new Date(fechaISO + 'T00:00:00');
                    let fechaActual;
                    if (switchElement.checked) {
                        let anioSiguiente = new Date().getFullYear() + 1;
                        fechaActual = new Date(`${anioSiguiente}-01-01T00:00:00`);
                    } else {
                        fechaActual = new Date(); // Fecha actual real
                    }

                    // Normalizar ambas fechas al inicio del día (00:00:00)
                    fechaActual.setHours(0, 0, 0, 0);
                    // Calcular la diferencia en meses

                    let diferenciaMeses =
                        (fechaActual.getFullYear() - fechaInicial.getFullYear()) * 12 +
                        (fechaActual.getMonth() - fechaInicial.getMonth());

                    if (fechaActual.getDate() < fechaInicial.getDate()) {
                        diferenciaMeses--; // No cuenta el mes incompleto
                    }
                    // Calcular días de vacaciones según las reglas:
                    let diasVacaciones = 0;

                    if (diferenciaMeses >= 6) {
                        // Calcular los años redondeando hacia abajo
                        let diferenciaAnios = Math.floor(diferenciaMeses / 12);

                        if (diferenciaAnios >= 1 && diferenciaAnios <= 6) {
                            diasVacaciones = 12 + (diferenciaAnios - 1) * 2;
                        } else if (diferenciaAnios > 6) {
                            diasVacaciones = 22; // Hasta 6 años
                            let aniosExtra = diferenciaAnios - 6;
                            diasVacaciones += Math.floor(aniosExtra / 5) * 2;
                        } else {
                            // Aún no ha cumplido el primer año pero ya pasaron 8 meses
                            diasVacaciones = 12;
                        }
                    }

                    // Calcular los días disponibles después de restar los días solicitados
                    let diasDisponibles = diasVacaciones - diasSolicitados;

                    if (diasDisponibles == 0 && diferenciaMeses >= 16) {
                        $('#switch_anio_entrante').show();
                    }
                    // Mostrar los días disponibles en la etiqueta correspondiente

                    if (switchElement.checked) {
                        $('#switch_anio_entrante').show();
                        $('h4[name="vacationDays"]').text(diasDisponibles > 0 ? `${diasDisponibles} días` : "0 días");
                    } else {
                        $('h4[name="vacationDays"]').text(diasDisponibles > 0 ? `${diasDisponibles} días` : "0 días");
                    }

                    $('input[name="labelVacationDaysIn"]').val(diasDisponibles);

                    if (diasDisponibles == 0) {
                        $('#solicitarVac').linkbutton('disable');
                    } else {
                        $('#solicitarVac').attr('hidden', false);
                        $('#solicitarVac').linkbutton('enable');
                    }
                }
            }
        });
    }
</script>

<body>
    <div class="container-fluid d-flex p-5" style="height: 700px">
        <div class="container">
            <div class="row">
                <div class="col-md-6"> <!-- Primera sección (70%) con margen -->
                    <!--TABLA DE LOS USUARIOS-->
                    <table id="userTable" class="easyui-datagrid" title="Empleados" style="width:100%;height:400px"
                        data-options="singleSelect:true">
                        <thead>
                            <tr>
                                <th data-options="field:'employee_number_id',width:70" align="center">Num_Emp</th>
                                <th data-options="field:'name',width:450">Nombre</th>
                                <th data-options="field:'estado',width:100" align="center">Estado</th>
                            </tr>
                        </thead>
                    </table>
                </div>
                <div class="col-md-6">
                    <!-- Segunda sección (30%) -->
                    <form id="editForm" action="post" hidden>
                        <div class="container" style="width: 600px;">
                            <div class="row">
                                <h2 class="col-sm-2">Detalles</h2>
                            </div>
                            <div class="input-group mb-2 w-25">
                                <span class="input-group-text" id="basic-addon1">#</span>
                                <input type="text" class="form-control" name="labelEmployeeNumberId" id="editID"
                                    aria-describedby="basic-addon1" readonly disabled>
                            </div>
                            <input type="text" class="form-control w-75 mb-2" id="editName" name="labelName" disabled>
                            <div class="row mb-2">
                                <div class="col-sm-4">
                                    <p class="mb-0">Fecha de ingreso:</p>
                                    <input type="text" class="form-control" id="editHireDate" name="labelHireDate"
                                        readonly disabled>
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
                                    <select id="editRole" name="labelRole" class="form-control"
                                        onchange="consultarDatosExtra()" disabled>
                                    </select>
                                </div>
                                <div class="col">
                                    <p class="mb-0">Departamento:</p>
                                    <input type="text" class="form-control" id="editDepartment" name="labelDepartment"
                                        disabled>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="mb-2">
                                    <p class="mb-0">Supervisor:</p>
                                    <input type="text" class="form-control" id="editSupervisor" name="labelSupervisor"
                                        disabled>
                                </div>
                                <div class="mb-2">
                                    <div class="alert alert-primary" role="alert">
                                        <div
                                            class="d-flex flex-column flex-md-row align-items-center justify-content-between gap-3">
                                            <!-- Texto de días de vacaciones -->
                                            <div class="d-flex flex-column align-items-start gap-1">
                                                <h4 name="vacationDays" class="mb-0 text-success"></h4>
                                                <p class="mb-0 fw-semibold" id="titleVac" style="font-size: 14px;">
                                                    DISPONIBLES</p>
                                            </div>


                                            <!-- Switch para cambiar de año -->
                                            <div id="switch_anio_entrante" class="p-3 rounded"
                                                style="background-color: #fff3cd; border: 1px solid #ffeeba;">
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox" role="switch"
                                                        id="switchCheckDefault" onchange="mostrarVacaciones()">
                                                    <label class="form-check-label fw-medium" for="switchCheckDefault">
                                                        Usar vacaciones del periodo <span id="anioSiguiente"
                                                            class="fw-bold"></span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div id="tt" class="easyui-tabs" style="width:100%;height:330px;"
                        data-options="onSelect: onTabSelect" hidden>

                        <div id="TabIngreso" title="Ingreso" style="display:none;">
                            <table id="ingresoTable" class="easyui-datagrid" style="width:100%;height:300px"
                                data-options="singleSelect:true" toolbar="#toolbar2">
                                <thead>
                                    <tr>
                                        <th data-options="field:'request_id',width:50" hidden>#</th>
                                        <th data-options="field:'type_request',width:50" hidden>#</th>
                                        <th data-options="field:'employee_number',width:50" hidden>#</th>
                                        <th data-options="field:'url_doc',width:50" hidden>#</th>
                                        <th data-options="field:'employee_name',width:220">Nombre</th>
                                        <th data-options="field:'estado',width:90" align="center">Estado</th>
                                        <th data-options="field:'required_date',width:150">Dia y hora solicitados</th>
                                        <th data-options="field:'request_date',width:150">Fecha de solicitud</th>
                                    </tr>
                                </thead>
                            </table>
                            <div id="toolbar2">
                                <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-add" plain="true"
                                    onclick="crearSolicitudI()" id="solicitar">Solicitar</a>
                                <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-remove"
                                    plain="true" onclick="cambiarEstadoSI()" id="cambiarEstadoI" disabled>Cambiar
                                    Estado</a>
                                <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-redo" plain="true"
                                    onclick="document.getElementById('fileInputIngreso').click();"
                                    id="subirContsIngreso">
                                    Subir Archivo
                                </a>
                                <input type="file" id="fileInputIngreso" style="display: none;" accept=".pdf"
                                    onchange="handleFileUpload(this.files[0],0)" />
                            </div>
                        </div>
                        <div id="TabSalida" title="Salida" style="display:none;">
                            <table id="salidaTable" class="easyui-datagrid" style="width:100%;height:300px"
                                data-options="singleSelect:true" toolbar="#toolbar1">
                                <thead>
                                    <tr>
                                        <th data-options="field:'request_id',width:50" hidden>#</th>
                                        <th data-options="field:'type_request',width:50" hidden>#</th>
                                        <th data-options="field:'employee_number',width:50" hidden>#</th>
                                        <th data-options="field:'url_doc',width:50" hidden>#</th>
                                        <th data-options="field:'employee_name',width:220">Nombre</th>
                                        <th data-options="field:'estado',width:90" align="center">Estado</th>
                                        <th data-options="field:'required_date',width:150">Dia y hora solicitados</th>
                                        <th data-options="field:'request_date',width:150">Fecha de solicitud</th>
                                    </tr>
                                </thead>
                            </table>
                            <div id="toolbar1">
                                <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-add" plain="true"
                                    onclick="crearSolicitudS()" id="solicitar">Solicitar</a>
                                <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-remove"
                                    plain="true" onclick="cambiarEstadoSI()" id="cambiarEstadoR" disabled>Cambiar
                                    Estado</a>
                                <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-redo" plain="true"
                                    onclick="document.getElementById('fileInputSalida').click();" id="subirContsSalida">
                                    Subir Archivo
                                </a>
                                <input type="file" id="fileInputSalida" style="display: none;" accept=".pdf"
                                    onchange="handleFileUpload(this.files[0],1)" />
                            </div>
                        </div>
                        <div id="tabVacaciones" title="Vacaciones" style="display:none;">
                            <table id="userTable2" class="easyui-datagrid" style="width:100%;height:300px"
                                data-options="singleSelect:true" toolbar="#toolbar">
                                <thead>
                                    <tr>
                                        <th data-options="field:'request_vacation_id',width:50" hidden>#</th>
                                        <th data-options="field:'back_date',width:50" hidden>#</th>
                                        <th data-options="field:'start_date',width:50" hidden>#</th>
                                        <th data-options="field:'finish_date',width:50" hidden>#</th>
                                        <th data-options="field:'work_shift',width:50" hidden>#</th>
                                        <th data-options="field:'employee_number',width:50" hidden>#</th>
                                        <th data-options="field:'url_doc',width:50" hidden>#</th>
                                        <th data-options="field:'employee_name',width:270">Nombre</th>
                                        <th data-options="field:'estado',width:90" align="center">Estado</th>
                                        <th data-options="field:'days',width:60" align="center">Dias</th>
                                        <th data-options="field:'request_date',width:90" align="center">Fecha</th>
                                        <th data-options="field:'year_i',width:60" align="center">Inicio</th>
                                        <th data-options="field:'year',width:60" align="center">Fin</th>
                                    </tr>
                                </thead>
                            </table>
                            <div id="toolbar">
                                <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-add" plain="true"
                                    onclick="crearSolicitud()" id="solicitarVac" hidden>Solicitar</a>
                                <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-remove"
                                    plain="true" onclick="cambiarEstado()" id="bajaButton" disabled>Cambiar Estado</a>
                                <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-redo" plain="true"
                                    onclick="document.getElementById('fileInputVacaciones').click();"
                                    id="subirContsVacaciones">
                                    Subir Archivo
                                </a>
                                <input type="file" id="fileInputVacaciones" style="display: none;" accept=".pdf"
                                    onchange="handleFileUpload(this.files[0],2)" />
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <form id="editFormVac" action="post" hidden>
                        <div class="container" style="width: 600px;">
                            <div class="row">
                                <h4 class="col-sm-8">Detalles de la solicitud</h4>
                                <div class="col-sm-4">
                                    <a href="javascript:void(0)" class="easyui-linkbutton " iconCls="icon-edit"
                                        plain="true" onclick="editUser()" id="editButton"
                                        style="border: 1px solid black">Editar solicitud</a>
                                </div>

                            </div>
                            <div class="row py-2">
                                <input type="hidden" class="form-control" name="labelRV">
                                <div class="col-sm-4">
                                    <p class="mb-0">Fecha de solicitud</p>
                                    <input type="date" class="form-control" id="editDateR" name="labelDateR" disabled>
                                </div>
                                <div class="col-sm-4">
                                    <p class="mb-0">Dias solicitados</p>
                                    <input type="number" class="form-control" id="editDays" name="labelDays"
                                        onchange="calculateEndDate()" disabled>
                                </div>
                                <div class="col-sm-4">
                                    <p class="mb-0">Turno del empleado:</p>
                                    <select id="editTurno" name="labelTurno" class="form-control"
                                        onchange="calculateEndDate()" disabled>
                                        <option value="0">Matutino</option>
                                        <option value="1">Nocturno</option>
                                        <option value="2">Mixto</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row py-2">
                                <div class="col-sm-6">
                                    <p class="mb-0">Fecha de comienzo</p>
                                    <input type="date" class="form-control" id="editDateC" name="labelDateC"
                                        onchange="calculateEndDate()" disabled>
                                </div>
                            </div>
                            <hr>
                            <div class="row py-2">
                                <div class="col-sm-6">
                                    <p class="mb-0">Fecha de fin</p>
                                    <input type="date" class="form-control" id="editDateF" name="labelDateF" disabled>
                                </div>
                                <div class="col-sm-6">
                                    <p class="mb-0">Retoma labores:</p>
                                    <input type="date" class="form-control" id="editDateL" name="labelDateL" disabled>
                                </div>
                                <input type="number" class="form-control" name="labelVacationDaysIn" id="vacationDaysIn"
                                    hidden>
                            </div>
                            <iframe id="doc_formato_vac" src="" width="100%" height="600px"></iframe>
                            <div class="row py-2">
                                <div class="col-sm-12">
                                    <button type="button" class="btn btn-warning" style="width: 100%;"
                                        id="editarUsuario" onclick="updateForm()" hidden>Editar</button>
                                </div>
                                <div class="col-sm-12">
                                    <button type="button" class="btn btn-warning" id="genButton" style="width: 100%;"
                                        hidden>Generar
                                        solicitud</button>
                                </div>
                            </div>

                        </div>
                    </form>
                    <form id="editFormSI" action="post" hidden>
                        <div class="container" style="width: 600px;">
                            <div class="row">
                                <h4 class="col-sm-8">Detalles de la solicitud</h4>
                                <div class="col-sm-4">
                                    <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-edit"
                                        plain="true" onclick="editSI()" id="editButtonSal"
                                        style="border: 1px solid black;">Editar solicitud</a>
                                </div>
                            </div>
                            <div class="row py-2">
                                <input type="hidden" class="form-control" name="labelR" id="editR" readonly disabled>
                                <div class="col-sm-6">
                                    <p class="mb-0">Fecha y hora solicitados</p>
                                    <input type="datetime-local" class="form-control" id="editDateRequired"
                                        name="labelDateRequired" onchange="calculateEndDate()" disabled>
                                </div>
                                <div class="col-sm-6">
                                    <p class="mb-0">Fecha de solicitud</p>
                                    <input type="date" class="form-control" id="editDateRequest" name="labelDateRequest"
                                        disabled>
                                </div>
                            </div>
                            <iframe id="doc_formato" src="" width="100%" height="600px"></iframe>
                            <div class="row py-2">
                                <div class="col-sm-12">
                                    <button type="button" class="btn btn-warning" id="editarSI" onclick="updateFormSI()"
                                        style="width: 100%;" hidden>Editar</button>
                                </div>
                                <div class="col-sm-12">
                                    <button type="button" class="btn btn-warning" id="genButtonS" style="width: 100%;"
                                        hidden>Generar
                                        solicitud</button>
                                    <button type="button" class="btn btn-warning" id="genButtonI" style="width: 100%;"
                                        hidden>Generar
                                        solicitud</button>
                                </div>
                            </div>

                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>

<!--MODAL-->
<div id="userDialogEstado" class="easyui-dialog" title="Cambiar estado" style="width:400px;height:200px;padding:10px"
    closed="true" buttons="#dlg-buttons">
    <form id="formEstado" method="post">
        <div class="form-group py-3">
            <label for="estadoSelect">Cambiar a:</label>
            <select id="estadoSelect" name="estadoSelect" class="form-control">
                <option value="2">APROBADA</option>
                <option value="3">RECHAZADA</option>
            </select>
        </div>
    </form>
</div>
<!--MODAL-BUTTONS-->
<div id="dlg-buttons">
    <button type="button" class="btn btn-success" id="cambiarEstadoButton"
        onclick="cambiarEstadoFunc()">Cambiar</button>
    <button type="button" class="btn btn-success" id="cambiarEstadoSButton"
        onclick="cambiarEstadoSIFunc()">Cambiar</button>
    <button type="button" class="btn btn-danger" onclick="$('#userDialogEstado').dialog('close')">Cancelar</button>
</div>