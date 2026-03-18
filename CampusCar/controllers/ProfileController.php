<?php
// controllers/ProfileController.php

class ProfileController {
    
    // Affiche la page Mon Profil
    public function showProfil() {
        // On vérifie que l'utilisateur est connecté
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?action=login');
            exit();
        }

        require_once __DIR__ . '/../models/UserModel.php';
        $userModel = new UserModel();
        
        // 1. On récupère les infos de base
        $infos_user = $userModel->getUtilisateurById($_SESSION['user_id']);
        
        // 2. On récupère la moyenne et le nombre d'avis
        $evaluations = $userModel->getMoyenneNotes($_SESSION['user_id']);
        
        // 3. On formate l'affichage de la note
        if ($evaluations['moyenne'] !== null) {
            $note_moyenne = round($evaluations['moyenne'], 1) . ' / 5';
            $total_avis = $evaluations['total_avis'] . ' avis';
        } else {
            $note_moyenne = "Nouveau";
            $total_avis = "0 avis";
        }

        // 4. On appelle la vue
        require_once __DIR__ . '/../views/profil.php';
    }

    // Affiche le formulaire
    public function showDevenirConducteur() {
        // On vérifie que l'utilisateur est bien connecté
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?action=login');
            exit();
        }
        require_once __DIR__ . '/../views/devenir_conducteur.php';
    }

    // Traite l'envoi du formulaire
    public function processDevenirConducteur() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?action=login');
            exit();
        }

        $date_permis = $_POST['date_permis'] ?? '';
        
        // 1. Calcul de l'ancienneté du permis
        $datePermisObj = new DateTime($date_permis);
        $aujourdhui = new DateTime();
        $interval = $aujourdhui->diff($datePermisObj);
        
        $moisAnciennete = ($interval->y * 12) + $interval->m;

        if ($moisAnciennete < 6) {
            $erreur = "Désolé, il faut un minimum de 6 mois de permis pour proposer des trajets.";
            require_once __DIR__ . '/../views/devenir_conducteur.php';
            return;
        }

        // 2. Gestion (simulée pour l'instant) de l'upload des fichiers
        // Ici, on utiliserait move_uploaded_file() en PHP
        $doc_permis = $_FILES['doc_permis']['name'] ?? 'non_fourni.pdf';
        $doc_assurance = $_FILES['doc_assurance']['name'] ?? 'non_fourni.pdf';
        $doc_carte_grise = $_FILES['doc_carte_grise']['name'] ?? 'non_fourni.pdf';

        // 3. Appel au Modèle pour insérer dans la BDD
        require_once __DIR__ . '/../models/ProfilConducteurModel.php';
        $profilModel = new ProfilConducteurModel();
        
        // La fonction va créer le profil, qui prendra automatiquement le statut 'en_attente' par défaut en BDD
        $profilCreated = $profilModel->createProfilConducteur($_SESSION['user_id'], $date_permis);

        if ($profilCreated) {
            // On enregistre dans la session qu'il a une demande en attente (pour cacher le bouton du menu)
            $_SESSION['is_driver'] = 'en_attente'; 

            $succes = "Votre demande a bien été enregistrée ! Elle est actuellement en attente de validation par un administrateur.";
            require_once __DIR__ . '/../views/devenir_conducteur.php';
        } else {
            $erreur = "Une erreur est survenue lors de l'enregistrement. Veuillez réessayer.";
            require_once __DIR__ . '/../views/devenir_conducteur.php';
        }
    }
    // Affiche la page Portefeuille
    public function showPortefeuille() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?action=login');
            exit();
        }

        require_once __DIR__ . '/../models/UserModel.php';
        $userModel = new UserModel();
        
        // On récupère les infos à jour de l'utilisateur (notamment le solde)
        $infos_user = $userModel->getUtilisateurById($_SESSION['user_id']);
        $solde = $infos_user['solde_virtuel'];

        require_once __DIR__ . '/../views/portefeuille.php';
    }
}
?>