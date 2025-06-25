<?php
// Verificăm dacă utilizatorul este logat
$is_logged_in = isset($_SESSION['user_id']);
$username = $is_logged_in ? $_SESSION['username'] : '';
$user_tip = $is_logged_in ? $_SESSION['tip'] : '';

// ADĂUGAT - Obține avatar pentru utilizatorul logat
$user_avatar = null;
if ($is_logged_in) {
    // Verifică dacă avatar-helper.php este inclus
    if (function_exists('displayAvatar')) {
        try {
            // Obține avatarul din baza de date
            $stmt = $conn->prepare("SELECT avatar FROM utilizatori WHERE id = ?");
            $stmt->execute([$_SESSION['user_id']]);
            $user_avatar = $stmt->fetchColumn();
        } catch (Exception $e) {
            // În caz de eroare, user_avatar rămâne null
            $user_avatar = null;
        }
    }
}
?>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <!-- Logo/Brand -->
        <a class="navbar-brand fw-bold" href="index.php">
            <i class="fas fa-basketball-ball me-2 text-warning"></i>
            BasketProgress
        </a>

        <!-- Toggle button pentru mobile -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Navbar content -->
        <div class="collapse navbar-collapse" id="navbarNav">
            <!-- Left side navigation -->
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link" href="logout.php">
                        <i class="fas fa-home me-1"></i>Acasă
                    </a>
                </li>
            </ul>

            <!-- Right side navigation -->
            <ul class="navbar-nav">
                <?php if ($is_logged_in): ?>
                    <!-- User is logged in -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <!-- ADĂUGAT - Avatar în navbar -->
                            <div class="me-2">
                                <?php if (function_exists('displayAvatar')): ?>
                                    <?= displayAvatar($_SESSION['user_id'], $username, $user_avatar, 32) ?>
                                <?php else: ?>
                                    <!-- Fallback dacă avatar-helper.php nu este inclus -->
                                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; font-size: 14px;">
                                        <?= strtoupper(substr($username, 0, 2)) ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="d-flex flex-column align-items-start">
                                <span class="fw-bold mb-0" style="line-height: 1;"><?= htmlspecialchars($username) ?></span>
                                <span class="badge bg-<?= $user_tip === 'admin' ? 'danger' : ($user_tip === 'antrenor' ? 'success' : ($user_tip === 'jucator' ? 'primary' : ($user_tip === 'preparator' ? 'warning' : 'info'))) ?>" style="font-size: 0.6rem;">
                                    <?= ucfirst($user_tip) ?>
                                </span>
                            </div>
                        </a>
                        
                        <ul class="dropdown-menu dropdown-menu-end">
                            <!-- ADĂUGAT - Header dropdown cu avatar mare -->
                            <li class="dropdown-header text-center py-3" style="border-bottom: 1px solid #dee2e6; margin-bottom: 8px;">
                                <?php if (function_exists('displayAvatar')): ?>
                                    <?= displayAvatar($_SESSION['user_id'], $username, $user_avatar, 64) ?>
                                <?php else: ?>
                                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center mx-auto" style="width: 64px; height: 64px; font-size: 24px;">
                                        <?= strtoupper(substr($username, 0, 2)) ?>
                                    </div>
                                <?php endif; ?>
                                <div class="mt-2">
                                    <strong><?= htmlspecialchars($username) ?></strong>
                                    <br>
                                    <small class="text-muted"><?= ucfirst($user_tip) ?></small>
                                </div>
                            </li>
                            
                            <?php 
                            // Define dashboard links based on user type
                            $dashboard_links = [
                                'admin' => ['icon' => 'fas fa-crown', 'text' => 'Panou Admin', 'link' => 'admin-panel.php'],
                                'antrenor' => ['icon' => 'fas fa-clipboard-list', 'text' => 'Dashboard Antrenor', 'link' => 'coach-dashboard.php'],
                                'jucator' => ['icon' => 'fas fa-running', 'text' => 'Dashboard Jucător', 'link' => 'dashboard-player.php'],
                                'preparator' => ['icon' => 'fas fa-heartbeat', 'text' => 'Dashboard Preparator', 'link' => 'physical-evaluation.php'],
                                'manager' => ['icon' => 'fas fa-briefcase', 'text' => 'Dashboard Manager', 'link' => 'team-statistics.php']
                            ];

                            if (isset($dashboard_links[$user_tip])) {
                                $dashboard = $dashboard_links[$user_tip];
                            ?>
                                <li>
                                    <a class="dropdown-item" href="<?= htmlspecialchars($dashboard['link']) ?>">
                                        <i class="<?= htmlspecialchars($dashboard['icon']) ?> me-2 text-primary"></i><?= htmlspecialchars($dashboard['text']) ?>
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                            <?php } ?>
                            
                            <li>
                                <a class="dropdown-item" href="player-profile.php">
                                    <i class="fas fa-user-edit me-2 text-info"></i>Profilul Meu
                                </a>
                            </li>
                            
                            <!-- ADĂUGAT - Link pentru schimbarea avatarului -->
                            <?php if (function_exists('displayAvatar')): ?>
                            <li>
                                <a class="dropdown-item" href="player-profile.php" title="Schimbă avatarul din pagina de profil">
                                    <i class="fas fa-camera me-2 text-warning"></i>Schimbă Avatar
                                </a>
                            </li>
                            <?php endif; ?>
                            
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a class="dropdown-item text-danger" href="logout.php">
                                    <i class="fas fa-sign-out-alt me-2"></i>Deconectare
                                </a>
                            </li>
                        </ul>
                    </li>
                <?php else: ?>
                    <!-- User is not logged in -->
                    <li class="nav-item">
                        <a class="nav-link" href="login.php">
                            <i class="fas fa-sign-in-alt me-1"></i>Autentificare
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="sign-up.php">
                            <i class="fas fa-user-plus me-1"></i>Înregistrare
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

<style>
/* Custom navbar styles */
.navbar-brand {
    font-size: 1.5rem;
    letter-spacing: 1px;
}

.navbar-nav .nav-link {
    font-weight: 500;
    transition: all 0.3s ease;
    border-radius: 6px;
    margin: 0 2px;
}

.navbar-nav .nav-link:hover {
    background-color: rgba(255, 255, 255, 0.1);
    transform: translateY(-1px);
}

/* ADĂUGAT - Stiluri pentru avatar în navbar */
.navbar-nav .dropdown-toggle {
    padding: 8px 12px;
    border-radius: 25px;
    transition: all 0.3s ease;
}

.navbar-nav .dropdown-toggle:hover {
    background-color: rgba(255, 255, 255, 0.15);
}

.dropdown-menu {
    border: none;
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
    border-radius: 10px;
    margin-top: 8px;
    min-width: 280px; /* MODIFICAT - Lățime mai mare pentru avatar */
}

.dropdown-header {
    background-color: #f8f9fa;
    border-radius: 10px 10px 0 0;
    padding: 20px !important;
}

.dropdown-item {
    font-weight: 500;
    padding: 12px 20px; /* MODIFICAT - Padding mai mare */
    transition: all 0.3s ease;
}

.dropdown-item:hover {
    background-color: #f8f9fa;
    transform: translateX(5px);
}

.dropdown-item.text-danger:hover {
    background-color: #f8d7da;
    color: #721c24 !important;
}

.dropdown-item i {
    width: 20px; /* ADĂUGAT - Lățime fixă pentru iconițe */
    text-align: center;
}

.badge {
    font-size: 0.7rem;
    font-weight: 600;
}

/* ADĂUGAT - Stiluri responsive pentru avatar */
@media (max-width: 991px) {
    .navbar-nav .nav-link {
        margin: 2px 0;
        padding: 10px 15px;
    }
    
    .dropdown-menu {
        border: 1px solid #dee2e6;
        margin-top: 0;
        min-width: 250px;
    }
    
    .dropdown-header {
        padding: 15px !important;
    }
    
    .navbar-nav .dropdown-toggle {
        border-radius: 8px;
        padding: 10px 15px;
    }
}

/* ADĂUGAT - Stiluri pentru avatar rotund */
.dropdown-menu img,
.navbar-nav img {
    border: 2px solid rgba(255, 255, 255, 0.2);
    transition: all 0.3s ease;
}

.dropdown-header img {
    border: 3px solid #fff;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.navbar-nav .dropdown-toggle:hover img {
    border-color: rgba(255, 255, 255, 0.4);
}

/* Smooth animations */
.navbar-collapse {
    transition: all 0.3s ease;
}

.dropdown-menu {
    animation: fadeInDown 0.3s ease;
}

@keyframes fadeInDown {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* ADĂUGAT - Animații pentru avatar */
.navbar-nav img,
.navbar-nav .rounded-circle {
    transition: transform 0.3s ease;
}

.navbar-nav .dropdown-toggle:hover img,
.navbar-nav .dropdown-toggle:hover .rounded-circle {
    transform: scale(1.1);
}
</style>