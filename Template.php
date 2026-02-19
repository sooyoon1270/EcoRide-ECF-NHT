<?php
class Template {
    protected $titre;
    public function __construct($titre) {
        $this->titre = $titre;
    }
    public function afficherHeader() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        ?>
        <!DOCTYPE html>
        <html lang="fr">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title><?php echo htmlspecialchars($this->titre); ?></title>
            <style>
                /* 1. VARIABLES (Toujours en haut !) */
                :root {
                    --eco-green: #2E7D32;
                    --wood-brown: #6D4C41;
                    --soft-bg: #f4f7f4;
                    --dark-text: #37474F;
                    --card-shadow: 0 4px 20px rgba(0,0,0,0.05);
                }
                /* 2. BASE */
                body {
                    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
                    background-color: var(--soft-bg);
                    color: var(--dark-text);
                    margin: 0;
                    padding: 0;
                }
                .container {
                    max-width: 1100px;
                    margin: 0 auto;
                    padding: 20px;
                }
                /* 3. NAVIGATION */
                nav {
                    background: #ffffff;
                    padding: 1rem 10%;
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                    box-shadow: 0 2px 15px rgba(0,0,0,0.03);
                }
                nav .logo {
                    font-size: 1.6rem;
                    font-weight: 800;
                    color: var(--eco-green);
                    text-decoration: none;
                }
                nav a {
                    text-decoration: none;
                    color: var(--dark-text);
                    font-weight: 500;
                    margin-left: 20px;
                    transition: all 0.3s ease;
                }
                nav a:hover {
                    color: var(--wood-brown);
                }
                /* 4. BOUTONS */
                .btn-eco {

                    background-color: var(--eco-green);
                    color: white !important;
                    padding: 12px 25px;
                    border-radius: 50px;
                    text-decoration: none;
                    display: inline-block;
                    border: none;
                    font-weight: 600;
                    transition: all 0.3s ease;
                    cursor: pointer;
                }
                .btn-eco:hover {
                    background-color: var(--wood-brown);
                    transform: translateY(-2px);
                    box-shadow: 0 5px 15px rgba(109, 76, 65, 0.4);
                }
                /* 5. SECTIONS PROFIL */
                .section-profil {

                    background: #ffffff;
                    border-radius: 15px;
                    padding: 25px;
                    margin-bottom: 25px;
                    border-left: 6px solid var(--eco-green);
                    box-shadow: var(--card-shadow);

                }
                .section-profil h3 {
                    margin-top: 0;
                    color: var(--eco-green);
                    display: flex;
                    align-items: center;
                    gap: 10px;
                }
                /* STYLE BOUTON ADMIN SPECIAL */
                .admin-btn {
                    color: #d32f2f;
                    font-weight: bold;
                    border: 1px solid #d32f2f;
                    padding: 5px 10px;
                    border-radius: 5px;
                }
                .admin-btn:hover {
                    background: #d32f2f;
                    color: white !important;
                }
            </style>
        </head>
        <body>
            <nav>
                <a href="index.php" class="logo">🌿 EcoRide</a>
                <div>
                    <a href="recherche.php">Recherche</a>
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <a href="profil.php">Mon Profil</a>
                        <?php if (isset($_SESSION['role']) && strtolower($_SESSION['role']) === 'admin'): ?>
                            <a href="admin_dashboard.php" class="admin-btn">🛠️ Admin</a>
                        <?php endif; ?>
                        <a href="deconnexion.php">Déconnexion</a>
                    <?php else: ?>
                        <a href="connexion.php">Connexion</a>
                        <a href="inscription.php">Inscription</a>
                    <?php endif; ?>
                </div>
            </nav>
            <div class="container">
        <?php
    }
    public function afficherFooter() {
        ?>
            </div>
            <footer style="text-align:center; padding:30px; background:#ffffff; border-top: 3px solid var(--eco-green); margin-top: 50px;">
                <p style="color: var(--dark-text); font-weight: bold;">EcoRide - 2026 | La mobilité verte</p>
            </footer>
        </body>
        </html>
        <?php
    }
}