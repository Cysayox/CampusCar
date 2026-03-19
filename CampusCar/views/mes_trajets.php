<?php include __DIR__ . '/layout/header.php'; ?>

<style>
    .page-container { max-width: 800px; margin: 40px auto; padding: 0 20px; }
    .page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
    .page-title { color: var(--bbc-fonce); font-size: 32px; font-weight: 800; margin: 0; }
    .filter-select { padding: 10px 20px; border-radius: 20px; border: 1px solid var(--bordure); font-size: 16px; font-weight: bold; color: var(--bbc-fonce); outline: none; cursor: pointer;}
    
    .trip-card-mes-trajets {
        display: block; 
        background-color: var(--blanc) !important; 
        border-radius: 16px; 
        padding: 24px;
        text-decoration: none; 
        color: var(--bbc-fonce); 
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
        margin-bottom: 16px;
        border: 1px solid var(--bordure); 
        transition: 0.2s ease;
    }
    .trip-card-mes-trajets:hover { transform: translateY(-2px); box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1); }
    
    .is-driver-card { border-left: 6px solid darkslateblue !important; }
    .is-passenger-card { border-left: 6px solid deepskyblue !important; }

    .role-badge { display: inline-block; padding: 4px 12px; border-radius: 12px; font-size: 12px; font-weight: bold; text-transform: uppercase; margin-bottom: 15px; }
    .role-conducteur { background-color: rgba(72, 61, 139, 0.15); color: darkslateblue; }
    .role-passager { background-color: rgba(0, 175, 245, 0.15); color: var(--bbc-bleu-vif); }

    /* Animation pour le point vert clignotant */
    @keyframes pulse {
        0% { box-shadow: 0 0 0 0 rgba(30, 142, 62, 0.7); }
        70% { box-shadow: 0 0 0 10px rgba(30, 142, 62, 0); }
        100% { box-shadow: 0 0 0 0 rgba(30, 142, 62, 0); }
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
    
    <?php if (!empty($trajets_en_cours)): ?>
        <div style="background-color: rgba(30, 142, 62, 0.1); border: 2px solid #1e8e3e; border-radius: 16px; padding: 20px; margin-bottom: 30px;">
            <h3 style="color: #1e8e3e; margin-top: 0; margin-bottom: 15px; display: flex; align-items: center; gap: 10px;">
                <span style="display:inline-block; width:12px; height:12px; background-color:#1e8e3e; border-radius:50%; animation: pulse 1.5s infinite;"></span>
                Trajet en cours
            </h3>
            
            <?php foreach ($trajets_en_cours as $t): ?>
                <?php renderTripCard($t); ?>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
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
    function filterTrips() {
        var selection = document.getElementById("tripFilter").value;
        localStorage.setItem("campuscar_filtre_trajets", selection); 
        document.getElementById("liste-avenir").style.display = (selection === "avenir") ? "block" : "none";
        document.getElementById("liste-passes").style.display = (selection === "passes") ? "block" : "none";
    }

    window.onload = function() {
        var choixSauvegarde = localStorage.getItem("campuscar_filtre_trajets");
        if (choixSauvegarde) {
            document.getElementById("tripFilter").value = choixSauvegarde;
            filterTrips(); 
        }
    };
</script>

<?php 
function renderTripCard($t) {
    $date_format = date('d/m/Y', strtotime($t['date_heure']));
    $heure_format = date('H\hi', strtotime($t['date_heure']));
    $is_conducteur = $t['is_mon_trajet_conducteur'] == 1;
    
    $card_class = $is_conducteur ? 'is-driver-card' : 'is-passenger-card';

    if ($t['sens_trajet'] === 'vers campus') {
        $adresse_parts = explode(',', $t['adresse_exterieure']);
        $depart = trim(preg_replace('/\s[0-9]{5}/', '', $adresse_parts[1] ?? $adresse_parts[0]));
        $arrivee = $t['nom_campus'];
    } else {
        $depart = $t['nom_campus'];
        $adresse_parts = explode(',', $t['adresse_exterieure']);
        $arrivee = trim(preg_replace('/\s[0-9]{5}/', '', $adresse_parts[1] ?? $adresse_parts[0]));
    }
?>
    <a href="index.php?action=trajet_details&id=<?= $t['id_trajet'] ?>" class="trip-card-mes-trajets <?= $card_class ?>">
        
        <span class="role-badge <?= $is_conducteur ? 'role-conducteur' : 'role-passager' ?>">
            <?= $is_conducteur ? 'Volant (Conducteur)' : 'Passager' ?>
        </span>
        
        <div class="trip-main-row" style="display:flex; justify-content:space-between; align-items:center; margin-bottom:10px;">
            <div style="font-size: 16px;"><strong>📅 <?= $date_format ?></strong> à <?= $heure_format ?></div>
            <div style="font-size: 20px; font-weight: bold;"><?= number_format($t['prix_course'], 2) ?> €</div>
        </div>
        
        <div style="border-top: 1px solid #ededed; padding-top: 12px; margin-top: 8px; color: #555; display: flex; justify-content: space-between; align-items: center;">
            
            <div style="font-weight: bold; color: var(--bbc-fonce); font-size: 15px;">
                🚗 <?= htmlspecialchars($depart) ?> <span style="color: deepskyblue; margin: 0 5px;">➔</span> <?= htmlspecialchars($arrivee) ?>
            </div>
            
            <span style="font-weight: 500; color: var(--bbc-fonce); font-size: 14px;">
                <?php if (!$is_conducteur): ?>
                    Avec <?= htmlspecialchars($t['conducteur_prenom']) ?> (Cond.)
                <?php else: ?>
                    <?php if ($t['nb_passagers'] == 0): ?>
                        <span style="color: gray;">En attente...</span>
                    <?php elseif ($t['nb_passagers'] == 1): ?>
                        Avec <?= htmlspecialchars($t['premier_passager_prenom']) ?>
                    <?php else: ?>
                        👥 <?= $t['nb_passagers'] ?> passagers
                    <?php endif; ?>
                <?php endif; ?>
            </span>
            
        </div>
    </a>
<?php } ?>

<?php include __DIR__ . '/layout/footer.php'; ?>