<?php
// VISTA PARA LA LISTA DE AUTORES
// En esta vista se muestran todas las peronas (autores) junto con links para añadir, modificar y borrar personas. 
// Observa cómo se construyen esos links para que proporcionen al controlador (index.php) 
// toda la información que necesita para trabajar (como mínimo, la variable action).

// Recuperamos la lista de autores
$listaAutores = $data["listaAutores"] ?? [];

// Si hay mensajes de feedback, los mostramos
if (isset($data["info"])) {
  echo "<div style='color:blue'>".$data["info"]."</div>";
}

if (isset($data["error"])) {
  echo "<div style='color:red'>".$data["error"]."</div>";
}

// Buscador
echo "<form action='index.php'>
        <input type='hidden' name='action' value='buscarAutores'>
        <input type='text' name='textoBusqueda'>
        <input type='submit' value='Buscar'>
      </form><br>";

// Tabla con los autores
if (count($listaAutores) == 0) {
  echo "No hay datos";
} else {
  echo "<table border ='1'>";
  echo "<tr><th>Nombre</th><th>Apellido</th><th>País</th><th></th><th></th></tr>";
  foreach ($listaAutores as $fila) {
    echo "<tr>";
    echo "<td>" . $fila->nombre . "</td>";
    echo "<td>" . $fila->apellido . "</td>";
    echo "<td>" . $fila->pais . "</td>";
    echo "<td><a href='index.php?action=formularioModificarAutor&idPersona=" . $fila->idPersona . "'>Modificar</a></td>";
    echo "<td><a href='index.php?action=borrarAutor&idPersona=" . $fila->idPersona . "'>Borrar</a></td>";
    echo "</tr>";
  }
  echo "</table>";
}
echo "<p><a href='index.php?action=formularioInsertarAutores'>Nuevo</a></p>";
