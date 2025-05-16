<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Gestion des Absences</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"/>
  <style>
    body {
      background: linear-gradient(135deg, #0f3460, #1a1a2e);
      color: white;
      font-family: 'Poppins', sans-serif;
    }

    .hero {
      padding: 100px 20px;
      text-align: center;
    }

    .hero h1 {
      font-size: 3rem;
      font-weight: bold;
    }

    .hero p {
      font-size: 1.2rem;
      margin-top: 1rem;
      opacity: 0.8;
    }

    .hero .btn {
      margin: 1rem 0.5rem;
      padding: 12px 30px;
    }

    .features {
      background-color: #ffffff10;
      border-radius: 10px;
      padding: 2rem;
      margin-top: 3rem;
    }

    footer {
      margin-top: 4rem;
      padding: 1rem 0;
      background-color: #0d1b2a;
      text-align: center;
      color: #ccc;
    }
  </style>
</head>
<body>

  <!-- HERO SECTION -->
  <section class="hero container">
    <h1>Gestion des Absences par QR Code</h1>
    <p>Un système moderne et sécurisé pour suivre la présence des étudiants en temps réel.</p>
    <div>
      <a href="index.php" class="btn btn-light">Connexion</a>
      <a href="register.php" class="btn btn-outline-light">Inscription</a>
    </div>
  </section>

  <!-- FEATURES -->
  <section class="container features text-center">
    <h2>Comment ça marche ?</h2>
    <div class="row mt-4">
      <div class="col-md-4">
        <h4>📲 Scan du QR Code</h4>
        <p>Chaque étudiant scanne un QR Code unique en entrant en classe.</p>
      </div>
      <div class="col-md-4">
        <h4>🕒 Présence Automatique</h4>
        <p>La présence est enregistrée automatiquement dans la base de données.</p>
      </div>
      <div class="col-md-4">
        <h4>📊 Suivi en Temps Réel</h4>
        <p>Les administrateurs et enseignants peuvent consulter les absences à tout moment.</p>
      </div>
    </div>
  </section>

  <!-- FOOTER -->
  <footer>
    &copy; 2025 - Gestion Absences QR 
  </footer>

</body>
</html>
