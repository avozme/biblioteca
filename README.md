# Biblioteca

Este repositorio contiene la solución a un **ejercicio de programación de una aplicación web backend con PHP MVC clásico**, sin framework, que utilizo en mis clases para ilustrar este modo de desarrollo habitual en la primera década del siglo XXI.

Es un caso práctico importante porque es la primera aplicación web “de verdad”, con una base de datos detrás y una estructura MVC, que muestro al alumnado.

La aplicación web gestiona, de forma muy simplificada, una biblioteca. Trabaja con una base de datos compuesta de tres tablas (ya dije que estaría muy simplificada): *libros*, *autores* y *escriben*. Esta última es una tabla pivote, es decir, es la tabla que implementa la relación N:N entre *libros* y *autores*.

La aplicación permite ver la lista de todos los libros disponibles, así como dar de alta libros nuevos y modificar o borrar los libros existentes. Lo mismo sucede con los autores. Por último, permite asociar autores a los libros (es decir, insertar datos en la tabla pivote *escriben*).

El código tiene una arquitectura modelo-vista-controlador MVC mejorable, pero que sirve como primera aproximación válida a este patrón de diseño de software. Hemos usado un solo controlador (que también hace labores de enrutamiento), pero sería fácil dividirlo en varios.
