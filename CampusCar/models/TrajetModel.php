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
}
?>
