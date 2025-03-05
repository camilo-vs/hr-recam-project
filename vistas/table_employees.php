<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Build CRUD Application with edit form in expanded row details - jQuery EasyUI Demo</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome para iconos -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

    <link rel="stylesheet" type="text/css" href="https://www.jeasyui.com/easyui/themes/default/easyui.css">
    <link rel="stylesheet" type="text/css" href="https://www.jeasyui.com/easyui/themes/icon.css">
    <link rel="stylesheet" type="text/css" href="https://www.jeasyui.com/easyui/demo/demo.css">
    
    <script type="text/javascript" src="https://www.jeasyui.com/easyui/jquery.min.js"></script>
    <script type="text/javascript" src="https://www.jeasyui.com/easyui/jquery.easyui.min.js"></script>
    <script type="text/javascript" src="https://www.jeasyui.com/easyui/datagrid-detailview.js"></script>
</head>
<body>
    <h2>Usuarios</h2>
    
    
    <table id="dg" class="easyui-datagrid" 
            style="width:100%;height:300px"
            url="index.php?c=usuarios&m=consultarUsuarios"
            toolbar="#toolbar" pagination="true"
            rownumbers="true"  fitColumns="true" singleSelect="true">
        <thead>
            <tr>
                <th field="id" width="10">ID</th>
                <th field="name" width="50">Nombre</th>
                <th field="user_type" width="15">Tipo</th>
                <th field="email" width="15">Activo</th>
            </tr>
        </thead>
    </table>

    <div id="toolbar" class="d-flex justify-content-between align-items-center">
        <div>
            <input id="searchbox" class="form-control d-inline-block" style="width:200px">
            <button class="btn btn-custom btn-sm" onclick="doSearch()">
                <i class="fa fa-search"></i>
            </button>
        </div>
        <div class="ms-auto">
            <a href="javascript:void(0)" class="btn btn-custom btn-sm" onclick="newItem()">
                <i class="fa fa-user-plus"></i>
            </a>
            <a href="javascript:void(0)" class="btn btn-custom btn-sm" onclick="destroyItem()">
                <i class="fa fa-trash"></i>
            </a>
        </div>
    </div>

    <script type="text/javascript">
        $(function(){
            $('#dg').datagrid({
                view: detailview,
                detailFormatter:function(index,row){
                    return '<div class="ddv"></div>';
                },
                onExpandRow: function(index,row){
                    var ddv = $(this).datagrid('getRowDetail',index).find('div.ddv');
                    ddv.panel({
                        border:false,
                        cache:true,
                        href:'show_form.php?index='+index,
                        onLoad:function(){
                            $('#dg').datagrid('fixDetailRowHeight',index);
                            $('#dg').datagrid('selectRow',index);
                            $('#dg').datagrid('getRowDetail',index).find('form').form('load',row);
                        }
                    });
                    $('#dg').datagrid('fixDetailRowHeight',index);
                }
            });
        });
        function saveItem(index){
            var row = $('#dg').datagrid('getRows')[index];
            var url = row.isNewRecord ? 'save_user.php' : 'update_user.php?id='+row.id;
            $('#dg').datagrid('getRowDetail',index).find('form').form('submit',{
                url: url,
                iframe: false,
                onSubmit: function(){
                    return $(this).form('validate');
                },
                success: function(data){
                    data = eval('('+data+')');
                    data.isNewRecord = false;
                    $('#dg').datagrid('collapseRow',index);
                    $('#dg').datagrid('updateRow',{
                        index: index,
                        row: data
                    });
                }
            });
        }
        function cancelItem(index){
            var row = $('#dg').datagrid('getRows')[index];
            if (row.isNewRecord){
                $('#dg').datagrid('deleteRow',index);
            } else {
                $('#dg').datagrid('collapseRow',index);
            }
        }
        function destroyItem(){
            var row = $('#dg').datagrid('getSelected');
            if (row){
                $.messager.confirm('Confirm','Are you sure you want to remove this user?',function(r){
                    if (r){
                        var index = $('#dg').datagrid('getRowIndex',row);
                        $.post('destroy_user.php',{id:row.id},function(){
                            $('#dg').datagrid('deleteRow',index);
                        });
                    }
                });
            }
        }
        function newItem(){
            $('#dg').datagrid('appendRow',{isNewRecord:true});
            var index = $('#dg').datagrid('getRows').length - 1;
            $('#dg').datagrid('expandRow', index);
            $('#dg').datagrid('selectRow', index);
        }
    </script>
    <style type="text/css">
        form{
            margin:0;
            padding:0;
        }
        .dv-table td{
            border:0;
        }
        .dv-table input{
            border:1px solid #ccc;
        }
    </style>
    <script type="text/javascript" src="js\script_trans.js"></script>
</body>
</html>