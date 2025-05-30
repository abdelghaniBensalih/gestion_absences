<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Gestion des Absences</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"/>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    body {
      background: linear-gradient(135deg, #0f3460, #1a1a2e);
      color: white;
      font-family: 'Poppins', sans-serif;
      min-height: 100vh;
      display: flex;
      flex-direction: column;
    }

    /* Navbar styles */
    .navbar {
      background-color: rgba(0, 0, 0, 0.2);
      backdrop-filter: blur(10px);
      padding: 15px 0;
      transition: all 0.3s ease;
    }

    .navbar-brand {
      font-weight: 700;
      font-size: 1.5rem;
      color: white;
    }

    .navbar-toggler {
      border: none;
    }
    
    .navbar-toggler:focus {
      box-shadow: none;
    }

    .nav-link {
      color: rgba(255, 255, 255, 0.8);
      margin: 0 10px;
      transition: all 0.3s ease;
    }
    
    .nav-link:hover {
      color: white;
      transform: translateY(-2px);
    }

    /* Hero section */
    .hero {
      padding: 120px 20px 80px;
      text-align: center;
    }

    .hero h1 {
      font-size: 3.2rem;
      font-weight: bold;
      margin-bottom: 1.5rem;
      animation: fadeInUp 1s ease;
    }

    .hero p {
      font-size: 1.3rem;
      margin-top: 1rem;
      opacity: 0.9;
      max-width: 700px;
      margin: 0 auto 2rem;
      animation: fadeInUp 1.2s ease;
    }

    .hero .btn {
      margin: 1rem 0.5rem;
      padding: 12px 30px;
      border-radius: 30px;
      font-weight: 500;
      transition: all 0.3s ease;
      animation: fadeInUp 1.4s ease;
    }
    
    .hero .btn:hover {
      transform: translateY(-3px);
      box-shadow: 0 5px 15px rgba(0,0,0,0.3);
    }

    /* Features section */
    .features {
      background-color: rgba(255, 255, 255, 0.08);
      border-radius: 15px;
      padding: 3rem;
      margin-top: 3rem;
      box-shadow: 0 10px 30px rgba(0,0,0,0.15);
      transition: all 0.3s ease;
    }
    
    .features:hover {
      transform: translateY(-5px);
    }
    
    .features h2 {
      margin-bottom: 2rem;
      position: relative;
      display: inline-block;
    }
    
    .features h2:after {
      content: '';
      position: absolute;
      width: 50%;
      height: 3px;
      background-color: #4e8cff;
      bottom: -10px;
      left: 25%;
    }

    .feature-card {
      padding: 1.5rem;
      border-radius: 10px;
      background-color: rgba(255, 255, 255, 0.05);
      height: 100%;
      transition: all 0.3s ease;
    }
    
    .feature-card:hover {
      background-color: rgba(255, 255, 255, 0.1);
      transform: translateY(-5px);
    }
    
    .feature-icon {
      font-size: 2.5rem;
      margin-bottom: 1rem;
      color: #4e8cff;
    }

    /* Footer */
    footer {
      margin-top: auto;
      padding: 2rem 0;
      background-color: rgba(13, 27, 42, 0.8);
      text-align: center;
      color: #ccc;
    }
    
    .footer-links {
      display: flex;
      justify-content: center;
      margin-bottom: 1rem;
    }
    
    .footer-links a {
      color: #ccc;
      margin: 0 15px;
      text-decoration: none;
      transition: color 0.3s ease;
    }
    
    .footer-links a:hover {
      color: white;
    }
    
    /* Animations */
    @keyframes fadeInUp {
      from {
        opacity: 0;
        transform: translateY(20px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }
    
    .animate {
      animation: fadeInUp 1s ease;
    }
  </style>
</head>
<body>
  <!-- NAVBAR -->
  <nav class="navbar navbar-expand-lg fixed-top">
    <div class="container">
      <a class="navbar-brand" href="#">QR-Présence</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
        <ul class="navbar-nav">
          <li class="nav-item">
            <a class="nav-link" href="#">Accueil</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="index.php">Connexion</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="register.php">Inscription</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="#">À propos</a>
          </li>
        </ul>
      </div>
    </div>
  </nav>

  <!-- HERO SECTION -->
  <section class="hero container">
    <h1>Gestion des Absences par QR Code</h1>
    <p>Un système moderne et sécurisé pour suivre la présence des étudiants en temps réel.</p>
    <div>
      <a href="index.php" class="btn btn-primary">Connexion</a>
      <a href="register.php" class="btn btn-outline-light">Inscription</a>
    </div>
  </section>

  <!-- FEATURES -->
  <section class="container features text-center">
    <h2>Comment ça marche ?</h2>
    <div class="row mt-5">
      <div class="col-md-4 mb-4">
        <div class="feature-card animate">
          <div class="feature-icon">
            <i class="fas fa-qrcode"></i>
          </div>
          <h4>Scan du QR Code</h4>
          <p>Chaque étudiant scanne un QR Code unique en entrant en classe pour signaler sa présence.</p>
        </div>
      </div>
      <div class="col-md-4 mb-4">
        <div class="feature-card animate">
          <div class="feature-icon">
            <i class="fas fa-clock"></i>
          </div>
          <h4>Présence Automatique</h4>
          <p>La présence est enregistrée automatiquement dans la base de données avec horodatage précis.</p>
        </div>
      </div>
      <div class="col-md-4 mb-4">
        <div class="feature-card animate">
          <div class="feature-icon">
            <i class="fas fa-chart-line"></i>
          </div>
          <h4>Suivi en Temps Réel</h4>
          <p>Les administrateurs et enseignants peuvent consulter les absences à tout moment via un tableau de bord intuitif.</p>
        </div>
      </div>
    </div>
  </section>

  <!-- EXTRA SECTION -->
  <section class="container mt-5 mb-5 animate">
    <div class="row align-items-center">
      <div class="col-lg-6 mb-4">
        <h2>Simplifiez la gestion de présence</h2>
        <p>Notre système offre une solution moderne pour remplacer les feuilles d'émargement traditionnelles. Gain de temps, réduction d'erreurs et données en temps réel pour une meilleure gestion pédagogique.</p>
        <a href="#" class="btn btn-outline-light mt-3">En savoir plus</a>
      </div>
      <div class="col-lg-6">
        <div class="features p-4">
          <h4><i class="fas fa-shield-alt me-2"></i> Sécurisé</h4>
          <p>Systèmes d'authentification et QR codes uniques pour chaque session</p>
          
          <h4><i class="fas fa-tachometer-alt me-2"></i> Rapide</h4>
          <p>Enregistrement instantané des présences sans perte de temps</p>
          
          <h4><i class="fas fa-file-export me-2"></i> Exportable</h4>
          <p>Rapports complets et données exportables en plusieurs formats</p>
        </div>
      </div>
    </div>
  </section>

  <!-- FOOTER -->
  <footer>
    <div class="container">
      <div class="footer-links">
        <a href="#">Accueil</a>
        <a href="#">À propos</a>
        <a href="#">Conditions d'utilisation</a>
        <a href="#">Confidentialité</a>
        <a href="#">Contact</a>
      </div>
      <p>&copy; 2025 - QR-Présence | Système de Gestion des Absences</p>
    </div>
  </footer>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    // Script pour l'animation au défilement
    document.addEventListener('DOMContentLoaded', function() {
      const navbar = document.querySelector('.navbar');
      
      window.addEventListener('scroll', function() {
        if (window.scrollY > 50) {
          navbar.style.padding = '10px 0';
          navbar.style.backgroundColor = 'rgba(0, 0, 0, 0.7)';
        } else {
          navbar.style.padding = '15px 0';
          navbar.style.backgroundColor = 'rgba(0, 0, 0, 0.2)';
        }
      });
      
      // Animation pour les éléments avec la classe animate
      const animateElements = document.querySelectorAll('.animate');
      
      const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
          if (entry.isIntersecting) {
            entry.target.style.opacity = 1;
            entry.target.style.transform = 'translateY(0)';
          }
        });
      });
      
      animateElements.forEach(element => {
        element.style.opacity = 0;
        element.style.transform = 'translateY(20px)';
        element.style.transition = 'all 0.6s ease';
        observer.observe(element);
      });
    });
  </script>
</body>
</html>