// Validar formulario de agregar producto
function validarFormularioAgregar() {
    let nombre = document.getElementById('nombre').value.trim();
    let cantidad = document.getElementById('cantidad').value;
    let precio = document.getElementById('precio').value;

    if (nombre === "" || cantidad <= 0 || precio <= 0) {
        alert("Por favor completa todos los campos correctamente.");
        return false;
    }
    return true;
}

// Confirmar eliminación de producto
function confirmarEliminacion() {
    return confirm("¿Estás seguro de que quieres eliminar este producto?");
}
