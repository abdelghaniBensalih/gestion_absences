<?php
require_once "../config/db.php";

/**
 * Script qui vérifie les séances terminées et génère les absences
 * pour les étudiants qui n'ont pas enregistré leur présence
 */

// Récupérer les séances terminées qui n'ont pas encore été traitées pour les absences
$now = date('Y-m-d H:i:s');
$sql = "SELECT s.id_seance, s.id_module, s.date_seance, s.duree, s.type_seance, 
               m.nom as module_nom, m.id_filiere 
        FROM seances s
        JOIN modules m ON s.id_module = m.id_module
        WHERE DATE_ADD(s.date_seance, INTERVAL s.duree MINUTE) <= ?
        AND s.absences_generees = 0";  // Ajout d'un flag pour éviter de traiter plusieurs fois

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

/**
 * Traite une séance terminée en générant les absences
 */
function processSeance($seance, $pdo) {
    $seanceId = $seance['id_seance'];
    $moduleId = $seance['id_module'];
    $filiereId = $seance['id_filiere'];
    
    try {
        // 1. Récupérer tous les étudiants inscrits à ce module
        $stmtEtudiants = $pdo->prepare(
            "SELECT e.apogee 
             FROM etudiants e 
             JOIN inscriptions i ON e.apogee = i.id_etudiant 
             WHERE i.id_module = ?"
        );
        $stmtEtudiants->execute([$moduleId]);
        $etudiants = $stmtEtudiants->fetchAll(PDO::FETCH_ASSOC);
        
        // 2. Récupérer les étudiants qui ont déjà enregistré leur présence
        $stmtPresents = $pdo->prepare(
            "SELECT apogee FROM presences WHERE id_seance = ?"
        );
        $stmtPresents->execute([$seanceId]);
        $presents = $stmtPresents->fetchAll(PDO::FETCH_COLUMN);
        
        // 3. Générer les absences pour les étudiants qui n'ont pas enregistré de présence
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
                } catch (PDOException $e) {
                    // Ignorer les doublons (constraint violations)
                    if ($e->getCode() != 23000) {
                        throw $e;
                    }
                }
            }
        }
        
        // 4. Marquer la séance comme traitée
        $stmtUpdateSeance = $pdo->prepare(
            "UPDATE seances SET absences_generees = 1 WHERE id_seance = ?"
        );
        $stmtUpdateSeance->execute([$seanceId]);
        
        echo "<!-- Séance #$seanceId: $absencesCount absences générées -->\n";
        
    } catch (PDOException $e) {
        error_log("Erreur lors du traitement de la séance #$seanceId: " . $e->getMessage());
        echo "<!-- Erreur séance #$seanceId: " . $e->getMessage() . " -->\n";
    }
}
?>