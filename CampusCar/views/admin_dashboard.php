<?php include __DIR__ . '/layout/header.php'; ?>

<style>
    .admin-container {
        max-width: 1000px;
        margin: 40px auto;
        padding: 30px;
        background: var(--blanc);
        border-radius: 16px;
        box-shadow: 0 4px 16px rgba(0,0,0,0.08);
        border: 1px solid var(--bordure);
    }
    
    .admin-title {
        color: var(--bbc-fonce);
        border-bottom: 2px solid var(--bordure);
        padding-bottom: 15px;
        margin-top: 0;
        margin-bottom: 20px;
    }
    
    .table-demandes {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
    }
    
    .table-demandes th, .table-demandes td {
        padding: 15px;
        text-align: left;
        border-bottom: 1px solid var(--bordure);
        color: var(--bbc-fonce);
    }
    
    .table-demandes th {
        background-color: var(--bbc-fond);
        color: var(--bbc-gris-texte);
        font-weight: 700;
        border-radius: 8px 8px 0 0;
    }
    
    .btn-action {
        padding: 8px 16px;
        border-radius: 20px;
        text-decoration: none;
        font-weight: bold;
        font-size: 14px;
        display: inline-block;
        transition: 0.2s;
    }
    
    .btn-valider {
        background-color: #e6f7ed;
        color: #1e8e3e;
        border: 1px solid #c2e8cf;
        margin-right: 10px;
    }
    
    .btn-valider:hover { background-color: #1e8e3e; color: white; }
    
    .btn-rejeter {
        background-color: #fce8e8;
        color: #e55353;
        border: 1px solid #fad2d2;
    }
    
    .btn-rejeter:hover { background-color: #e55353; color: white; }
    
    .alert-msg {
        padding: 12px 20px;
        border-radius: 8px;
        margin-bottom: 20px;
        font-weight: 600;
    }
</style>

<main>
    <div class="admin-container">
        <h2 class="admin-title">Demandes Conducteurs en attente</h2>

        <?php if (isset($_GET['success']) && $_GET['success'] == 'valide'): ?>
            <div class="alert-msg" style="background: #e6f7ed; color: #1e8e3e;">✅ Le profil a bien été validé ! L'étudiant peut maintenant publier des trajets.</div>
        <?php elseif (isset($_GET['success']) && $_GET['success'] == 'rejete'): ?>
            <div class="alert-msg" style="background: #fce8e8; color: #e55353;">🗑️ La demande a été rejetée et supprimée de la base de données.</div>
        <?php endif; ?>

        <?php if (empty($demandes)): ?>
            <div style="text-align: center; padding: 60px 20px; color: var(--bbc-gris-texte);">
                <h3 style="margin:0 0 10px 0;">Aucune demande en attente </h3>
                <p style="margin:0;">Votre boîte de réception est vide.</p>
            </div>
        <?php else: ?>
            <table class="table-demandes">
                <thead>
                    <tr>
                        <th>Étudiant</th>
                        <th>ID Sésame</th>
                        <th>Date du permis</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($demandes as $d): ?>
                    <tr>
                        <td><strong><?= htmlspecialchars($d['prenom'] . ' ' . $d['nom']) ?></strong></td>
                        <td><?= htmlspecialchars($d['id_sesame']) ?></td>
                        <td><?= date('d/m/Y', strtotime($d['date_permis'])) ?></td>
                        <td>
                            <a href="index.php?action=admin_valider&id=<?= $d['id_profil'] ?>" class="btn-action btn-valider" onclick="return confirm('Valider ce conducteur ?');">Valider</a>
                            <a href="index.php?action=admin_rejeter&id=<?= $d['id_profil'] ?>" class="btn-action btn-rejeter" onclick="return confirm('Êtes-vous sûr de vouloir rejeter et supprimer cette demande ?');">Rejeter</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>

    </div>
</main>

<?php include __DIR__ . '/layout/footer.php'; ?>