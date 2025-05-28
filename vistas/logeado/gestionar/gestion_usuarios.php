<script>
    //Funcionalidad al cargar la pagina
    $(document).ready(function () {
        // Función para alternar la visibilidad de la contraseña - Contraseña
        document.getElementById('togglePassword').addEventListener('click', function () {
            var passwordField = document.getElementById('password');
            var passwordIcon = this.querySelector('i');

            if (passwordField.type === "password") {
                passwordField.type = "text";
                passwordIcon.classList.remove('bi-eye-slash');
                passwordIcon.classList.add('bi-eye');
            } else {
                passwordField.type = "password";
                passwordIcon.classList.remove('bi-eye');
                passwordIcon.classList.add('bi-eye-slash');
            }
        });

        // Función para alternar la visibilidad de la contraseña - Verificar Contraseña
        document.getElementById('toggleConfirmPassword').addEventListener('click', function () {
            var confirmPasswordField = document.getElementById('confirmPassword');
            var confirmPasswordIcon = this.querySelector('i');

            if (confirmPasswordField.type === "password") {
                confirmPasswordField.type = "text";
                confirmPasswordIcon.classList.remove('bi-eye-slash');
                confirmPasswordIcon.classList.add('bi-eye');
            } else {
                confirmPasswordField.type = "password";
                confirmPasswordIcon.classList.remove('bi-eye');
                confirmPasswordIcon.classList.add('bi-eye-slash');
            }
        });

        $('#userTable').datagrid({
            onSelect: function (index, row) {
                // Habilitar botones cuando se selecciona una fila
                if (row.estado != 'BAJA') {
                    $('#editButton').linkbutton('enable');
                    $('#bajaButton').linkbutton('enable');
                } else {
                    $('#editButton').linkbutton('disable');
                    $('#bajaButton').linkbutton('enable');
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
            }
        });

        $.ajax({
            url: 'index.php?c=usuarios&m=consultarUsuarios',
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
    });

    function newUser() {
        $('#crearUsuario').show();
        $('#editarUsuario').hide();
        $('#userDialog').dialog('open').dialog('setTitle', 'Nuevo Usuario');
        $('#userForm').form('clear');
    }

    function editUser() {
        $('#userForm').form('clear');
        var row = $('#userTable').datagrid('getSelected');
        if (row.user_type == 'Administrador') {
            tipo = 1;
        } else {
            tipo = 2;
        }
        $('#userType').val(tipo);
        $('#username').val(row.name);
        $('#crearUsuario').hide();
        $('#editarUsuario').show();
        $('#userDialog').dialog('open').dialog('setTitle', 'Editar Usuario');
    }

    function submitForm() {
        var password = $('#password').val();
        var confirmPassword = $('#confirmPassword').val();

        if (password !== confirmPassword) {
            $.messager.alert('Error', 'Las contraseñas no coinciden', 'error');
            return;
        }

        var passwordRegex = /^(?=.*[0-9])(?=.*[!@#$%^&*(),.?":{}|<>]).{8,}$/;

        if (!passwordRegex.test(password)) {
            $.messager.alert('Alerta', 'La contraseña debe tener al menos 8 caracteres, incluir un número y un carácter especial.', 'info');
            return;
        }

        if ($('#userForm')[0].checkValidity()) {
            var userData = $('#userForm').serialize();

            $.messager.progress({
                title: 'Procesando...',
                msg: 'Por favor espere mientras se crea el usuario.'
            });

            $.ajax({
                url: 'index.php?c=usuarios&m=crearUsuario',
                type: 'POST',
                dataType: 'json',
                data: userData,
                cache: false,
                success: function (respuesta) {
                    $.messager.progress('close');

                    if (respuesta.error === true) {
                        $.messager.alert('Error', respuesta.msg, 'error');
                    } else {
                        if (respuesta.creado) {
                            if (respuesta.datos['userType'] == 1) {
                                tipo = 'ADMINISTRADOR';
                            } else  if (respuesta.datos['userType'] == 2) {
                                tipo = 'RECURSOS HUMANOS';
                            } else  {
                                tipo = 'CONTABILIDAD';
                            }
                            var newRow = {
                                id: respuesta.id,
                                name: respuesta.datos['username'],
                                estado: 'ACTIVO',
                                user_type: tipo,
                                creation_date: new Date().toLocaleString('es-ES', {
                                    year: 'numeric',
                                    month: '2-digit',
                                    day: '2-digit',
                                    hour: '2-digit',
                                    minute: '2-digit',
                                    second: '2-digit'
                                }).replace(",", ""),
                                created_by: respuesta.created_by,
                                update_date: '',
                                updated_by: ''
                            }

                            $('#userTable').datagrid('insertRow', {
                                index: 0,
                                row: newRow
                            });
                            $('#userDialog').dialog('close');
                            $.messager.alert('Se realizo la petición', 'Se creo el nuevo usuario!', 'info');
                        } else {
                            $.messager.alert('Error', respuesta.msg, 'error');
                        }
                    }
                }
            });

        } else {
            $.messager.alert('Alerta', 'Por favor complete todos los campos', 'info');
        }
    }

    function updateForm() {
        var row = $('#userTable').datagrid('getSelected');

        var index = $('#userTable').datagrid('getRowIndex', row);
        var isValid = true;

        var password = $('#password').val();
        var confirmPassword = $('#confirmPassword').val();


        if (row.user_type == 'Administrador') {
            type_row = 1;
        } else {
            type_row = 2;
        }
        if (row.name == $('#username').val() && type_row == $('#userType').val() && password == '' && confirmPassword == '') {
            $.messager.alert('Info', 'No se detecta ningun cambio.', 'info');
            return;
        }
        if (!row) {
            $.messager.alert('Error', 'Por favor selecciona un usuario para actualizar.', 'error');
            return;
        }

        if (password !== '' || confirmPassword !== '') {
            if (password !== confirmPassword) {
                isValid = false;
                $.messager.alert('Error', 'Las contraseñas no coinciden', 'error');
                return;
            }
            var passwordRegex = /^(?=.*[0-9])(?=.*[!@#$%^&*(),.?":{}|<>]).{8,}$/;
            if (!passwordRegex.test(password)) {
                $.messager.alert('Alerta', 'La contraseña debe tener al menos 8 caracteres, incluir un número y un carácter especial.', 'info');
                return;
            }
        }

        if (!$('#userForm')[0].checkValidity() && (password !== '' || confirmPassword !== '')) {
            $.messager.alert('Alerta', 'Por favor complete todos los campos requeridos.', 'info');
            return;
        }

        var userData = $('#userForm').serialize();
        var userId = row.id;

        userData += '&id=' + userId;

        $.ajax({
            url: 'index.php?c=usuarios&m=editarUsuario',
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
                        var tipo = (respuesta.datos['userType'] == 1) ? 'Administrador' : 'Usuario';
                        var updatedData = {
                            name: respuesta.datos['username'],
                            user_type: tipo,
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
            estado = 2;
        } else {
            cambio = 'ACTIVO';
            estado = 1;
        }
        $.messager.confirm('Confirmación', 'Se cambiara el estado a ' + cambio + ' del usuario ' + row.name + ' ¿Está seguro?', function (r) {
            if (r) {
                $.ajax({
                    url: 'index.php?c=usuarios&m=cambiarEstado',
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        id: row.id,
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
                                }else{
                                    $('#editButton').linkbutton('disable');
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

        // Definir el estilo dinámico en base a la columna 'user_type'
        var userTypeCol = dg.datagrid('getColumnOption', 'user_type');
        userTypeCol.styler = function (value, row, index) {
            if (value === 'ADMINISTRADOR') {
                return 'background-color: #482c21; color: white; font-weight: bold;';
            } else if (value === 'RECURSOS HUMANOS') {
                return 'background-color: #a73e2b; color: white; font-weight: bold;';
            }else if (value === 'CONTABILIDAD') {
                return 'background-color: #d07e0e; color: white; font-weight: bold;';
            }  else {
                return '';
            }
        };

        // Refrescar las filas para aplicar los estilos
        dg.datagrid('reload');
    });
</script>

<body>
    <div class="container py-5">
        <div class="row">
            <div class="col-md-12">
                <!--TABLA DE LOS USUARIOS-->
                <table id="userTable" class="easyui-datagrid" title="Gestion de usuarios"
                    style="width:100%;height:800px" data-options="singleSelect:true,collapsible:true"
                    toolbar="#toolbar">
                    <thead>
                        <tr>
                            <th data-options="field:'id',width:50,hidden:true">ID</th>
                            <th data-options="field:'name',width:200">Nombre</th>
                            <th data-options="field:'estado',width:200" align="center">Estado</th>
                            <th data-options="field:'user_type',width:200" align="center">Tipo Usuario</th>
                            <th data-options="field:'creation_date',width:175" align="center">Fecha de alta</th>
                            <th data-options="field:'created_by',width:150" align="center">Alta por</th>
                            <th data-options="field:'update_date',width:165" align="center">Fecha actualizo</th>
                            <th data-options="field:'updated_by',width:200" align="center">Actualizado por</th>
                        </tr>
                    </thead>
                </table>
                <!-- Botones de la tabla -->
                <div id="toolbar">
                    <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-add" plain="true"
                        onclick="newUser()">Nuevo Usuario</a>
                    <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-edit" plain="true"
                        onclick="editUser()" id="editButton" disabled>Editar Usuario</a>
                    <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-remove" plain="true"
                        onclick="cambiarEstado()" id="bajaButton" disabled>Cambiar Estado</a>
                </div>

                <!--MODAL-->
                <div id="userDialog" class="easyui-dialog" title="Crear Usuario"
                    style="width:400px;height:514px;padding:10px" closed="true" buttons="#dlg-buttons">
                    <form id="userForm" method="post">
                        <div class="form-group py-3">
                            <label for="userType">Tipo de Usuario:</label>
                            <select id="userType" name="userType" class="form-control">
                                <option value="1">ADMINISTRADOR</option>
                                <option value="2">RECURSOS HUMANOS</option>
                                <option value="3">CONTABILIDAD</option>
                            </select>
                        </div>
                        <div class="form-group py-3">
                            <label for="username" id="label_nombre">Nombre:</label>
                            <input id="username" name="username" type="text" class="form-control"
                                onchange="consultarNombre()" required="true">
                        </div>
                        <div class="form-group py-3">
                            <label for="password">Contraseña:</label>
                            <div class="input-group">
                                <input id="password" name="password" type="password" class="form-control"
                                    required="true">
                                <div class="input-group-append">
                                    <span class="input-group-text" id="togglePassword" style="cursor: pointer;">
                                        <i class="bi bi-eye-slash"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group py-3">
                            <label for="confirmPassword">Verificar Contraseña:</label>
                            <div class="input-group">
                                <input id="confirmPassword" name="confirmPassword" type="password" class="form-control"
                                    required="true">
                                <div class="input-group-append">
                                    <span class="input-group-text" id="toggleConfirmPassword" style="cursor: pointer;">
                                        <i class="bi bi-eye-slash"></i>
                                    </span>
                                </div>
                            </div>
                        </div>

                    </form>
                </div>

                <!-- Botones del modal -->
                <div id="dlg-buttons">
                    <button type="button" class="btn btn-success" id="crearUsuario"
                        onclick="submitForm()">Crear</button>
                    <button type="button" class="btn btn-warning" id="editarUsuario"
                        onclick="updateForm()">Editar</button>
                    <button type="button" class="btn btn-danger"
                        onclick="$('#userDialog').dialog('close')">Cancelar</button>
                </div>
            </div>
        </div>
    </div>
</body>