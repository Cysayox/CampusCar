<?php
// models/UserModel.php
require_once 'Database.php';

class UserModel {
    private $conn;
    private $table_name = "UTILISATEUR";

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    // Fonction pour vérifier l'identifiant Sésame et le mot de passe
    // Fonction pour vérifier l'identifiant Sésame et le mot de passe
    public function verifierUtilisateur($id_sesame, $mot_de_passe_saisi) {
        // On cherche l'utilisateur et on vérifie s'il existe dans profil_conducteur
        $query = "SELECT u.*, 
                         IF(p.id_profil IS NOT NULL, 1, 0) AS is_driver 
                  FROM " . $this->table_name . " u
                  LEFT JOIN profil_conducteur p ON u.id_utilisateur = p.id_utilisateur
                  WHERE u.id_sesame = :id_sesame LIMIT 1";
                  
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_sesame', $id_sesame);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // On vérifie le mot de passe
            if (password_verify($mot_de_passe_saisi, $row['mot_de_passe'])) {
                return $row; // C'est tout bon !
            }
        }
        return false;
    }

    // Fonction pour créer un nouvel utilisateur (inscription)
    public function creerUtilisateur($id_sesame, $mot_de_passe, $nom, $prenom) {
        // Hasher le mot de passe pour la sécurité
        $mot_de_passe_hache = password_hash($mot_de_passe, PASSWORD_DEFAULT);

        // Préparer la requête d'insertion
        $query = "INSERT INTO " . $this->table_name . " (id_sesame, mot_de_passe, nom, prenom) VALUES (:id_sesame, :mot_de_passe, :nom, :prenom)";
        
        $stmt = $this->conn->prepare($query);

        // Nettoyer et lier les paramètres
        $stmt->bindParam(':id_sesame', $id_sesame);
        $stmt->bindParam(':mot_de_passe', $mot_de_passe_hache);
        $stmt->bindParam(':nom', $nom);
        $stmt->bindParam(':prenom', $prenom);

        // Exécuter la requête
        if ($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        return false;
    }
    
    // Récupère toutes les infos d'un utilisateur grâce à son ID
    public function getUtilisateurById($id_utilisateur) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id_utilisateur = :id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id_utilisateur, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Calcule la réputation d'un utilisateur (moyenne des notes sur ses trajets, hors ses propres avis)
    public function getMoyenneNotes($id_utilisateur) {
        $query = "SELECT AVG(e.note_etoiles) as moyenne, COUNT(e.note_etoiles) as total_avis 
                  FROM evaluer e
                  WHERE e.id_trajet IN (
                      -- On récupère tous les trajets où l'utilisateur était soit conducteur...
                      SELECT id_trajet FROM trajet WHERE id_conducteur = :id
                      UNION
                      -- ...soit passager
                      SELECT id_trajet FROM reserver WHERE id_passager = :id
                  ) 
                  -- Et on exclut les notes que l'utilisateur s'est auto-attribuées
                  AND e.id_evaluateur != :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id_utilisateur, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>