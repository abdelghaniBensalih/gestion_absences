<?php
$title = "Inscription";
$success = false;
require "config/db.php";

// Récupérer les filières
try {
    $stmtFilieres = $pdo->query("SELECT * FROM filieres");
    $lignesF = $stmtFilieres->fetchAll(PDO::FETCH_ASSOC);
    
    // Récupérer les modules
    $stmtModules = $pdo->query("SELECT * FROM modules");
    $lignesM = $stmtModules->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $erreurDB = "Erreur lors de la récupération des données: " . $e->getMessage();
}

if (isset($_POST['subEtu'])) {
    // Validation des champs
    $errors = [];
    
    // Valider nom
    if (empty($_POST['nom'])) {
        $errors[] = "Le nom est requis";
    } else {
        $nom = htmlspecialchars(trim($_POST['nom']));
    }
    
    // Valider prénom
    if (empty($_POST['prenom'])) {
        $errors[] = "Le prénom est requis";
    } else {
        $prenom = htmlspecialchars(trim($_POST['prenom']));
    }
    
    // Valider apogée
    if (empty($_POST['apogee'])) {
        $errors[] = "Le numéro apogée est requis";
    } elseif (!preg_match('/^\d{7}$/', $_POST['apogee'])) {
        $errors[] = "Le numéro apogée doit contenir exactement 7 chiffres";
    } else {
        $apogee = intval($_POST['apogee']);
    }
    
    // Valider email
    if (empty($_POST['email'])) {
        $errors[] = "L'email est requis";
    } else {
        $email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
        if ($email === false) {
            $errors[] = "Format d'email invalide";
        }
    }
    
    // Valider mot de passe
    if (empty($_POST['password'])) {
        $errors[] = "Le mot de passe est requis";
    } elseif (strlen($_POST['password']) < 6) {
        $errors[] = "Le mot de passe doit contenir au moins 6 caractères";
    } else {
        $password = $_POST['password'];
    }
    
    // Valider filière
    if (empty($_POST['filiere'])) {
        $errors[] = "Veuillez sélectionner une filière";
    } else {
        $id_filiere = intval($_POST['filiere']);
        
        // Vérifier si la filière existe
        $filiereExists = false;
        foreach ($lignesF as $filiere) {
            if ($filiere['id_filiere'] == $id_filiere) {
                $filiereExists = true;
                break;
            }
        }
        
        if (!$filiereExists) {
            $errors[] = "La filière sélectionnée n'existe pas";
        }
    }
    
    // Si aucune erreur, procéder à l'inscription
    if (empty($errors)) {
        try {
            // Vérifier si l'étudiant existe déjà (apogée ou email)
            $stmtCheck = $pdo->prepare("SELECT COUNT(*) FROM etudiants WHERE apogee = ? OR email = ?");
            $stmtCheck->execute([$apogee, $email]);
            
            if ($stmtCheck->fetchColumn() > 0) {
                $Erreur = <<< EOT
                <div class="alert alert-danger" role="alert">
                  <i class="fas fa-exclamation-circle me-2"></i>Un compte avec ce numéro apogée ou cet email existe déjà.
                </div>
EOT;
            } else {
                // Hacher le mot de passe
                $passwordHash = password_hash($password, PASSWORD_DEFAULT);
                
                // Démarrer une transaction
                $pdo->beginTransaction();
                
                // Insérer l'étudiant
                $sqlEtudiant = "INSERT INTO etudiants (apogee, nom, prenom, email, mot_de_passe, id_filiere, date_creation) 
                               VALUES (?, ?, ?, ?, ?, ?, NOW())";
                $stmtEtudiant = $pdo->prepare($sqlEtudiant);
                $stmtEtudiant->execute([$apogee, $nom, $prenom, $email, $passwordHash, $id_filiere]);
                
                // Inscrire l'étudiant à tous les modules de sa filière
                $stmtModulesFiliere = $pdo->prepare("SELECT id_module FROM modules WHERE id_filiere = ?");
                $stmtModulesFiliere->execute([$id_filiere]);
                $modulesFiliere = $stmtModulesFiliere->fetchAll(PDO::FETCH_ASSOC);
                
                $sqlInscription = "INSERT INTO inscriptions (id_etudiant, id_module, annee_academique) VALUES (?, ?, '2024-2025')";
                $stmtInscription = $pdo->prepare($sqlInscription);
                
                foreach ($modulesFiliere as $module) {
                    $stmtInscription->execute([$apogee, $module['id_module']]);
                }
                
                // Valider la transaction
                $pdo->commit();
                
                // Inscription réussie
                $success = true;
                
                // Rediriger vers la page de connexion après 2 secondes
                header("Refresh: 2; URL=index.php");
            }
        } catch (PDOException $e) {
            // Annuler la transaction en cas d'erreur
            $pdo->rollBack();
            
            $Erreur = <<< EOT
            <div class="alert alert-danger" role="alert">
              <i class="fas fa-exclamation-circle me-2"></i>Une erreur s'est produite lors de l'inscription: {$e->getMessage()}
            </div>
EOT;
        }
    } else {
        // Afficher les erreurs de validation
        $errorList = implode('</li><li>', $errors);
        $Erreur = <<< EOT
        <div class="alert alert-danger" role="alert">
          <i class="fas fa-exclamation-circle me-2"></i>Veuillez corriger les erreurs suivantes:
          <ul><li>$errorList</li></ul>
        </div>
EOT;
    }
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Inscription - QR-Présence</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    body {
      margin: 0;
      padding: 0;
      font-family: 'Poppins', sans-serif;
      background: linear-gradient(135deg, #0f3460, #1a1a2e);
      color: #fff;
      display: flex;
      flex-direction: column;
      min-height: 100vh;
      position: relative;
    }

    .navbar {
      background-color: rgba(0, 0, 0, 0.2);
      backdrop-filter: blur(10px);
      padding: 15px 0;
    }

    .navbar-brand {
      font-weight: 700;
      font-size: 1.5rem;
      color: white;
      text-decoration: none;
    }

    .main-container {
      display: flex;
      flex-grow: 1;
      align-items: center;
      justify-content: center;
      padding: 40px 15px;
    }

    .register-card {
      background: rgba(255, 255, 255, 0.08);
      backdrop-filter: blur(12px);
      border: 1px solid rgba(255, 255, 255, 0.1);
      border-radius: 15px;
      padding: 40px;
      width: 100%;
      max-width: 480px;
      box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
      animation: fadeIn 0.6s ease-out;
    }

    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(20px); }
      to { opacity: 1; transform: translateY(0); }
    }

    .register-card h1 {
      text-align: center;
      margin-bottom: 25px;
      color: #fff;
      font-weight: 600;
      position: relative;
      padding-bottom: 15px;
    }

    .register-card h1::after {
      content: '';
      position: absolute;
      width: 60px;
      height: 3px;
      background-color: #4e8cff;
      bottom: 0;
      left: 50%;
      transform: translateX(-50%);
    }

    .register-card label {
      display: block;
      margin-bottom: 6px;
      font-weight: 500;
      font-size: 0.95rem;
    }

    .register-card input,
    .register-card select {
      width: 100%;
      padding: 12px 15px;
      margin-bottom: 20px;
      border: none;
      border-radius: 8px;
      background-color: rgba(255, 255, 255, 0.1);
      color: white;
      outline: none;
      transition: all 0.3s;
      border: 1px solid rgba(255, 255, 255, 0.1);
    }

    .register-card input::placeholder {
      color: rgba(255, 255, 255, 0.5);
    }

    .register-card input:focus,
    .register-card select:focus {
      background-color: rgba(255, 255, 255, 0.15);
      border-color: rgba(255, 255, 255, 0.3);
      box-shadow: 0 0 0 3px rgba(78, 140, 255, 0.25);
    }

    #btn {
      width: 100%;
      background-color: #4e8cff;
      color: #fff;
      font-weight: 600;
      padding: 14px;
      border-radius: 8px;
      font-size: 16px;
      border: none;
      cursor: pointer;
      transition: all 0.3s ease;
      margin-top: 10px;
    }

    #btn:hover {
      transform: translateY(-2px);
      background-color: #3a78e7;
      box-shadow: 0 5px 15px rgba(78, 140, 255, 0.4);
    }

    #btn:active {
      transform: translateY(1px);
    }

    .message {
      text-align: center;
      margin-top: 20px;
      font-size: 0.9rem;
      color: rgba(255, 255, 255, 0.7);
    }

    .message a {
      color: #4e8cff;
      text-decoration: none;
      font-weight: 500;
      transition: all 0.3s;
    }

    .message a:hover {
      color: #fff;
    }

    .alert-success {
      background-color: rgba(40, 167, 69, 0.2);
      border-left: 4px solid #28a745;
      color: #fff;
      padding: 12px;
      border-radius: 6px;
      font-size: 0.9rem;
      margin: 15px 0;
      display: flex;
      align-items: center;
    }

    .alert-danger {
      background-color: rgba(220, 53, 69, 0.2);
      border-left: 4px solid #dc3545;
      color: #fff;
      padding: 12px;
      border-radius: 6px;
      font-size: 0.9rem;
      margin: 15px 0;
    }

    .alert-danger ul {
      margin-bottom: 0;
    }

    option {
      background-color: #0f3460;
      color: white;
      padding: 10px;
    }

    .form-field {
      position: relative;
      margin-bottom: 20px;
    }

    .field-icon {
      position: absolute;
      right: 15px;
      top: 40px;
      cursor: pointer;
      color: rgba(255, 255, 255, 0.6);
    }

    .password-strength {
      height: 5px;
      margin-top: -15px;
      margin-bottom: 15px;
      border-radius: 5px;
      background-color: rgba(255, 255, 255, 0.1);
      overflow: hidden;
    }

    .password-strength-meter {
      height: 100%;
      width: 0%;
      transition: width 0.3s ease;
    }

    .weak { width: 33%; background-color: #dc3545; }
    .medium { width: 66%; background-color: #ffc107; }
    .strong { width: 100%; background-color: #28a745; }

    .back-home {
      position: absolute;
      top: 20px;
      left: 20px;
      color: white;
      text-decoration: none;
      display: flex;
      align-items: center;
      font-size: 0.9rem;
      transition: all 0.3s;
    }

    .back-home i {
      margin-right: 5px;
    }

    .back-home:hover {
      color: #4e8cff;
      transform: translateX(-3px);
    }

    @media (max-width: 576px) {
      .register-card {
        padding: 30px 20px;
      }
    }
  </style>
</head>

<body>
  <a href="acceuil.php" class="back-home">
    <i class="fas fa-arrow-left"></i> Retour à l'accueil
  </a>

  <div class="main-container">
    <div class="register-card">
      <h1>Inscription</h1>
      <form action="" method="post" id="registerForm">
        <div class="form-field">
          <label for="nom">Nom</label>
          <input type="text" pattern="[A-Za-z]+ *[A-Za-z]*" name="nom" id="nom" placeholder="Entrez votre nom" required value="<?= isset($nom) ? $nom : '' ?>">
        </div>

        <div class="form-field">
          <label for="prenom">Prénom</label>
          <input type="text" name="prenom" pattern="[A-Za-z]*" id="prenom" placeholder="Entrez votre prénom" required value="<?= isset($prenom) ? $prenom : '' ?>">
        </div>

        <div class="form-field">
          <label for="apogee">Numéro Apogée</label>
          <input type="text" pattern="[0-9]{7}" name="apogee" id="apogee" placeholder="7 chiffres" required value="<?= isset($apogee) ? $apogee : '' ?>">
        </div>

        <div class="form-field">
          <label for="email">Email</label>
          <input type="email" name="email" id="email" placeholder="exemple@email.com" required value="<?= isset($email) ? $email : '' ?>">
        </div>

        <div class="form-field">
          <label for="password">Mot de passe</label>
          <input type="password" name="password" id="password" placeholder="Créez un mot de passe sécurisé" required>
          <i class="field-icon fas fa-eye-slash toggle-password" onclick="togglePassword()"></i>
          <div class="password-strength">
            <div class="password-strength-meter"></div>
          </div>
        </div>

        <div class="form-field">
          <label for="filiere">Filière</label>
          <select name="filiere" id="filiere" required>
            <option value="">-- Choisir la filière --</option>
            <?php if (isset($lignesF) && is_array($lignesF)): ?>
              <?php foreach ($lignesF as $ligne): ?>
                <option value="<?= $ligne['id_filiere'] ?>" <?= (isset($id_filiere) && $id_filiere == $ligne['id_filiere']) ? 'selected' : '' ?>>
                  <?= htmlspecialchars($ligne['nom']) ?>
                </option>
              <?php endforeach; ?>
            <?php endif; ?>
          </select>
        </div>

        <input type="submit" name="subEtu" id="btn" value="S'inscrire">

        <?php if ($success): ?>
          <div class="alert alert-success" role="alert">
            <i class="fas fa-check-circle me-2"></i> Votre inscription a été effectuée avec succès ! Redirection...
          </div>
        <?php elseif (isset($Erreur)): ?>
          <?= $Erreur ?>
        <?php endif; ?>
      </form>
      <div class="message">
        Vous avez déjà un compte ? <a href="index.php">Se connecter</a>
      </div>
    </div>
  </div>

  <script>
    // Toggle password visibility
    function togglePassword() {
      const passwordInput = document.getElementById('password');
      const toggleIcon = document.querySelector('.toggle-password');
      
      if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        toggleIcon.classList.remove('fa-eye-slash');
        toggleIcon.classList.add('fa-eye');
      } else {
        passwordInput.type = 'password';
        toggleIcon.classList.remove('fa-eye');
        toggleIcon.classList.add('fa-eye-slash');
      }
    }

    // Password strength indicator
    document.getElementById('password').addEventListener('input', function() {
      const password = this.value;
      const meter = document.querySelector('.password-strength-meter');
      
      // Remove existing classes
      meter.classList.remove('weak', 'medium', 'strong');
      
      if (password.length > 0) {
        // Simple password strength check
        if (password.length < 8) {
          meter.classList.add('weak');
        } else if (password.length >= 8 && password.length < 12) {
          meter.classList.add('medium');
        } else {
          meter.classList.add('strong');
        }
      }
    });

    // Form validation enhancement
    document.getElementById('registerForm').addEventListener('submit', function(e) {
      const password = document.getElementById('password').value;
      const email = document.getElementById('email').value;
      
      // Additional validation if needed
      if (password.length < 6) {
        alert('Le mot de passe doit contenir au moins 6 caractères');
        e.preventDefault();
      }
    });
  </script>
</body>

</html>