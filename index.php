<?php
/* 
   BIBLIOTECA VERSIÓN PHP MVC SIMPLE

   Este es el CONTROLADOR/ENRUTADOR.

   En aplicaciones grandes suele haber varios controladores. 
   Aquí, de momento, vamos a apañarnos solo con uno.
*/
    session_start();            // Para poder usar variables de sesión en login de usuarios
    include_once("models/libro.php");    // Modelo de libros
    include_once("models/persona.php");  // Modelo de personas (autores)
    include_once("models/user.php");     // Modelo de usuarios registrados (para el login/autenticación)
    include_once("view.php");   // Plantilla de las vistas

    // Miramos el valor de la variable "action", si existe. Si no, le asignamos una acción por defecto
    if (isset($_REQUEST["action"])) {
        $action = $_REQUEST["action"];
    } else {
        $action = "mostrarFormLogin";  // Acción por defecto
    }

    // Creamos un objeto de tipo Biblioteca y llamamos al método $action()
    $biblio = new Biblioteca();
    $biblio->$action();

    class Biblioteca {
        private $db;     // Conexión con la base de datos
        private $libro, $persona, $user; // Modelos

        public function __construct() {
            $this->libro = new Libro();
            $this->persona = new Persona();
            $this->user = new User();
        }

        // --------------------------------- FORMULARIO DE LOGIN -----------------------------------------
        public function mostrarFormLogin() {
            View::render("users/login");
        }

        // --------------------------------- PROCESAR FORMULARIO LOGIN -----------------------------------
        public function procesarFormLogin() {
            $username = $this->filtrarEntrada($_REQUEST["username"]);
            $pass = $this->filtrarEntrada($_REQUEST["pass"]);
            $resultado = $this->user->checkLogin($username, $pass);
            if ($resultado != null) {
                // El usuario y la contraseña existen. Guardamos su id y username como variables de sesión.
                $_SESSION['idUser'] = $resultado->id;
                $_SESSION['username'] = $resultado->username;
                $data["listaLibros"] = $this->libro->getAll();
                $data["info"] = "Bienvenido, $username";
                View::render("libro/all", $data);            
            } else {
                $data["mensaje"] = "Error, usuario o contraseña incorrectos";
                View::render("users/login", $data);
            }

        }

        // --------------------------------- CERRAR SESIÓN ---------------------------------------------
        public function logout() {
            session_destroy();
            header("Location: index.php");  // Recargamos el controlador, que nos dirigirá al formulario de login
        }

        // --------------------------------- MOSTRAR LISTA DE LIBROS ----------------------------------------
        public function mostrarListaLibros() {
            $data["listaLibros"] = $this->libro->getAll();
            View::render("libro/all", $data);
        }

        // --------------------------------- FORMULARIO ALTA DE LIBROS ----------------------------------------

        public function formularioInsertarLibros() {
            // Comprobamos si el usuario está logueado
            if (isset($_SESSION["idUser"])) {
                $data["todosLosAutores"] = $this->persona->getAll();
                $data["autoresLibro"] = array();  // Array vacío (el libro aún no tiene autores asignados)
                View::render("libro/form", $data);
            }
            else {
                // Usuario no logueado: error de permisos
                $data["mensaje"] = "No tienes permisos para hacer eso";
                View::render("users/login", $data);
            }
    }
        // --------------------------------- INSERTAR LIBROS ----------------------------------------

        public function insertarLibro() {
            // Comprobamos si el usuario está logueado
            if (isset($_SESSION["idUser"])) {
                // Primero, recuperamos todos los datos del formulario
                $titulo = $this->filtrarEntrada($_REQUEST["titulo"]);
                $genero = $this->filtrarEntrada($_REQUEST["genero"]);
                $pais = $this->filtrarEntrada($_REQUEST["pais"]);
                $ano = $this->filtrarEntrada($_REQUEST["ano"]);
                $numPaginas = $this->filtrarEntrada($_REQUEST["numPaginas"]);
                $autores = $this->filtrarEntrada($_REQUEST["autor"]);  // Así debería quedarse cuando los autores estén implementados 
                
                // Le pedimos al modelo que guarde el libro en la BD
                $result = $this->libro->insert($titulo, $genero, $pais, $ano, $numPaginas);
                if ($result == 1) {
                    // Si la inserción del libro ha funcionado, continuamos insertando los autores, pero
                    // necesitamos conocer el id del libro que acabamos de insertar
                    $idLibro = $this->libro->getMaxId();
                    // Ya podemos insertar todos los autores junto con el libro en "escriben"
                    $result = $this->libro->insertAutores($idLibro, $autores);
                    if ($result > 0) {
                        $data["info"] = "Libro insertado con éxito";
                    } else {
                        $data["error"] = "Error al insertar los autores del libro";
                    }
                } else {
                    // Si la inserción del libro ha fallado, mostramos mensaje de error
                    $data["error"] = "Error al insertar el libro";
                }
                $data["listaLibros"] = $this->libro->getAll();
                View::render("libro/all", $data);
            }
            else {
                // Usuario no logueado: Error de permisos
                $data["mensaje"] = "No tienes permisos para hacer eso";
                View::render("users/login", $data);
            }

        }

        // --------------------------------- BORRAR LIBROS ----------------------------------------

        public function borrarLibro() {
            // Comprobamos si el usuario está logueado
            if (isset($_SESSION["idUser"])) {
                // Recuperamos el id del libro que hay que borrar
                $idLibro = $this->filtrarEntrada($_REQUEST["idLibro"]);
                // Pedimos al modelo que intente borrar el libro
                $result = $this->libro->delete($idLibro);
                // Comprobamos si el borrado ha tenido éxito
                if ($result == 0) {
                    $data["error"] = "rowcount = $result -- Ha ocurrido un error al borrar el libro. Por favor, inténtelo de nuevo";
                } else {
                    $data["info"] = "Libro borrado con éxito";
                }
                $data["listaLibros"] = $this->libro->getAll();
                View::render("libro/all", $data);
            }
            else {
                // Usuario no logueado: error de permisos
                $data["mensaje"] = "No tienes permisos para hacer eso";
                View::render("users/login", $data);
            }

        }

        // --------------------------------- FORMULARIO MODIFICAR LIBROS ----------------------------------------

        public function formularioModificarLibro() {
            // Comprobamos si el usuario está logueado
            if (isset($_SESSION["idUser"])) {
                // Recuperamos los datos del libro a modificar
                $data["libro"] = $this->libro->get($_REQUEST["idLibro"]);
                // Renderizamos la vista de inserción de libros, pero enviándole los datos del libro recuperado.
                // Esa vista necesitará la lista de todos los autores y, además, la lista
                // de los autores de este libro en concreto.
                $data["todosLosAutores"] = $this->persona->getAll();
                $data["autoresLibro"] = $this->persona->getAutores($_REQUEST["idLibro"]);
                View::render("libro/form", $data);
            }
            else {
                // Usuario no logueado: error de permisos
                $data["mensaje"] = "No tienes permisos para hacer eso";
                View::render("users/login", $data);
            }
        }

        // --------------------------------- MODIFICAR LIBROS ----------------------------------------

        public function modificarLibro() {
            // Comprobamos si el usuario está logueado
            if (isset($_SESSION["idUser"])) {
                // Primero, recuperamos todos los datos del formulario
                $idLibro = $this->filtrarEntrada($_REQUEST["idLibro"]);
                $titulo = $this->filtrarEntrada($_REQUEST["titulo"]);
                $genero = $this->filtrarEntrada($_REQUEST["genero"]);
                $pais = $this->filtrarEntrada($_REQUEST["pais"]);
                $ano = $this->filtrarEntrada($_REQUEST["ano"]);
                $numPaginas = $this->filtrarEntrada($_REQUEST["numPaginas"]);
                $autores = $this->filtrarEntrada($_REQUEST["autor"]);

                $data["info"] = "";
                $data["error"] = "";

                // Pedimos al modelo que haga el update de la tabla de libros
                $result = $this->libro->update($idLibro, $titulo, $genero, $pais, $ano, $numPaginas);
                if ($result == 1) {
                    $data["info"] = $data["info"]."Libro actualizado con éxito. ";
                } else {
                    // Si la modificación del libro ha fallado, puede ser
                    $data["error"] = $data["info"]."No se han hecho cambios en los datos del libro. ";
                }

                // Pedimos al modelo que haga el update de la tabla privote (escriben)
                $this->libro->deleteAutores($idLibro);  // Primero eliminamos los autores actuales
                $result = $this->libro->insertAutores($idLibro, $autores);  // Insertamos los autores seleccionados
                if ($result > 0) {
                    $data["info"] = $data["info"]."Se han actualizado los autores del libro. ";
                } else {
                    $data["error"] = $data["error"]."No se han actualizado los autores del libro. ";
                }

                $data["listaLibros"] = $this->libro->getAll();
                View::render("libro/all", $data);
            }
            else {
                // Usuario no logueado: error de permisos
                $data["mensaje"] = "No tienes permisos para hacer eso";
                View::render("users/login", $data);
            }
        }

        // --------------------------------- BUSCAR LIBROS ----------------------------------------

        public function buscarLibros() {
            // Recuperamos el texto de búsqueda de la variable de formulario
            $textoBusqueda = $this->filtrarEntrada($_REQUEST["textoBusqueda"]);
            // Buscamos los libros que coinciden con la búsqueda
            $data["listaLibros"] = $this->libro->search($textoBusqueda);
            $data["info"] = "Resultados de la búsqueda: <i>$textoBusqueda</i>";
            // Mostramos el resultado en la misma vista que la lista completa de libros
            View::render("libro/all", $data);
        }

        // --------------------------------- MOSTRAR LISTA DE AUTORES ----------------------------------------
        public function mostrarListaAutores() {
            $data["listaAutores"] = $this->persona->getAll();
            View::render("autor/all", $data);
        }

        // --------------------------------- FORMULARIO ALTA DE AUTORES ----------------------------------------
        public function formularioInsertarAutores() {
            // Comprobamos si el usuario está logueado
            if (isset($_SESSION["idUser"])) {
                View::render("autor/form");
            }
            else {
                // Usuario no logueado: error de permisos
                $data["mensaje"] = "No tienes permisos para hacer eso";
                View::render("users/login", $data);
            }
        }

        // --------------------------------- INSERTAR AUTORES ----------------------------------------
        public function insertarAutor() {
            // Comprobamos si el usuario está logueado
            if (isset($_SESSION["idUser"])) {
                $nombre   = $this->filtrarEntrada($_REQUEST["nombre"]);
                $apellido = $this->filtrarEntrada($_REQUEST["apellido"]);
                $pais     = $this->filtrarEntrada($_REQUEST["pais"]);

                $result = $this->persona->insert($nombre, $apellido, $pais);

                if ($result == 1) {
                    $data["info"] = "Autor insertado con éxito";
                } else {
                    $data["error"] = "Error al insertar el autor";
                }
                $data["listaAutores"] = $this->persona->getAll();
                View::render("autor/all", $data);
            }
            else {
                // Usuario no logueado: error de permisos
                $data["mensaje"] = "No tienes permisos para hacer eso";
                View::render("users/login", $data);
            }
        }

        // --------------------------------- BORRAR AUTORES ----------------------------------------
        public function borrarAutor() {
            // Comprobamos si el usuario está logueado
            if (isset($_SESSION["idUser"])) {
                $idPersona = $this->filtrarEntrada($_REQUEST["idPersona"]);
                // Comprobamos si el autor tiene libros asociados
                $numLibros = count($this->persona->getLibros($idPersona));
                if ($numLibros > 0) {
                    $data["error"] = "No se puede borrar el autor porque tiene libros asociados";
                } else {
                    $result = $this->persona->delete($idPersona);
                    if ($result == 1) {
                        $data["info"] = "Autor borrado con éxito";
                    } else {
                        $data["error"] = "Error al borrar el autor";
                    }
                }

                $data["listaAutores"] = $this->persona->getAll();
                View::render("autor/all", $data);
            }
            else {
                // Usuario no logueado: error de permisos
                $data["mensaje"] = "No tienes permisos para hacer eso";
                View::render("users/login", $data);
            }
        }


        // --------------------------------- FORMULARIO MODIFICAR AUTORES ----------------------------------------
        public function formularioModificarAutor() {
            // Comprobamos si el usuario está logueado
            if (isset($_SESSION["idUser"])) {
                $data["autor"] = $this->persona->get($_REQUEST["idPersona"]);
                View::render("autor/form", $data);
            }
            else {
                // Usuario no logueado: error de permisos
                $data["mensaje"] = "No tienes permisos para hacer eso";
                View::render("users/login", $data);
            }
        }

        // --------------------------------- MODIFICAR AUTORES ----------------------------------------
        public function modificarAutor() {
            // Comprobamos si el usuario está logueado
            if (isset($_SESSION["idUser"])) {
                $idPersona = $this->filtrarEntrada($_REQUEST["idPersona"]);
                $nombre    = $this->filtrarEntrada($_REQUEST["nombre"]);
                $apellido  = $this->filtrarEntrada($_REQUEST["apellido"]);
                $pais      = $this->filtrarEntrada($_REQUEST["pais"]);

                $result = $this->persona->update($idPersona, $nombre, $apellido, $pais);

                if ($result == 1) {
                    $data["info"] = "Autor modificado con éxito";
                } else {
                    $data["error"] = "Error al modificar el autor";
                }
                $data["listaAutores"] = $this->persona->getAll();
                View::render("autor/all", $data);
            }
            else {
                // Usuario no logueado: error de permisos
                $data["mensaje"] = "No tienes permisos para hacer eso";
                View::render("users/login", $data);
            }
        }

        // --------------------------------- BUSCAR AUTORES ----------------------------------------
        public function buscarAutores() {
            $textoBusqueda = $_REQUEST["textoBusqueda"];
            $data["listaAutores"] = $this->persona->search($textoBusqueda);
            $data["info"] = "Resultados de la búsqueda: <i>$textoBusqueda</i>";
            View::render("autor/all", $data);
        }

        // ---------------------------------- FILTRO PARA LAS ENTRADAS ------------------------------
        // Este método limpia las entradas que provienen de un formulario y hace una limpieza básica.
        function filtrarEntrada($entrada) {
            if (is_string($entrada)) {
                // Elimina espacios en blanco al inicio y al final
                $entrada = trim($entrada);
                // Elimina barras invertidas (para evitar escapes no deseados)
                $entrada = stripslashes($entrada);
                // Convierte caracteres especiales en entidades HTML para evitar ataques por XSS
                $entrada = htmlspecialchars($entrada, ENT_QUOTES, 'UTF-8');

                // Se puede mejorar el método haciendo comprobaciones más exhaustivas
            }
            return $entrada;
        }        

    } // class