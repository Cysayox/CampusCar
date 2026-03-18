<footer class="site-footer">
        <div class="footer-container">
            <div class="footer-column brand-col">
                <h3><span style="color: darkslateblue;">Campus</span><span style="color: deepskyblue;">Car</span></h3>
                <p>L'application de covoiturage exclusive aux étudiants de l'Université des Antilles. Partagez vos trajets, faites des économies et protégez la planète.</p>
            </div>
            
            <div class="footer-column">
                <h4>Découvrir</h4>
                <ul>
                    <li><a href="index.php?action=recherche">Rechercher un trajet</a></li>
                    <li><a href="index.php?action=devenir_conducteur">Proposer un trajet</a></li>
                    <li><a href="index.php?action=devenir_conducteur">Devenir Conducteur</a></li>
                </ul>
            </div>

            <div class="footer-column">
                <h4>Informations</h4>
                <ul>
                    <li><a href="#">Mentions légales</a></li>
                    <li><a href="#">Conditions d'utilisation</a></li>
                    <li><a href="#">Contactez-nous</a></li>
                </ul>
            </div>

            <div class="footer-column social-col">
                <h4>Suivez-nous</h4>
                <div class="social-links">
                    <a href="https://www.instagram.com/univantilles/" title="Instagram" target="_blank">
                        <img src="assets/images/instagram.png" alt="Logo Instagram">
                    </a>
                    <a href="https://www.linkedin.com/school/universit-des-antilles/" title="LinkedIn" target="_blank">
                        <img src="assets/images/linkedin.png" alt="Logo LinkedIn">
                    </a>
                </div>
            </div>
        </div>
        
        <div class="footer-bottom">
            <p>&copy; <?= date('Y') ?> CampusCar - Université des Antilles. Covoiturage étudiant sécurisé.</p>
        </div>
    </footer>

    <script>
        // Le script pour le menu déroulant du header
        function toggleProfileMenu() {
            document.getElementById("profileMenu").classList.toggle("show-dropdown");
        }

        window.onclick = function(event) {
            if (!event.target.matches('.profile-icon')) {
                var dropdowns = document.getElementsByClassName("dropdown-content");
                for (var i = 0; i < dropdowns.length; i++) {
                    var openDropdown = dropdowns[i];
                    if (openDropdown.classList.contains('show-dropdown')) {
                        openDropdown.classList.remove('show-dropdown');
                    }
                }
            }
        }
    </script>
</body>
</html>