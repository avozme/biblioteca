<?php

// MODELO DE PERSONAS (AUTORES) CON PDO
// Este modelo accede a la tabla de Personas

class Persona
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

    // Obtiene todas las personas (autores)
    public function getAll()
    {
        $sql = "SELECT * FROM personas ORDER BY apellido, nombre";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    // Obtiene una persona por su ID
    public function get($id)
    {
        $sql = "SELECT * FROM personas WHERE idPersona = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([":id" => $id]);
        return $stmt->fetch(PDO::FETCH_OBJ);
    }

    // Devuelve el último id asignado en la tabla personas
    public function getMaxId()
    {
        $sql = "SELECT MAX(idPersona) AS ultimoIdPersona FROM personas";
        $stmt = $this->db->query($sql);
        return $stmt->fetch(PDO::FETCH_OBJ)->ultimoIdPersona;
    }

    // Inserta una persona
    public function insert($nombre, $apellido, $pais)
    {
        $sql = "INSERT INTO personas (nombre, apellido, pais)
                VALUES (:nombre, :apellido, :pais)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ":nombre"   => $nombre,
            ":apellido" => $apellido,
            ":pais"     => $pais
        ]);
        return $stmt->rowCount();
    }

    // Actualiza una persona
    public function update($idPersona, $nombre, $apellido, $pais)
    {
        $sql = "UPDATE personas SET
                    nombre = :nombre,
                    apellido = :apellido,
                    pais = :pais
                WHERE idPersona = :idPersona";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ":nombre"    => $nombre,
            ":apellido"  => $apellido,
            ":pais"      => $pais,
            ":idPersona" => $idPersona
        ]);
        return $stmt->rowCount();
    }

    // Elimina una persona
    public function delete($idPersona)
    {
        $sql = "DELETE FROM personas WHERE idPersona = :idPersona";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([":idPersona" => $idPersona]);
        return $stmt->rowCount();
    }

    // Busca personas por nombre, apellido o país
    public function search($textoBusqueda)
    {
        $sql = "SELECT * FROM personas
                WHERE nombre LIKE :texto
                   OR apellido LIKE :texto
                   OR pais LIKE :texto
                ORDER BY apellido, nombre";
        $stmt = $this->db->prepare($sql);
        $param = "%" . $textoBusqueda . "%";
        $stmt->execute([":texto" => $param]);
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    // Obtiene todos los autores de un libro concreto
    public function getAutores($idLibro)
    {
        $sql = "SELECT personas.*
                FROM personas
                INNER JOIN escriben ON personas.idPersona = escriben.idPersona
                WHERE escriben.idLibro = :idLibro
                ORDER BY personas.apellido, personas.nombre";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([":idLibro" => $idLibro]);
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    // Obtiene todos los libros de un autor concreto
    public function getLibros($idAutor) {
        $sql = "SELECT libros.*
                FROM libros
                INNER JOIN escriben ON libros.idLibro = escriben.idLibro
                WHERE escriben.idPersona = :idPersona
                ORDER BY libros.titulo";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([":idPersona" => $idAutor]);
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }
}
