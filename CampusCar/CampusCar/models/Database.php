<?php
// models/Database.php

class Database {
    // Mets ici les vrais identifiants de ta base de données locale (WAMP, XAMPP, MAMP, ou PostGreSQL)
    private $host = "localhost";
    private $db_name = "campuscar"; // Le nom de ta base de données
    private $username = "root";        // Souvent 'root' en local
    private $password = "";            // Souvent vide sous Windows, ou 'root' sous Mac
    public $conn;

    // Fonction pour obtenir la connexion
    public function getConnection() {
        $this->conn = null;
        try {
            // Exemple pour MySQL (Si tu utilises PostgreSQL, remplace "mysql:" par "pgsql:")
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name . ";charset=utf8", $this->username, $this->password);
            
            // On demande à PDO de nous afficher les erreurs SQL s'il y en a
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $exception) {
            echo "Erreur de connexion : " . $exception->getMessage();
        }
        return $this->conn;
    }
}
?>