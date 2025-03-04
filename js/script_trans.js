$('#dg').datagrid({
    url: 'index.php?c=usuarios&m=consultarUsuarios',
    method: 'GET',
    loadFilter: function(data) {
        if (data.error) {
            console.error("Error al cargar datos:", data.msg);
            return { total: 0, rows: [] }; // Si hay error, manda un array vacío
        }
        return { total: data.usuarios.length, rows: data.usuarios }; // Formato esperado por EasyUI
    }
});