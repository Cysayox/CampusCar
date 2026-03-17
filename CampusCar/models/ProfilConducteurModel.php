<?php
require_once 'Database.php';

class ProfilConducteurModel {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    /**
     * Crée un profil conducteur.
     *
     * @param int $id_utilisateur L'ID de l'utilisateur.
     * @param string $date_permis La date d'obtention du permis.
     * @return bool True si la création a réussi, false sinon.
     */
    public function createProfilConducteur($id_utilisateur, $date_permis) {
        $sql = "INSERT INTO profil_conducteur (id_utilisateur, date_permis) VALUES (:id_utilisateur, :date_permis)";
        try {
            $stmt = $this->db->getConnection()->prepare($sql);
            $stmt->bindParam(':id_utilisateur', $id_utilisateur, PDO::PARAM_INT);
            $stmt->bindParam(':date_permis', $date_permis);
            return $stmt->execute();
        } catch (PDOException $e) {
            // Gérer l'erreur, par exemple en la journalisant
            // error_log($e->getMessage());
            return false;
        }
    }
    // Vérifie si un utilisateur est conducteur
    public function isConducteur($id_utilisateur) {
        $sql = "SELECT id_profil FROM profil_conducteur WHERE id_utilisateur = :id";
        $stmt = $this->db->getConnection()->prepare($sql);
        $stmt->bindParam(':id', $id_utilisateur, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->rowCount() > 0; // Renvoie vrai s'il trouve un profil
    }
}
