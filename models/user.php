<?php

// MODELO DE USUARIOS CON PDO
// El modelo de usuarios de esta aplicación solo se usa para hacer el login.
// Es decir, no hay inserciones de usuarios, borrados ni otras operaciones habituales de los modelos.

class User
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

    // Comprueba si un usuario y una contraseña existen en la tabla users.
    // Devuelve un objeto con los datos del usuario en caso de que exista o null si no existe.
    public function checkLogin($username, $pass)
    {
        $sql = "SELECT * FROM users WHERE username = '$username' AND password = '$pass'";
        $stmt = $this->db->query($sql);
        if ($stmt->rowCount() != 0) {
            $registro = $stmt->fetch(PDO::FETCH_OBJ);
            return $registro;
        } else {
            return null;
        }
    }
}