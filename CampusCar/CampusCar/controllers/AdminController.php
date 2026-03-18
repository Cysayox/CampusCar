<?php
// controllers/AdminController.php
require_once __DIR__ . '/../models/ProfilConducteurModel.php';

class AdminController {
    
    // 🔒 Fonction de sécurité stricte : on expulse ceux qui ne sont pas admin
    private function checkAdmin() {
        if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
            header('Location: index.php?action=accueil');
            exit();
        }
    }

    // 1. Afficher le tableau de bord
    public function showDashboard() {
        $this->checkAdmin(); // On vérifie la sécurité en premier
        
        $profilModel = new ProfilConducteurModel();
        $demandes = $profilModel->getDemandesEnAttente(); // On récupère les profils "en_attente"
        
        require_once __DIR__ . '/../views/admin_dashboard.php';
    }

    // 2. Traiter le clic sur "Valider"
    public function validerDemande() {
        $this->checkAdmin();
        $id_profil = $_GET['id'] ?? null;
        
        if ($id_profil) {
            $profilModel = new ProfilConducteurModel();
            $profilModel->validerDemande($id_profil);
        }
        
        // On redirige vers le panel avec un message de succès
        header('Location: index.php?action=admin_dashboard&success=valide');
        exit();
    }

    // 3. Traiter le clic sur "Rejeter"
    public function rejeterDemande() {
        $this->checkAdmin();
        $id_profil = $_GET['id'] ?? null;
        
        if ($id_profil) {
            $profilModel = new ProfilConducteurModel();
            $profilModel->rejeterDemande($id_profil);
        }
        
        // On redirige vers le panel avec un message de rejet
        header('Location: index.php?action=admin_dashboard&success=rejete');
        exit();
    }
}
?>