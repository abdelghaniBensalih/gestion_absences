<?php
/**
 * Système d'authentification centralisé pour l'application QR-Présence
 * 
 * Ce fichier contient toutes les fonctions nécessaires pour gérer l'authentification
 * des étudiants et des administrateurs, la validation des sessions, et la sécurité
 * des accès aux différentes parties de l'application.
 */

//session_start();

/**
 * Vérifie si un étudiant est authentifié
 * 
 * @return bool Vrai si l'étudiant est authentifié, faux sinon
 */
function isStudentLoggedIn() {
    return isset($_SESSION['auth']) && $_SESSION['auth'] === "Oui" && isset($_COOKIE['apogee']) && isset($_COOKIE['mot_de_passe']);
}

/**
 * Vérifie si un administrateur est authentifié
 * 
 * @return bool Vrai si l'administrateur est authentifié, faux sinon
 */
function isAdminLoggedIn() {
    return isset($_SESSION['authAdmin']) && $_SESSION['authAdmin'] === "Oui" && isset($_COOKIE['id_administrateur']) && isset($_COOKIE['mot_de_passeAd']);
}

/**
 * Renvoie l'identifiant de l'étudiant connecté ou null si non connecté
 * 
 * @return string|null Le numéro apogée de l'étudiant connecté ou null
 */
function getCurrentStudentId() {
    return isStudentLoggedIn() ? $_COOKIE['apogee'] : null;
}

/**
 * Renvoie l'identifiant de l'administrateur connecté ou null si non connecté
 * 
 * @return string|null L'identifiant de l'administrateur connecté ou null
 */
function getCurrentAdminId() {
    return isAdminLoggedIn() ? $_COOKIE['id_administrateur'] : null;
}

/**
 * Authentifie un étudiant et stocke ses informations dans la session
 * 
 * @param string $apogee Le numéro apogée de l'étudiant
 * @param string $password Le mot de passe de l'étudiant (non haché)
 * @param bool $remember Indique si on doit se souvenir de l'utilisateur
 * @return bool Vrai si l'authentification a réussi, faux sinon
 */
function loginStudent($apogee, $password, $remember = true) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("SELECT * FROM etudiants WHERE apogee = ?");
        $stmt->execute([$apogee]);
        $student = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($student && password_verify($password, $student['mot_de_passe'])) {
            $_SESSION['auth'] = "Oui";
            $_SESSION['apogee'] = $apogee;
            $_SESSION['user_type'] = 'student';
            $_SESSION['user_info'] = [
                'nom' => $student['nom'],
                'prenom' => $student['prenom'],
                'email' => $student['email']
            ];
            
            if ($remember) {
                // Cookies valides pendant 24 heures
                setcookie("apogee", $apogee, time() + 3600 * 24, "/");
                setcookie("mot_de_passe", $password, time() + 3600 * 24, "/");
            }
            
            return true;
        }
        
        return false;
    } catch (PDOException $e) {
        // Log l'erreur
        error_log('Erreur d\'authentification étudiant: ' . $e->getMessage());
        return false;
    }
}

/**
 * Authentifie un administrateur et stocke ses informations dans la session
 * 
 * @param string $adminId L'identifiant de l'administrateur
 * @param string $password Le mot de passe de l'administrateur
 * @param bool $remember Indique si on doit se souvenir de l'utilisateur
 * @return bool Vrai si l'authentification a réussi, faux sinon
 */
function loginAdmin($adminId, $password, $remember = true) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("SELECT * FROM administrateurs WHERE id_administrateur = ?");
        $stmt->execute([$adminId]);
        $admin = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($admin && $admin['mot_de_passe'] === $password) {
            $_SESSION['authAdmin'] = "Oui";
            $_SESSION['id_administrateur'] = $adminId;
            $_SESSION['user_type'] = 'admin';
            
            if ($remember) {
                // Cookies valides pendant 24 heures
                setcookie("id_administrateur", $adminId, time() + 3600 * 24, "/");
                setcookie("mot_de_passeAd", $password, time() + 3600 * 24, "/");
            }
            
            return true;
        }
        
        return false;
    } catch (PDOException $e) {
        // Log l'erreur
        error_log('Erreur d\'authentification admin: ' . $e->getMessage());
        return false;
    }
}

/**
 * Déconnecte l'utilisateur courant (étudiant ou administrateur)
 */
function logout() {
    // Suppression des cookies étudiants
    if (isset($_COOKIE['apogee'])) {
        setcookie("apogee", "", time() - 3600, "/");
        setcookie("mot_de_passe", "", time() - 3600, "/");
    }
    
    // Suppression des cookies administrateur
    if (isset($_COOKIE['id_administrateur'])) {
        setcookie("id_administrateur", "", time() - 3600, "/");
        setcookie("mot_de_passeAd", "", time() - 3600, "/");
    }
    
    // Destruction de la session
    session_unset();
    session_destroy();
}

/**
 * Redirige vers la page de connexion si l'utilisateur n'est pas authentifié
 * 
 * @param bool $adminOnly Indique si la page est réservée aux administrateurs
 */
function requireLogin($adminOnly = false) {
    if ($adminOnly && !isAdminLoggedIn()) {
        // Redirection vers la page de connexion admin
        header("Location: " . getAppBaseUrl() . "/index.php");
        exit();
    } elseif (!$adminOnly && !isStudentLoggedIn() && !isAdminLoggedIn()) {
        // Redirection vers la page de connexion générale
        header("Location: " . getAppBaseUrl() . "/index.php");
        exit();
    }
}

/**
 * Obtient l'URL de base de l'application
 * 
 * @return string L'URL de base de l'application
 */
function getAppBaseUrl() {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
    $domainName = $_SERVER['HTTP_HOST'];
    
    // Déterminer le chemin de base en fonction de la structure du projet
    $pathParts = explode('/', trim($_SERVER['REQUEST_URI'], '/'));
    
    // Supposons que "gestion_absences" est le dossier racine de l'application
    $rootFolderIndex = array_search('gestion_absences', $pathParts);
    
    if ($rootFolderIndex !== false) {
        $basePath = implode('/', array_slice($pathParts, 0, $rootFolderIndex + 1));
        return $protocol . $domainName . '/' . $basePath;
    }
    
    // Fallback - retourne simplement le domaine
    return $protocol . $domainName;
}

/**
 * Redirige l'utilisateur vers la page de tableau de bord appropriée
 * en fonction de son type (étudiant ou administrateur)
 */
function redirectToDashboard() {
    if (isAdminLoggedIn()) {
        header("Location: " . getAppBaseUrl() . "/admin/dashbord_Admin.php");
        exit();
    } elseif (isStudentLoggedIn()) {
        header("Location: " . getAppBaseUrl() . "/dashbord_etudiant.php");
        exit();
    } else {
        // Si non connecté, rediriger vers la page de connexion
        header("Location: " . getAppBaseUrl() . "/index.php");
        exit();
    }
}

/**
 * Génère un token de sécurité CSRF et le stocke dans la session
 * 
 * @return string Le token CSRF généré
 */
function generateCSRFToken() {
    $token = bin2hex(random_bytes(32));
    $_SESSION['csrf_token'] = $token;
    return $token;
}

/**
 * Vérifie si le token CSRF fourni est valide
 * 
 * @param string $token Le token CSRF à vérifier
 * @return bool Vrai si le token est valide, faux sinon
 */
function validateCSRFToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Limite le nombre de tentatives de connexion pour éviter les attaques par force brute
 * 
 * @param string $identifier Identifiant unique (comme une adresse IP)
 * @param int $maxAttempts Nombre maximum de tentatives autorisées
 * @param int $timeWindow Fenêtre de temps en secondes
 * @return bool Vrai si limite dépassée, faux sinon
 */
function checkRateLimit($identifier, $maxAttempts = 5, $timeWindow = 300) {
    $cacheFile = sys_get_temp_dir() . '/rate_limit_' . md5($identifier) . '.json';
    
    // Initialisation ou lecture du cache
    if (file_exists($cacheFile)) {
        $data = json_decode(file_get_contents($cacheFile), true);
    } else {
        $data = ['attempts' => 0, 'timestamp' => time()];
    }
    
    // Réinitialiser le compteur si la fenêtre de temps est dépassée
    if (time() - $data['timestamp'] > $timeWindow) {
        $data = ['attempts' => 0, 'timestamp' => time()];
    }
    
    // Incrémenter le compteur
    $data['attempts']++;
    file_put_contents($cacheFile, json_encode($data));
    
    // Vérifier si la limite est dépassée
    return $data['attempts'] > $maxAttempts;
}

/**
 * Réinitialise le compteur de tentatives pour un identifiant donné
 * 
 * @param string $identifier Identifiant unique (comme une adresse IP)
 */
function resetRateLimit($identifier) {
    $cacheFile = sys_get_temp_dir() . '/rate_limit_' . md5($identifier) . '.json';
    
    if (file_exists($cacheFile)) {
        unlink($cacheFile);
    }
}

/**
 * Nettoie et valide une entrée utilisateur
 * 
 * @param string $input La chaîne à nettoyer
 * @return string La chaîne nettoyée
 */
function sanitizeInput($input) {
    $input = trim($input);
    $input = stripslashes($input);
    $input = htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
    return $input;
}
?>