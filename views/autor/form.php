<?php
// VISTA PARA INSERCIÓN/EDICIÓN DE AUTORES

// Esta vista se utilizará tanto para insertar nuevas personas como para modificar las que ya existen en la base de datos.
// Es habitual usar la misma vista para las dos cosas, puesto que son prácticamente iguales. 

// Determinamos si estamos modificando o insertando
if (isset($autor)) {   
    echo "<h1>Modificación de autor</h1>";
    extract($data);   // Extrae el contenido de $data y lo convierte en variables individuales ($autor)
} else {
    echo "<h1>Inserción de autor</h1>";
}

// Datos del autor (si existe)
$idPersona = $autor->idPersona ?? ""; 
$nombre    = $autor->nombre ?? "";
$apellido  = $autor->apellido ?? "";
$pais      = $autor->pais ?? "";

// Formulario
echo "<table><form action='index.php' method='get'>
        <input type='hidden' name='idPersona' value='".$idPersona."'>
        <tr width='20%'><td>Nombre:</td><td> <input type='text' name='nombre' value='".$nombre."'></td></tr>
        <tr><td>Apellido:</td><td> <input type='text' name='apellido' value='".$apellido."'></td></tr>
        <tr><td>País:</td><td> <input type='text' name='pais' value='".$pais."'></td></tr></table>";

if (isset($autor)) {
    echo "  <input type='hidden' name='action' value='modificarAutor'>";
} else {
    echo "  <input type='hidden' name='action' value='insertarAutor'>";
}

echo "	<input type='submit'></form>";
echo "<p><a href='index.php'>Volver</a></p>";
