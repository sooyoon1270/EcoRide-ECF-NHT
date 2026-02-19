<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once "Database.php";

// Sécurité : il faut être connecté pour réserver
if (!isset($_SESSION['user_id'])) {
    header("Location: connexion.php");
    exit();
}

$id_trajet = $_POST['id_trajet'] ?? null;
$id_passager = $_SESSION['user_id'];

if ($id_trajet) {
    $db = (new Database())->getConnection();

    // 1. On vérifie s'il reste des places
    $check = $db->prepare("SELECT places, id_utilisateur FROM trajets WHERE id = ?");
    $check->execute([$id_trajet]);
    $trajet = $check->fetch();

    if ($trajet && $trajet['places'] > 0 && $trajet['id_utilisateur'] != $id_passager) {

        // 2. On insère la réservation
        $ins = $db->prepare("INSERT INTO reservations (id_trajet, id_utilisateur, date_reservation) VALUES (?, ?, NOW())");
        if ($ins->execute([$id_trajet, $id_passager])) {
            // 3. ON DÉCREMENTE LES PLACES
            $upd = $db->prepare("UPDATE trajets SET places = places - 1 WHERE id = ?");
            $upd->execute([$id_trajet]);
            header("Location: profil.php?status=reservation_ok");
        }
    } else {
        header("Location: recherche.php?error=impossible");
    }
} else {
    header("Location: recherche.php");
}
exit();