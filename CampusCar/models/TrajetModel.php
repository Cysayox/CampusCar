<?php
// models/TrajetModel.php
require_once __DIR__ . '/Database.php';

class TrajetModel {
    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    // Fonction pour afficher les trajets par défaut sur la page d'accueil
    public function getProchainsTrajets() {
        // On récupère le trajet, le nom du campus, les infos du conducteur
        // ET on calcule sa note moyenne et son nombre d'avis en direct !
        $query = "SELECT t.*, u.prenom, u.nom, c.nom_campus,
                  (SELECT AVG(note_etoiles) FROM evaluer WHERE id_evalue = t.id_conducteur) AS note_moyenne,
                  (SELECT COUNT(*) FROM evaluer WHERE id_evalue = t.id_conducteur) AS nb_avis
                  FROM trajet t
                  JOIN utilisateur u ON t.id_conducteur = u.id_utilisateur
                  JOIN campus c ON t.id_campus_cible = c.id_campus
                  WHERE t.date_heure >= NOW() 
                  ORDER BY t.date_heure ASC
                  LIMIT 20";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Fonction pour rechercher un trajet selon les critères du formulaire
    public function rechercherTrajets($id_campus, $sens_trajet, $date_trajet, $latitude = null, $longitude = null) {
        
        // On ajoute les mêmes sous-requêtes ici pour les résultats de recherche
        $sql_select = "SELECT t.*, u.prenom, u.nom, c.nom_campus,
                       (SELECT AVG(note_etoiles) FROM evaluer WHERE id_evalue = t.id_conducteur) AS note_moyenne,
                       (SELECT COUNT(*) FROM evaluer WHERE id_evalue = t.id_conducteur) AS nb_avis";
        
        $sql_from = " FROM trajet t JOIN utilisateur u ON t.id_conducteur = u.id_utilisateur JOIN campus c ON t.id_campus_cible = c.id_campus";
        $sql_where = " WHERE t.id_campus_cible = :campus AND t.sens_trajet = :sens AND DATE(t.date_heure) = :date_recherche";
        
        // ... (Le reste de ta fonction avec $sql_order et ST_Distance_Sphere reste identique)
        $sql_order = " ORDER BY t.date_heure ASC"; // Tri par défaut

        // Si on a des coordonnées GPS, on calcule la distance et on trie par celle-ci
        if ($latitude !== null && $longitude !== null) {
            $sql_select .= ", ST_Distance_Sphere(point(t.longitude, t.latitude), point(:longitude, :latitude)) AS distance_metres";
            $sql_order = " ORDER BY distance_metres ASC";
        }

        $query = $sql_select . $sql_from . $sql_where . $sql_order;
        
        $stmt = $this->conn->prepare($query);
        
        // On lie les variables PHP aux marqueurs SQL
        $stmt->bindParam(':campus', $id_campus, PDO::PARAM_INT);
        $stmt->bindParam(':sens', $sens_trajet, PDO::PARAM_STR);
        $stmt->bindParam(':date_recherche', $date_trajet, PDO::PARAM_STR);

        if ($latitude !== null && $longitude !== null) {
            $stmt->bindParam(':latitude', $latitude, PDO::PARAM_STR);
            $stmt->bindParam(':longitude', $longitude, PDO::PARAM_STR);
        }
        
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // --- CODE DU COLLÈGUE INTÉGRÉ ICI ---
    // Récupère l'historique de tous les trajets (conducteur ou passager)
    public function getMesTrajets($id_utilisateur) {
        $sql = "
            SELECT t.*, c.nom_campus, u.prenom as conducteur_prenom, u.nom as conducteur_nom,
                   (CASE WHEN t.id_conducteur = :id THEN 1 ELSE 0 END) as is_mon_trajet_conducteur,
                   
                   -- NOUVEAU : Compte le nombre total de réservations pour ce trajet
                   (SELECT COUNT(*) FROM reserver WHERE id_trajet = t.id_trajet) as nb_passagers,
                   
                   -- NOUVEAU : Récupère le prénom du premier passager qui a réservé
                   (SELECT u2.prenom 
                    FROM reserver r2 
                    JOIN utilisateur u2 ON r2.id_passager = u2.id_utilisateur 
                    WHERE r2.id_trajet = t.id_trajet 
                    LIMIT 1) as premier_passager_prenom,

                   -- NOUVEAU : Récupère le nom du premier passager (pour l'affichage)
                   (SELECT u2.nom 
                    FROM reserver r2 
                    JOIN utilisateur u2 ON r2.id_passager = u2.id_utilisateur 
                    WHERE r2.id_trajet = t.id_trajet 
                    LIMIT 1) as premier_passager_nom

            FROM trajet t
            JOIN campus c ON t.id_campus_cible = c.id_campus
            JOIN utilisateur u ON t.id_conducteur = u.id_utilisateur
            LEFT JOIN reserver r ON t.id_trajet = r.id_trajet
            WHERE t.id_conducteur = :id OR r.id_passager = :id
            GROUP BY t.id_trajet
            ORDER BY t.date_heure DESC
        ";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id_utilisateur, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    // Fonction pour créer un nouveau trajet dans la base de données
    public function creerTrajet($date_heure, $prix_course, $places_dispo, $id_conducteur, $adresse_exterieure, $sens_trajet, $id_campus_cible) {
        $sql = "INSERT INTO trajet (date_heure, prix_course, places_dispo, id_conducteur, adresse_exterieure, sens_trajet, id_campus_cible)
                VALUES (:date_heure, :prix_course, :places_dispo, :id_conducteur, :adresse_exterieure, :sens_trajet, :id_campus_cible)";
        
        $stmt = $this->conn->prepare($sql);
        
        $stmt->bindParam(':date_heure', $date_heure);
        $stmt->bindParam(':prix_course', $prix_course);
        $stmt->bindParam(':places_dispo', $places_dispo);
        $stmt->bindParam(':id_conducteur', $id_conducteur);
        $stmt->bindParam(':adresse_exterieure', $adresse_exterieure);
        $stmt->bindParam(':sens_trajet', $sens_trajet);
        $stmt->bindParam(':id_campus_cible', $id_campus_cible);
        
        return $stmt->execute();
    }
    // Récupère les détails d'un trajet et le statut de l'utilisateur vis-à-vis de ce trajet
    public function getTrajetById($id_trajet, $id_utilisateur) {
        $sql = "
            SELECT t.*, c.nom_campus, c.pole_geographique, 
                   u.prenom as conducteur_prenom, u.nom as conducteur_nom, u.note_moyenne_calc,
                   (SELECT COUNT(*) FROM reserver WHERE id_trajet = t.id_trajet) as nb_passagers,
                   (SELECT COUNT(*) FROM evaluer WHERE id_evalue = t.id_conducteur) as nb_avis,
                   (SELECT AVG(note_etoiles) FROM evaluer WHERE id_evalue = t.id_conducteur) as vraie_note
            FROM trajet t
            JOIN campus c ON t.id_campus_cible = c.id_campus
            JOIN utilisateur u ON t.id_conducteur = u.id_utilisateur
            WHERE t.id_trajet = :id_trajet LIMIT 1
        ";
        
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id_trajet', $id_trajet, PDO::PARAM_INT);
        $stmt->execute();
        $trajet = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($trajet) {
            // Est-ce que c'est le trajet de l'utilisateur (Conducteur) ?
            $trajet['is_driver'] = ($trajet['id_conducteur'] == $id_utilisateur);
            
            // Est-ce que l'utilisateur est inscrit à ce trajet (Passager) ?
            $sql_pass = "SELECT id_reservation FROM reserver WHERE id_trajet = :id_trajet AND id_passager = :id_passager LIMIT 1";
            $stmt_pass = $this->conn->prepare($sql_pass);
            $stmt_pass->bindParam(':id_trajet', $id_trajet, PDO::PARAM_INT);
            $stmt_pass->bindParam(':id_passager', $id_utilisateur, PDO::PARAM_INT);
            $stmt_pass->execute();
            
            $trajet['is_passenger'] = ($stmt_pass->rowCount() > 0);
        }

        return $trajet;
    }
    // Récupère la liste des passagers inscrits à un trajet
    public function getPassagersTrajet($id_trajet) {
        $sql = "SELECT u.prenom, u.nom FROM reserver r 
                JOIN utilisateur u ON r.id_passager = u.id_utilisateur 
                WHERE r.id_trajet = :id_trajet";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id_trajet', $id_trajet, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    // --- FONCTION POUR METTRE À JOUR UN TRAJET ---
    public function updateTrajet($id_trajet, $date_heure, $prix_course, $places_dispo, $id_conducteur, $adresse_exterieure, $sens_trajet, $id_campus_cible) {
        $sql = "UPDATE trajet 
                SET date_heure = :date_heure, 
                    prix_course = :prix_course, 
                    places_dispo = :places_dispo, 
                    adresse_exterieure = :adresse_exterieure, 
                    sens_trajet = :sens_trajet, 
                    id_campus_cible = :id_campus_cible 
                WHERE id_trajet = :id_trajet AND id_conducteur = :id_conducteur";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':date_heure', $date_heure);
        $stmt->bindParam(':prix_course', $prix_course);
        $stmt->bindParam(':places_dispo', $places_dispo);
        $stmt->bindParam(':adresse_exterieure', $adresse_exterieure);
        $stmt->bindParam(':sens_trajet', $sens_trajet);
        $stmt->bindParam(':id_campus_cible', $id_campus_cible);
        $stmt->bindParam(':id_trajet', $id_trajet, PDO::PARAM_INT);
        $stmt->bindParam(':id_conducteur', $id_conducteur, PDO::PARAM_INT);
        
        return $stmt->execute();
    }

    // --- FONCTION POUR SUPPRIMER (ANNULER) UN TRAJET ---
    public function deleteTrajet($id_trajet, $id_conducteur) {
        // 1. Par sécurité, on supprime d'abord les éventuelles réservations liées à ce trajet
        $sql_resa = "DELETE FROM reserver WHERE id_trajet = :id";
        $stmt_resa = $this->conn->prepare($sql_resa);
        $stmt_resa->bindParam(':id', $id_trajet, PDO::PARAM_INT);
        $stmt_resa->execute();

        // 2. Ensuite, on supprime le trajet lui-même (en vérifiant que c'est bien le bon conducteur)
        $sql = "DELETE FROM trajet WHERE id_trajet = :id_trajet AND id_conducteur = :id_conducteur";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id_trajet', $id_trajet, PDO::PARAM_INT);
        $stmt->bindParam(':id_conducteur', $id_conducteur, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
?>