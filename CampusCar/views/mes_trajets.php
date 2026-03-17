<?php include __DIR__ . '/layout/header.php'; ?>

<style>
    .page-container { max-width: 800px; margin: 40px auto; padding: 0 20px; }
    .page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
    .page-title { color: var(--bbc-fonce); font-size: 32px; font-weight: 800; margin: 0; }
    .filter-select { padding: 10px 20px; border-radius: 20px; border: 1px solid var(--bordure); font-size: 16px; font-weight: bold; color: var(--bbc-fonce); outline: none; }
    
    .trip-card-mes-trajets {
        display: block; background-color: var(--blanc); border-radius: 16px; padding: 24px;
        text-decoration: none; color: var(--bbc-fonce); box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
        margin-bottom: 16px; border: 1px solid var(--bordure); transition: 0.2s ease;
    }
    .trip-card-mes-trajets:hover { transform: translateY(-2px); box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1); border-color: deepskyblue; }
    
    .role-badge { display: inline-block; padding: 4px 12px; border-radius: 12px; font-size: 12px; font-weight: bold; text-transform: uppercase; margin-bottom: 15px; }
    /* Couleur "Campus" (Dark Slate Blue) pour le Conducteur */
    .role-conducteur { 
        background-color: rgba(72, 61, 139, 0.15); /* Fond transparent bleuté */
        color: darkslateblue; 
    }
    
    /* Couleur "Car" (Bleu clair/vif) pour le Passager */
    .role-passager { 
        background-color: rgba(0, 175, 245, 0.15); 
        color: var(--bbc-bleu-vif); 
    }
</style>

<main class="page-container">
    <div class="page-header">
        <h2 class="page-title">Vos trajets</h2>
        <select class="filter-select" id="tripFilter" onchange="filterTrips()">
            <option value="avenir">À venir</option>
            <option value="passes">Historique (Passés)</option>
        </select>
    </div>

    <div id="liste-avenir">
        <?php if (empty($trajets_a_venir)): ?>
            <div style="text-align:center; padding: 40px; color: gray;">Vous n'avez aucun trajet prévu.</div>
        <?php else: foreach ($trajets_a_venir as $t): renderTripCard($t); endforeach; endif; ?>
    </div>

    <div id="liste-passes" style="display: none;">
        <?php if (empty($trajets_passes)): ?>
            <div style="text-align:center; padding: 40px; color: gray;">Votre historique est vide.</div>
        <?php else: foreach ($trajets_passes as $t): renderTripCard($t); endforeach; endif; ?>
    </div>
</main>

<script>
    // Fonction appelée quand on change le menu déroulant
    function filterTrips() {
        var selection = document.getElementById("tripFilter").value;
        
        // 1. On sauvegarde le choix dans la mémoire du navigateur
        localStorage.setItem("campuscar_filtre_trajets", selection);
        
        // 2. On affiche/masque les bonnes listes
        document.getElementById("liste-avenir").style.display = (selection === "avenir") ? "block" : "none";
        document.getElementById("liste-passes").style.display = (selection === "passes") ? "block" : "none";
    }

    // 3. Dès que la page est chargée (après un rafraîchissement par exemple)
    window.onload = function() {
        // On vérifie s'il y a un choix qui avait été sauvegardé précédemment
        var choixSauvegarde = localStorage.getItem("campuscar_filtre_trajets");
        
        if (choixSauvegarde) {
            // On remet le menu déroulant sur la bonne valeur ("avenir" ou "passes")
            document.getElementById("tripFilter").value = choixSauvegarde;
            
            // On déclenche l'affichage correspondant
            filterTrips();
        }
    };
</script>

<?php 
function renderTripCard($t) {
    $date_format = date('d/m/Y', strtotime($t['date_heure']));
    $heure_format = date('H\hi', strtotime($t['date_heure']));
    $is_conducteur = $t['is_mon_trajet_conducteur'] == 1;
?>
    <a href="index.php?action=trajet_details&id=<?= $t['id_trajet'] ?>" class="trip-card-mes-trajets">
        <span class="role-badge <?= $is_conducteur ? 'role-conducteur' : 'role-passager' ?>">
            <?= $is_conducteur ? 'Conducteur' : 'Passager' ?>
        </span>
        <div class="trip-main-row" style="display:flex; justify-content:space-between; align-items:center; margin-bottom:10px;">
            <div style="font-size: 16px;"><strong>📅 <?= $date_format ?></strong> à <?= $heure_format ?></div>
            <div style="font-size: 20px; font-weight: bold;"><?= number_format($t['prix_course'], 2) ?> €</div>
        </div>
        <div style="border-top: 1px solid #ededed; padding-top: 12px; margin-top: 8px; color: #555; display: flex; justify-content: space-between; align-items: center;">
            <span>🚗 <?= htmlspecialchars($t['nom_campus']) ?></span>
            
            <span style="font-weight: 500; color: var(--bbc-fonce);">
                <?php if (!$is_conducteur): ?>
                    Avec <?= htmlspecialchars($t['conducteur_prenom']) ?> <?= htmlspecialchars($t['conducteur_nom']) ?>
                <?php else: ?>
                    <?php if ($t['nb_passagers'] == 0): ?>
                        <span style="color: gray;">En attente de passagers...</span>
                    <?php elseif ($t['nb_passagers'] == 1): ?>
                        Avec <?= htmlspecialchars($t['premier_passager_prenom']) ?> <?= htmlspecialchars($t['premier_passager_nom']) ?>
                    <?php else: ?>
                        👥 <?= $t['nb_passagers'] ?> passagers
                    <?php endif; ?>
                <?php endif; ?>
            </span>
        </div>
    </a>
<?php } ?>

<?php include __DIR__ . '/layout/footer.php'; ?>