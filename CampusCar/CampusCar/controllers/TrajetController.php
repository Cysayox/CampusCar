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
}
?>
