<?php
require_once "../config/db.php";
// Inclure la bibliothèque PHPMailer
require 'lib/PHPMailer/vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;


// Récupérer les séances terminées qui n'ont pas encore été traitées pour les absences
$now = date('Y-m-d H:i:s');
$sql = "SELECT s.id_seance, s.id_module, s.date_seance, s.duree, s.type_seance, 
               m.nom as module_nom, m.id_filiere 
        FROM seances s
        JOIN modules m ON s.id_module = m.id_module
        WHERE DATE_ADD(s.date_seance, INTERVAL s.duree MINUTE) <= ?
        AND s.absences_generees = 0";

try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$now]);
    $seancesTerminees = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (count($seancesTerminees) > 0) {
        echo "<!-- " . count($seancesTerminees) . " séances terminées trouvées -->\n";
        
        foreach ($seancesTerminees as $seance) {
            processSeance($seance, $pdo);
        }
    } else {
        echo "<!-- Aucune séance terminée à traiter -->\n";
    }
} catch (PDOException $e) {
    error_log("Erreur lors de la vérification des séances terminées: " . $e->getMessage());
    echo "<!-- Erreur: " . $e->getMessage() . " -->\n";
}

// Fonction qui traite une séance terminée en générant les absences
function processSeance($seance, $pdo) {
    $seanceId = $seance['id_seance'];
    $moduleId = $seance['id_module'];

    try {
        // 1. Étudiants inscrits à ce module
        $stmtEtudiants = $pdo->prepare(
            "SELECT e.apogee 
             FROM etudiants e 
             JOIN inscriptions i ON e.apogee = i.id_etudiant 
             WHERE i.id_module = ?"
        );
        $stmtEtudiants->execute([$moduleId]);
        $etudiants = $stmtEtudiants->fetchAll(PDO::FETCH_ASSOC);
        
        // 2. Étudiants présents
        $stmtPresents = $pdo->prepare(
            "SELECT apogee FROM presences WHERE id_seance = ?"
        );
        $stmtPresents->execute([$seanceId]);
        $presents = $stmtPresents->fetchAll(PDO::FETCH_COLUMN);
        
        // 3. Générer les absences
        $stmtInsertAbsence = $pdo->prepare(
            "INSERT INTO absences (id_seance, apogee, justifiee) VALUES (?, ?, 0)"
        );
        
        $absencesCount = 0;
        foreach ($etudiants as $etudiant) {
            $apogee = $etudiant['apogee'];
            if (!in_array($apogee, $presents)) {
                try {
                    $stmtInsertAbsence->execute([$seanceId, $apogee]);
                    $absencesCount++;

                    // 4. Récupérer email de l'étudiant
                    $stmtMail = $pdo->prepare("SELECT email, nom, prenom FROM etudiants WHERE apogee = ?");
                    $stmtMail->execute([$apogee]);
                    $etuInfo = $stmtMail->fetch(PDO::FETCH_ASSOC);

                    if ($etuInfo && !empty($etuInfo['email'])) {
                        $mail = new PHPMailer(true);
                        try {
                        
                        $mail->isSMTP();
                          $mail->Host = 'smtp.gmail.com';
                          $mail->SMTPAuth = true;
                          $mail->Username = 'oukikredouane21@gmail.com'; // Remplacez par l'email de l'administration
                          $mail->Password = 'fxbg tmyd lxru ibwj'; // App password recommandé
                         $mail->SMTPSecure = 'tls';
                         $mail->Port = 587;

                           $mail->setFrom('oukikredouane21@gmail.com', 'Administration');
                            $mail->addAddress($etuInfo['email'], $etuInfo['nom'].' '.$etuInfo['prenom']);

                            $mail->isHTML(true);
                            $mail->Subject = "Absence a justifier";
                            $mail->Body = "Bonjour {$etuInfo['prenom']} {$etuInfo['nom']},<br>
                            Vous etiez absent à la séance du <b>{$seance['date_seance']}</b>.<br>
                            Merci de justifier votre absence le plus tôt possible via la platefoforme Espace Etdiant.";

                            $mail->send();
                        } catch (Exception $e) {
                            error_log("Erreur mail {$etuInfo['email']}: " . $mail->ErrorInfo);
                        }
                    }

                } catch (PDOException $e) {
                    if ($e->getCode() != 23000) {
                        throw $e;
                    }
                }
            }
        }

        // 5. Marquer la séance comme traitée
        $stmtUpdateSeance = $pdo->prepare(
            "UPDATE seances SET absences_generees = 1 WHERE id_seance = ?"
        );
        $stmtUpdateSeance->execute([$seanceId]);

        echo "<!-- Séance #$seanceId: $absencesCount absences générées -->\n";

    } catch (PDOException $e) {
        error_log("Erreur séance #$seanceId: " . $e->getMessage());
        echo "<!-- Erreur séance #$seanceId: " . $e->getMessage() . " -->\n";
    }
}
?>
