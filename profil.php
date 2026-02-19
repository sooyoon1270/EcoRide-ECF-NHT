<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header("Location: connexion.php");
    exit();
}
require_once "Database.php";
require_once "Template.php";


$database = new Database();
$pdo = $database->getConnection();
$user_id = $_SESSION['user_id'];
// 1. RÉSERVATIONS : On récupère les réservations + le statut du trajet (voyage_statut)
$sql_res = "SELECT t.*, r.statut as resa_statut, t.statut_id as voyage_statut
            FROM reservations r
            JOIN trajets t ON r.id_trajet = t.id
            WHERE r.id_utilisateur = :user_id";
$stmt_res = $pdo->prepare($sql_res);
$stmt_res->execute([':user_id' => $user_id]);
$mes_reservations = $stmt_res->fetchAll(PDO::FETCH_ASSOC);

// 2. TRAJETS PROPOSÉS : On récupère les trajets que l'utilisateur conduit
$sql_mes_trajets = "SELECT * FROM trajets WHERE id_utilisateur = :user_id ORDER BY date_depart ASC";
$stmt_mes_trajets = $pdo->prepare($sql_mes_trajets);
$stmt_mes_trajets->execute([':user_id' => $user_id]);
$mes_offres = $stmt_mes_trajets->fetchAll(PDO::FETCH_ASSOC);

// 3. VÉHICULES
$stmt_voiture = $pdo->prepare("SELECT * FROM voitures WHERE id_utilisateur = ?");
$stmt_voiture->execute([$user_id]);
$mes_voitures = $stmt_voiture->fetchAll(PDO::FETCH_ASSOC);
// 4. AVIS REÇUS (uniquement ceux validés par l'admin)
$sql_avis = "SELECT a.*, u.prenom as auteur 
             FROM avis a 
             JOIN utilisateurs u ON a.id_expediteur = u.id 
             WHERE a.id_destinataire = :user_id 
             AND a.statut_id = 'valide' 
             ORDER BY a.date_avis DESC";
$stmt_avis = $pdo->prepare($sql_avis);
$stmt_avis->execute([':user_id' => $user_id]);
$mes_avis = $stmt_avis->fetchAll(PDO::FETCH_ASSOC);


$page = new Template("Mon Profil - EcoRide");
$page->afficherHeader();
?>
<main style="max-width: 900px; margin: 40px auto; font-family: sans-serif; padding: 0 20px;">

       <?php if (isset($_GET['status'])): ?>
     <div style="background: #d4edda; color: #155724; padding: 15px; border-radius: 12px; margin-bottom: 25px; border: 1px solid #c3e6cb; text-align: center; font-weight: bold;">
            <?php
                if($_GET['status'] === 'success_add') echo "✅ Trajet publié et -2 crédits débités !";
                if($_GET['status'] === 'updated') echo "🔄 Statut du trajet mis à jour !";
                if($_GET['status'] === 'avis_sent') echo "📩 Avis envoyé et en attente de modération !";
            ?>
        </div>
    <?php endif; ?>

    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; background: #fff; padding: 20px; border-radius: 15px; box-shadow: 0 4px 10px rgba(0,0,0,0.05); margin-bottom: 30px;">
        <div>
            <h2 style="color: #2e7d32; margin: 0 0 10px 0;">👤 Mon Profil</h2>
            <p style="margin: 5px 0;"><strong><?php echo htmlspecialchars(($_SESSION['prenom'] ?? '') . " " . ($_SESSION['nom'] ?? '')); ?></strong></p>
            <p style="margin: 5px 0; color: #666;"><?php echo htmlspecialchars($_SESSION['email']); ?></p>
        </div>
        <div style="background: #f1f8e9; padding: 15px 25px; border-radius: 12px; border: 2px solid #2e7d32; text-align: center;">
            <span style="font-size: 0.8em; color: #555; font-weight: bold; text-transform: uppercase;">Mon Solde</span><br>
            <strong style="font-size: 2em; color: #2e7d32;"><?php echo $_SESSION['credits'] ?? 0; ?></strong>
            <span style="color: #2e7d32;"> éco-crédits</span>
        </div>
    </div>

    <div style="margin-bottom: 30px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
            <h3 style="margin: 0;">🚗 Mes Véhicules</h3>
            <a href="ajouter_voiture.php" style="background: #eee; color: #333; padding: 5px 12px; border-radius: 5px; text-decoration: none; font-size: 0.9em; border: 1px solid #ddd;">+ Ajouter</a>
        </div>
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 15px;">
            <?php foreach ($mes_voitures as $v): ?>
                <div style="background: white; padding: 15px; border-radius: 10px; border: 1px solid #eee; box-shadow: 0 2px 5px rgba(0,0,0,0.05);">
                    <strong><?php echo htmlspecialchars($v['marque'] . " " . $v['modele']); ?></strong><br>
                    <small style="color: #888;">Immat: <?php echo htmlspecialchars($v['immatriculation']); ?></small>
                </div>
            <?php endforeach; ?>
        </div>
    </div>


    <div style="margin-bottom: 30px; background: white; padding: 20px; border-radius: 15px; box-shadow: 0 4px 10px rgba(0,0,0,0.05);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h3 style="margin: 0;">📅 Mes trajets proposés</h3>
            <a href="proposer_trajet.php" style="background: #2e7d32; color: white; padding: 10px 15px; border-radius: 8px; text-decoration: none; font-weight: bold; font-size: 0.9em;">➕ Nouveau Trajet</a>
        </div>

        <?php if (empty($mes_offres)): ?>
            <p style="color: #888; font-style: italic;">Vous n'avez publié aucun trajet.</p>
        <?php else: ?>
            <?php foreach ($mes_offres as $offre): ?>
                <div style="padding: 15px; border: 1px solid #f0f0f0; border-radius: 10px; margin-bottom: 10px; display: flex; justify-content: space-between; align-items: center;">
                    <div>
                        <strong><?php echo htmlspecialchars($offre['depart']); ?> ➔ <?php echo htmlspecialchars($offre['arrivee']); ?></strong><br>
                        <small style="color: #666;">Départ : <?php echo date('d/m/Y à H:i', strtotime($offre['date_depart'])); ?></small>
                    </div>
                    <div>
                        <?php if (!isset($offre['statut_id']) || $offre['statut_id'] == 1): ?>
                            <a href="update_statut.php?id=<?php echo $offre['id']; ?>&new_statut=2" style="background: #2e7d32; color: white; padding: 8px 15px; border-radius: 5px; text-decoration: none; font-size: 0.85em;">🚀 Démarrer</a>
                        <?php elseif ($offre['statut_id'] == 2): ?>
                            <a href="update_statut.php?id=<?php echo $offre['id']; ?>&new_statut=3" style="background: #f39c12; color: white; padding: 8px 15px; border-radius: 5px; text-decoration: none; font-size: 0.85em;">🏁 Arrivée</a>
                        <?php else: ?>
                            <span style="color: #27ae60; font-weight: bold; font-size: 0.9em;">✅ Terminé</span>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>


    <div style="background: white; padding: 20px; border-radius: 15px; box-shadow: 0 4px 10px rgba(0,0,0,0.05);">
        <h3>🎟️ Mes réservations</h3>
        <?php if (empty($mes_reservations)): ?>
            <p style="color: #888; font-style: italic;">Aucune réservation pour le moment.</p>
        <?php else: ?>
            <?php foreach ($mes_reservations as $res): ?>
                <div style="padding: 15px; border-bottom: 1px solid #f0f0f0; display: flex; justify-content: space-between; align-items: center;">
                    <div>
                        <strong><?php echo htmlspecialchars($res['depart']); ?> ➔ <?php echo htmlspecialchars($res['arrivee']); ?></strong><br>
                        <small style="color: #666;">Billet : <?php echo htmlspecialchars($res['resa_statut']); ?></small>
                    </div>
                    <div>
                        <?php if (isset($res['voyage_statut']) && $res['voyage_statut'] == 3): ?>
                            <a href="laisser_avis.php?id_trajet=<?php echo $res['id']; ?>" style="background: #f1f8e9; color: #2e7d32; border: 1px solid #2e7d32; padding: 7px 12px; border-radius: 5px; text-decoration: none; font-size: 0.85em; font-weight: bold;">⭐ Noter</a>
                        <?php else: ?>
                            <span style="font-size: 0.8em; color: #bbb;">Trajet non terminé</span>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>


    <div style="text-align: center; margin-top: 40px;">
        <a href="deconnexion.php" style="color: #d32f2f; text-decoration: none; font-weight: bold; border: 1px solid #d32f2f; padding: 10px 30px; border-radius: 50px; font-size: 0.9em;">Se déconnecter</a>
    </div>
</main>
<?php $page->afficherFooter(); ?>