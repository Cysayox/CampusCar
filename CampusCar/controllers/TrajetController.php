<?php
require_once __DIR__ . '/../models/TrajetModel.php';

class TrajetController {
    
    public function showMesTrajets() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?action=login');
            exit();
        }

        $trajetModel = new TrajetModel();
        $mes_trajets = $trajetModel->getMesTrajets($_SESSION['user_id']);
        
        $maintenant = new DateTime();
        $trajets_a_venir = [];
        $trajets_passes = [];

        // On trie les trajets en PHP pour séparer "À venir" et "Passés"
        foreach ($mes_trajets as $t) {
            $date_trajet = new DateTime($t['date_heure']);
            if ($date_trajet >= $maintenant) {
                $trajets_a_venir[] = $t;
            } else {
                $trajets_passes[] = $t;
            }
        }

        require_once __DIR__ . '/../views/mes_trajets.php';
    }
    // 1. Affiche le formulaire pour proposer un trajet
    public function showProposerTrajet() {
        // On vérifie que l'utilisateur est connecté ET qu'il est bien conducteur
        if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_driver']) || $_SESSION['is_driver'] != 1) {
            header('Location: index.php?action=accueil');
            exit();
        }

        // On a besoin de la liste des campus pour le menu déroulant
        require_once __DIR__ . '/../models/CampusModel.php';
        $campusModel = new CampusModel();
        $liste_campus = $campusModel->getTousLesCampus();

        require_once __DIR__ . '/../views/proposer_trajet.php';
    }

    // 2. Traite les données envoyées par le formulaire
    public function processProposerTrajet() {
        if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_driver']) || $_SESSION['is_driver'] != 1) {
            header('Location: index.php?action=accueil');
            exit();
        }

        // Récupération des données
        $date_trajet = $_POST['date_trajet'] ?? '';
        $heure_trajet = $_POST['heure_trajet'] ?? '';
        $sens_trajet = $_POST['sens_trajet'] ?? '';
        $id_campus_cible = $_POST['campus'] ?? '';
        $adresse_exterieure = $_POST['adresse'] ?? '';
        $places_dispo = $_POST['places_dispo'] ?? 1;
        $prix_course = $_POST['prix_course'] ?? 0;

        // On assemble la date et l'heure au format lisible par SQL (YYYY-MM-DD HH:MM:SS)
        $date_heure = $date_trajet . ' ' . $heure_trajet . ':00';

        $trajetModel = new TrajetModel();
        $result = $trajetModel->creerTrajet($date_heure, $prix_course, $places_dispo, $_SESSION['user_id'], $adresse_exterieure, $sens_trajet, $id_campus_cible);

        if ($result) {
            // Si c'est un succès, on le renvoie vers son historique de trajets
            header('Location: index.php?action=mes_trajets');
        } else {
            // Si erreur (rare), on redirige vers l'accueil
            header('Location: index.php?action=accueil');
        }
        exit();
    }
    // Affiche les détails d'un trajet précis
    public function showTrajetDetails() {
        // 1. Si l'utilisateur n'est pas connecté, on l'oblige à se connecter
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?action=login');
            exit();
        }

        // 2. On vérifie qu'on a bien un ID dans l'URL (ex: ?action=trajet_details&id=3)
        $id_trajet = $_GET['id'] ?? null;
        if (!$id_trajet) {
            header('Location: index.php?action=accueil');
            exit();
        }

        // 3. On récupère les infos du trajet
        $trajetModel = new TrajetModel();
        $trajet = $trajetModel->getTrajetById($id_trajet, $_SESSION['user_id']);

        // 4. Si le trajet n'existe pas
        if (!$trajet) {
            header('Location: index.php?action=accueil');
            exit();
        }

        // --- NOUVEAU : Récupération des passagers si c'est le conducteur ---
        $passagers_list = [];
        if ($trajet['is_driver']) {
            $passagers_list = $trajetModel->getPassagersTrajet($id_trajet);
        }

        // 5. On affiche la vue
        require_once __DIR__ . '/../views/trajet_details.php';
    }
}
?>