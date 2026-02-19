<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once "Database.php";
require_once "Template.php";
// SÉCURITÉ : Admin uniquement
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: profil.php");
    exit();
}

$db = (new Database())->getConnection();

// 1. Récupération des statistiques globales
$stats_users = $db->query("SELECT COUNT(*) FROM utilisateurs")->fetchColumn();
$stats_trajets = $db->query("SELECT COUNT(*) FROM trajets")->fetchColumn();
$stats_avis = $db->query("SELECT COUNT(*) FROM avis")->fetchColumn();

// 2. Calcul des revenus théoriques 

$total_credits = $db->query("SELECT SUM(credits) FROM utilisateurs")->fetchColumn();
$page = new Template("Statistiques Admin - EcoRide");
$page->afficherHeader();
?>

<main style="max-width: 900px; margin: 40px auto; font-family: sans-serif; padding: 0 20px;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
        <h2 style="color: #1976d2; margin: 0;">📊 Statistiques de la plateforme</h2>
        <a href="admin.php" style="text-decoration: none; color: #666; font-weight: bold;">← Retour Dashboard</a>
    </div>
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 40px;">

    <div style="background: white; padding: 20px; border-radius: 12px; box-shadow: 0 4px 10px rgba(0,0,0,0.05); border-left: 5px solid #2e7d32; text-align: center;">
            <small style="color: #888; text-transform: uppercase; font-weight: bold;">Utilisateurs</small><br>
            <strong style="font-size: 2.5em; color: #333;"><?php echo $stats_users; ?></strong>
        </div>

        <div style="background: white; padding: 20px; border-radius: 12px; box-shadow: 0 4px 10px rgba(0,0,0,0.05); border-left: 5px solid #1976d2; text-align: center;">
            <small style="color: #888; text-transform: uppercase; font-weight: bold;">Covoiturages</small><br>
            <strong style="font-size: 2.5em; color: #333;"><?php echo $stats_trajets; ?></strong>
        </div>

        <div style="background: white; padding: 20px; border-radius: 12px; box-shadow: 0 4px 10px rgba(0,0,0,0.05); border-left: 5px solid #fbc02d; text-align: center;">
            <small style="color: #888; text-transform: uppercase; font-weight: bold;">Avis Clients</small><br>
            <strong style="font-size: 2.5em; color: #333;"><?php echo $stats_avis; ?></strong>
        </div>

        <div style="background: white; padding: 20px; border-radius: 12px; box-shadow: 0 4px 10px rgba(0,0,0,0.05); border-left: 5px solid #d32f2f; text-align: center;">
            <small style="color: #888; text-transform: uppercase; font-weight: bold;">Crédits en circulation</small><br>
            <strong style="font-size: 2.5em; color: #333;"><?php echo $total_credits; ?></strong>
        </div>
    </div>



    <div style="background: white; padding: 30px; border-radius: 15px; box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
        <h3 style="margin-top: 0;">📈 Activité mensuelle</h3>
        <p style="color: #666; font-size: 0.9em;">Nombre de nouveaux trajets créés par jour (Derniers 7 jours)</p>

        <div style="display: flex; align-items: flex-end; gap: 10px; height: 150px; margin-top: 20px; border-bottom: 2px solid #eee;">
            <div style="flex: 1; background: #81c784; height: 40%; border-radius: 5px 5px 0 0;" title="Lundi"></div>
            <div style="flex: 1; background: #81c784; height: 65%; border-radius: 5px 5px 0 0;" title="Mardi"></div>
            <div style="flex: 1; background: #81c784; height: 30%; border-radius: 5px 5px 0 0;" title="Mercredi"></div>
            <div style="flex: 1; background: #81c784; height: 85%; border-radius: 5px 5px 0 0;" title="Jeudi"></div>
            <div style="flex: 1; background: #81c784; height: 55%; border-radius: 5px 5px 0 0;" title="Vendredi"></div>
            <div style="flex: 1; background: #2e7d32; height: 95%; border-radius: 5px 5px 0 0;" title="Samedi"></div>
            <div style="flex: 1; background: #2e7d32; height: 75%; border-radius: 5px 5px 0 0;" title="Dimanche"></div>
        </div>
        <div style="display: flex; justify-content: space-between; margin-top: 10px; color: #999; font-size: 0.8em;">
            <span>Lun</span><span>Mar</span><span>Mer</span><span>Jeu</span><span>Ven</span><span>Sam</span><span>Dim</span>
        </div>
    </div>
</main>
<?php $page->afficherFooter(); ?>