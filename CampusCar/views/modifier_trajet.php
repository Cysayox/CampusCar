<?php include __DIR__ . '/layout/header.php'; ?>

<style>
    .form-container {
        background-color: var(--blanc);
        padding: 40px;
        border-radius: 16px;
        box-shadow: 0 4px 16px rgba(0,0,0,0.08);
        width: 100%;
        max-width: 600px;
        margin: 40px auto;
        border: 1px solid var(--bordure);
    }
    
    .form-container h2 {
        color: var(--bbc-fonce);
        text-align: center;
        margin-top: 0;
        margin-bottom: 10px;
        font-weight: 800;
        font-size: 24px;
    }

    .form-group { margin-bottom: 20px; }
    .form-group label { display: block; margin-bottom: 8px; font-weight: 700; color: var(--bbc-fonce); font-size: 14px; }
    .form-group input, .form-group select {
        width: 100%; padding: 12px 16px; border: 1px solid var(--bordure);
        border-radius: 8px; box-sizing: border-box; font-size: 15px; color: var(--bbc-fonce);
        transition: border-color 0.2s;
    }
    .form-group input:focus, .form-group select:focus { outline: none; border-color: var(--bbc-bleu-vif); }
    .grid-2-cols { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; }
</style>

<main>
    <div class="form-container">
        <h2>✏️ Modifier l'annonce</h2>
        <p style="text-align: center; color: var(--bbc-gris-texte); margin-bottom: 30px;">Mettez à jour les informations de votre trajet.</p>

        <form action="index.php?action=process_modifier_trajet" method="POST">
            
            <input type="hidden" name="id_trajet" value="<?= htmlspecialchars($trajet['id_trajet']) ?>">

            <div class="form-group">
                <label>Sens du trajet</label>
                <select name="sens_trajet" required>
                    <option value="vers campus" <?= $trajet['sens_trajet'] == 'vers campus' ? 'selected' : '' ?>>Aller vers le campus (Je pars de chez moi)</option>
                    <option value="depuis campus" <?= $trajet['sens_trajet'] == 'depuis campus' ? 'selected' : '' ?>>Partir du campus (Je rentre chez moi)</option>
                </select>
            </div>

            <div class="form-group">
                <label>Campus concerné</label>
                <select name="campus" required>
                    <option value="">Sélectionnez le campus...</option>
                    <?php foreach ($liste_campus as $c): ?>
                        <option value="<?= htmlspecialchars($c['id_campus']) ?>" <?= $trajet['id_campus_cible'] == $c['id_campus'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($c['nom_campus']) ?> (<?= htmlspecialchars($c['pole_geographique']) ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label>Adresse du point de rencontre (hors campus)</label>
                <input type="text" name="adresse" list="liste-communes" value="<?= htmlspecialchars($trajet['adresse_exterieure']) ?>" required autocomplete="off">
                <datalist id="liste-communes"></datalist>
            </div>

            <?php 
                // On sépare la date et l'heure venant de la base de données
                $date_pre_remplie = date('Y-m-d', strtotime($trajet['date_heure']));
                $heure_pre_remplie = date('H:i', strtotime($trajet['date_heure']));
            ?>

            <div class="grid-2-cols">
                <div class="form-group">
                    <label>Date du trajet</label>
                    <input type="date" name="date_trajet" min="<?= date('Y-m-d') ?>" value="<?= $date_pre_remplie ?>" required>
                </div>
                <div class="form-group">
                    <label>Heure de départ</label>
                    <input type="time" name="heure_trajet" value="<?= $heure_pre_remplie ?>" required>
                </div>
            </div>

            <div class="grid-2-cols">
                <div class="form-group">
                    <label>Places disponibles</label>
                    <input type="number" name="places_dispo" min="1" max="4" value="<?= htmlspecialchars($trajet['places_dispo']) ?>" required>
                </div>
                <div class="form-group">
                    <label>Prix par passager (€)</label>
                    <input type="number" name="prix_course" min="0" max="5" step="0.50" value="<?= htmlspecialchars($trajet['prix_course']) ?>" required>
                </div>
            </div>

            <div style="display: flex; gap: 16px; margin-top: 30px;">
                <a href="index.php?action=trajet_details&id=<?= $trajet['id_trajet'] ?>" class="btn-submit" style="flex: 1; background-color: var(--blanc); color: var(--bbc-fonce); text-align: center; text-decoration: none; border: 1px solid var(--bordure);">Annuler</a>
                <button type="submit" class="btn-submit" style="flex: 1; margin: 0;">Enregistrer</button>
            </div>
        </form>
    </div>
</main>

<script>
    // Même script d'autocomplétion API Gouv que sur l'autre page
    const inputAdresse = document.querySelector('input[name="adresse"]');
    const datalistCommunes = document.getElementById('liste-communes');

    inputAdresse.addEventListener('input', async function(e) {
        const saisie = e.target.value;
        if (saisie.length >= 3) {
            try {
                const apiURL = `https://api-adresse.data.gouv.fr/search/?q=${encodeURIComponent(saisie)}&autocomplete=1&limit=5`;
                const reponse = await fetch(apiURL);
                const data = await reponse.json();
                
                datalistCommunes.innerHTML = '';
                
                if (data.features) {
                    const adressesAntilles = data.features.filter(lieu => {
                        const cp = lieu.properties.postcode;
                        return cp && (cp.startsWith('971') || cp.startsWith('972'));
                    });

                    adressesAntilles.forEach(lieu => {
                        const option = document.createElement('option');
                        option.value = lieu.properties.label; 
                        datalistCommunes.appendChild(option);
                    });
                }
            } catch (erreur) {
                console.error(erreur);
            }
        }
    });
</script>

<?php include __DIR__ . '/layout/footer.php'; ?>