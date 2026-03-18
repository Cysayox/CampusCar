<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Inscription Temporaire - CampusCar</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        main {
            max-width: 400px;
            margin: 40px auto;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 8px;
        }
        .input-group { margin-bottom: 15px; }
        .input-group label { display: block; margin-bottom: 5px; }
        .input-group input[type="text"],
        .input-group input[type="password"],
        .input-group input[type="date"] {
            width: 100%;
            padding: 8px;
            box-sizing: border-box; /* Assure que le padding n'augmente pas la largeur */
        }
        .input-group .checkbox-label {
            display: flex;
            align-items: center;
            cursor: pointer;
        }
        .input-group input[type="checkbox"] {
            width: auto;
            margin-right: 10px;
        }
        .btn-submit {
            width: 100%;
            padding: 12px;
            background-color: var(--bbc-bleu-vif);
            color: var(--blanc);
            border: none;
            border-radius: 24px;
            cursor: pointer;
            font-size: 16px;
            font-weight: 700;
            transition: background-color 0.2s;
        }
        .btn-submit:hover {
            background-color: var(--bbc-bleu-hover);
        }
    </style>
</head>
<body>
    <header><h1><a href="index.php">CampusCar</a> - Inscription</h1></header>
    <main>
        <form action="index.php?action=do_register" method="POST">
            <div class="input-group">
                <label for="id_sesame">ID Sésame (email étudiant)</label>
                <input type="text" id="id_sesame" name="id_sesame" required>
            </div>
            <div class="input-group">
                <label for="mot_de_passe">Mot de passe</label>
                <input type="password" id="mot_de_passe" name="mot_de_passe" required>
            </div>
            <div class="input-group">
                <label for="nom">Nom</label>
                <input type="text" id="nom" name="nom" required>
            </div>
            <div class="input-group">
                <label for="prenom">Prénom</label>
                <input type="text" id="prenom" name="prenom" required>
            </div>
            <div class="input-group">
                <label class="checkbox-label">
                    <input type="checkbox" id="is_driver" name="is_driver" value="1">
                    Devenir conducteur
                </label>
            </div>
            <div class="input-group" id="driver_fields" style="display: none;">
                <label for="date_permis">Date d'obtention du permis</label>
                <input type="date" id="date_permis" name="date_permis">
            </div>
            <button type="submit" class="btn-submit">Créer le compte</button>
        </form>
    </main>

    <script>
        document.getElementById('is_driver').addEventListener('change', function() {
            var driverFields = document.getElementById('driver_fields');
            var datePermisInput = document.getElementById('date_permis');
            if (this.checked) {
                driverFields.style.display = 'block';
                datePermisInput.required = true;
            } else {
                driverFields.style.display = 'none';
                datePermisInput.required = false;
            }
        });
    </script>

</body>
</html>
