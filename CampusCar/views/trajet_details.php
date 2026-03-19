<?php include __DIR__ . '/layout/header.php'; ?>

<style>
    .details-container {
        max-width: 1000px;
        margin: 40px auto; 
        background: var(--blanc);
        border-radius: 16px;
        box-shadow: 0 4px 16px rgba(0,0,0,0.08);
        border: 1px solid var(--bordure);
        display: flex; 
        overflow: hidden;
    }

    .back-link {
        display: inline-block;
        margin-bottom: 25px; 
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
    /* CSS pour le formulaire d'évaluation intégré */
    .eval-box { background-color: var(--blanc); border: 2px solid #f5b000; border-radius: 12px; padding: 20px; margin-top: 15px; }
    .rating-css { display: flex; flex-direction: row-reverse; justify-content: flex-end; margin-bottom: 10px; }
    .rating-css input { display: none; }
    .rating-css label { font-size: 32px; color: #e1e4e8; cursor: pointer; transition: 0.2s; padding: 0 2px; }
    .rating-css label:hover, .rating-css label:hover ~ label, .rating-css input:checked ~ label { color: #f5b000; }
    .eval-textarea { width: 100%; padding: 12px; border: 1px solid var(--bordure); border-radius: 8px; resize: vertical; min-height: 80px; margin-bottom: 15px; font-family: inherit;}
    .eval-textarea:focus { outline: none; border-color: #f5b000; }
    
    /* Le textarea réduit comme demandé */
    .eval-textarea { 
        width: 100%; 
        padding: 12px; 
        border: 1px solid var(--bordure); 
        border-radius: 8px; 
        resize: vertical; 
        min-height: 50px; /* <-- Réduit ici ! */
        margin-bottom: 15px; 
        font-family: inherit;
        box-sizing: border-box;
    }
    .eval-textarea:focus { outline: none; border-color: #f5b000; }
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
                    Vous êtes inscrit(e)
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
                
                // === LOGIQUE TEMPORELLE (En cours ou Passé) ===
                $maintenant = time();
                $is_past = $timestamp < ($maintenant - 4 * 3600); // Passé si + de 4 heures
                $is_en_cours = ($timestamp <= $maintenant && $timestamp >= ($maintenant - 4 * 3600)); // En cours
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

            <?php if ($trajet['nb_passagers'] > 0): ?>
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
                
                <?php if ($is_en_cours): ?>
                    
                    <?php if ($trajet['is_passenger']): ?>
                        <button id="btnPayerTrajet" class="action-btn" style="background-color: #1e8e3e; color: white; border: none; cursor: pointer;">
                            📷 Scanner pour Payer
                        </button>
                    <?php elseif ($trajet['is_driver']): ?>
                        <button id="btnPayerTrajet" class="action-btn" style="background-color: #1e8e3e; color: white; border: none; cursor: pointer;">
                            📷 Scanner le passager
                        </button>
                    <?php else: ?>
                        <div class="action-btn" style="background-color: #e1e4e8; color: #888; cursor: not-allowed;">En cours</div>
                    <?php endif; ?>

                    <script>
                        document.getElementById('btnPayerTrajet')?.addEventListener('click', function() {
                            let cameraAccess = confirm("campuscar.univ-antilles.fr souhaite accéder à votre appareil photo pour scanner le QR Code.");
                            if(cameraAccess) {
                                alert("✅ Scan réussi !\nConfirmation du paiement du trajet (<?= number_format($trajet['prix_course'], 2) ?> €).");
                                window.location.href = "index.php?action=mes_trajets";
                            } else {
                                alert("❌ Accès à la caméra refusé.");
                            }
                        });
                    </script>

                <?php elseif ($is_past): ?>
                    
                    <div class="action-btn" style="background-color: #e1e4e8; color: #888; cursor: not-allowed;">Trajet terminé</div>
                    
                    <?php if ($trajet['is_passenger'] || $trajet['is_driver']): ?>
                        
                        <?php
                        $deja_evalue = false;
                        if (!empty($evaluations_recues)) {
                            foreach ($evaluations_recues as $ev) {
                                if (isset($ev['id_evaluateur']) && $ev['id_evaluateur'] == $_SESSION['user_id']) {
                                    $deja_evalue = true;
                                    break;
                                }
                            }
                        }
                        ?>
                        
                        <?php if (!$deja_evalue): ?>
                            <div class="eval-box">
                                <h4 style="margin-top: 0; margin-bottom: 10px; color: var(--bbc-fonce);">⭐ Évaluer ce trajet</h4>
                                <form action="index.php?action=process_evaluer_trajet" method="POST">
                                    <input type="hidden" name="id_trajet" value="<?= $trajet['id_trajet'] ?>">
                                    
                                    <div class="rating-css">
                                        <input type="radio" id="star5" name="note" value="5" checked>
                                        <label for="star5" title="5 étoiles">★</label>
                                        <input type="radio" id="star4" name="note" value="4">
                                        <label for="star4" title="4 étoiles">★</label>
                                        <input type="radio" id="star3" name="note" value="3">
                                        <label for="star3" title="3 étoiles">★</label>
                                        <input type="radio" id="star2" name="note" value="2">
                                        <label for="star2" title="2 étoiles">★</label>
                                        <input type="radio" id="star1" name="note" value="1">
                                        <label for="star1" title="1 étoile">★</label>
                                    </div>
                                    
                                    <textarea name="commentaire" class="eval-textarea" placeholder="Un petit mot sur le trajet ? (Optionnel)"></textarea>
                                    
                                    <button type="submit" class="action-btn" style="background-color: #f5b000; color: white; border: none; padding: 12px; margin-bottom: 0;">
                                        Publier mon avis
                                    </button>
                                </form>
                            </div>
                        <?php else: ?>
                            <div class="action-btn" style="background-color: #eef1f2; color: var(--bbc-fonce); cursor: default; border: 1px solid var(--bordure); margin-top: 10px;">
                                ✅ Vous avez déjà évalué ce trajet.
                            </div>
                        <?php endif; ?>

                    <?php endif; ?>

                <?php else: ?>

                    <?php if ($trajet['is_driver']): ?>
                        
                        <?php if ($trajet['nb_passagers'] == 0): // On ne peut modifier que s'il n'y a pas de passagers ?>
                            <a href="index.php?action=modifier_trajet&id=<?= $trajet['id_trajet'] ?>" class="action-btn btn-edit">Modifier le trajet</a>
                        <?php endif; ?>
                        
                        <a href="index.php?action=annuler_trajet&id=<?= $trajet['id_trajet'] ?>" class="action-btn btn-cancel-resa" onclick="return confirm('Êtes-vous sûr de vouloir annuler ce trajet ? Cette action est irréversible.');">
                            Annuler le trajet
                        </a>

                    <?php elseif ($trajet['is_passenger']): ?>
                        
                        <a href="index.php?action=annuler_reservation&id=<?= $trajet['id_trajet'] ?>" class="action-btn btn-cancel-resa" onclick="return confirm('Êtes-vous sûr de vouloir annuler votre réservation ?');">
                            Annuler ma réservation
                        </a>

                    <?php elseif (($trajet['places_dispo'] - $trajet['nb_passagers']) > 0): ?>

                        <a href="index.php?action=reserver&id=<?= $trajet['id_trajet'] ?>" class="action-btn btn-reserve">
                            Réserver ce trajet (<?= ($trajet['places_dispo'] - $trajet['nb_passagers']) ?> places restantes)
                        </a>

                    <?php else: ?>
                        <div class="action-btn" style="background-color: #e1e4e8; color: #888; cursor: not-allowed;">
                            Trajet complet
                        </div>
                    <?php endif; ?>

                <?php endif; ?>
                
            </div>
            
            <?php if ($is_past): ?>
                <div style="margin-top: 30px; border-top: 1px solid var(--bordure); padding-top: 20px;">
                    <h3 style="color: var(--bbc-fonce); font-size: 18px; margin-bottom: 15px;">⭐ Avis sur ce trajet</h3>
                    
                    <?php if (empty($evaluations_recues)): ?>
                        <p style="color: var(--bbc-gris-texte); font-size: 14px; font-style: italic;">
                            Aucun avis n'a encore été publié pour ce trajet.
                        </p>
                    <?php else: ?>
                        <?php foreach ($evaluations_recues as $avis): ?>
                            
                            <?php 
                            // On détecte si c'est l'avis que MOI j'ai écrit
                            $c_est_mon_avis = (isset($avis['id_evaluateur']) && $avis['id_evaluateur'] == $_SESSION['user_id']); 
                            ?>

                            <div style="background-color: var(--blanc); border: 1px solid <?= $c_est_mon_avis ? 'var(--bbc-bleu-vif)' : 'var(--bordure)' ?>; border-radius: 12px; padding: 15px; margin-bottom: 12px; box-shadow: 0 2px 4px rgba(0,0,0,0.02);">
                                
                                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                                    <strong style="color: var(--bbc-fonce); font-size: 15px;">
                                        👤 <?= htmlspecialchars($avis['prenom'] . ' ' . $avis['nom']) ?>
                                        
                                        <?php if ($c_est_mon_avis): ?>
                                            <span style="font-size: 12px; font-weight: normal; color: var(--bbc-bleu-vif); margin-left: 8px; background-color: rgba(0, 175, 245, 0.1); padding: 2px 8px; border-radius: 10px;">
                                                Votre avis publié
                                            </span>
                                        <?php endif; ?>
                                    </strong>
                                    
                                    <span style="color: #f5b000; font-size: 18px; letter-spacing: 2px;">
                                        <?= str_repeat('★', $avis['note_etoiles']) ?><span style="color: #e1e4e8;"><?= str_repeat('★', 5 - $avis['note_etoiles']) ?></span>
                                    </span>
                                </div>
                                
                                <p style="margin: 0; color: var(--bbc-fonce); font-size: 14px; line-height: 1.5; font-style: italic;">
                                    "<?= htmlspecialchars($avis['commentaire']) ?>"
                                </p>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

        </div>
    </div>
</main>

<?php include __DIR__ . '/layout/footer.php'; ?>