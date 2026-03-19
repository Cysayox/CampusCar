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
        
        $maintenant = time();
        
        $trajets_en_cours = [];
        $trajets_a_venir = [];
        $trajets_passes = [];

        // On trie les trajets en 3 catégories
        foreach ($mes_trajets as $t) {
            $timestamp_trajet = strtotime($t['date_heure']);
            
            // Si le trajet a commencé il y a moins de 4 heures (4 * 3600 secondes) = EN COURS
            if ($timestamp_trajet <= $maintenant && $timestamp_trajet >= ($maintenant - 4 * 3600)) {
                $trajets_en_cours[] = $t;
            } 
            // S'il est dans le futur = À VENIR
            elseif ($timestamp_trajet > $maintenant) {
                $trajets_a_venir[] = $t;
            } 
            // S'il date de plus de 4 heures = PASSÉ
            else {
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

        // On récupère la liste des passagers pour TOUT LE MONDE (Conducteur ET Passagers)
        // C'est nécessaire pour l'affichage ET pour le système d'évaluation mutuelle !
        $passagers_list = $trajetModel->getPassagersTrajet($id_trajet);

        // --- NOUVEAU : Récupération des évaluations reçues si le trajet est passé ---
        $evaluations_recues = [];
        if (strtotime($trajet['date_heure']) < time()) {
            $evaluations_recues = $trajetModel->getEvaluationsPourTrajet($id_trajet);
        }

        // 5. On affiche la vue
        require_once __DIR__ . '/../views/trajet_details.php';
    }
    // --------------------------------------------------------
    // --- LOGIQUE POUR ANNULER UN TRAJET (CONDUCTEUR) ---
    // --------------------------------------------------------
    public function processAnnulerTrajet() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?action=login');
            exit();
        }

        $id_trajet = $_GET['id'] ?? null;
        if ($id_trajet) {
            $trajetModel = new TrajetModel();
            // On supprime en passant l'ID de l'utilisateur pour être sûr qu'il est bien le proprio
            $trajetModel->deleteTrajet($id_trajet, $_SESSION['user_id']);
        }
        
        // On redirige vers l'historique une fois annulé
        header('Location: index.php?action=mes_trajets');
        exit();
    }

    // --------------------------------------------------------
    // --- LOGIQUE POUR MODIFIER UN TRAJET (CONDUCTEUR) ---
    // --------------------------------------------------------
    
    // 1. Afficher le formulaire de modification pré-rempli
    public function showModifierTrajet() {
        if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_driver']) || $_SESSION['is_driver'] != 1) {
            header('Location: index.php?action=accueil');
            exit();
        }

        $id_trajet = $_GET['id'] ?? null;
        if (!$id_trajet) {
            header('Location: index.php?action=mes_trajets');
            exit();
        }

        $trajetModel = new TrajetModel();
        $trajet = $trajetModel->getTrajetById($id_trajet, $_SESSION['user_id']);

        // Sécurité : Le trajet doit exister, on doit être le conducteur, ET il ne doit y avoir aucun passager
        if (!$trajet || !$trajet['is_driver'] || $trajet['nb_passagers'] > 0) {
            header('Location: index.php?action=mes_trajets');
            exit();
        }

        // On a besoin de la liste des campus pour le menu déroulant
        require_once __DIR__ . '/../models/CampusModel.php';
        $campusModel = new CampusModel();
        $liste_campus = $campusModel->getTousLesCampus();

        // On appelle la vue
        require_once __DIR__ . '/../views/modifier_trajet.php';
    }

    // 2. Traiter la soumission du formulaire de modification
    public function processModifierTrajet() {
        if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_driver']) || $_SESSION['is_driver'] != 1) {
            header('Location: index.php?action=accueil');
            exit();
        }

        $id_trajet = $_POST['id_trajet'] ?? null;
        if (!$id_trajet) {
            header('Location: index.php?action=mes_trajets');
            exit();
        }

        // Récupération des données modifiées
        $date_trajet = $_POST['date_trajet'] ?? '';
        $heure_trajet = $_POST['heure_trajet'] ?? '';
        $sens_trajet = $_POST['sens_trajet'] ?? '';
        $id_campus_cible = $_POST['campus'] ?? '';
        $adresse_exterieure = $_POST['adresse'] ?? '';
        $places_dispo = $_POST['places_dispo'] ?? 1;
        $prix_course = $_POST['prix_course'] ?? 0;

        $date_heure = $date_trajet . ' ' . $heure_trajet . ':00';

        $trajetModel = new TrajetModel();
        $trajetModel->updateTrajet($id_trajet, $date_heure, $prix_course, $places_dispo, $_SESSION['user_id'], $adresse_exterieure, $sens_trajet, $id_campus_cible);

        // On renvoie vers la page de détails mise à jour !
        header('Location: index.php?action=trajet_details&id=' . $id_trajet);
        exit();
    }
    // --------------------------------------------------------
    // --- LOGIQUE POUR RÉSERVER UN TRAJET (PASSAGER) ---
    // --------------------------------------------------------
    public function processReserver() {
        // 1. L'utilisateur doit être connecté pour réserver
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?action=login');
            exit();
        }

        $id_trajet = $_GET['id'] ?? null;
        if ($id_trajet) {
            $trajetModel = new TrajetModel();
            
            // Sécurité : on vérifie que le trajet existe et qu'il reste de la place
            $trajet = $trajetModel->getTrajetById($id_trajet, $_SESSION['user_id']);
            
            if ($trajet && !$trajet['is_driver'] && $trajet['nb_passagers'] < $trajet['places_dispo']) {
                // On valide la réservation !
                $trajetModel->reserverTrajet($id_trajet, $_SESSION['user_id']);
                
                // (Bonus futur : c'est ici qu'on pourrait déduire le prix du solde_virtuel du passager)
            }
        }
        
        // On redirige vers la page du trajet pour qu'il voit la mise à jour (bouton vert "Inscrit")
        header('Location: index.php?action=trajet_details&id=' . $id_trajet);
        exit();
    }

    // --------------------------------------------------------
    // --- LOGIQUE POUR ANNULER SA RÉSERVATION (PASSAGER) ---
    // --------------------------------------------------------
    public function processAnnulerReservation() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?action=login');
            exit();
        }

        $id_trajet = $_GET['id'] ?? null;
        if ($id_trajet) {
            $trajetModel = new TrajetModel();
            $trajetModel->annulerReservation($id_trajet, $_SESSION['user_id']);
            
            // (Bonus futur : c'est ici qu'on rembourserait le solde_virtuel du passager)
        }
        
        // On redirige vers l'historique de ses trajets
        header('Location: index.php?action=mes_trajets');
        exit();
    }

    // --------------------------------------------------------------
    // --- LOGIQUE POUR ÉVALUER UN TRAJET (CONDUCTEUR & PASSAGER) ---
    // --------------------------------------------------------------
    public function processEvaluerTrajet() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?action=login');
            exit();
        }

        $id_trajet = $_POST['id_trajet'] ?? null;
        $note = $_POST['note'] ?? 5;
        $commentaire = trim($_POST['commentaire'] ?? '');

        if ($id_trajet) {
            $trajetModel = new TrajetModel();
            $trajetModel->ajouterEvaluation($id_trajet, $_SESSION['user_id'], $note, $commentaire);
        }

        header('Location: index.php?action=trajet_details&id=' . $id_trajet);
        exit();
    }
}
?>
