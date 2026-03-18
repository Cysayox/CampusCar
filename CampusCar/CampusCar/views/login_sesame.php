<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Authentification Sésame - Université des Antilles</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            /* L'image de fond qui prend toute la page */
            background-image: url('assets/images/login-bg.jpeg'); 
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            
            /* Couleur de secours si l'image met du temps à charger */
            background-color: #0a2342; 
        }
        
        .login-container {
            /* Fond blanc transparent (60% d'opacité) */
            background-color: rgba(255, 255, 255, 0.60); 
            
            /* L'effet magique "Verre dépoli" (floute ce qui est derrière) */
            backdrop-filter: blur(8px); 
            -webkit-backdrop-filter: blur(8px);
            
            padding: 40px;
            border-radius: 12px; /* Un peu plus arrondi */
            box-shadow: 0 8px 32px rgba(0,0,0,0.3); /* Ombre plus diffuse pour faire ressortir la carte */
            width: 100%;
            max-width: 400px;
            border-top: 5px solid #0a2342; /* Le bleu marine de l'UA */
        }
        
        .login-header {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .login-header h1 {
            color: #0a2342;
            font-size: 24px;
            margin-bottom: 5px;
            font-weight: 800;
        }
        
        .login-header p {
            color: #555;
            font-size: 14px;
            margin: 0;
            font-weight: 500;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #222;
            font-weight: bold;
            font-size: 14px;
        }
        
        .form-group input {
            width: 100%;
            padding: 12px;
            border: 1px solid #ced4da;
            border-radius: 6px;
            box-sizing: border-box;
            font-size: 16px;
            background-color: rgba(255, 255, 255, 0.9); /* Champs très légèrement transparents aussi */
        }
        
        .form-group input:focus {
            outline: none;
            border-color: #0a2342;
            box-shadow: 0 0 0 3px rgba(10, 35, 66, 0.2);
        }
        
        .btn-login {
            width: 100%;
            padding: 14px;
            background-color: #0a2342;
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: background-color 0.3s, transform 0.1s;
        }
        
        .btn-login:hover {
            background-color: #1d4e89;
        }
        
        .btn-login:active {
            transform: scale(0.98); /* Petit effet de clic */
        }
        
        .error-message {
            background-color: rgba(248, 215, 218, 0.9);
            color: #721c24;
            padding: 12px;
            border-radius: 6px;
            margin-bottom: 20px;
            font-size: 14px;
            text-align: center;
            border: 1px solid #f5c6cb;
        }
        
        .return-link {
            display: block;
            text-align: center;
            margin-top: 24px;
            color: #0a2342;
            text-decoration: none;
            font-size: 14px;
            font-weight: 600;
            transition: color 0.2s;
        }
        
        .return-link:hover {
            text-decoration: underline;
            color: #00aff5; /* Rappel du bleu BlaBlaCar au survol */
        }
    </style>
</head>
<body>

<div class="login-container">
    <div class="login-header">
        <h1>Service d'Authentification</h1>
        <p>Université des Antilles (Sésame)</p>
    </div>

    <?php if (isset($erreur)): ?>
        <div class="error-message">
            <?= htmlspecialchars($erreur) ?>
        </div>
    <?php endif; ?>

    <form action="index.php?action=login_process" method="POST">
        <div class="form-group">
            <label for="id_sesame">Identifiant Sésame</label>
            <input type="text" id="id_sesame" name="id_sesame" placeholder="ex: jean.dupont" required>
        </div>
        
        <div class="form-group">
            <label for="mot_de_passe">Mot de passe</label>
            <input type="password" id="mot_de_passe" name="mot_de_passe" required>
        </div>
        
        <button type="submit" class="btn-login">Se connecter</button>
    </form>

    <a href="index.php" class="return-link">← Retour à CampusCar</a>
</div>

</body>
</html>