<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CampusCar - Le covoiturage de l'UA</title>
    <link rel="stylesheet" href="assets/css/style.css"> 
</head>
<body>

   <header>
        <a href="index.php?action=accueil" class="logo-link">
            <h1><span style="color: darkslateblue;">Campus</span><span style="color: deepskyblue;">Car</span></h1>
        </a>

        <div class="header-actions">
            <?php if (isset($_SESSION['user_id'])): ?>
                <span>Bonjour, <strong><?= htmlspecialchars($_SESSION['user_nom']) ?></strong></span>
                
                <?php if (isset($_SESSION['user_solde']) && $_SESSION['user_role'] !== 'admin'): ?>
                    <span class="solde-badge">💳 <?= number_format($_SESSION['user_solde'], 2, ',', ' ') ?> €</span>
                <?php endif; ?>

                <?php if (isset($_SESSION['is_driver']) && $_SESSION['is_driver'] == 1): ?>
                    <a href="index.php?action=proposer_trajet" class="btn-header" style="border: 2px solid var(--bbc-bleu-vif); color: var(--bbc-bleu-vif);">➕ Proposer un trajet</a>
                <?php endif; ?>

                <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'): ?>
                    <a href="index.php?action=admin_dashboard" class="btn-header" style="background-color: #ffc107; color: #000; font-weight: bold;">⚙️ Panel Admin</a>
                <?php endif; ?>

                <div class="profile-container">
                    <img src="assets/images/avatar.svg" alt="Mon compte" class="profile-icon" onclick="toggleProfileMenu()" title="Mon compte" style="object-fit: cover;">
                    
                    <div id="profileMenu" class="dropdown-content">
                        <a href="index.php?action=profil">👤 Mon Profil</a>
                        
                        <?php if ($_SESSION['user_role'] !== 'admin'): ?>
                            <a href="index.php?action=portefeuille">💳 Portefeuille</a>
                            <a href="index.php?action=mes_trajets">🚗 Mes Trajets</a>
                            
                            <?php if (!isset($_SESSION['is_driver']) || $_SESSION['is_driver'] == 0): ?>
                                <a href="index.php?action=devenir_conducteur">📝 Devenir Conducteur</a>
                            <?php endif; ?>
                            
                        <?php endif; ?>
                        
                        <a href="index.php?action=logout" class="logout-text">🚪 Se déconnecter</a>
                    </div>
                </div>

            <?php else: ?>
                <a href="index.php?action=login" class="btn-login-gradient">Se connecter</a>
            <?php endif; ?>
        </div>
    </header>