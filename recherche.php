<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once "Database.php";
require_once "Template.php";
require_once "Accueil.php"; // Indispensable pour appeler la classe PageAccueil

$database = new Database();
$pdo = $database->getConnection();
$pa = new PageAccueil(); // On instancie la classe pour utiliser la barre de recherche

// 1. RÉCUPÉRATION DES FILTRES
$depart = $_GET['dep'] ?? '';
$arrivee = $_GET['arr'] ?? '';
$date = $_GET['date_rep'] ?? '';
$eco = $_GET['eco'] ?? '';
$prix_max = $_GET['prix_max'] ?? '';

// 2. CONSTRUCTION DE LA REQUÊTE SQL DYNAMIQUE
$sql = "SELECT t.*, u.prenom, v.marque, v.modele, v.energie
        FROM trajets t
        JOIN utilisateurs u ON t.id_utilisateur = u.id
        LEFT JOIN voitures v ON t.id_voiture = v.id
        WHERE 1=1"; 
$params = [];

if (!empty($depart)) {
    $sql .= " AND t.depart LIKE :depart";
    $params[':depart'] = "%$depart%";
}
if (!empty($arrivee)) {
    $sql .= " AND t.arrivee LIKE :arrivee";
    $params[':arrivee'] = "%$arrivee%";
}
if (!empty($date)) {
    $sql .= " AND DATE(t.date_depart) = :date";
    $params[':date'] = $date;
}
if ($eco === '1') {
    $sql .= " AND t.est_electrique = 1";
}
if (!empty($prix_max)) {
    $sql .= " AND t.prix <= :prix_max";
    $params[':prix_max'] = $prix_max;
}

$sql .= " ORDER BY t.date_depart ASC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$trajets = $stmt->fetchAll(PDO::FETCH_ASSOC);

$page = new Template("Rechercher un trajet - EcoRide");
$page->afficherHeader();
?>
<style>
    .suggestion-box {
        position: absolute; width: 100%; background: white;
        z-index: 1000; border: 1px solid #ddd; display: none;
        box-shadow: 0 4px 10px rgba(0,0,0,0.1); border-radius: 0 0 5px 5px;
    }
    .suggestion-item { padding: 10px; cursor: pointer; border-bottom: 1px solid #eee; }
    .suggestion-item:last-child { border: none; }
    .suggestion-item:hover { background: #f0f0f0; }
    .trajet-card:hover { transform: translateY(-5px); }
</style>

<main style="padding: 20px; font-family: sans-serif; background: #fdfdfd; min-height: 80vh;">
    
    <?php 
    // APPEL DE LA BARRE DE RECHERCHE UNIFIÉE
    // On passe les variables actuelles pour que les champs restent remplis après validation
    $pa->afficherBarreRecherche($depart, $arrivee, $date, $eco, $prix_max); 
    ?>

    <h2 style="text-align: center; color: #333; margin-bottom: 5px;">Trajets disponibles</h2>
    <div style="width: 60px; height: 4px; background: #2e7d32; margin: 0 auto 40px;"></div>
    
    <div style="display: flex; flex-wrap: wrap; justify-content: center; gap: 25px;">
        <?php if (count($trajets) > 0): ?>
            <?php foreach ($trajets as $t): ?>
                <div class="trajet-card" style="width: 350px; border-radius: 15px; box-shadow: 0 10px 25px rgba(0,0,0,0.08); background: white; overflow: hidden; transition: 0.3s; border: 1px solid #eee;">
                    <div style="background: #2e7d32; color: white; padding: 20px; text-align: center;">
                        <h3 style="margin: 0; font-size: 1.2em;"><?php echo htmlspecialchars($t['depart']); ?> ➔ <?php echo htmlspecialchars($t['arrivee']); ?></h3>
                    </div>
    
                    <div style="padding: 20px;">
                        <div style="display: flex; justify-content: space-between; margin-bottom: 15px;">
                            <span>📅 <strong><?php echo date('d/m/Y', strtotime($t['date_depart'])); ?></strong></span>
                            <span>🕒 <strong><?php echo date('H:i', strtotime($t['date_depart'])); ?></strong></span>
                        </div>

                        <div style="background: #f9f9f9; padding: 12px; border-radius: 10px; margin-bottom: 15px;">
                            <small style="color: #666; font-weight: bold; display: block; margin-bottom: 5px;">🚙 VÉHICULE</small>
                            <strong><?php echo !empty($t['marque']) ? htmlspecialchars($t['marque'] . " " . $t['modele']) : "Véhicule non précisé"; ?></strong>
                            <?php if(!empty($t['energie'])): ?>
                                <span style="font-size: 0.75em; background: #e8f5e9; color: #2e7d32; padding: 3px 8px; border-radius: 10px; margin-left: 5px; font-weight: bold;">
                                    <?php echo htmlspecialchars($t['energie']); ?>
                                </span>
                            <?php endif; ?>
                            <br><small style="color: #888;">Chauffeur : <?php echo htmlspecialchars($t['prenom']); ?></small>
                        </div>

                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <div>
                                <span style="font-size: 1.5em; color: #2e7d32; font-weight: bold;"><?php echo $t['prix']; ?> €</span>
                                <br><small style="color: #999;"><?php echo $t['places']; ?> places restantes</small>
                            </div>
                            <a href="detail_trajet.php?id=<?php echo $t['id']; ?>" style="background: #1a73e8; color: white; padding: 10px 20px; text-decoration: none; border-radius: 8px; font-weight: bold; font-size: 0.9em;">Détails</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div style="text-align: center; padding: 50px;">
                <span style="font-size: 4em;">🔍</span>
                <p style="color: #666; font-size: 1.2em; margin-top: 10px;">Désolé, aucun trajet ne correspond à vos critères.</p>
                <a href="recherche.php" style="color: #2e7d32; font-weight: bold;">Réinitialiser la recherche</a>
            </div>
        <?php endif; ?>
    </div>
</main>

<script>
    function initAutocomplete(inputId, boxId) {
        const input = document.getElementById(inputId);
        const box = document.getElementById(boxId);
        if(!input || !box) return; // Sécurité si les éléments n'existent pas

        input.addEventListener('input', function() {
            let val = this.value;
            if (val.length > 2) {
                fetch(`https://api-adresse.data.gouv.fr/search/?q=${val}&type=municipality&limit=5`)
                    .then(r => r.json())
                    .then(data => {
                        box.innerHTML = "";
                        box.style.display = "block";
                        data.features.forEach(f => {
                            let d = document.createElement('div');
                            d.className = 'suggestion-item';
                            d.innerText = f.properties.label;
                            d.onclick = () => { input.value = f.properties.label; box.style.display = "none"; };
                            box.appendChild(d);
                        });
                    });
            } else { box.style.display = "none"; }
        });
        document.addEventListener('click', (e) => { if (e.target !== input) box.style.display = "none"; });
    }
    initAutocomplete('depart', 'suggestions-dep');
    initAutocomplete('arrivee', 'suggestions-arr');
</script>
<?php $page->afficherFooter(); ?>