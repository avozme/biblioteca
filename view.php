<?php

// PLANTILLA DE LAS VISTAS

/*
    Esta clase proporciona un método con una plantilla muy primitiva, 
    pero que es una buena forma de comprender qué es eso de las plantillas para las vistas.

    Fíjate que podemos usar este método desde cualquier controlador para mostrar cualquier vista
    y que todas ellas compartirán la misma estructura sin necesidad de escribir una y otra vez
    el código de la cabecera, el pie o el menú de navegación.

    La variable $data contiene los datos que deben ser mostrados en la vista. 
    Es una variable genérica, que puede contener un array con cualquier cosa dentro 
    (por ejemplo, una lista de libros, si la vista es "mostrar todos los libros"). 
    Cada vista en concreto se encargará de recorrer ese array y extraer de él los datos que necesite.

    Si alguna vista no necesita ningún dato, el array $data valdrá null, que es su valor por defecto.
*/

class View {
    public static function render($nombreVista, $data = null) {
        include("views/header.php");
        include("views/nav.php");
        include("views/$nombreVista.php");
        include("views/footer.php");
    }
}
