<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once "Database.php";

// Sécurité : Vérifier si l'utilisateur est connecté et si le formulaire a été envoyé
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_SESSION['user_id'])) {

    // 1. Vérification des crédits (il faut au moins 2 crédits pour publier)
    if (!isset($_SESSION['credits']) || $_SESSION['credits'] < 2) {
        header("Location: proposer_trajet.php?error=credits_insuffisants");
        exit();
    }

    $database = new Database();
    $pdo = $database->getConnection();

    // 2. Récupération et nettoyage des données du formulaire
    $id_user = $_SESSION['user_id'];
    $depart = trim($_POST['depart']);
    $arrivee = trim($_POST['arrivee']);
    $date_depart = $_POST['date']; // Correspond au 'name="date"' du formulaire
    $prix = $_POST['prix'];
    $places = $_POST['places'];
    $id_voiture = $_POST['id_voiture']; // L'ID de la voiture choisie dans le menu déroulant

    // Préférences (0 ou 1)
    $accepte_animaux = isset($_POST['animaux']) ? 1 : 0;
    $fumeur_autorise = isset($_POST['fumeur']) ? 1 : 0;
    $est_electrique = isset($_POST['est_electrique']) ? 1 : 0;

    try {
        // Début de la transaction (pour s'assurer que tout se passe bien ou rien du tout)
        $pdo->beginTransaction();

        // 3. Insertion du trajet avec l'id_voiture
        $sql = "INSERT INTO trajets (
                    depart, 
                    arrivee, 
                    date_depart, 
                    prix, 
                    places, 
                    id_utilisateur, 
                    id_voiture, 
                    statut_id, 
                    accepte_animaux, 
                    fumeur_autorise, 
                    est_electrique
                ) VALUES (?, ?, ?, ?, ?, ?, ?, 1, ?, ?, ?)";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $depart, 
            $arrivee, 
            $date_depart, 
            $prix, 
            $places, 
            $id_user, 
            $id_voiture, 
            $accepte_animaux, 
            $fumeur_autorise, 
            $est_electrique
        ]);

        // 4. Débit des 2 crédits dans la base de données
        $updateCredits = $pdo->prepare("UPDATE utilisateurs SET credits = credits - 2 WHERE id = ?");
        $updateCredits->execute([$id_user]);

        // On valide les changements
        $pdo->commit();

        // 5. Mise à jour de la variable de session pour l'affichage immédiat
        $_SESSION['credits'] -= 2;

        // Redirection vers le profil ou l'espace personnel avec un message de succès
        header("Location: profil.php?status=trajet_ajoute");
        exit();

    } catch (PDOException $e) {
        // En cas d'erreur, on annule tout (le trajet n'est pas créé et les crédits ne sont pas perdus)
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        // Affiche l'erreur pour t'aider à débugger (à retirer en production)
        die("Erreur lors de la publication : " . $e->getMessage());
    }

} else {
    // Si on essaie d'accéder au fichier sans formulaire, on renvoie à la page de proposition
    header("Location: proposer_trajet.php");
    exit();
}