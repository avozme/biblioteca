<?php

// MODELO DE LIBROS CON PDO
// El modelo de libros realiza todos los accesos a la base de datos para trabajar con la tabla Libros.
// También accede, en contadas ocasiones, a la tabla pivote (escriben), para la que no hemos hecho modelo.

class Libro
{
    private $db;

    // Constructor. Habilita la conexión con la base de datos.
    public function __construct()
    {
        $dsn = "mysql:host=mariadb;dbname=pruebas;charset=utf8mb4";
        $usuario = "user";
        $clave = "1234";

        try {
            $this->db = new PDO($dsn, $usuario, $clave);
        } catch (PDOException $e) {
            die("Error en la conexión: " . $e->getMessage());
        }
    }

    // Obtiene todos los libros con sus autores
    public function getAll()
    {
        $sql = "SELECT * FROM libros
                INNER JOIN escriben ON libros.idLibro = escriben.idLibro
                INNER JOIN personas ON escriben.idPersona = personas.idPersona
                ORDER BY libros.titulo";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    // Obtiene un libro por su ID
    public function get($id)
    {
        $sql = "SELECT * FROM libros WHERE idLibro = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([":id" => $id]);
        return $stmt->fetch(PDO::FETCH_OBJ);
    }

    // Devuelve el último id asignado en la tabla libros
    public function getMaxId()
    {
        $sql = "SELECT MAX(idLibro) AS ultimoIdLibro FROM libros";
        $stmt = $this->db->query($sql);
        return $stmt->fetch(PDO::FETCH_OBJ)->ultimoIdLibro;
    }

    // Inserta un libro
    public function insert($titulo, $genero, $pais, $ano, $numPaginas)
    {
        $sql = "INSERT INTO libros (titulo, genero, pais, ano, numPaginas)
                VALUES (:titulo, :genero, :pais, :ano, :numPaginas)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ":titulo"     => $titulo,
            ":genero"     => $genero,
            ":pais"       => $pais,
            ":ano"        => $ano,
            ":numPaginas" => $numPaginas
        ]);
        return $stmt->rowCount();
    }

    // Inserta los autores de un libro
    public function insertAutores($idLibro, $autores)
    {
        $sql = "INSERT INTO escriben (idLibro, idPersona) VALUES (:idLibro, :idPersona)";
        $stmt = $this->db->prepare($sql);
        $correctos = 0;

        foreach ($autores as $idAutor) {
            $stmt->execute([
                ":idLibro"  => $idLibro,
                ":idPersona"=> $idAutor
            ]);
            $correctos += $stmt->rowCount();
        }

        return $correctos;
    }

    // Elimina los autores de un libro
    public function deleteAutores($idLibro)
    {
        $sql = "DELETE FROM escriben WHERE idLibro = :idLibro";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([":idLibro"  => $idLibro]);
        return $stmt->rowCount();
    }


    // Actualiza un libro
    public function update($idLibro, $titulo, $genero, $pais, $ano, $numPaginas)
    {
        $sql = "UPDATE libros SET
                    titulo = :titulo,
                    genero = :genero,
                    pais = :pais,
                    ano = :ano,
                    numPaginas = :numPaginas
                WHERE idLibro = :idLibro";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ":titulo"     => $titulo,
            ":genero"     => $genero,
            ":pais"       => $pais,
            ":ano"        => $ano,
            ":numPaginas" => $numPaginas,
            ":idLibro"    => $idLibro
        ]);
        return $stmt->rowCount();
    }

    // Elimina un libro
    public function delete($idLibro)
    {
        $sql = "DELETE FROM libros WHERE idLibro = :idLibro";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([":idLibro" => $idLibro]);
        return $stmt->rowCount();
    }

    // Busca un texto en libros y autores
    public function search($textoBusqueda)
    {
        $sql = "SELECT * FROM libros
                INNER JOIN escriben ON libros.idLibro = escriben.idLibro
                INNER JOIN personas ON escriben.idPersona = personas.idPersona
                WHERE libros.titulo LIKE :texto
                   OR libros.genero LIKE :texto
                   OR personas.nombre LIKE :texto
                   OR personas.apellido LIKE :texto
                ORDER BY libros.titulo";
        $stmt = $this->db->prepare($sql);
        $param = "%" . $textoBusqueda . "%";
        $stmt->execute([":texto" => $param]);
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }
}