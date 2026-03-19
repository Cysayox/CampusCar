<?php include __DIR__ . '/layout/header.php'; ?>

<style>
    .details-container {
        max-width: 1000px;
        margin: 40px auto; /* On remet la marge normale */
        background: var(--blanc);
        border-radius: 16px;
        box-shadow: 0 4px 16px rgba(0,0,0,0.08);
        border: 1px solid var(--bordure);
        display: flex; 
        overflow: hidden;
    }

    /* --- LE BOUTON RETOUR INTÉGRÉ --- */
    .back-link {
        display: inline-block;
        margin-bottom: 25px; /* Pousse le contenu vers le bas */
        color: var(--bbc-bleu-vif);
        text-decoration: none;
        font-weight: bold;
        font-size: 16px;
        transition: color 0.2s;
    }
    .back-link:hover {
        color: var(--bbc-fonce);
        text-decoration: underline;
    }

    .details-left { flex: 2; padding: 40px; border-right: 1px solid var(--bordure); }
    .details-right { flex: 1; padding: 40px; background-color: #f9fafb; display: flex; flex-direction: column; }

    @media (max-width: 768px) {
        .details-container { flex-direction: column; }
        .details-left { border-right: none; border-bottom: 1px solid var(--bordure); }
    }

    .driver-info { display: flex; align-items: center; gap: 15px; padding: 15px; background-color: var(--gris-fond); border-radius: 12px; margin-top: 30px; }

    .action-btn { display: block; width: 100%; padding: 16px; border-radius: 24px; font-size: 16px; font-weight: bold; text-align: center; text-decoration: none; margin-bottom: 12px; transition: 0.2s; box-sizing: border-box; }
    .btn-reserve { background-color: var(--bbc-bleu-vif); color: white; border: none; }
    .btn-reserve:hover { background-color: var(--bbc-bleu-hover); }
    .btn-edit { background-color: white; color: var(--bbc-fonce); border: 2px solid var(--bbc-fonce); }
    .btn-edit:hover { background-color: #eef1f2; }
    .btn-cancel-resa { background-color: #fce8e8; color: #e55353; border: 1px solid #fad2d2; }
    .btn-cancel-resa:hover { background-color: #f9d6d6; }

    .status-badge { display: inline-block; padding: 8px 16px; border-radius: 20px; font-weight: bold; margin-bottom: 20px; }

    .passenger-list { background-color: var(--blanc); border: 1px solid var(--bordure); border-radius: 12px; padding: 15px; margin-bottom: 30px; }
    .passenger-item { padding: 10px 0; border-bottom: 1px solid #e1e4e8; display: flex; align-items: center; gap: 10px; font-weight: 500; color: var(--bbc-fonce); }
    .passenger-item:last-child { border-bottom: none; }
</style>

<main>
    <div class="details-container">
        
        <div class="details-left">
            
            <a href="javascript:history.back()" class="back-link">← Retour</a><br>

            <?php if ($trajet['is_driver']): ?>
                <div class="status-badge" style="background-color: rgba(72, 61, 139, 0.15); color: darkslateblue;">
                    🚗 C'est votre annonce
                </div>
            <?php elseif ($trajet['is_passenger']): ?>
                <div class="status-badge" style="background-color: rgba(0, 175, 245, 0.15); color: var(--bbc-bleu-vif);">
                    ✅ Vous êtes inscrit(e)
                </div>
            <?php endif; ?>

            <?php 
                $jours_fr = ['Sunday' => 'Dimanche', 'Monday' => 'Lundi', 'Tuesday' => 'Mardi', 'Wednesday' => 'Mercredi', 'Thursday' => 'Jeudi', 'Friday' => 'Vendredi', 'Saturday' => 'Samedi'];
                $mois_fr = ['January' => 'janvier', 'February' => 'février', 'March' => 'mars', 'April' => 'avril', 'May' => 'mai', 'June' => 'juin', 'July' => 'juillet', 'August' => 'août', 'September' => 'septembre', 'October' => 'octobre', 'November' => 'novembre', 'December' => 'décembre'];
                
                $timestamp = strtotime($trajet['date_heure']);
                $nom_jour = $jours_fr[date('l', $timestamp)];
                $num_jour = date('d', $timestamp);
                $nom_mois = $mois_fr[date('F', $timestamp)];
                
                $date_format_fr = "$nom_jour $num_jour $nom_mois";
                $heure_format = date('H\hi', $timestamp);
                
                // --- ON VÉRIFIE SI LE TRAJET EST PASSÉ ---
                $is_past = $timestamp < time();
            ?>

            <h2 style="margin: 0 0 5px 0; color: var(--bbc-fonce); font-size: 28px;"><?= $date_format_fr ?></h2>
            <div style="font-size: 20px; font-weight: 600; color: var(--bbc-gris-texte); margin-bottom: 30px;">
                Départ à <?= $heure_format ?>
            </div>

            <div style="margin-bottom: 24px;">
                <h3 style="margin-top: 0; color: var(--bbc-fonce);">Itinéraire</h3>
                <div style="margin-top: 15px; padding-left: 20px; border-left: 4px solid var(--bbc-bleu-vif);">
                    <p style="margin: 0 0 15px 0; font-size: 16px;">
                        <span style="display:inline-block; width: 10px; height: 10px; border-radius: 50%; border: 2px solid var(--bbc-bleu-vif); margin-left: -27px; background: white; margin-right: 10px;"></span>
                        <?= $trajet['sens_trajet'] == 'vers campus' ? htmlspecialchars($trajet['adresse_exterieure']) : htmlspecialchars($trajet['nom_campus']) ?>
                    </p>
                    <p style="margin: 0; font-weight: bold; font-size: 16px;">
                        <span style="display:inline-block; width: 10px; height: 10px; border-radius: 50%; border: 2px solid var(--bbc-bleu-vif); margin-left: -27px; background: var(--bbc-bleu-vif); margin-right: 10px;"></span>
                        <?= $trajet['sens_trajet'] == 'vers campus' ? htmlspecialchars($trajet['nom_campus']) : htmlspecialchars($trajet['adresse_exterieure']) ?>
                    </p>
                </div>
            </div>

            <div class="driver-info">
                <div style="font-size: 40px;">👤</div>
                <div>
                    <h3 style="margin: 0; color: var(--bbc-fonce);"><?= htmlspecialchars($trajet['conducteur_prenom']) ?> <?= htmlspecialchars($trajet['conducteur_nom']) ?></h3>
                    <div style="color: #f5b000; font-weight: bold; margin-top: 4px;">
                        ★ <?= $trajet['nb_avis'] > 0 ? number_format($trajet['vraie_note'], 1) : 'Nouveau' ?> 
                        <span style="color: var(--bbc-gris-texte); font-size: 14px; font-weight: normal;">(<?= $trajet['nb_avis'] ?> avis)</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="details-right">
            
            <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #e1e4e8; padding-bottom: 20px; margin-bottom: 20px;">
                <span style="font-size: 18px; color: var(--bbc-fonce); font-weight: bold;">Prix total</span>
                <span style="font-size: 32px; font-weight: 800; color: var(--bbc-bleu-vif);"><?= number_format($trajet['prix_course'], 2) ?> €</span>
            </div>

            <div style="margin-bottom: 20px; font-size: 15px; color: var(--bbc-fonce);">
                <strong>Places occupées :</strong> <?= $trajet['nb_passagers'] ?> sur <?= $trajet['places_dispo'] ?>
            </div>

            <?php if ($trajet['is_driver'] && $trajet['nb_passagers'] > 0): ?>
                <div class="passenger-list">
                    <strong style="display: block; margin-bottom: 10px; color: var(--bbc-fonce);">Passagers inscrits :</strong>
                    <?php foreach ($passagers_list as $p): ?>
                        <div class="passenger-item">
                            <span>👤</span> <?= htmlspecialchars($p['prenom'] . ' ' . $p['nom']) ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <div style="margin-top: auto;">
                <?php if ($is_past): ?>
                    <div class="action-btn" style="background-color: #e1e4e8; color: #888; cursor: not-allowed;">Trajet terminé</div>
                
                <?php else: ?>
                    
                    <?php if (!$trajet['is_driver'] && !$trajet['is_passenger']): ?>
                        <?php if ($trajet['nb_passagers'] < $trajet['places_dispo']): ?>
                            <a href="index.php?action=reserver&id=<?= $trajet['id_trajet'] ?>" class="action-btn btn-reserve">Réserver ce trajet</a>
                        <?php else: ?>
                            <div class="action-btn" style="background-color: #e1e4e8; color: #888; cursor: not-allowed;">Complet</div>
                        <?php endif; ?>
                    
                    <?php elseif ($trajet['is_driver']): ?>
                        <?php if ($trajet['nb_passagers'] == 0): ?>
                            <a href="index.php?action=modifier_trajet&id=<?= $trajet['id_trajet'] ?>" class="action-btn btn-edit">✏️ Modifier le trajet</a>
                        <?php else: ?>
                            <div class="action-btn" style="background-color: #e1e4e8; color: #888; cursor: not-allowed;" title="Impossible de modifier un trajet ayant déjà des passagers.">✏️ Modifier le trajet</div>
                        <?php endif; ?>
                        <a href="index.php?action=annuler_trajet&id=<?= $trajet['id_trajet'] ?>" class="action-btn btn-cancel-resa" onclick="return confirm('Voulez-vous vraiment annuler ce trajet ?');">❌ Annuler le trajet</a>

                    <?php elseif ($trajet['is_passenger']): ?>
                        <a href="index.php?action=annuler_reservation&id=<?= $trajet['id_trajet'] ?>" class="action-btn btn-cancel-resa" onclick="return confirm('Voulez-vous vraiment annuler votre place ?');">❌ Annuler ma réservation</a>
                    <?php endif; ?>
                    
                <?php endif; ?>
            </div>

        </div>
    </div>
</main>

<?php include __DIR__ . '/layout/footer.php'; ?>
