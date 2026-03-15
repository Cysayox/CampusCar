<?php
// controllers/HomeController.php

require_once __DIR__ . '/../models/TrajetModel.php';
// On ajoute le nouveau modèle Campus
require_once __DIR__ . '/../models/CampusModel.php'; 

class HomeController {
    
    public function index() {
        $campusModel = new CampusModel();
        $liste_campus = $campusModel->getTousLesCampus();
        
        // On met $trajets à NULL par défaut pour ne rien afficher sur l'accueil
        $trajets = null; 
        
        require_once __DIR__ . '/../views/home.php';
    }

    public function recherche() {
        // On récupère les données du formulaire en utilisant $_GET
        $id_campus = $_GET['campus'] ?? null;
        $sens_trajet = $_GET['sens_trajet'] ?? null;
        $date_trajet = $_GET['jour_aller'] ?? null;
        $adresse = $_GET['adresse'] ?? null;

        $latitude = null;
        $longitude = null;

        // Si une adresse est fournie, on interroge l'API du gouvernement
        if ($adresse) {
            $apiUrl = 'https://api-adresse.data.gouv.fr/search/?q=' . urlencode($adresse) . '&limit=1';
            
            // On utilise file_get_contents avec un contexte pour gérer les erreurs HTTP
            $context = stream_context_create(['http' => ['ignore_errors' => true]]);
            $response = @file_get_contents($apiUrl, false, $context);

            if ($response) {
                $data = json_decode($response, true);
                if (!empty($data['features'])) {
                    // Attention: l'API renvoie [longitude, latitude]
                    $longitude = $data['features'][0]['geometry']['coordinates'][0];
                    $latitude = $data['features'][0]['geometry']['coordinates'][1];
                }
            }
        }

        // On appelle le Modèle pour faire la recherche, en passant les coordonnées
        $trajetModel = new TrajetModel();
        $trajets = $trajetModel->rechercherTrajets($id_campus, $sens_trajet, $date_trajet, $latitude, $longitude);

        // On a aussi besoin des campus pour réafficher le formulaire correctement
        $campusModel = new CampusModel();
        $liste_campus = $campusModel->getTousLesCampus();

        // On renvoie tout ça à la vue
        require_once __DIR__ . '/../views/home.php';
    }
}
?>