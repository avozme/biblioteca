<?php
// VISTA PARA INSERCIÓN/EDICIÓN DE LIBROS

// Esta vista se utilizará tanto para insertar nuevos libros como para modificar los que ya existen en la base de datos.
// Es habitual usar la misma vista para las dos cosas, puesto que son prácticamente iguales. 
// La vista será lo suficientemente "lista" como para saber si está insertando o modificando: 
// si la variable $data tiene los datos de libro, significará que estamos modificando un libro. 
// En cambio, si la variable $data viene vacía, significa que estamos añadiendo un libro nuevo.

extract($data);   // Extrae el contenido de $data y lo convierte en variables individuales ($libro, $todosLosAutores y $autoresLibro)

// Vamos a usar la misma vista para insertar y modificar. Para saber si hacemos una cosa u otra,
// usaremos la variable $libro: si existe, es porque estamos modificando un libro. Si no, estamos insertando uno nuevo.
if (isset($libro)) {   
    echo "<h1>Modificación de libros</h1>";
} else {
    echo "<h1>Inserción de libros</h1>";
}

// Sacamos los datos del libro (si existe) a variables individuales para mostrarlo en los inputs del formulario.
// (Si no hay libro, dejamos los campos en blanco y el formulario servirá para inserción).
$idLibro = $libro->idLibro ?? ""; 
$titulo = $libro->titulo ?? "";
$genero = $libro->genero ?? "";
$pais = $libro->pais ?? "";
$ano = $libro->ano ?? "";
$numPags = $libro->numPaginas ?? "";

// Creamos el formulario con los campos del libro
echo "<table><form action = 'index.php' method = 'get'>
        <input type='hidden' name='idLibro' value='".$idLibro."'>
        <tr><td width='20%'>Título:</td><td><input type='text' name='titulo' value='".$titulo."'></td></tr>
        <tr><td>Género:</td><td><input type='text' name='genero' value='".$genero."'></td></tr>
        <tr><td>País:</td><td><input type='text' name='pais' value='".$pais."'></td></tr>
        <tr><td>Año:</td><td><input type='text' name='ano' value='".$ano."'></td></tr>
        <tr><td>Número de páginas:</td><td><input type='text' name='numPaginas' value='".$numPags."'></td></tr>";

echo "<tr><td>Autores:</td><td><select name='autor[]' multiple size='5'>";
foreach ($todosLosAutores as $fila) {
    if (in_array($fila->idPersona, $autoresLibro))
        echo "<option value='$fila->idPersona' selected>$fila->nombre $fila->apellido</option>";
    else
        echo "<option value='$fila->idPersona'>$fila->nombre $fila->apellido</option>";
}
echo "</select></td></tr></table>";

// Finalizamos el formulario
if (isset($libro)) {
    echo "  <input type='hidden' name='action' value='modificarLibro'>";
} else {
    echo "  <input type='hidden' name='action' value='insertarLibro'>";
}
echo "	<input type='submit'></form>";
echo "<p><a href='index.php'>Volver</a></p>";
