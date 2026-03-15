<?php
// models/CampusModel.php
require_once __DIR__ . '/Database.php';

class CampusModel {
    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    // Fonction pour récupérer tous les campus triés par Pôle (Martinique/Guadeloupe)
    public function getTousLesCampus() {
        $query = "SELECT id_campus, nom_campus, pole_geographique FROM CAMPUS ORDER BY pole_geographique DESC, nom_campus ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>