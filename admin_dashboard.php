<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once "Database.php";
require_once "Template.php";

// SÉCURITÉ : Vérification du rôle admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: profil.php");
    exit();
}

// CONNEXION À LA BASE DE DONNÉES
$database = new Database();
$db = $database->getConnection();

// --- TRAITEMENT DES ACTIONS DE MODÉRATION ---
if (isset($_GET['action']) && isset($_GET['id'])) {
    $id_avis = (int)$_GET['id'];
    
    if ($_GET['action'] === 'valider') {
        // On passe le statut à 1 (Validé)
        $stmt = $db->prepare("UPDATE avis SET statut_id = 1 WHERE id = ?");
        $stmt->execute([$id_avis]);
        header("Location: admin_dashboard.php?status=valide");
        exit();
    }
}

// --- RÉCUPÉRATION DES STATISTIQUES ---
$total_users = $db->query("SELECT COUNT(*) FROM utilisateurs")->fetchColumn();
$total_trajets = $db->query("SELECT COUNT(*) FROM trajets")->fetchColumn();

// --- RÉCUPÉRATION DES AVIS ---
// 1. Avis en attente (statut_id = 2 selon votre capture phpMyAdmin)
$sql_attente = "SELECT a.*, u.prenom as auteur 
                FROM avis a 
                JOIN utilisateurs u ON a.id_expediteur = u.id 
                WHERE a.statut_id = 2 
                ORDER BY a.date_avis ASC";
$avis_en_attente = $db->query($sql_attente)->fetchAll(PDO::FETCH_ASSOC);

// 2. Historique des derniers avis validés (statut_id = 1)
$sql_valides = "SELECT * FROM avis WHERE statut_id = 1 ORDER BY date_avis DESC LIMIT 5";
$avis_recents = $db->query($sql_valides)->fetchAll(PDO::FETCH_ASSOC);

$page = new Template("Administration - EcoRide");
$page->afficherHeader();
?>

<main style="max-width: 1000px; margin: 40px auto; font-family: sans-serif; padding: 0 20px;">
    <h2 style="color: #1b5e20;">🛠️ Tableau de bord Administrateur</h2>

    <div style="display: flex; gap: 15px; margin-bottom: 30px; flex-wrap: wrap;">
        <a href="admin_users.php" style="background: #2e7d32; color: white; padding: 12px 20px; border-radius: 8px; text-decoration: none; font-weight: bold;">👥 Utilisateurs</a>
        <a href="admin_stats.php" style="background: #1976d2; color: white; padding: 12px 20px; border-radius: 8px; text-decoration: none; font-weight: bold;">📊 Statistiques</a>
    </div>

    <div style="display: flex; gap: 20px; margin-bottom: 30px;">
        <div style="flex: 1; background: #e8f5e9; padding: 20px; border-radius: 10px; text-align: center; border: 1px solid #c8e6c9;">
            <strong style="font-size: 1.5em;"><?php echo $total_users; ?></strong><br>Utilisateurs
        </div>
        <div style="flex: 1; background: #e3f2fd; padding: 20px; border-radius: 10px; text-align: center; border: 1px solid #bbdefb;">
            <strong style="font-size: 1.5em;"><?php echo $total_trajets; ?></strong><br>Trajets
        </div>
    </div>

    <h3 style="color: #d32f2f;">⏳ Avis en attente de modération</h3>
    <div style="background: white; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); overflow: hidden; margin-bottom: 30px;">
        <table style="width: 100%; border-collapse: collapse;">
            <thead style="background: #d32f2f; color: white;">
                <tr>
                    <th style="padding: 12px; text-align: left;">Auteur</th>
                    <th style="padding: 12px; text-align: left;">Commentaire</th>
                    <th style="padding: 12px; text-align: center;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($avis_en_attente)): ?>
                    <tr><td colspan="3" style="padding: 20px; text-align: center; color: #888;">Aucun avis à modérer.</td></tr>
                <?php else: ?>
                    <?php foreach ($avis_en_attente as $aa): ?>
                    <tr style="border-bottom: 1px solid #eee;">
                        <td style="padding: 12px;"><?php echo htmlspecialchars($aa['auteur']); ?></td>
                        <td style="padding: 12px; font-style: italic;">"<?php echo htmlspecialchars($aa['commentaire']); ?>"</td>
                        <td style="padding: 12px; text-align: center;">
                            <a href="admin_dashboard.php?action=valider&id=<?php echo $aa['id']; ?>" 
                               style="background: #2e7d32; color: white; padding: 5px 10px; border-radius: 5px; text-decoration: none; font-size: 0.8em; font-weight: bold;">✅ Valider</a>
                            <a href="admin_delete_avis.php?id=<?php echo $aa['id']; ?>"
                               onclick="return confirm('Supprimer cet avis ?')"
                               style="background: #d32f2f; color: white; padding: 5px 10px; border-radius: 5px; text-decoration: none; font-size: 0.8em; font-weight: bold; margin-left: 5px;">🗑️ Refuser</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <h3 style="color: #2e7d32;">⭐ Derniers avis publiés</h3>
    <div style="background: white; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); overflow: hidden;">
        <table style="width: 100%; border-collapse: collapse;">
            <thead style="background: #2e7d32; color: white;">
                <tr>
                    <th style="padding: 12px; text-align: left;">Date</th>
                    <th style="padding: 12px; text-align: left;">Note</th>
                    <th style="padding: 12px; text-align: left;">Commentaire</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($avis_recents as $avis): ?>
                <tr style="border-bottom: 1px solid #eee;">
                    <td style="padding: 12px;"><?php echo date('d/m/y', strtotime($avis['date_avis'])); ?></td>
                    <td style="padding: 12px; color: #fbc02d;">★ <?php echo $avis['note']; ?>/5</td>
                    <td style="padding: 12px;">"<?php echo htmlspecialchars($avis['commentaire']); ?>"</td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</main>

<?php $page->afficherFooter(); ?>