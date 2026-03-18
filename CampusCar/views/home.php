<?php include __DIR__ . '/layout/header.php'; ?>

<style>
    /* Surlignage léger pour la page d'accueil / recherche */
    .trip-card.is-driver-highlight {
        border-left: 6px solid darkslateblue;
        background-color: rgba(72, 61, 139, 0.03);
    }
    
    .trip-card.is-passenger-highlight {
        border-left: 6px solid deepskyblue;
        background-color: rgba(0, 175, 245, 0.03);
    }
</style>

<main>
    <section class="hero-banner">
        
        <div class="hero-content" style="flex-direction: column; align-items: center; gap: 20px;">
            <h1 style="margin-bottom: 0;">CampusCar<br>L'appli de covoiturage pour les étudiants</h1>
        </div>

        <form class="search-bar" action="index.php" method="GET" id="searchForm" style="max-width: 1200px;">
            <input type="hidden" name="action" value="recherche">   
            
            <div class="input-group" id="bloc-adresse" style="flex: 2.5; position: relative;">
                <label id="label-adresse">Départ</label>
                <input type="text" id="input-adresse" name="adresse" placeholder="Ex: Fort-de-France, Lamentin..." required autocomplete="off" value="<?= htmlspecialchars($adresse ?? '') ?>">
                
                <div id="suggestions-adresse" class="custom-dropdown"></div>
            </div>

            <button type="button" class="btn-swap" id="swapBtn" title="Inverser le sens du trajet">⇄</button>

            <div class="input-group" id="bloc-campus" style="flex: 2.5;">
                <label id="label-campus">Destination</label>
                <select name="campus" required>
                    <option value="">Sélectionnez un campus...</option>
                    
                    <?php 
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

            <div class="input-group" style="flex: 0 0 90px; border-right: none;">
                <label>Passager(s)</label>
                <input type="number" name="passagers" min="1" max="4" value="1">
            </div>

            <button type="submit" class="btn-submit">Rechercher</button>

            <input type="hidden" name="sens_trajet" id="sens_trajet" value="vers campus">
        </form>
    </section>

    <?php if ($trajets !== null): ?>
    <div class="results-container" style="margin-top: 40px;"> <h2 style="text-align: center; margin-bottom: 20px; color: var(--bbc-fonce);">Trajets disponibles</h2>
        
        <?php 
        if (count($trajets) > 0): 
            foreach ($trajets as $t): 
                $heure_depart = date('H\hi', strtotime($t['date_heure']));
                
                if ($t['sens_trajet'] === 'vers campus') {
                    $adresse_parts = explode(',', $t['adresse_exterieure']);
                    $depart = trim(preg_replace('/\s[0-9]{5}/', '', $adresse_parts[1] ?? $adresse_parts[0]));
                    $arrivee = $t['nom_campus'];
                } else {
                    $depart = $t['nom_campus'];
                    $adresse_parts = explode(',', $t['adresse_exterieure']);
                    $arrivee = trim(preg_replace('/\s[0-9]{5}/', '', $adresse_parts[1] ?? $adresse_parts[0]));
                }

                // LOGIQUE DE SURLIGNAGE (Bandes de couleurs)
                $highlight_class = '';
                if (isset($_SESSION['user_id'])) {
                    if ($t['id_conducteur'] == $_SESSION['user_id']) {
                        $highlight_class = 'is-driver-card'; // Bande Violette (Défini dans style.css ou le header)
                    } else {
                        // Petite vérification rapide pour voir s'il est passager de ce trajet
                        require_once __DIR__ . '/../models/Database.php';
                        $db_check = (new Database())->getConnection();
                        $stmt_check = $db_check->prepare("SELECT id_reservation FROM reserver WHERE id_trajet = ? AND id_passager = ?");
                        $stmt_check->execute([$t['id_trajet'], $_SESSION['user_id']]);
                        if ($stmt_check->rowCount() > 0) {
                            $highlight_class = 'is-passenger-card'; // Bande Bleu Ciel
                        }
                    }
                }
        ?>
        
            <a href="index.php?action=trajet_details&id=<?= $t['id_trajet'] ?>" class="trip-card <?= $highlight_class ?>" style="<?= $highlight_class === 'is-driver-card' ? 'border-left: 6px solid darkslateblue; background-color: rgba(72, 61, 139, 0.02);' : ($highlight_class === 'is-passenger-card' ? 'border-left: 6px solid deepskyblue; background-color: rgba(0, 175, 245, 0.02);' : '') ?>">
                
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
                        <span style="color: #f5b000; font-size: 16px;">★</span> 
                        <?= number_format($t['note_moyenne'] ?? 5, 1, ',', '') ?> 
                        <span style="color: var(--bbc-gris-texte); font-weight: normal; font-size: 13px;">(<?= $t['nb_avis'] ?? 0 ?>)</span>
                    </div>
                </div>
            </a>
        <?php endforeach; else: ?>
            <div class="no-results">
                <h3>🚗 Aucun trajet trouvé</h3>
                <p>Essayez de modifier vos critères de recherche ou la date.</p>
            </div>
        <?php 
        endif; 
        ?>
    </div>
    <?php else: ?>
    <div class="results-container" style="margin-top: 40px;">
        <div class="no-results">
            <h3>Prêt à partir ?</h3>
            <p>Lance la recherche pour trouver un covoiturage.</p>
        </div>
    </div>
    <?php endif; ?>
</main>

<script>
    const swapBtn = document.getElementById('swapBtn');
    const blocAdresse = document.getElementById('bloc-adresse');
    const blocCampus = document.getElementById('bloc-campus');
    const labelAdresse = document.getElementById('label-adresse');
    const labelCampus = document.getElementById('label-campus');
    const sensTrajetInput = document.getElementById('sens_trajet');

    let versCampus = true; 

    swapBtn.addEventListener('click', () => {
        if (versCampus) {
            blocCampus.parentNode.insertBefore(blocCampus, blocAdresse);
            blocCampus.parentNode.insertBefore(swapBtn, blocAdresse);
            labelCampus.innerText = "Départ";
            labelAdresse.innerText = "Destination";
            sensTrajetInput.value = "depuis campus";
        } else {
            blocAdresse.parentNode.insertBefore(blocAdresse, blocCampus);
            blocAdresse.parentNode.insertBefore(swapBtn, blocCampus);
            labelAdresse.innerText = "Départ";
            labelCampus.innerText = "Destination";
            sensTrajetInput.value = "vers campus";
        }
        versCampus = !versCampus; 
    });

    const sensActuel = "<?= htmlspecialchars($_GET['sens_trajet'] ?? 'vers campus') ?>";
    if (sensActuel === 'depuis campus') {
        swapBtn.click();
    }

    const inputAdresse = document.getElementById('input-adresse');
    const boxSuggestions = document.getElementById('suggestions-adresse');
    let timeoutRecherche; 

    inputAdresse.addEventListener('input', function(e) {
        clearTimeout(timeoutRecherche); 
        
        const saisie = e.target.value;
        
        if (saisie.length >= 3) {
            timeoutRecherche = setTimeout(async () => {
                try {
                    const apiURL = `https://api-adresse.data.gouv.fr/search/?q=${encodeURIComponent(saisie)}&lat=14.6&lon=-61.0&autocomplete=1&limit=5`;
                    const reponse = await fetch(apiURL);
                    const data = await reponse.json();
                    
                    boxSuggestions.innerHTML = '';
                    let hasResults = false;

                    if (data.features) {
                        const adressesAntilles = data.features.filter(lieu => {
                            const cp = lieu.properties.postcode;
                            return cp && (cp.startsWith('971') || cp.startsWith('972'));
                        });

                        adressesAntilles.forEach(lieu => {
                            hasResults = true;
                            const divItem = document.createElement('div');
                            divItem.className = 'custom-dropdown-item';
                            divItem.innerHTML = `${lieu.properties.label}`; 
                            
                            divItem.addEventListener('click', function() {
                                inputAdresse.value = lieu.properties.label;
                                boxSuggestions.style.display = 'none';
                            });
                            
                            boxSuggestions.appendChild(divItem);
                        });
                    }
                    boxSuggestions.style.display = hasResults ? 'block' : 'none';
                } catch (erreur) {
                    console.error(erreur);
                }
            }, 300); 
        } else {
            boxSuggestions.style.display = 'none';
        }
    });

    document.addEventListener('click', function(e) {
        if (e.target !== inputAdresse && e.target !== boxSuggestions) {
            boxSuggestions.style.display = 'none';
        }
    });
</script>

<?php include __DIR__ . '/layout/footer.php'; ?>