<?php
// controllers/AuthController.php

// On inclut le Modèle pour pouvoir interroger la base de données
require_once __DIR__ . '/../models/UserModel.php';

class AuthController {
    
    // 1. Affiche simplement la page de connexion (la Vue)
    public function showLogin() {
        require_once __DIR__ . '/../views/login_sesame.php';
    }

    // 2. Traite les données quand l'étudiant clique sur "Se connecter"
    public function processLogin() {
        // On récupère les données du formulaire
        $id_sesame = $_POST['id_sesame'] ?? '';
        $mot_de_passe = $_POST['mot_de_passe'] ?? '';

        // Vérification basique : est-ce que les champs sont remplis ?
        if (empty($id_sesame) || empty($mot_de_passe)) {
            $erreur = "Veuillez remplir tous les champs.";
            require_once __DIR__ . '/../views/login_sesame.php';
            return; // On arrête l'exécution de la fonction ici
        }

        // On fait appel au Modèle pour interroger la vraie base de données
        $userModel = new UserModel();
        $user = $userModel->verifierUtilisateur($id_sesame, $mot_de_passe);

        if ($user) {
            // C'est le bon mot de passe ! On enregistre les infos dans la session
            // (Pas de session_start() ici car le routeur index.php s'en occupe déjà !)
            
            $_SESSION['user_id'] = $user['id_utilisateur'];
            $_SESSION['user_sesame'] = $user['id_sesame'];
            $_SESSION['user_nom'] = $user['prenom'] . ' ' . $user['nom'];
            $_SESSION['user_solde'] = $user['solde_virtuel'];
            $_SESSION['user_role'] = $user['role'] ?? 'etudiant'; 
            
            // --- NOUVEAUTÉ ---
            // On mémorise si l'utilisateur est un conducteur (1) ou non (0)
            $_SESSION['is_driver'] = $user['is_driver']; 

            // On redirige vers la page d'accueil de CampusCar
            header('Location: index.php');
            exit();
        
        } else {
            // Mauvais identifiants (mot de passe faux ou compte inexistant)
            $erreur = "Identifiant Sésame ou mot de passe incorrect.";
            require_once __DIR__ . '/../views/login_sesame.php';
        }
    }

    // 3. Pour se déconnecter
    public function logout() {
        // On détruit toutes les variables de session
        $_SESSION = array();
        session_destroy();
        
        // On redirige vers la page de connexion au lieu de l'accueil !
        header('Location: index.php?action=login');
        exit();
    }

    // Affiche la page d'inscription
    public function showRegisterForm() {
        require_once __DIR__ . '/../views/register.php';
    }

    // Traite les données du formulaire d'inscription
    public function doRegister() {
        $id_sesame = $_POST['id_sesame'] ?? '';
        $mot_de_passe = $_POST['mot_de_passe'] ?? '';
        $nom = $_POST['nom'] ?? '';
        $prenom = $_POST['prenom'] ?? '';
        $is_driver = isset($_POST['is_driver']);
        $date_permis = $_POST['date_permis'] ?? '';

        if (empty($id_sesame) || empty($mot_de_passe) || empty($nom) || empty($prenom) || ($is_driver && empty($date_permis))) {
            // Si un champ obligatoire est manquant (y compris la date du permis si conducteur)
            header('Location: index.php?action=register&error=1');
            exit();
        }

        $userModel = new UserModel();
        $newUserId = $userModel->creerUtilisateur($id_sesame, $mot_de_passe, $nom, $prenom);

        if ($newUserId) {
            // L'utilisateur a bien été créé. Doit-on aussi créer un profil conducteur ?
            if ($is_driver) {
                require_once __DIR__ . '/../models/ProfilConducteurModel.php';
                $profilModel = new ProfilConducteurModel();
                $profilCreated = $profilModel->createProfilConducteur($newUserId, $date_permis);

                if (!$profilCreated) {
                    // Optionnel : gérer le cas où le profil conducteur n'a pas pu être créé
                    // Par exemple, en supprimant l'utilisateur ou en affichant une erreur spécifique.
                    // Pour l'instant, on redirige vers le login en considérant l'inscription principale comme réussie.
                }
            }
            
            // Tout s'est bien passé, on redirige vers la page de connexion
            header('Location: index.php?action=login&success=1');
            exit();

        } else {
            // L'utilisateur n'a pas pu être créé (peut-être un id_sesame dupliqué)
            header('Location: index.php?action=register&error=2');
            exit();
        }
    }
}
?>
