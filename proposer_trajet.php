<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Sécurité : on vérifie que l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header("Location: connexion.php"); 
    exit();
}

require_once "Database.php";
require_once "Template.php";

$database = new Database();
$pdo = $database->getConnection();

$page = new Template("EcoRide - Proposer un Trajet");
$page->afficherHeader();
?>

<main style="padding: 50px; max-width: 500px; margin: 0 auto;">
    <h2 style="color: #2e7d32; text-align: center;">Proposer un nouveau trajet ⚡</h2>
    
    <form action="proposer_action.php" method="POST" style="display: flex; flex-direction: column; gap: 15px; background: #f9f9f9; padding: 20px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
        
        <label style="font-weight: bold;">Ville de Départ :</label>
        <input type="text" name="depart" placeholder="Ex: Paris" required style="padding: 10px; border: 1px solid #ccc; border-radius: 4px;">
        
        <label style="font-weight: bold;">Ville d'Arrivée :</label>
        <input type="text" name="arrivee" placeholder="Ex: Lyon" required style="padding: 10px; border: 1px solid #ccc; border-radius: 4px;">
        
        <label style="font-weight: bold;">Date et heure :</label>
        <input type="datetime-local" name="date" required style="padding: 10px; border: 1px solid #ccc; border-radius: 4px;">
        
        <label style="font-weight: bold;">Prix (€) :</label>
        <input type="number" name="prix" step="0.01" placeholder="Ex: 15.50" required style="padding: 10px; border: 1px solid #ccc; border-radius: 4px;">
        
        <label style="font-weight: bold;">Nombre de places :</label>
        <input type="number" name="places" placeholder="Nombre de places disponibles" required style="padding: 10px; border: 1px solid #ccc; border-radius: 4px;">

        <label style="font-weight: bold;">Choisir votre véhicule :</label>
        <select name="id_voiture" required style="padding: 10px; border: 1px solid #ccc; border-radius: 4px; background: white;">
            <option value="">-- Sélectionnez une voiture --</option>
            <?php
            // On récupère uniquement les voitures appartenant à l'utilisateur connecté
            $stmt = $pdo->prepare("SELECT id, modele, immatriculation FROM voitures WHERE id_utilisateur = ?");
            $stmt->execute([$_SESSION['user_id']]);
            $voitures = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (count($voitures) > 0) {
                foreach ($voitures as $v) {
                    echo "<option value='{$v['id']}'>{$v['modele']} ({$v['immatriculation']})</option>";
                }
            } else {
                echo "<option value='0'>Aucune voiture enregistrée</option>";
            }
            ?>
        </select>

        <div style="display: flex; flex-direction: column; gap: 10px; margin-top: 10px; padding: 10px; border-top: 1px solid #ddd;">
            <label style="display: flex; align-items: center; gap: 10px; cursor: pointer;">
                <input type="checkbox" name="animaux" value="1" style="width: 18px; height: 18px;"> 
                <span>Autoriser les animaux 🐶</span>
            </label>

            <label style="display: flex; align-items: center; gap: 10px; cursor: pointer;">
                <input type="checkbox" name="fumeur" value="1" style="width: 18px; height: 18px;"> 
                <span>Fumeur autorisé 🚬</span>
            </label>
            
            <label style="display: flex; align-items: center; gap: 10px; cursor: pointer;">
                <input type="checkbox" name="est_electrique" value="1" style="width: 18px; height: 18px;"> 
                <span>Covoiturage Éco (Électrique) 🌱</span>
            </label>
        </div>

        <button type="submit" style="background-color: #28a745; color: white; border: none; padding: 15px; border-radius: 5px; cursor: pointer; font-weight: bold; font-size: 1.1em; margin-top: 10px; transition: 0.3s;">
            Publier le trajet ⚡
        </button>
    </form>
</main>

<?php $page->afficherFooter(); ?>