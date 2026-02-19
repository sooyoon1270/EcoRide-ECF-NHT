<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once "Template.php";

if (!isset($_SESSION['user_id']) || !isset($_GET['id_trajet'])) {
    header("Location: profil.php");
    exit();
}


$id_trajet = (int)$_GET['id_trajet'];
$page = new Template("Laisser un avis - EcoRide");
$page->afficherHeader();
?>


<main style="max-width: 500px; margin: 40px auto; background: #fff; padding: 30px; border-radius: 15px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); font-family: sans-serif;">
    <h2 style="color: #2e7d32; text-align: center;">⭐ Votre avis</h2>
    <p style="text-align: center; color: #666;">Comment s'est déroulé votre trajet ?</p>

    <form action="avis_action.php" method="POST" style="margin-top: 20px;">
        <input type="hidden" name="id_trajet" value="<?php echo $id_trajet; ?>">
        <div style="margin-bottom: 20px;">
            <label style="display: block; margin-bottom: 8px; font-weight: bold;">Note :</label>
            <select name="note" required style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; background: #f9f9f9;">
                <option value="5">⭐⭐⭐⭐⭐ (Excellent)</option>
                <option value="4">⭐⭐⭐⭐ (Très bien)</option>
                <option value="3">⭐⭐⭐ (Bien)</option>
                <option value="2">⭐⭐ (Moyen)</option>
                <option value="1">⭐ (À éviter)</option>
            </select>
        </div>

        <div style="margin-bottom: 20px;">
            <label style="display: block; margin-bottom: 8px; font-weight: bold;">Commentaire :</label>
            <textarea name="commentaire" required rows="4" placeholder="Points forts, ponctualité, conduite..." style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; background: #f9f9f9; resize: vertical;"></textarea>
        </div>

        <button type="submit" style="width: 100%; background: #2e7d32; color: white; padding: 14px; border: none; border-radius: 8px; font-weight: bold; cursor: pointer; font-size: 1em;">
            Envoyer mon avis
        </button>

        <div style="text-align: center; margin-top: 15px;">
            <a href="profil.php" style="color: #888; text-decoration: none; font-size: 0.9em;">Annuler</a>
        </div>
    </form>
</main>
<?php $page->afficherFooter(); ?>