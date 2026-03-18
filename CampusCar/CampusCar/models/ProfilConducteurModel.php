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
    } // <--- C'EST CETTE ACCOLADE QUI MANQUAIT !

    /**
     * Récupère toutes les demandes de conducteurs en attente
     */
    public function getDemandesEnAttente() {
        $sql = "SELECT p.*, u.nom, u.prenom, u.id_sesame 
                FROM profil_conducteur p
                JOIN utilisateur u ON p.id_utilisateur = u.id_utilisateur
                WHERE p.statut_validation = 'en_attente'
                ORDER BY p.id_profil ASC";
        $stmt = $this->db->getConnection()->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Valide un profil conducteur (Passe de 'en_attente' à 'valide')
     */
    public function validerDemande($id_profil) {
        $sql = "UPDATE profil_conducteur SET statut_validation = 'valide' WHERE id_profil = :id_profil";
        $stmt = $this->db->getConnection()->prepare($sql);
        $stmt->bindParam(':id_profil', $id_profil, PDO::PARAM_INT);
        return $stmt->execute();
    }

    /**
     * Rejette et supprime une demande de profil conducteur
     */
    public function rejeterDemande($id_profil) {
        $sql = "DELETE FROM profil_conducteur WHERE id_profil = :id_profil AND statut_validation = 'en_attente'";
        $stmt = $this->db->getConnection()->prepare($sql);
        $stmt->bindParam(':id_profil', $id_profil, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
?>