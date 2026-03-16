<?php include __DIR__ . '/layout/header.php'; ?>

<style>
    .form-container {
        background-color: var(--blanc);
        padding: 40px;
        border-radius: 16px; /* Arrondi style BlaBlaCar */
        box-shadow: 0 4px 16px rgba(0,0,0,0.08); /* Ombre douce */
        width: 100%;
        max-width: 500px;
        margin: 60px auto; /* Centré verticalement et horizontalement */
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
    
    .form-subtitle {
        text-align: center;
        color: var(--bbc-gris-texte);
        font-size: 14px;
        margin-bottom: 30px;
    }

    .form-group {
        margin-bottom: 24px;
    }

    .form-group label {
        display: block;
        margin-bottom: 8px;
        font-weight: 700;
        color: var(--bbc-fonce);
        font-size: 14px;
    }

    /* Style des inputs calqué sur ta barre de recherche */
    .form-group input[type="date"], 
    .form-group input[type="file"] {
        width: 100%;
        padding: 12px 16px;
        border: 1px solid var(--bordure);
        border-radius: 8px;
        box-sizing: border-box;
        font-size: 15px;
        color: var(--bbc-fonce);
        transition: border-color 0.2s;
    }

    .form-group input:focus {
        outline: none;
        border-color: var(--bbc-bleu-vif);
    }

    .alert {
        padding: 16px;
        border-radius: 8px;
        margin-bottom: 24px;
        text-align: center;
        font-weight: 600;
        font-size: 14px;
    }
    
    .alert-error { 
        background-color: #fce8e8; 
        color: #e55353; 
        border: 1px solid #fad2d2;
    }
    
    .alert-success { 
        background-color: #e6f7ed; 
        color: #1e8e3e; 
        border: 1px solid #c2e8cf;
    }

    .action-buttons {
        display: flex;
        gap: 16px;
        margin-top: 32px;
    }

    /* Ton bouton "Annuler" */
    .btn-cancel {
        flex: 1;
        background-color: var(--blanc);
        color: var(--bbc-fonce);
        text-align: center;
        padding: 14px;
        border-radius: 24px;
        text-decoration: none;
        font-weight: 700;
        font-size: 16px;
        border: 1px solid var(--bordure);
        transition: 0.2s;
    }
    
    .btn-cancel:hover {
        background-color: var(--bordure);
    }
    
    /* Adaptation du bouton btn-submit pour qu'il prenne 50% de la largeur */
    .btn-submit-half {
        flex: 1;
        margin: 0; /* On annule la marge que tu avais mise dans style.css */
    }
</style>

<main>
    <div class="form-container">
        <h2>🚗 Devenir Conducteur</h2>
        <p class="form-subtitle">Partagez vos frais de route en toute sécurité.</p>

        <?php if (isset($erreur)): ?>
            <div class="alert alert-error"><?= htmlspecialchars($erreur) ?></div>
        <?php endif; ?>

        <?php if (isset($succes)): ?>
            <div class="alert alert-success"><?= htmlspecialchars($succes) ?></div>
            <div style="text-align: center; margin-top: 24px;">
                <a href="index.php?action=accueil" class="btn-submit" style="text-decoration: none;">Proposer un trajet</a>
            </div>
        <?php else: ?>

            <form action="index.php?action=process_devenir_conducteur" method="POST" enctype="multipart/form-data">
                
                <div class="form-group">
                    <label>Date d'obtention du permis (6 mois minimum)</label>
                    <input type="date" name="date_permis" required max="<?= date('Y-m-d') ?>">
                </div>

                <div class="form-group">
                    <label>Copie du Permis de conduire (PDF/Image)</label>
                    <input type="file" name="doc_permis" accept=".pdf, image/jpeg, image/png" required>
                </div>

                <div class="form-group">
                    <label>Attestation d'assurance en cours</label>
                    <input type="file" name="doc_assurance" accept=".pdf, image/jpeg, image/png" required>
                </div>

                <div class="form-group">
                    <label>Carte grise du véhicule</label>
                    <input type="file" name="doc_carte_grise" accept=".pdf, image/jpeg, image/png" required>
                </div>

                <div class="action-buttons">
                    <a href="index.php?action=accueil" class="btn-cancel">Annuler</a>
                    <button type="submit" class="btn-submit btn-submit-half">Soumettre</button>
                </div>
            </form>

        <?php endif; ?>
    </div>
</main>

<?php include __DIR__ . '/layout/footer.php'; ?>