<?php include __DIR__ . '/layout/header.php'; ?>

<style>
    .profile-card {
        background-color: var(--blanc);
        padding: 40px;
        border-radius: 16px;
        box-shadow: 0 4px 16px rgba(0,0,0,0.08);
        width: 100%;
        max-width: 600px;
        margin: 60px auto;
        border: 1px solid var(--bordure);
        text-align: center;
    }

    /* Le gros avatar au centre */
    .profile-avatar-large {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        background-color: var(--bbc-fond);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border: 4px solid var(--blanc);
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        margin-bottom: 20px;
        font-size: 50px;
    }

    .profile-name {
        color: var(--bbc-fonce);
        font-size: 28px;
        font-weight: 800;
        margin: 0 0 10px 0;
    }

    .profile-role {
        display: inline-block;
        background-color: rgba(0, 175, 245, 0.1); /* Bleu BlaBlaCar très clair */
        color: var(--bbc-bleu-vif);
        padding: 6px 16px;
        border-radius: 20px;
        font-weight: 700;
        font-size: 14px;
        text-transform: uppercase;
        letter-spacing: 1px;
        margin-bottom: 30px;
    }

    .profile-stats-container {
        display: flex;
        justify-content: space-around;
        border-top: 1px solid var(--bordure);
        padding-top: 30px;
        margin-top: 20px;
    }

    .stat-box {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .stat-value {
        font-size: 24px;
        font-weight: 800;
        color: var(--bbc-fonce);
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
    }

    .stat-label {
        font-size: 14px;
        color: var(--bbc-gris-texte);
        font-weight: 500;
    }
</style>

<main>
    <div class="profile-card">
        
        <img src="assets/images/avatar_profil.svg" alt="Photo de profil" class="profile-avatar-large" style="object-fit: cover;">

        <h2 class="profile-name">
            <?= htmlspecialchars($infos_user['prenom']) ?> <?= htmlspecialchars($infos_user['nom']) ?>
        </h2>

        <div class="profile-role">
            <?= htmlspecialchars($infos_user['role']) ?>
        </div>

        <div class="profile-stats-container">
            <div class="stat-box">
                <div class="stat-value">
                    <span style="color: #f5b000;">★</span> <?= htmlspecialchars($note_moyenne) ?>
                </div>
                <div class="stat-label"><?= htmlspecialchars($total_avis) ?></div>
            </div>

            <div class="stat-box">
                <div class="stat-value">
                    💳 <?= number_format($infos_user['solde_virtuel'], 2, ',', ' ') ?> €
                </div>
                <div class="stat-label">Solde actuel</div>
            </div>
        </div>

        <div style="margin-top: 40px;">
            <a href="index.php" class="btn-cancel" style="display: inline-block; padding: 12px 30px; width: auto;">Retour à l'accueil</a>
        </div>

    </div>
</main>

<?php include __DIR__ . '/layout/footer.php'; ?>
