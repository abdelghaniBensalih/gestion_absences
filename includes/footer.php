<?php
/**
 * Pied de page standardisé pour l'application QR-Présence
 * Inclut les scripts communs, les liens de navigation et les informations de copyright
 */
require_once dirname(__FILE__) . "/auth.php";
?>

<footer class="footer">
    <div class="container">
        <div class="footer-content">
            <div class="footer-section">
                <h4>QR-Présence</h4>
                <p>Système de gestion des présences par QR code</p>
                <div class="social-icons">
                    <a href="#" class="social-icon"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" class="social-icon"><i class="fab fa-twitter"></i></a>
                    <a href="#" class="social-icon"><i class="fab fa-instagram"></i></a>
                    <a href="#" class="social-icon"><i class="fab fa-linkedin-in"></i></a>
                </div>
            </div>
            
            <div class="footer-section">
                <h4>Liens rapides</h4>
                <ul class="footer-links">
                    <li><a href="<?= getAppBaseUrl() ?>/">Accueil</a></li>
                    <?php if (isStudentLoggedIn()): ?>
                        <li><a href="<?= getAppBaseUrl() ?>/dashbord_etudiant.php">Mon tableau de bord</a></li>
                    <?php elseif (isAdminLoggedIn()): ?>
                        <li><a href="<?= getAppBaseUrl() ?>/admin/dashbord_Admin.php">Administration</a></li>
                    <?php else: ?>
                        <li><a href="<?= getAppBaseUrl() ?>/index.php">Connexion</a></li>
                        <li><a href="<?= getAppBaseUrl() ?>/register.php">Inscription</a></li>
                    <?php endif; ?>
                    <li><a href="<?= getAppBaseUrl() ?>/contact.php">Contact</a></li>
                </ul>
            </div>
            
            <div class="footer-section">
                <h4>Contact</h4>
                <p><i class="fas fa-map-marker-alt"></i> Université XYZ, Campus Principal</p>
                <p><i class="fas fa-envelope"></i> contact@qr-presence.com</p>
                <p><i class="fas fa-phone"></i> +212 500 000 000</p>
            </div>
        </div>
        
        <div class="footer-bottom">
            <div class="copyright">
                &copy; <?= date('Y') ?> QR-Présence. Tous droits réservés.
            </div>
            <div class="footer-bottom-links">
                <a href="<?= getAppBaseUrl() ?>/mentions-legales.php">Mentions légales</a>
                <a href="<?= getAppBaseUrl() ?>/confidentialite.php">Politique de confidentialité</a>
                <a href="<?= getAppBaseUrl() ?>/cgu.php">CGU</a>
            </div>
        </div>
    </div>
</footer>

<!-- Bouton de retour en haut -->
<button id="back-to-top" title="Retour en haut"><i class="fas fa-arrow-up"></i></button>

<script>
    // Script pour le bouton "Retour en haut"
    document.addEventListener('DOMContentLoaded', function() {
        var backToTopButton = document.getElementById('back-to-top');
        
        // Afficher/masquer le bouton en fonction du défilement
        window.addEventListener('scroll', function() {
            if (window.pageYOffset > 300) {
                backToTopButton.classList.add('show');
            } else {
                backToTopButton.classList.remove('show');
            }
        });
        
        // Retour en haut en douceur
        backToTopButton.addEventListener('click', function(e) {
            e.preventDefault();
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
    });
    
    // Animation des éléments du footer au chargement
    document.addEventListener('DOMContentLoaded', function() {
        const footerSections = document.querySelectorAll('.footer-section');
        
        footerSections.forEach((section, index) => {
            setTimeout(() => {
                section.style.opacity = '1';
                section.style.transform = 'translateY(0)';
            }, 100 * index);
        });
    });
</script>

<style>
    .footer {
        background: rgba(15, 52, 96, 0.95);
        color: #fff;
        padding: 3rem 0 1rem;
        margin-top: 3rem;
        backdrop-filter: blur(10px);
        border-top: 1px solid rgba(255, 255, 255, 0.1);
    }
    
    .footer-content {
        display: flex;
        flex-wrap: wrap;
        justify-content: space-between;
        margin-bottom: 2rem;
    }
    
    .footer-section {
        flex: 1;
        min-width: 250px;
        margin-bottom: 1.5rem;
        padding: 0 1rem;
        opacity: 0;
        transform: translateY(20px);
        transition: all 0.5s ease;
    }
    
    .footer-section h4 {
        font-size: 1.2rem;
        margin-bottom: 1rem;
        position: relative;
        font-weight: 600;
    }
    
    .footer-section h4::after {
        content: "";
        position: absolute;
        left: 0;
        bottom: -5px;
        width: 40px;
        height: 2px;
        background-color: #4e8cff;
    }
    
    .footer-links {
        list-style: none;
        padding: 0;
        margin: 0;
    }
    
    .footer-links li {
        margin-bottom: 0.5rem;
    }
    
    .footer-links a {
        color: rgba(255, 255, 255, 0.8);
        text-decoration: none;
        transition: all 0.3s ease;
        display: inline-block;
    }
    
    .footer-links a:hover {
        color: #4e8cff;
        transform: translateX(5px);
    }
    
    .social-icons {
        margin-top: 1rem;
        display: flex;
    }
    
    .social-icon {
        display: inline-flex;
        justify-content: center;
        align-items: center;
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.1);
        color: #fff;
        margin-right: 0.5rem;
        text-decoration: none;
        transition: all 0.3s ease;
    }
    
    .social-icon:hover {
        background: #4e8cff;
        transform: translateY(-3px);
    }
    
    .footer-section p {
        color: rgba(255, 255, 255, 0.7);
        margin-bottom: 0.7rem;
        display: flex;
        align-items: center;
    }
    
    .footer-section p i {
        margin-right: 0.5rem;
        color: #4e8cff;
        min-width: 20px;
    }
    
    .footer-bottom {
        border-top: 1px solid rgba(255, 255, 255, 0.1);
        padding-top: 1.5rem;
        display: flex;
        flex-wrap: wrap;
        justify-content: space-between;
        align-items: center;
        font-size: 0.9rem;
    }
    
    .footer-bottom-links {
        display: flex;
        flex-wrap: wrap;
    }
    
    .footer-bottom-links a {
        color: rgba(255, 255, 255, 0.7);
        margin-left: 1.5rem;
        text-decoration: none;
        transition: color 0.3s ease;
    }
    
    .footer-bottom-links a:hover {
        color: #4e8cff;
    }
    
    #back-to-top {
        display: flex;
        justify-content: center;
        align-items: center;
        position: fixed;
        bottom: 30px;
        right: 30px;
        width: 45px;
        height: 45px;
        background-color: #4e8cff;
        color: white;
        border: none;
        border-radius: 50%;
        cursor: pointer;
        opacity: 0;
        visibility: hidden;
        transition: all 0.4s ease;
        z-index: 1000;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
    }
    
    #back-to-top.show {
        opacity: 1;
        visibility: visible;
    }
    
    #back-to-top:hover {
        background-color: #3a78e7;
        transform: translateY(-5px);
    }
    
    @media (max-width: 768px) {
        .footer-content {
            flex-direction: column;
        }
        
        .footer-section {
            margin-bottom: 2rem;
            padding: 0;
        }
        
        .footer-bottom {
            flex-direction: column;
            text-align: center;
        }
        
        .footer-bottom-links {
            margin-top: 1rem;
            justify-content: center;
        }
        
        .footer-bottom-links a {
            margin: 0 0.5rem;
        }
    }
</style>