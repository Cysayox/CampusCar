<?php
// index.php (À la racine du projet)

// 1. On démarre la session en tout premier pour savoir si l'utilisateur est connecté
session_start();

// 2. On inclut les Contrôleurs dont on va avoir besoin
require_once __DIR__ . '/controllers/HomeController.php';
require_once __DIR__ . '/controllers/AuthController.php';
require_once __DIR__ . '/controllers/ProfileController.php';
// L'ajout de ton collègue :
require_once __DIR__ . '/controllers/TrajetController.php';

// 3. On récupère l'action demandée dans l'URL (ex: index.php?action=login)
// Si aucune action n'est précisée, on définit 'accueil' par défaut
$action = $_GET['action'] ?? 'accueil';

// 4. Le fameux "Aiguillage" (Routeur)
switch ($action) {
    
    // --- ROUTES DE L'ACCUEIL ---
    case 'accueil':
        $controller = new HomeController();
        $controller->index();
        break;

    case 'recherche':
        $controller = new HomeController();
        $controller->recherche();
        break;

    // --- ROUTES DE L'AUTHENTIFICATION ---
    case 'login':
        $controller = new AuthController();
        $controller->showLogin();
        break;

    case 'login_process':
        $controller = new AuthController();
        $controller->processLogin();
        break;

    case 'logout':
        $controller = new AuthController();
        $controller->logout();
        break;

    case 'register':
        $controller = new AuthController();
        $controller->showRegisterForm();
        break;

    case 'do_register':
        $controller = new AuthController();
        $controller->doRegister();
        break;
    
    // --- ROUTES PROFIL / CONDUCTEUR ---
    case 'profil':
        $controller = new ProfileController();
        $controller->showProfil();
        break;

    case 'devenir_conducteur':
        $controller = new ProfileController();
        $controller->showDevenirConducteur();
        break;

    case 'process_devenir_conducteur':
        $controller = new ProfileController();
        $controller->processDevenirConducteur();
        break;
        
    case 'portefeuille':
        $controller = new ProfileController();
        $controller->showPortefeuille();
        break;    

    // --- ROUTES TRAJETS (L'ajout de ton collègue) ---
    case 'mes_trajets':
        $controller = new TrajetController();
        $controller->showMesTrajets();
        break;
    case 'proposer_trajet':
        $controller = new TrajetController();
        $controller->showProposerTrajet();
        break;

    case 'process_proposer_trajet':
        $controller = new TrajetController();
        $controller->processProposerTrajet();
        break;
    
    case 'trajet_details':
        $controller = new TrajetController();
        $controller->showTrajetDetails();
        break;
        
    // --- ROUTES ADMINISTRATION ---
    case 'admin_dashboard':
        require_once __DIR__ . '/controllers/AdminController.php';
        $controller = new AdminController();
        $controller->showDashboard();
        break;

    case 'admin_valider':
        require_once __DIR__ . '/controllers/AdminController.php';
        $controller = new AdminController();
        $controller->validerDemande();
        break;

    case 'admin_rejeter':
        require_once __DIR__ . '/controllers/AdminController.php';
        $controller = new AdminController();
        $controller->rejeterDemande();
        break;

    
    // --- PAGE INTROUVABLE (Erreur 404) ---
    // Le "default" doit toujours être à la fin !
    default:
        // Si l'utilisateur tape une action qui n'existe pas
        echo "<h1>Erreur 404</h1>";
        echo "<p>La page que vous recherchez n'existe pas.</p>";
        echo "<a href='index.php'>Retour à l'accueil</a>";
        break;
}
?>