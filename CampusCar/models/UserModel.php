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
    public function verifierUtilisateur($id_sesame, $mot_de_passe_saisi) {
        // 1. On cherche l'utilisateur par son id_sesame
        $query = "SELECT * FROM " . $this->table_name . " WHERE id_sesame = :id_sesame LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_sesame', $id_sesame);
        $stmt->execute();

        // 2. Si l'utilisateur existe
        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // 3. On vérifie le mot de passe avec password_verify()
            // (Assure-toi d'avoir bien utilisé password_hash() quand tu as créé tes faux comptes !)
            if (password_verify($mot_de_passe_saisi, $row['mot_de_passe'])) {
                return $row; // C'est tout bon, on renvoie les infos de Marc !
            }
        }
        // Si on arrive ici, soit l'id n'existe pas, soit le mot de passe est faux
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

    // Calcule la moyenne des notes reçues (la méthode la plus simple via SQL)
    public function getMoyenneNotes($id_utilisateur) {
        $query = "SELECT AVG(note_etoiles) as moyenne, COUNT(note_etoiles) as total_avis 
                  FROM evaluer WHERE id_evalue = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id_utilisateur, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>