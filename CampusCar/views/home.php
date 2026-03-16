<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CampusCar - Le covoiturage de l'UA</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

   <?php include __DIR__ . '/layout/header.php'; ?>

    <main>
        <section class="hero-banner">
            
            <div class="hero-content">
                <h1>CampusCar<br>L'appli de covoiturage pour les étudiants</h1>
            </div>

            <form class="search-bar" action="index.php" method="GET" id="searchForm">
                <input type="hidden" name="action" value="recherche">   
                
                <div class="input-group" id="bloc-adresse">
                    <label id="label-adresse">Départ</label>
                    <input type="text" name="adresse" list="liste-communes" placeholder="Ex: Fort-de-France, Lamentin..." required autocomplete="off" value="<?= htmlspecialchars($adresse ?? '') ?>">
                    
                    <datalist id="liste-communes">
                        <option value="Fort-de-France">
                        <option value="Schœlcher">
                        <option value="Le Lamentin">
                        <option value="Saint-Joseph">
                        <option value="Le Robert">
                        <option value="Le François">
                        <option value="Ducos">
                        <option value="Sainte-Luce">
                        <option value="Sainte-Marie">
                        <option value="Trinité">
                        <option value="Le Marin">
                        <option value="Rivière-Salée">
                        
                        <option value="Pointe-à-Pitre">
                        <option value="Les Abymes">
                        <option value="Baie-Mahault">
                        <option value="Le Gosier">
                        <option value="Sainte-Anne">
                        <option value="Saint-François">
                        <option value="Le Moule">
                        <option value="Morne-à-l'Eau">
                        <option value="Petit-Bourg">
                        <option value="Sainte-Rose">
                        <option value="Basse-Terre">
                        <option value="Saint-Claude">
                    </datalist>
                </div>

                <button type="button" class="btn-swap" id="swapBtn" title="Inverser le sens du trajet">⇄</button>

                <div class="input-group" id="bloc-campus">
                    <label id="label-campus">Destination</label>
                    <select name="campus" required>
                        <option value="">Sélectionnez un campus...</option>
                        
                        <?php 
                        // On boucle sur la liste des campus envoyée par le Contrôleur
                        if (!empty($liste_campus)): 
                            foreach ($liste_campus as $c): 
                        ?>
                            <option value="<?= htmlspecialchars($c['id_campus']) ?>" <?= (isset($id_campus) && $id_campus == $c['id_campus']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($c['nom_campus']) ?> (<?= htmlspecialchars($c['pole_geographique']) ?>)
                            </option>
                        <?php 
                            endforeach; 
                        endif; 
                        ?>
                        
                    </select>
                </div>

                <div class="input-group">
                    <label>Aller</label>
                    <input type="date" name="jour_aller" value="<?= htmlspecialchars($date_trajet ?? date('Y-m-d')) ?>" required>
                </div>

                <div class="input-group">
                    <label>Retour <span style="font-weight: normal; font-size: 10px; color: #888;">(Optionnel)</span></label>
                    <input type="date" name="jour_retour">
                </div>

                <div class="input-group" style="min-width: 80px;">
                    <label>Passager(s)</label>
                    <input type="number" name="passagers" min="1" max="4" value="1">
                </div>

                <button type="submit" class="btn-submit">Rechercher</button>

                <input type="hidden" name="sens_trajet" id="sens_trajet" value="vers campus">
            </form>
        </section>

        <?php 
        // On n'affiche cette zone QUE si une recherche a été lancée ($trajets n'est pas null)
        if ($trajets !== null): 
        ?>
        <div class="results-container" style="margin-top: 40px;"> <h2 style="text-align: center; margin-bottom: 20px; color: var(--bbc-fonce);">Trajets disponibles</h2>
            
            <?php 
            if (count($trajets) > 0): 
                foreach ($trajets as $t): 
                    // Formatage de l'heure avec le "h" (ex: 10h30)
                    $heure_depart = date('H\hi', strtotime($t['date_heure']));
                    
                    // Définir le Départ et l'Arrivée
                    if ($t['sens_trajet'] === 'vers campus') {
                        // On extrait la commune de l'adresse, en supposant le format "Rue, Ville CP"
                        $adresse_parts = explode(',', $t['adresse_exterieure']);
                        // On prend la 2ème partie, on enlève le code postal (5 chiffres) et on nettoie
                        $depart = trim(preg_replace('/\s[0-9]{5}/', '', $adresse_parts[1] ?? $adresse_parts[0]));
                        $arrivee = $t['nom_campus'];
                    } else {
                        $depart = $t['nom_campus'];
                        $adresse_parts = explode(',', $t['adresse_exterieure']);
                        // Même logique pour l'arrivée
                        $arrivee = trim(preg_replace('/\s[0-9]{5}/', '', $adresse_parts[1] ?? $adresse_parts[0]));
                    }
            ?>
                <a href="index.php?action=trajet_details&id=<?= $t['id_trajet'] ?>" class="trip-card">
                    
                    <div class="trip-main-row">
                        <div class="trip-route-horizontal">
                            <span><span class="city-name"><?= htmlspecialchars($depart) ?></span> <?= $heure_depart ?></span>
                            <span class="route-line-dashed"></span>
                            <span class="city-name"><?= htmlspecialchars($arrivee) ?></span>
                        </div>
                        <div class="trip-price">
                            <?= number_format($t['prix_course'], 0) ?> €
                        </div>
                    </div>

                    <div class="trip-driver-simple">
                        <div style="display: flex; align-items: center; gap: 10px;">
                            <img src="assets/images/avatar.svg" alt="Photo de profil" class="driver-avatar" style="width:32px; height:32px; border-radius: 50%; object-fit: cover; border: 1px solid #ededed;">
                            <span><?= htmlspecialchars($t['prenom'] . ' ' . $t['nom']) ?></span>
                        </div>
                        
                        <div class="driver-rating" style="display: flex; align-items: center; gap: 4px; font-weight: 600;">
                            <span style="color: #f5b000; font-size: 16px;">★</span> 4.8 <span style="color: var(--bbc-gris-texte); font-weight: normal; font-size: 13px;">(12)</span>
                        </div>
                    </div>

                </a>
            <?php 
                endforeach; 
            else: 
            ?>
                <div class="no-results">
                    <h3>🚗 Aucun trajet trouvé</h3>
                    <p>Essayez de modifier vos critères de recherche ou la date.</p>
                </div>
            <<?php 
            endif; 
            ?>
        </div>
        <?php 
        // SINON : Aucune recherche n'a été lancée (Affichage par défaut)
        else: 
        ?>
        <div class="results-container" style="margin-top: 40px;">
            <div class="no-results">
                <h3>Prêt à partir ?</h3>
                <p>Lance la recherche pour trouver un covoiturage.</p>
            </div>
        </div>
        <?php endif; // Fin du bloc conditionnel global ?>
    </main>

    <?php include __DIR__ . '/layout/footer.php'; ?>

    <script>
        const swapBtn = document.getElementById('swapBtn');
        const blocAdresse = document.getElementById('bloc-adresse');
        const blocCampus = document.getElementById('bloc-campus');
        const labelAdresse = document.getElementById('label-adresse');
        const labelCampus = document.getElementById('label-campus');
        const sensTrajetInput = document.getElementById('sens_trajet');

        let versCampus = true; // Par défaut, on va VERS le campus

        swapBtn.addEventListener('click', () => {
            // Logique visuelle : on inverse l'ordre dans le DOM (HTML)
            if (versCampus) {
                // On met le campus en premier (Départ) et l'adresse en second (Destination)
                blocCampus.parentNode.insertBefore(blocCampus, blocAdresse);
                blocCampus.parentNode.insertBefore(swapBtn, blocAdresse);
                
                // On change les labels
                labelCampus.innerText = "Départ";
                labelAdresse.innerText = "Destination";
                
                // On met à jour le champ caché pour le Contrôleur
                sensTrajetInput.value = "depuis campus";
            } else {
                // On remet l'adresse en premier (Départ) et le campus en second (Destination)
                blocAdresse.parentNode.insertBefore(blocAdresse, blocCampus);
                blocAdresse.parentNode.insertBefore(swapBtn, blocCampus);
                
                // On change les labels
                labelAdresse.innerText = "Départ";
                labelCampus.innerText = "Destination";
                
                // On met à jour le champ caché pour le Contrôleur
                sensTrajetInput.value = "vers campus";
            }
            versCampus = !versCampus; // On inverse la variable
        });
    
    // Fonction pour ouvrir/fermer le menu de profil
    function toggleProfileMenu() {
        document.getElementById("profileMenu").classList.toggle("show-dropdown");
    }

    // Fermer le menu si l'utilisateur clique n'importe où ailleurs sur la page
    window.onclick = function(event) {
        if (!event.target.matches('.profile-icon')) {
            var dropdowns = document.getElementsByClassName("dropdown-content");
            for (var i = 0; i < dropdowns.length; i++) {
                var openDropdown = dropdowns[i];
                if (openDropdown.classList.contains('show-dropdown')) {
                    openDropdown.classList.remove('show-dropdown');
                }
            }
        }
    }
</script>
</body>
</html>