<?php
// controllers/ProfileController.php

class ProfileController {
    
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

        // 3. Appel futur au Modèle pour insérer dans la BDD
        // $profilModel = new ProfilModel();
        // $profilModel->creerProfilConducteur($_SESSION['user_id'], $date_permis, $doc_permis, ...);

        $succes = "Félicitations ! Vos documents sont valides (Permis > 6 mois). Votre profil conducteur est activé.";
        require_once __DIR__ . '/../views/devenir_conducteur.php';
    }
}
?>