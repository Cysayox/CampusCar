<?php include __DIR__ . '/layout/header.php'; ?>

<style>
    /* En-tête bleu style Izly */
    .wallet-header {
        background-color: deepskyblue;
        color: white;
        text-align: center;
        padding: 50px 20px;
        margin-bottom: 40px;
    }

    .wallet-header h2 {
        font-size: 28px;
        margin-bottom: 10px;
        font-weight: 700;
    }

    .wallet-header p {
        font-size: 16px;
        max-width: 600px;
        margin: 0 auto;
        opacity: 0.9;
    }

    /* Bloc d'actions principales (Solde + Boutons) */
    .wallet-actions {
        background-color: var(--blanc);
        max-width: 800px;
        margin: -80px auto 40px auto;
        padding: 30px;
        border-radius: 12px;
        box-shadow: 0 8px 24px rgba(0,0,0,0.1);
        display: flex;
        justify-content: space-between;
        align-items: center;
        border: 1px solid var(--bordure);
        flex-wrap: wrap; /* Utile pour les petits écrans */
        gap: 20px;
    }

    .current-balance {
        font-size: 36px;
        font-weight: 800;
        color: var(--bbc-fonce);
    }

    /* Conteneur pour aligner les deux boutons */
    .wallet-buttons-container {
        display: flex;
        gap: 15px;
    }

    /* Bouton Scanner (Style secondaire, fond blanc et contour bleu) */
    .btn-scan {
        background-color: var(--blanc);
        color: var(--bbc-fonce);
        padding: 14px 24px;
        border-radius: 24px;
        font-size: 16px;
        font-weight: bold;
        border: 2px solid var(--bbc-fonce);
        cursor: pointer;
        transition: 0.2s;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .btn-scan:hover {
        background-color: var(--bordure);
        transform: translateY(-2px);
    }

    /* Bouton Payer (Style principal, fond bleu foncé) */
    .btn-pay {
        background-color: var(--bbc-fonce);
        color: white;
        padding: 14px 24px;
        border-radius: 24px;
        font-size: 16px;
        font-weight: bold;
        border: 2px solid var(--bbc-fonce);
        cursor: pointer;
        transition: 0.2s;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .btn-pay:hover {
        background-color: #032b32;
        transform: translateY(-2px);
    }

    /* Grille de rechargement (Style Izly) */
    .recharge-section {
        max-width: 1000px;
        margin: 0 auto 60px auto;
        padding: 0 20px;
    }

    .recharge-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
        gap: 20px;
    }

    .recharge-card {
        background: var(--blanc);
        border-radius: 8px;
        padding: 25px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        display: flex;
        align-items: center;
        gap: 20px;
        cursor: pointer;
        border: 1px solid var(--bordure);
        transition: 0.2s;
    }

    .recharge-card:hover {
        box-shadow: 0 8px 16px rgba(0,0,0,0.1);
        border-color: deepskyblue;
    }

    .recharge-card h4 {
        margin: 0;
        color: var(--bbc-fonce);
        font-size: 16px;
    }

    .recharge-icon {
        font-size: 30px;
        color: deepskyblue;
    }

    /* === MODAL (Popup) POUR LE QR CODE === */
    .modal {
        display: none; 
        position: fixed; 
        z-index: 1000; 
        left: 0;
        top: 0;
        width: 100%; 
        height: 100%; 
        background-color: rgba(0,0,0,0.6); 
        backdrop-filter: blur(4px);
    }

    .modal-content {
        background-color: #fefefe;
        margin: 10% auto; 
        padding: 30px;
        border-radius: 16px;
        width: 90%;
        max-width: 400px;
        text-align: center;
        box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        margin: 0; /* On retire l'ancien margin */
    }

    .close-btn {
        color: #aaa;
        position: absolute;
        top: 15px;
        right: 20px;
        font-size: 28px;
        font-weight: bold;
        cursor: pointer;
    }

    .close-btn:hover {
        color: var(--bbc-fonce);
    }

    .qr-image {
        width: 100%;
        max-width: 250px;
        margin: 20px 0;
        border: 10px solid white;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
</style>

<main>
    <div class="wallet-header">
        <h2>RECHARGER</h2>
        <p>Rechargez votre compte CampusCar à tout moment de façon simple et sécurisée, par virement immédiat, par carte bancaire ou espèces.</p>
    </div>

    <div class="wallet-actions">
        <div>
            <div style="color: var(--bbc-gris-texte); font-weight: bold; font-size: 14px; text-transform: uppercase;">Solde Actuel</div>
            <div class="current-balance"><?= number_format($solde, 2, ',', ' ') ?> €</div>
        </div>
        
        <div class="wallet-buttons-container">
            <button class="btn-scan" id="btnScanner">
                Scanner
            </button>
            <button class="btn-pay" id="btnPayer">
                 QR Code
            </button>
        </div>
    </div>

    <section class="recharge-section">
        <div class="recharge-grid">
            <div class="recharge-card">
                <div class="recharge-icon"><img src="assets/images/billet.svg" alt="Logo billet"></div>
                <h4>Rechargement par virement immédiat sécurisé <br>(à partir de 5€)</h4>
            </div>
            <div class="recharge-card">
                <div class="recharge-icon"><img src="assets/images/carte_bancaire.svg" alt="Logo carte bancaire"></div>
                <h4>Rechargement par carte bancaire <br>(à partir de 10 €)</h4>
            </div>
            <div class="recharge-card">
                <div class="recharge-icon"><img src="assets/images/tiers.svg" alt="Logo tiers"></div>
                <h4>Rechargement par un tiers</h4>
            </div>
            <div class="recharge-card">
                <div class="recharge-icon"><img src="assets/images/campus.svg" alt="Logo campus"></div>
                <h4>Rechargement sur Campus</h4>
            </div>
        </div>
    </section>

    <div id="qrModal" class="modal">
        <div class="modal-content">
            <span class="close-btn" id="closeModal">&times;</span>
            <h3 style="color: var(--bbc-fonce); margin-top: 0;">Mon QR Code</h3>
            <p style="color: var(--bbc-gris-texte); font-size: 14px;">Présentez ce QR Code au passager pour qu'il le scanne et valide le paiement de la course.</p>
            
            <img src="assets/images/qrcode.png" alt="QR Code de paiement" class="qr-image">
            
            <p style="font-weight: bold; font-size: 18px; color: deepskyblue;">Solde : <?= number_format($solde, 2, ',', ' ') ?> €</p>
        </div>
    </div>
</main>

<script>
    // --- GESTION DU BOUTON PAYER (QR CODE) ---
    var modal = document.getElementById("qrModal");
    var btnPayer = document.getElementById("btnPayer");
    var span = document.getElementById("closeModal");

    btnPayer.onclick = function() {
        modal.style.display = "block";
    }

    span.onclick = function() {
        modal.style.display = "none";
    }

    window.onclick = function(event) {
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }

    // --- GESTION DU BOUTON SCANNER (SIMULATION CAMERA) ---
    var btnScanner = document.getElementById("btnScanner");
    
    btnScanner.onclick = function() {
        // La fonction confirm() du navigateur ressemble à une demande d'autorisation
        let cameraAccess = confirm("campuscar.univ-antilles.fr souhaite accéder à votre appareil photo.");
        
        if(cameraAccess) {
            // Si l'utilisateur clique sur "OK"
            alert("Caméra activée. En attente du scan d'un QR Code...");
        } else {
            // Si l'utilisateur clique sur "Annuler"
            alert("Accès à la caméra refusé.");
        }
    }
</script>

<?php include __DIR__ . '/layout/footer.php'; ?>
