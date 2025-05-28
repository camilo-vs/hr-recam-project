<link src="assets/css/empleados.css" rel="stylesheet" />
<script>
    //Funcionalidad al cargar la pagina
    $(document).ready(function () {
        $('#cont_estado').hide();
        $('#cont_f_estado').hide();
        $('#btn_edit_img').hide();
        $('#userTable').datagrid({
            onSelect: function (index, row) {
                // Deshabilitar y habilitar botón según el estado
                if (row.estado !== 'BAJA') {
                    $('#cont_estado').hide();
                    $('#cont_f_estado').hide();
                    $('#editButton').linkbutton('enable');
                    $('#bajaButton').linkbutton('enable');
                } else {
                    $('#cont_estado').show();
                    $('#cont_f_estado').show();
                    $('#editButton').linkbutton('disable');
                    $('#bajaButton').linkbutton('enable');
                    $('#chan_f_b').val(row.change_date);
                }
                $('input[name="labelEmployeeNumberId"]').val(row.employee_number_id);
                $('input[name="labelName"]').val(row.name);
                $('input[name="labelHireDate"]').val(row.hire_date);
                $('select[name="labelGenre"]').val(row.genre_wname);
                $('#nss').val(row.nss);
                $('#curp').val(row.curp);
                $('#rfc').val(row.rfc);
                $('#phone').val(row.phone);
                $('#address').val(row.address);
                $('#birth_date').val(row.birth_date);
                $('select[name="labelRole"]').val(row.role_wname);
                $('input[name="labelDepartment"]').val(row.department);
                $('input[name="labelSupervisor"]').val(row.supervisor);
                $('#editTYPE').val(row.id_type);
                $('#btn_edit_img').hide();
                if (row.url_img == '' || row.url_img == null) {
                    document.getElementById('preview-imagen').src = "assets/img/empleados/sin_imagen.png";
                } else {
                    document.getElementById('preview-imagen').src = row.url_img;
                }
            },
            onUnselect: function (index, row) {
                // Deshabilitar botones cuando se deselecciona la fila
                if (row.estado != 'BAJA') {
                    $('#editButton').linkbutton('enable');
                    $('#bajaButton').linkbutton('enable');
                } else {
                    $('#editButton').linkbutton('disable');
                    $('#bajaButton').linkbutton('enable');
                }

                $('input[name="labelName"]').prop("disabled", true);
                $('select[name="labelGenre"]').prop("disabled", true);
                $('select[name="labelRole"]').prop("disabled", true);
                $('input[name="vacationDays"]').prop("disabled", true);
                $('#editTYPE').prop("disabled", true);
                $('#editID').prop("disabled", true);
                $('#nss').prop("disabled", true);
                $('#curp').prop("disabled", true);
                $('#rfc').prop("disabled", true);
                $('#birth_date').prop("disabled", true);
                $('#phone').prop("disabled", true);
                $('#address').prop("disabled", true);
                $('#editarUsuario').attr('hidden', true);
            }
        });

        $.ajax({
            url: 'index.php?c=empleados&m=consultarEmpleados',
            type: 'POST',
            dataType: 'json',
            cache: false,
            success: function (respuesta) {
                if (respuesta.error === true) {
                    $.messager.alert('Error', respuesta.msg, 'error');
                } else {
                    $('#userTable').datagrid('loadData', respuesta.registros);
                    $('#userTable').datagrid('enableFilter');
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
                    $('#r_puesto').combobox({
                        data: respuesta.departamentos,
                        valueField: 'role_id',
                        textField: 'name',
                        panelHeight: 'auto'
                    });
                    // Vaciar el select y agregar opción por defecto
                    var select = $('#editRole');
                    select.empty();
                    // Iterar el arreglo y agregar cada opción
                    $.each(respuesta.departamentos, function (index, departamento) {
                        select.append('<option value="' + departamento.role_id + '">' + departamento.name + '</option>');
                    });

                    // Vaciar el select y agregar opción por defecto
                    var select = $('#puesto');
                    select.empty();
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
        //FUNCIONALIDADES FECHAS REPORTES
        $('#r_fecha_inicio').datebox({
            onChange: function (newValue, oldValue) {
                const fechaFin = $('#r_fecha_fin').datebox('getValue');
                console.log("llego");
                if (newValue && fechaFin) {
                    const inicio = new Date(newValue);
                    const fin = new Date(fechaFin);
                    if (inicio > fin) {
                        $.messager.alert('Validación', 'La fecha de inicio no puede ser mayor que la fecha final.', 'warning');
                        // Opcional: limpiar el campo que causó el error
                        $('#r_fecha_inicio').datebox('clear');
                    }
                }
            }
        });

        $('#r_fecha_fin').datebox({
            onChange: function (newValue, oldValue) {
                const fechaInicio = $('#r_fecha_inicio').datebox('getValue');
                if (newValue && fechaInicio) {
                    const fin = new Date(newValue);
                    const inicio = new Date(fechaInicio);
                    if (fin < inicio) {
                        $.messager.alert('Validación', 'La fecha final no puede ser menor que la fecha de inicio.', 'warning');
                        // Opcional: limpiar el campo que causó el error
                        $('#r_fecha_fin').datebox('clear');
                    }
                }
            }
        });

        document.getElementById('input-imagen').addEventListener('change', function (e) {
            const archivo = e.target.files[0];
            if (archivo) {
                const lector = new FileReader();
                lector.onload = function (event) {
                    document.getElementById('preview-imagen').src = event.target.result;
                }
                lector.readAsDataURL(archivo);
            }
        });

    });

    function newUser() {
        $('#crearUsuario').show();
        $('#userDialog').dialog('open').dialog('setTitle', 'Nuevo Empleado');
        $('#userForm').form('clear');
        $('#employee_number_id').val('');
    }

    function editUser() {
        $('input[name="labelName"]').prop("disabled", false);
        $('input[name="labelEmployeeNumberId"]').prop("disabled", false);
        $('select[name="labelGenre"]').prop("disabled", false);
        $('select[name="labelRole"]').prop("disabled", false);
        $('#nss').prop("disabled", false);
        $('#curp').prop("disabled", false);
        $('#rfc').prop("disabled", false);
        $('#birth_date').prop("disabled", false);
        $('#phone').prop("disabled", false);
        $('#address').prop("disabled", false);
        $('#fecha_nacimiento').prop("disabled", false);
        $('#telefono').prop("disabled", false);
        $('#direaccion').prop("disabled", false);
        $('#editTYPE').prop("disabled", false);
        $('#editarUsuario').removeAttr('hidden');
        $('#btn_edit_img').show();
    }

    function submitForm() {
        if ($('#userForm')[0].checkValidity()) {
            var userData = $('#userForm').serialize();
            $.messager.progress({
                title: 'Procesando...',
                msg: 'Por favor espere mientras se crea el usuario.'
            });

            $.ajax({
                url: 'index.php?c=empleados&m=crearEmpleado',
                type: 'POST',
                dataType: 'json',
                data: userData,
                cache: false,
                success: function (respuesta) {
                    $.messager.progress('close');
                    if (respuesta.error == true) {
                        $.messager.alert('Error', respuesta.msg, 'error');
                    } else {
                        if (respuesta.creado != false) {
                            // Encadena la llamada a consultarDatosExtraTABLA
                            consultarDatosExtraTABLA().done(function (respExtra) {
                                if (respExtra.error === true) {
                                    $.messager.alert('Error', respExtra.msg, 'error');
                                    return;
                                }
                                let hire_date_raw = $('#new_hire_date').val(); // formato: yyyy-mm-dd
                                let parts = hire_date_raw.split('-'); // [yyyy, mm, dd]
                                let hire_date = `${parts[2]}/${parts[1]}/${parts[0]}`;

                                var newRow = {
                                    employee_number_id: respuesta.datos['employee_number_id'],
                                    name: respuesta.datos['username'],
                                    genre_wname: $('#genero option:selected').val(),
                                    estado: 'ACTIVO',
                                    genero: $('#genero option:selected').text(),
                                    role_name: $('#puesto option:selected').text(),
                                    role_wname: $('#puesto option:selected').val(),
                                    nss: $('#new_nss').val(),
                                    curp: $('#new_curp').val(),
                                    rfc: $('#new_rfc').val(),
                                    birth_date: $('#new_birth_date').val(),
                                    phone: $('#new_phone').val(),
                                    address: $('#new_address').val(),
                                    // Usando los valores obtenidos en la función consultarDatosExtraTABLA:
                                    supervisor: respExtra.supervisorName,
                                    department: respExtra.departmentName,
                                    hire_date: hire_date
                                };

                                $('#userTable').datagrid('insertRow', {
                                    index: 0,
                                    row: newRow
                                });
                                $('#userDialog').dialog('close');
                                $.messager.alert('Se realizo la petición', 'Se creo el nuevo usuario!', 'info');
                            }).fail(function (jqXHR, textStatus, errorThrown) {
                                console.error("Error en consultarDatosExtraTABLA:", errorThrown);
                                $.messager.alert('Error', 'No se pudieron obtener los datos extra.', 'error');
                            });
                        } else {
                            $.messager.alert('Error', respuesta.msg, 'error');
                        }
                    }
                }
            });
        } else {
            document.getElementById('userForm').reportValidity();

            // Validación personalizada de campos clave
            const campos = [
                { id: 'new_nss', errorId: 'nssError' },
                { id: 'new_curp', errorId: 'curpError' },
                { id: 'new_rfc', errorId: 'rfcError' },
                { id: 'new_phone', errorId: 'phoneError' }
            ];

            campos.forEach(campo => {
                const input = document.getElementById(campo.id);
                const errorElem = document.getElementById(campo.errorId);

                if (!input.checkValidity()) {
                    errorElem.textContent = input.title;
                } else {
                    errorElem.textContent = '';
                }
            });
        }
    }

    function updateForm() {
        var row = $('#userTable').datagrid('getSelected');
        if (!row) {
            $.messager.alert('Error', 'Por favor selecciona un usuario para actualizar.', 'error');
            return;
        }

        // Validación básica
        var idType = $('#editTYPE').val();
        var employeeID = $('#editID').val();
        var name = $('#editName').val();
        var genre = $('#editGenre').val();
        var role = $('#editRole').val();

        if (!idType || !employeeID || !name || !genre || !role) {
            $.messager.alert('Error', 'Por favor completa todos los campos obligatorios.', 'error');
            return;
        }

        // Comparar si hubo cambios
        if (
            valoresIguales(row.id_type, idType) &&
            valoresIguales(row.employee_number_id, employeeID) &&
            valoresIguales(row.name, name) &&
            valoresIguales(row.genre_wname, genre) &&
            valoresIguales(row.role_wname, role) &&
            valoresIguales(row.nss, $('#nss').val()) &&
            valoresIguales(row.curp, $('#curp').val()) &&
            valoresIguales(row.rfc, $('#rfc').val()) &&
            valoresIguales(row.birth_date, $('#birth_date').val()) &&
            valoresIguales(row.phone, $('#phone').val()) &&
            valoresIguales(row.address, $('#address').val()) &&
            !$('#input-imagen').val()
        ) {
            $.messager.alert('Info', 'No se detecta ningún cambio.', 'info');
            return;
        }

        const userId = row.employee_number_id;
        let type;
        switch (idType) {
            case "1":
                type = "RECAM";
                break;
            case "2":
                type = "RECAM - GPI";
                break;
            default:
                type = "GPI";
        }

        const index = $('#userTable').datagrid('getRowIndex', row);

        // Crear FormData para incluir imagen
        var formData = new FormData($('#editForm')[0]);
        formData.append('id', userId);

        const imagen = $('#input-imagen')[0].files[0];
        if (imagen) {
            formData.append('imagen', imagen);
        }

        $.ajax({
            url: 'index.php?c=empleados&m=editarEmpleado',
            type: 'POST',
            data: formData,
            processData: false, // Importante para FormData
            contentType: false,
            dataType: 'json',
            success: function (respuesta) {
                $.messager.progress('close');
                if (respuesta.error === true) {
                    $.messager.alert('Error', respuesta.msg, 'error');
                } else {
                    if (respuesta.cambio) {
                        // Actualizar tabla
                        var updatedData = {
                            employee_number_id: employeeID,
                            type: type,
                            id_type: idType,
                            name: name,
                            url_img: respuesta.url ? respuesta.url : row.url_img,
                            genero: $('#editGenre option:selected').text(),
                            role_name: $('#editRole option:selected').text(),
                            role_wname: role,
                            department: $('#editDepartment').val(),
                            supervisor: $('#editSupervisor').val(),
                            nss: $('#nss').val(),
                            curp: $('#curp').val(),
                            rfc: $('#rfc').val(),
                            birth_date: $('#birth_date').val(),
                            phone: $('#phone').val(),
                            address: $('#address').val(),
                            update_date: new Date().toLocaleString('es-ES', {
                                year: 'numeric',
                                month: '2-digit',
                                day: '2-digit',
                                hour: '2-digit',
                                minute: '2-digit',
                                second: '2-digit'
                            }).replace(",", ""),
                            updated_by: respuesta.created_by
                        };

                        $('#userTable').datagrid('updateRow', {
                            index: index,
                            row: updatedData
                        });

                        // Bloquear campos
                        $('#editTYPE, input[name="labelName"], input[name="labelEmployeeNumberId"]').prop("disabled", true);
                        $('select[name="labelGenre"], select[name="labelRole"]').prop("disabled", true);
                        $('input[name="vacationDays"], #nss, #curp, #rfc, #birth_date, #phone, #address').prop("disabled", true);
                        $('#editarUsuario').attr('hidden', true);
                        $('#btn_edit_img').hide();
                        $('#userDialog').dialog('close');
                        $.messager.alert('Petición realizada', '¡El usuario fue actualizado correctamente!', 'info');
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

    function valoresIguales(a, b) {
        const va = (a === null || a === undefined) ? '' : a.toString().trim();
        const vb = (b === null || b === undefined) ? '' : b.toString().trim();
        return va === vb;
    }

    function cambiarEstado() {
        var row = $('#userTable').datagrid('getSelected');
        if (!row) {
            $.messager.alert('Error', 'Selecciona un usuario.', 'error');
            return;
        }

        var index = $('#userTable').datagrid('getRowIndex', row);
        var cambio = (row.estado == 'ACTIVO') ? 'BAJA' : 'ACTIVO';
        var estado = (row.estado == 'ACTIVO') ? 0 : 1;

        // Crear el div y almacenarlo en una variable
        var $dialog = $('<div></div>').append(`
        <div style="padding:15px;">
            <label>Selecciona una fecha:</label><br>
            <input type="date" id="fechaCambio" style="width:100%; margin-top:5px;">
        </div>
    `);

        $dialog.dialog({
            modal: true,
            title: 'Fecha del cambio de estado',
            width: 350,
            height: 180,
            closed: false,
            cache: false,
            buttons: [{
                text: 'Confirmar',
                handler: function () {
                    var fecha = $('#fechaCambio').val();
                    if (!fecha) {
                        $.messager.alert('Error', 'Por favor selecciona una fecha.', 'error');
                        return;
                    }

                    $.messager.confirm('Confirmación', 'Se cambiará el estado a ' + cambio + ' del usuario ' + row.name + '. ¿Está seguro?', function (r) {
                        if (r) {
                            $.ajax({
                                url: 'index.php?c=empleados&m=cambiarEstado',
                                type: 'POST',
                                dataType: 'json',
                                data: {
                                    employee_number_id: row.employee_number_id,
                                    estado: estado,
                                    fecha_cambio: fecha
                                },
                                cache: false,
                                success: function (respuesta) {
                                    if (respuesta.error === true) {
                                        $.messager.alert('Error', respuesta.msg, 'error');
                                    } else if (respuesta.cambio) {

                                        var partes = fecha.split('-'); // [yyyy, mm, dd]
                                        var fechaFormateada = partes[2] + '/' + partes[1] + '/' + partes[0];
                                        var updatedData = {
                                            estado: cambio,
                                            update_date: new Date().toLocaleString('es-ES'),
                                            updated_by: respuesta.created_by,
                                            change_date: fechaFormateada,
                                            change_type: respuesta.id_updated.toUpperCase()
                                        };

                                        $('#userTable').datagrid('updateRow', {
                                            index: index,
                                            row: updatedData
                                        });

                                        if (estado == 1) {
                                            $('#cont_estado').hide();
                                            $('#cont_f_estado').hide();
                                            $('#editButton').linkbutton('enable');
                                        } else {
                                            $('#cont_estado').show();
                                            $('#chan_f_b').val(fechaFormateada);
                                            $('#cont_f_estado').show();
                                            $('#editButton').linkbutton('disable');
                                        }

                                        $('input[name="labelName"]').prop("disabled", true);
                                        $('select[name="labelGenre"]').prop("disabled", true);
                                        $('select[name="labelRole"]').prop("disabled", true);
                                        $('input[name="vacationDays"]').prop("disabled", true);
                                        $('#editarUsuario').attr('hidden', true);

                                        $.messager.alert('Éxito', '¡El usuario cambió su estado a ' + cambio + '!', 'info');
                                    } else {
                                        $.messager.alert('Error', respuesta.msg, 'error');
                                    }

                                    // Cerrar el diálogo al terminar
                                    $dialog.dialog('close');
                                }
                            });
                        }
                    });
                }
            }, {
                text: 'Cancelar',
                handler: function () {
                    $dialog.dialog('close');
                }
            }]
        });
    }




    function consultarNombre() {
        $('#alerta').remove();
        var row = $('#userTable').datagrid('getSelected');
        var name_actu = '';
        if (row != null) {
            name_actu = row.name;
        }
        if ($('#username').val() != name_actu) {
            $.ajax({
                url: 'index.php?c=usuarios&m=validarNombre',
                type: 'POST',
                dataType: 'json',
                data: {
                    name: $('#username').val()
                },
                cache: false,
                success: function (respuesta) {

                    if (respuesta.error === true) {
                        $.messager.alert('Error', respuesta.msg, 'error');
                    } else {
                        if (respuesta.registros['total'] > 0) {
                            $('#crearUsuario').prop('disabled', true);
                            $('#label_nombre').after('<p id="alerta" style="color:red;font-size:13px">El nombre del usuario ya esta dado de alta.</p>');
                        } else {
                            $('#crearUsuario').prop('disabled', false);
                        }
                    }
                }
            });
        }
    }

    //COLORES DE CELDAS
    $(function () {
        var dg = $('#userTable');

        // Obtener opciones de columna y aplicar styler a 'estado'
        var estadoCol = dg.datagrid('getColumnOption', 'estado');
        estadoCol.styler = function (value, row, index) {
            if (value === 'ACTIVO') {
                return 'background-color: green; color: white; font-weight: bold;';
            } else if (value === 'BAJA') {
                return 'background-color: red; color: white; font-weight: bold;';
            }
            return '';
        };

        // Obtener opciones de columna y aplicar styler a 'type'
        var tipoCol = dg.datagrid('getColumnOption', 'type');
        tipoCol.styler = function (value, row, index) {
            if (value === 'GPI') {
                return 'background-color: #577bea; color: white; font-weight: bold;';
            } else if (value === 'RECAM') {
                return 'background-color: #f5b041; color: white; font-weight: bold;';
            } else if (value === 'RECAM - GPI') {
                return 'background-color: #cc25e3; color: white; font-weight: bold;';
            }
            return '';
        };

        // Recargar para aplicar estilos
        dg.datagrid('reload');
    });


    function consultarDatosExtraTABLA() {
        // Obtiene el índice seleccionado
        var index = $('#puesto').val();
        // Retorna el objeto AJAX (Deferred)
        return $.ajax({
            url: 'index.php?c=empleados&m=consultarDatosExtra',
            type: 'POST',
            dataType: 'json',
            data: {
                index: index
            },
            cache: false
        });
    }

    function consultarDatosExtra() {
        // Obtiene el índice seleccionado
        var index = $('#editRole').val();
        $.ajax({
            url: 'index.php?c=empleados&m=consultarDatosExtra',
            type: 'POST',
            dataType: 'json',
            data: {
                index: index
            },
            cache: false,
            success: function (respuesta) {
                if (respuesta.error === true) {
                    alert('Error: ' + respuesta.msg);
                } else {
                    $('input[name="labelSupervisor"]').val(respuesta.supervisorName);
                    $('input[name="labelDepartment"]').val(respuesta.departmentName);
                }
            },
            error: function (xhr, status, error) {
                console.error("Error AJAX:", error);
            }
        });
    }

    function openModalReport() {
        $('#r_empresa').combobox('setValue', '');
        $('#r_genero').combobox('setValue', '');
        $('#r_puesto').combobox('setValue', '');
        $('#r_fecha_inicio').datebox('clear');
        $('#r_fecha_fin').datebox('clear');
        $('#modal_report').dialog('open');       // Abre la modal

    }

    function generarReporte() {
        const empresa = $('#r_empresa').combobox('getValue');
        const genero = $('#r_genero').combobox('getValue');
        const puesto = $('#r_puesto').textbox('getValue');
        const fechaInicio = $('#r_fecha_inicio').datebox('getValue');
        const fechaFin = $('#r_fecha_fin').datebox('getValue');
        const estado = $('#r_estado').textbox('getValue');

        if (!empresa && !genero && !puesto && !fechaInicio && !fechaFin && !estado) {
            $.messager.alert('Validación', 'Debes llenar al menos un campo para generar el reporte.', 'warning');
            return;
        }

        if ((fechaInicio && !fechaFin) || (!fechaInicio && fechaFin)) {
            $.messager.alert('Validación', 'Debes llenar ambas fechas de ingreso (inicio y fin).', 'warning');
            return;
        }

        $.ajax({
            url: 'index.php?c=empleados&m=generarReporte',
            type: 'POST',
            dataType: 'json',
            data: {
                empresa: empresa,
                genero: genero,
                puesto: puesto,
                fechaInicio: fechaInicio,
                fechaFin: fechaFin,
                estado: estado
            },
            cache: false,
            success: function (respuesta) {
                if (!respuesta || !respuesta.registros || respuesta.registros.length === 0) {
                    $.messager.alert('Info', 'No se encontraron datos para el reporte.', 'info');
                    return;
                }

                // Obtener fecha y hora actual para el nombre del archivo
                const ahora = new Date();
                const yyyy = ahora.getFullYear();
                const mm = String(ahora.getMonth() + 1).padStart(2, '0');
                const dd = String(ahora.getDate()).padStart(2, '0');
                const hh = String(ahora.getHours()).padStart(2, '0');
                const min = String(ahora.getMinutes()).padStart(2, '0');
                const ss = String(ahora.getSeconds()).padStart(2, '0');

                const nombreArchivo = `reporte_empleados_${yyyy}-${mm}-${dd}_${hh}-${min}-${ss}.xlsx`;

                // Crear hoja de cálculo y libro
                const worksheet = XLSX.utils.json_to_sheet(respuesta.registros);
                const workbook = XLSX.utils.book_new();
                XLSX.utils.book_append_sheet(workbook, worksheet, "Reporte");

                // Descargar archivo Excel con fecha/hora
                XLSX.writeFile(workbook, nombreArchivo);
            },
            error: function (xhr, status, error) {
                console.error("Error AJAX:", error);
            }
        });
    }


</script>
<style>
    .btn-cerrar {
        background: #d9534f;
        color: white;
    }

    .btn-generar {
        background: #5cb85c;
        color: white;
    }
</style>

<body>
    <div class="container-fluid py-4">
        <!-- Tabla de usuarios -->
        <div class="row mb-4">
            <div class="col-12">
                <table id="userTable" class="easyui-datagrid w-100" title="Gestiónar empleados" style="height:360px;"
                    data-options="singleSelect:true,collapsible:true" toolbar="#toolbar">
                    <thead>
                        <tr>
                            <th data-options="field:'employee_number_id',width:100" align="center">Num Emp</th>
                            <th data-options="field:'name',width:280">Nombre</th>
                            <th data-options="field:'type',width:100" align="center">Empresa</th>
                            <th data-options="field:'estado',width:100" align="center">Estado</th>
                            <th data-options="field:'department',width:130" align="center">Departamento</th>
                            <th data-options="field:'role_name',width:240">Cargo</th>
                            <th data-options="field:'supervisor',width:280">Supervisor</th>
                            <th data-options="field:'hire_date',width:155" align="center">Fecha de alta</th>
                            <th data-options="field:'change_date',width:280" align="center">Cambio de estado</th>
                            <th data-options="field:'change_type',width:155" align="center">Realizado</th>
                            <th data-options="field:'role_wname',width:155" hidden></th>
                            <th data-options="field:'genre_wname',width:155" hidden></th>
                        </tr>
                    </thead>
                </table>

                <!-- Botones de la tabla -->
                <div id="toolbar" class="mt-2">
                    <?php if ($_SESSION['user_type'] != 3): ?>
                        <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-add" plain="true"
                            onclick="newUser()">Nuevo Empleado</a>
                        <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-remove" plain="true"
                            onclick="cambiarEstado()" id="bajaButton" disabled>Cambiar Estado</a>
                    <?php endif; ?>

                    <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-print" plain="true"
                        onclick="openModalReport()">Generar Reporte</a>
                </div>
            </div>
        </div>

        <!-- Formulario de edición -->
        <div class="row">
            <div class="col-12">
                <form id="editForm">
                    <div class="card p-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h4 class="mb-0">Detalles del Empleado</h4>
                            <?php if ($_SESSION['user_type'] != 3): ?>
                                <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-edit" plain="true"
                                    onclick="editUser()" id="editButton" disabled>Editar Usuario</a>+
                            <?php endif; ?>
                        </div>

                        <div class="row mb-2">
                            <div class="col-md-2 text-center">
                                <img src="assets/img/empleados/sin_imagen.png" id="preview-imagen"
                                    style="width: 260px; height: 260px; object-fit: cover; border: 1px solid black;"
                                    class="mb-2 rounded" />


                                <!-- Botón con estilo Bootstrap -->
                                <label for="input-imagen" class="btn btn-primary btn-sm w-100" id="btn_edit_img">
                                    Subir Imagen
                                </label>
                                <input type="file" id="input-imagen" accept="image/*" style="display: none;" />
                            </div>


                            <div class="col-md-10">
                                <div class="row">
                                    <div class="col-md-1" id="cont_estado">
                                        <label>Estado</label>
                                        <input type="text" class="form-control" disabled value="BAJA"
                                            style="background-color: red;color:white">
                                    </div>
                                    <div class="col-md-2" id="cont_f_estado">
                                        <label>Fecha Baja</label>
                                        <input type="text" class="form-control" id="chan_f_b" disabled
                                            style="background-color: red;color:white">
                                    </div>
                                    <div class="col-md-1">
                                        <label>Num Emp</label>
                                        <input type="text" class="form-control" id="editID" name="labelEmployeeNumberId"
                                            disabled>
                                    </div>
                                    <div class="col-md-2">
                                        <label>Empresa</label>
                                        <select id="editTYPE" name="labelType" class="form-control" disabled>
                                            <option value=""></option>
                                            <option value="0">GPI</option>
                                            <option value="1">RECAM</option>
                                            <option value="2">RECAM - GPI</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label>Nombre</label>
                                        <input type="text" class="form-control" id="editName" name="labelName" disabled>
                                    </div>
                                    <div class="col-md-2">
                                        <label>Fecha de ingreso</label>
                                        <input type="text" class="form-control" name="labelHireDate" readonly disabled>
                                    </div>
                                    <div class="col-md-1">
                                        <label>Género</label>
                                        <select id="editGenre" name="labelGenre" class="form-control" disabled>
                                            <option value=""></option>
                                            <option value="0">Femenino</option>
                                            <option value="1">Masculino</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label>NSS</label>
                                        <input type="text" class="form-control" id="nss" name="nss" disabled>
                                    </div>
                                    <div class="col-md-4">
                                        <label>CURP</label>
                                        <input type="text" class="form-control" id="curp" name="curp" disabled>
                                    </div>
                                    <div class="col-md-4">
                                        <label>RFC</label>
                                        <input type="text" class="form-control" id="rfc" name="rfc" disabled>
                                    </div>
                                    <div class="col-md-12">
                                        <hr>
                                    </div>
                                    <div class="col-md-6">
                                        <label>Fecha de nacimiento</label>
                                        <input type="date" class="form-control" id="birth_date" name="birth_date"
                                            disabled>
                                    </div>
                                    <div class="col-md-6">
                                        <label>Teléfono</label>
                                        <input type="number" class="form-control" id="phone" name="phone" disabled>
                                    </div>
                                    <div class="col-md-12">
                                        <label>Dirección</label>
                                        <input type="text" class="form-control" id="address" name="address" disabled>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <hr>

                        <div class="row mb-2">
                            <div class="col-md-6">
                                <label>Puesto del empleado</label>
                                <select id="editRole" name="labelRole" class="form-control"
                                    onchange="consultarDatosExtra()" disabled></select>
                            </div>
                            <div class="col-md-6">
                                <label>Departamento</label>
                                <input type="text" class="form-control" id="editDepartment" name="labelDepartment"
                                    disabled>
                            </div>
                        </div>

                        <div class="mb-2">
                            <label>Supervisor</label>
                            <input type="text" class="form-control" id="editSupervisor" name="labelSupervisor" disabled>
                        </div>

                        <div class="mt-4">
                            <button type="button" class="btn btn-warning w-100" id="editarUsuario"
                                onclick="updateForm()" hidden>Editar</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

</body>

<!--MODAL-->
<div id="userDialog" class="easyui-dialog" title="Crear Empleado" style="width:600px;height:auto;padding:10px"
    closed="true" buttons="#dlg-buttons">
    <form id="userForm" method="post">
        <div class="row">
            <div class="col-md-6 py-2">
                <label for="new_hire_date">Fecha de ingreso:</label>
                <input id="new_hire_date" name="new_hire_date" type="date" class="form-control" required>
            </div>
            <div class="col-md-6 py-2">
                <label for="employee_number_id">Número de empleado:</label>
                <input id="employee_number_id" name="employee_number_id" type="number" class="form-control" required>
            </div>
            <div class="col-md-6 py-2">
                <label for="username">Nombre:</label>
                <input id="username" name="username" type="text" class="form-control" required>
            </div>
            <div class="col-md-6 py-2">
                <label for="genero">Género:</label>
                <select id="genero" name="genero" class="form-control">
                    <option value="0">Femenino</option>
                    <option value="1">Masculino</option>
                </select>
            </div>
            <div class="col-md-6 py-2">
                <label for="puesto">Puesto:</label>
                <select id="puesto" name="puesto" class="form-control"></select>
            </div>
            <div class="col-md-6 py-2">
                <label for="new_nss">NSS:</label>
                <input id="new_nss" name="new_nss" pattern="^\d{11}$" maxlength="11" minlength="11" type="text"
                    class="form-control" title="Debe contener exactamente 11 dígitos numéricos">
                <small id="nssError" class="text-danger"></small>
            </div>
            <div class="col-md-6 py-2">
                <label for="new_curp">CURP:</label>
                <input id="new_curp" pattern="^[a-zA-Z0-9]{18}$" maxlength="18"
                    title="CURP inválida. Debe tener 18 caracteres alfanuméricos según el formato oficial"
                    name="new_curp" type="text" class="form-control">
                <small id="curpError" class="text-danger"></small>
            </div>
            <div class="col-md-6 py-2">
                <label for="new_rfc">RFC:</label>
                <input id="new_rfc" name="new_rfc" pattern="^[a-zA-Z0-9]{13}$" maxlength="13"
                    title="RFC inválido. Debe tener 12 o 13 caracteres con el formato correcto" type="text"
                    class="form-control">
                <small id="rfcError" class="text-danger"></small>
            </div>
            <div class="col-md-6 py-2">
                <label for="new_birth_date">Fecha de nacimiento:</label>
                <input id="new_birth_date" name="new_birth_date" type="date" class="form-control">
            </div>
            <div class="col-md-6 py-2">
                <label for="new_phone">Teléfono:</label>
                <input id="new_phone" name="new_phone" pattern="^\d{10}$" maxlength="10" minlength="10"
                    title="Debe contener exactamente 10 dígitos numéricos" type="tel" class="form-control">
                <small id="phoneError" class="text-danger"></small>
            </div>
            <div class="col-md-12 py-2">
                <label for="new_address">Dirección:</label>
                <textarea id="new_address" name="new_address" class="form-control" rows="2"></textarea>
            </div>
        </div>
    </form>
</div>
<!-- Botones del modal -->
<div id="dlg-buttons">
    <button type="button" class="btn btn-success" id="crearUsuario" onclick="submitForm()">Crear</button>
    <button type="button" class="btn btn-danger" onclick="$('#userDialog').dialog('close')">Cancelar</button>
</div>

<!-- MODAL REPORTES -->
<div id="modal_report" class="easyui-dialog" title="Reporte de Empleados" style="width:500px;height:auto;padding:10px"
    closed="true" modal="true" buttons="#dlg-buttons">

    <form id="formReporte" method="post" style="display: flex; flex-direction: column; gap: 10px;">
        <!-- Empresa -->
        <div class="row" style="width: 100%;">
            <div class="col-md-6">
                <label for="r_estado">Estado:</label>
                <select id="r_estado" name="r_estado" class="easyui-combobox form-control" style="width:100%"
                    editable="false" panelHeight="auto">
                    <option value="1">ACTIVO</option>
                    <option value="0">BAJA</option>
                </select>
            </div>
            <div class="col-md-6">
                <label for="empresa">Empresa:</label>
                <select id="r_empresa" name="empresa" class="easyui-combobox form-control" style="width:100%"
                    editable="false" panelHeight="auto">
                    <option value="">-- Selecciona una empresa --</option>
                    <option value="0">GPI</option>
                    <option value="1">RECAM</option>
                    <option value="2">RECAM - GPI</option>
                </select>
            </div>
        </div>

        <!-- Rango de fechas de ingreso -->
        <div style="display: flex; flex-direction: column; gap: 5px;">
            <label style="font-weight: bold;">Fecha de ingreso:</label>
            <div style="display: flex; gap: 10px; align-items: center;">
                <label for="fecha_inicio">Inicio:</label>
                <input id="r_fecha_inicio" name="fecha_inicio" class="easyui-datebox" style="width:45%">
                <label for="fecha_fin">Fin:</label>
                <input id="r_fecha_fin" name="fecha_fin" class="easyui-datebox" style="width:45%">
            </div>
        </div>

        <!-- Género -->
        <div>
            <label for="genero">Género:</label>
            <select id="r_genero" name="genero" class="easyui-combobox" style="width:100%" editable="false"
                panelHeight="auto">
                <option value="">-- Selecciona género --</option>
                <option value="M">Masculino</option>
                <option value="F">Femenino</option>
                <option value="O">Otro</option>
            </select>
        </div>

        <!-- Puesto -->
        <div>
            <label for="puesto">Puesto:</label>
            <select id="r_puesto" name="puesto" class="easyui-combobox" editable="false" style="width:100%"></select>
        </div>
    </form>
</div>

<div id="dlg-buttons">
    <a href="javascript:void(0)" class="easyui-linkbutton btn-generar" onclick="generarReporte()">Generar</a>
    <a href="javascript:void(0)" class="easyui-linkbutton btn-cerrar"
        onclick="$('#modal_report').dialog('close')">Cerrar</a>
</div>