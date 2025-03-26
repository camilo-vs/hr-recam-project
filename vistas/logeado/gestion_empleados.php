<script>
    //Funcionalidad al cargar la pagina
    $(document).ready(function () {

        $('#userTable').datagrid({
            onSelect: function (index, row) {
                // Deshabilitar y habilitar botón según el estado
                if (row.estado !== 'BAJA') {
                    $('#editButton').linkbutton('enable');
                    $('#bajaButton').linkbutton('enable');
                } else {
                    $('#editButton').linkbutton('disable');
                    $('#bajaButton').linkbutton('enable');
                }

                $('input[name="labelEmployeeNumberId"]').val(row.employee_number_id);
                $('input[name="labelName"]').val(row.name);
                $('input[name="labelHireDate"]').val(row.hire_date);
                $('select[name="labelGenre"]').val(row.genre_wname);
                $('select[name="labelRole"]').val(row.role_wname);
                $('input[name="labelDepartment"]').val(row.department);
                $('input[name="labelSupervisor"]').val(row.supervisor);

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
                    // Vaciar el select y agregar opción por defecto
                    var select = $('#editRole');
                    select.empty();
                    // Iterar el arreglo y agregar cada opción
                    $.each(respuesta.departamentos, function(index, departamento) {
                        select.append('<option value="' + departamento.role_id + '">' + departamento.name + '</option>');
                    });

                    // Vaciar el select y agregar opción por defecto
                    var select = $('#puesto');
                    select.empty();
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

    function newUser() {
        $('#crearUsuario').show();
        $('#userDialog').dialog('open').dialog('setTitle', 'Nuevo Usuario');
        $('#userForm').form('clear');
        $('#employee_number_id').form('clear');
    }

    function editUser() {
                $('input[name="labelName"]').prop("disabled", false);
                $('select[name="labelGenre"]').prop("disabled", false);
                $('select[name="labelRole"]').prop("disabled", false);
                $('#editarUsuario').removeAttr('hidden');
    }

    function submitForm() {
        if ($('#userForm')[0].checkValidity()) {
            var userData = $('#userForm').serialize();
            console.log(userData);
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
                    if (respuesta.error === true) {
                        $.messager.alert('Error', respuesta.msg, 'error');
                    } else {
                        // Encadena la llamada a consultarDatosExtraTABLA
                        consultarDatosExtraTABLA().done(function(respExtra) {
                            if (respExtra.error === true) {
                                $.messager.alert('Error', respExtra.msg, 'error');
                                return;
                            }

                            var newRow = {
                                employee_number_id: respuesta.datos['employee_number_id'],
                                name: respuesta.datos['username'],
                                genre_wname: $('#genero option:selected').val(),
                                estado: 'ACTIVO',
                                genero: $('#genero option:selected').text(),
                                role_name: $('#puesto option:selected').text(),
                                role_wname: $('#puesto option:selected').val(),
                                // Usando los valores obtenidos en la función consultarDatosExtraTABLA:
                                supervisor: respExtra.supervisorName,
                                department: respExtra.departmentName,
                                hire_date: new Date().toLocaleString('es-ES', {
                                    year: 'numeric',
                                    month: '2-digit',
                                    day: '2-digit',
                                    hour: '2-digit',
                                    minute: '2-digit',
                                    second: '2-digit'
                                }).replace(",", "")
                            };

                            $('#userTable').datagrid('insertRow', {
                                index: 0,
                                row: newRow
                            });
                            $('#userDialog').dialog('close');
                            $.messager.alert('Se realizo la petición', 'Se creo el nuevo usuario!', 'info');
                        }).fail(function(jqXHR, textStatus, errorThrown) {
                            console.error("Error en consultarDatosExtraTABLA:", errorThrown);
                            $.messager.alert('Error', 'No se pudieron obtener los datos extra.', 'error');
                        });
                    }
                }
            });
        } else {
            $.messager.alert('Alerta', 'Por favor complete todos los campos', 'info');
        }
    }



    function updateForm() {
        var row = $('#userTable').datagrid('getSelected');
        if (!row) {
            $.messager.alert('Error', 'Por favor selecciona un usuario para actualizar.', 'error');
            return;
        }

        var index = $('#userTable').datagrid('getRowIndex', row);

        // Se comparan todos los campos relevantes
        if (
            row.name === $('#editName').val() &&
            row.genre_wname === $('#editGenre').val() &&
            row.role_wname === $('#editRole').val()
        ) {
            $.messager.alert('Info', 'No se detecta ningún cambio.', 'info');
            return;
        }

        var userData = $('#editForm').serialize();
        var userId = row.employee_number_id;
        userData += '&id=' + userId;

        $.ajax({
            url: 'index.php?c=empleados&m=editarEmpleado',
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
                            name: $('#editName').val(),
                            genero: $('#editGenre option:selected').text(),
                            role_name:  $('#editRole option:selected').text(),
                            role_wname: $('#editRole').val(),
                            department: $('#editDepartment').val(),
                            supervisor: $('#editSupervisor').val(),
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

                        $('#userDialog').dialog('close');
                        $.messager.alert('Se realizó la petición', '¡El usuario fue actualizado correctamente!', 'info');
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



    function cambiarEstado() {
        var row = $('#userTable').datagrid('getSelected');
        var index = $('#userTable').datagrid('getRowIndex', row);
        if (row.estado == 'ACTIVO') {
            cambio = 'BAJA';
            estado = 0;
        } else {
            cambio = 'ACTIVO';
            estado = 1;
        }
        $.messager.confirm('Confirmación', 'Se cambiara el estado a ' + cambio + ' del usuario ' + row.name + ' ¿Está seguro?', function (r) {
            if (r) {
                $.ajax({
                    url: 'index.php?c=empleados&m=cambiarEstado',
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        employee_number_id: row.employee_number_id,
                        estado: estado
                    },
                    cache: false,
                    success: function (respuesta) {
                        if (respuesta.error === true) {
                            $.messager.alert('Error', respuesta.msg, 'error');
                        } else {
                            if (respuesta.cambio) {
                                var updatedData = {
                                    estado: cambio,
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
                                if(estado == 1){
                                    $('#editButton').linkbutton('enable');
                                    $('input[name="labelName"]').prop("disabled", true);
                                    $('select[name="labelGenre"]').prop("disabled", true);
                                    $('select[name="labelRole"]').prop("disabled", true);
                                    $('input[name="vacationDays"]').prop("disabled", true);
                                    $('#editarUsuario').attr('hidden', true);
                                }else{
                                    $('#editButton').linkbutton('disable');
                                    $('input[name="labelName"]').prop("disabled", true);
                                    $('select[name="labelGenre"]').prop("disabled", true);
                                    $('select[name="labelRole"]').prop("disabled", true);
                                    $('input[name="vacationDays"]').prop("disabled", true);
                                    $('#editarUsuario').attr('hidden', true);
                                }
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

    function consultarDatosExtraTABLA(){
        // Obtiene el índice seleccionado
        var index = $('#puesto').prop('selectedIndex');

        // Retorna el objeto AJAX (Deferred)
        return $.ajax({
            url: 'index.php?c=empleados&m=consultarDatosExtra',
            type: 'POST',
            dataType: 'json',
            data: { index: index },
            cache: false
        });
    }


    function consultarDatosExtra(){
        // Obtiene el índice seleccionado
        var index = $('#editRole').prop('selectedIndex');

        $.ajax({
            url: 'index.php?c=empleados&m=consultarDatosExtra',
            type: 'POST',
            dataType: 'json',
            data: { index: index },
            cache: false,
            success: function (respuesta) {
                if (respuesta.error === true) {
                    alert('Error: ' + respuesta.msg);
                } else {
                    $('input[name="labelSupervisor"]').val(respuesta.supervisorName);
                    $('input[name="labelDepartment"]').val(respuesta.departmentName);
                }
            },
            error: function(xhr, status, error) {
                console.error("Error AJAX:", error);
            }
        });
    }

</script>

<body>
    <div class="container-fluid d-flex p-5" style="height: 800px;">
        <!-- Primera sección (70%) con margen -->
                    <!--TABLA DE LOS USUARIOS-->
                    <table id="userTable" class="easyui-datagrid" title="Gestion de usuarios"
                        style="width:65.7%;height:100%" data-options="singleSelect:true,collapsible:true"
                        toolbar="#toolbar">
                        <thead>
                            <tr>
                                <th data-options="field:'employee_number_id',width:50">#</th>
                                <th data-options="field:'name',width:200">Nombre</th>
                                <th data-options="field:'estado',width:100">Estado</th>
                                <th data-options="field:'genero',width:150">Genero</th>
                                <th data-options="field:'department',width:175">Departamento</th>
                                <th data-options="field:'role_name',width:200">Cargo</th>
                                <th data-options="field:'supervisor',width:165">Supervisor</th>
                                <th data-options="field:'hire_date',width:155">Fecha de alta</th>
                                <th data-options="field:'role_wname',width:155" hidden></th>
                                <th data-options="field:'genre_wname',width:155" hidden></th>
                            </tr>
                        </thead>
                    </table>
                    <!-- Botones de la tabla -->
                    <div id="toolbar">
                        <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-add" plain="true"
                            onclick="newUser()">Nuevo Usuario</a>
                        <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-remove" plain="true"
                            onclick="cambiarEstado()" id="bajaButton" disabled>Cambiar Estado</a>
                    </div>

                    <!--MODAL-->
                    <div id="userDialog" class="easyui-dialog" title="Crear Usuario"
                        style="width:400px;height:514px;padding:10px" closed="true" buttons="#dlg-buttons">
                        <form id="userForm" method="post">
                            <div class="form-group py-3">
                                <label for="employee_number_id" id="label_number">Numero de empleado:</label>
                                <input id="employee_number_id" name="employee_number_id" type="number" class="form-control" required="true">
                            </div>
                            <div class="form-group py-3">
                                <label for="puesto">Puesto:</label>
                                <select id="puesto" name="puesto" class="form-control">
                                </select>
                            </div>
                            <div class="form-group py-3">
                                <label for="username" id="label_nombre">Nombre:</label>
                                <input id="username" name="username" type="text" class="form-control" required="true">
                            </div>
                            <div class="form-group py-3">
                                <label for="genero">Genero:</label>
                                <select id="genero" name="genero" class="form-control">
                                    <option value="0">Femenino</option>
                                    <option value="1">Masculino</option>
                                </select>
                            </div>
                        </form>
                    </div>
                    <!-- Botones del modal -->
                    <div id="dlg-buttons">
                        <button type="button" class="btn btn-success" id="crearUsuario"
                            onclick="submitForm()">Crear</button>
                        <button type="button" class="btn btn-danger"
                            onclick="$('#userDialog').dialog('close')">Cancelar</button>
                    </div>

        <!-- Segunda sección (30%) -->
        <form id="editForm" action="post">
            <div class="container" style="width: 600px;">
                <div class="row">
                    <h2 class="col-sm-2">Detalles</h2>
                    <div class="col-sm-7"></div>
                    <a href="javascript:void(0)" class="easyui-linkbutton col-sm-3" iconCls="icon-edit" plain="true"
                            onclick="editUser()" id="editButton" disabled>Editar Usuario</a>
                </div>
                <div class="input-group mb-2 w-25">
                    <span class="input-group-text" id="basic-addon1">#</span>
                    <input type="text" class="form-control" name="labelEmployeeNumberId" id="editID" aria-describedby="basic-addon1" readonly disabled>
                </div>
                <input type="text" class="form-control w-75 mb-2" id="editName" name="labelName" disabled>
                <div class="row mb-2">
                    <div class="col-sm-4">
                        <p class="mb-0">Fecha de ingreso:</p>
                        <input type="text" class="form-control" name="labelHireDate" readonly disabled>
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
                </div>
                <div class="row mb-5">
                    <div class="mb-2">
                        <button type="button" class="btn btn-warning w-50 " id="editarUsuario"
                        onclick="updateForm()" hidden>Editar</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</body>
