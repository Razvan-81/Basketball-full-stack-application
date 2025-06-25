<?php
session_start();
require 'db_connect.php';
require 'avatar-helper.php'; // ADĂUGAT pentru avatare

if (!isset($_SESSION['user_id']) || $_SESSION['tip'] !== 'admin') {
    header("Location: login.php");
    exit;
}

// Handle form submissions
$mesaj = '';
$eroare = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        // PROCESARE UPLOAD AVATAR - ADĂUGAT
        if (isset($_POST['action']) && $_POST['action'] === 'upload_avatar') {
            $user_id_avatar = $_POST['user_id'];
            
            // Verifică permisiunile (admin poate modifica orice avatar)
            if ($_SESSION['tip'] !== 'admin' && $_SESSION['user_id'] != $user_id_avatar) {
                throw new Exception("Nu ai permisiuni să modifici acest avatar!");
            }
            
            // Obține avatar vechi pentru ștergere
            $stmt = $conn->prepare("SELECT avatar FROM utilizatori WHERE id = ?");
            $stmt->execute([$user_id_avatar]);
            $old_avatar = $stmt->fetchColumn();
            
            // Upload nou avatar
            $new_avatar = handleAvatarUpload($user_id_avatar, $_FILES['avatar']);
            
            // Actualizează în BD
            $stmt = $conn->prepare("UPDATE utilizatori SET avatar = ? WHERE id = ?");
            $stmt->execute([$new_avatar, $user_id_avatar]);
            
            // Șterge avatarul vechi
            if ($old_avatar) {
                deleteOldAvatar($old_avatar);
            }
            
            $mesaj = "Avatar actualizat cu succes pentru utilizatorul cu ID: " . $user_id_avatar;
        }
        
        // ADAUGA PAGINA
        elseif (isset($_POST['adauga_pagina'])) {
            if (empty($_POST['nume']) || empty($_POST['link'])) {
                throw new Exception("Numele și link-ul sunt obligatorii!");
            }
            
            $stmt = $conn->prepare("INSERT INTO pagini (nume, link) VALUES (:nume, :link)");
            $stmt->bindParam(':nume', $_POST['nume']);
            $stmt->bindParam(':link', $_POST['link']);
            $stmt->execute();
            $mesaj = "Pagina adăugată cu succes!";
        }
        
        // STERGE PAGINA
        elseif (isset($_POST['sterge_pagina'])) {
            // Șterge mai întâi drepturile
            $stmt = $conn->prepare("DELETE FROM drepturi WHERE pagina_id = :id");
            $stmt->bindParam(':id', $_POST['pagina_id']);
            $stmt->execute();
            
            // Apoi șterge pagina
            $stmt = $conn->prepare("DELETE FROM pagini WHERE id = :id");
            $stmt->bindParam(':id', $_POST['pagina_id']);
            $stmt->execute();
            $mesaj = "Pagina și drepturile asociate au fost șterse!";
        }
        
        // ACORDA DREPT
        elseif (isset($_POST['acorda_drept'])) {
            $stmt = $conn->prepare("INSERT IGNORE INTO drepturi (utilizator_id, pagina_id) VALUES (:utilizator_id, :pagina_id)");
            $stmt->bindParam(':utilizator_id', $_POST['utilizator_id']);
            $stmt->bindParam(':pagina_id', $_POST['pagina_id']);
            $stmt->execute();
            $mesaj = "Drept acordat cu succes!";
        }
        
        // REVOCA DREPT
        elseif (isset($_POST['revoca_drept'])) {
            $stmt = $conn->prepare("DELETE FROM drepturi WHERE utilizator_id = :utilizator_id AND pagina_id = :pagina_id");
            $stmt->bindParam(':utilizator_id', $_POST['utilizator_id']);
            $stmt->bindParam(':pagina_id', $_POST['pagina_id']);
            $stmt->execute();
            $mesaj = "Drept revocat cu succes!";
        }
        
    } catch (Exception $e) {
        $eroare = $e->getMessage();
    }
}

// Prepare data for display
$pagini_system = [
    'jucator' => [
        'icon' => 'fas fa-running',
        'color' => 'primary',
        'title' => 'Jucător',
        'pages' => [
            'Tablou de Bord' => 'dashboard-player.php',
            'Jurnal Antrenamente' => 'training-log.php',
            'Grafice Performanță' => 'mental-performance.php',
            'Plan Nutriție' => 'nutrition-plan.php',
            'Obiective Personale' => 'player-goals.php',
            'Profil Jucător' => 'player-profile.php'
        ]
    ],
    'antrenor' => [
        'icon' => 'fas fa-clipboard-list',
        'color' => 'success',
        'title' => 'Antrenor',
        'pages' => [
            'Panou Control' => 'coach-dashboard.php',
            'Performanță Echipă' => 'team-performance.php',
            'Gestionare Jucători' => 'player-management.php',
            'Program Antrenament' => 'training-program.php',
            'Analiză Meci' => 'coach-match-analysis.php',
            'Strategii & Scheme' => 'coach-strategy-playbook.php'
        ]
    ],
    'preparator' => [
        'icon' => 'fas fa-heartbeat',
        'color' => 'warning',
        'title' => 'Preparator Fizic',
        'pages' => [
            'Evaluare Fizică' => 'physical-evaluation.php',
            'Plan Recuperare' => 'recovery-plan.php',
            'Prevenție Accidentări' => 'injury-prevention.php'
        ]
    ],
    'manager' => [
        'icon' => 'fas fa-briefcase',
        'color' => 'info',
        'title' => 'Manager Echipă',
        'pages' => [
            'Statistici Echipă' => 'team-statistics.php',
            'Transferuri Jucători' => 'player-transfers.php',
            'Management Buget' => 'budget-management.php',
            'Negocieri Contract' => 'manager-contract-negotiation.php',
            'Gestionare Sponsori' => 'manager-sponsorship.php'
        ]
    ],
    'admin' => [
        'icon' => 'fas fa-crown',
        'color' => 'danger',
        'title' => 'Administrator',
        'pages' => [
            'Panou Admin' => 'admin-panel.php',
            'Gestionare Complete' => 'admin-gestionare.php',
            'Pagini Sistem' => 'admin-pagini.php',
            'Vizualizare Date' => 'view_data.php'
        ]
    ],
    'generale' => [
        'icon' => 'fas fa-globe',
        'color' => 'secondary',
        'title' => 'Pagini Generale',
        'pages' => [
            'Acasă' => 'index.php',
            'Login' => 'login.php',
            'Logout' => 'logout.php',
            'Echipe' => 'echipe.php',
            'Jucători' => 'jucatori.php'
        ]
    ]
];
?>
<?php include 'header.php'; ?>

<div class="container mt-4">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>
                    <i class="fas fa-sitemap me-2"></i>Gestionare Pagini Sistem
                </h2>
                <a href="admin-panel.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Înapoi la Panoul Admin
                </a>
            </div>
            
            <?php if ($mesaj): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i><?= $mesaj ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            
            <?php if ($eroare): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i><?= $eroare ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Navigation Tabs -->
    <ul class="nav nav-tabs" id="pagesTab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="overview-tab" data-bs-toggle="tab" data-bs-target="#overview" type="button">
                <i class="fas fa-eye me-2"></i>Prezentare Generală
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="manage-tab" data-bs-toggle="tab" data-bs-target="#manage" type="button">
                <i class="fas fa-cogs me-2"></i>Gestionare Pagini
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="permissions-tab" data-bs-toggle="tab" data-bs-target="#permissions" type="button">
                <i class="fas fa-key me-2"></i>Drepturi Acces
            </button>
        </li>
    </ul>

    <div class="tab-content pt-4" id="pagesTabContent">
        <!-- ==================== OVERVIEW TAB ==================== -->
        <div class="tab-pane fade show active" id="overview" role="tabpanel">
            <div class="row">
                <?php foreach ($pagini_system as $rol => $data): ?>
                <div class="col-lg-6 col-xl-4 mb-4">
                    <div class="card h-100 shadow-sm">
                        <div class="card-header bg-<?= $data['color'] ?> text-white">
                            <h5 class="card-title mb-0">
                                <i class="<?= $data['icon'] ?> me-2"></i><?= $data['title'] ?>
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="list-group list-group-flush">
                                <?php foreach ($data['pages'] as $nume => $link): ?>
                                <div class="list-group-item d-flex justify-content-between align-items-center py-2 px-0">
                                    <span>
                                        <i class="fas fa-file-alt me-2 text-muted"></i><?= $nume ?>
                                    </span>
                                    <small class="text-muted"><?= $link ?></small>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <div class="card-footer text-muted">
                            <small><i class="fas fa-info-circle me-1"></i><?= count($data['pages']) ?> pagini disponibile</small>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- ==================== MANAGE PAGES TAB ==================== -->
        <div class="tab-pane fade" id="manage" role="tabpanel">
            <!-- Add Page Form -->
            <div class="card mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-plus me-2"></i>Adaugă Pagină Nouă</h5>
                </div>
                <div class="card-body">
                    <form method="POST" class="row g-3">
                        <div class="col-md-5">
                            <label class="form-label">Nume Pagină *</label>
                            <input name="nume" class="form-control" placeholder="Ex: Dashboard Antrenor" required>
                        </div>
                        <div class="col-md-5">
                            <label class="form-label">Link Pagină *</label>
                            <input name="link" class="form-control" placeholder="Ex: coach-dashboard.php" required>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">&nbsp;</label>
                            <button name="adauga_pagina" class="btn btn-success w-100">
                                <i class="fas fa-plus"></i> Adaugă
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Pages List -->
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-list me-2"></i>Toate Paginile din Sistem</h5>
                    <div class="col-md-4">
                        <input type="text" id="searchPages" class="form-control" placeholder="🔍 Caută pagini...">
                    </div>
                </div>
                <div class="card-body p-0">
                    <?php
                    try {
                        $stmt = $conn->query("SELECT * FROM pagini ORDER BY id ASC");
                        echo '<div class="table-responsive">
                                <table class="table table-hover mb-0" id="pagesTable">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>ID</th>
                                            <th>Nume Pagină</th>
                                            <th>Link</th>
                                            <th>Nr. Utilizatori cu Acces</th>
                                            <th>Acțiuni</th>
                                        </tr>
                                    </thead>
                                    <tbody>';
                        while ($row = $stmt->fetch()) {
                            // Count users with access
                            $count_stmt = $conn->prepare("SELECT COUNT(*) as count FROM drepturi WHERE pagina_id = :pagina_id");
                            $count_stmt->bindParam(':pagina_id', $row['id']);
                            $count_stmt->execute();
                            $count = $count_stmt->fetch()['count'];
                            
                            echo "<tr>
                                    <td><strong>#{$row['id']}</strong></td>
                                    <td><i class='fas fa-file-alt me-2'></i><strong>{$row['nume']}</strong></td>
                                    <td><code>{$row['link']}</code></td>
                                    <td><span class='badge bg-info'>{$count} utilizatori</span></td>
                                    <td>
                                        <form method='POST' class='d-inline'>
                                            <input type='hidden' name='pagina_id' value='{$row['id']}'>
                                            <button name='sterge_pagina' class='btn btn-sm btn-outline-danger' onclick='return confirm(\"Ștergi pagina {$row['nume']}? Toate drepturile vor fi revocate!\")'>
                                                <i class='fas fa-trash'></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>";
                        }
                        echo '</tbody></table></div>';
                    } catch (Exception $e) {
                        echo '<div class="alert alert-danger m-3">Eroare: ' . $e->getMessage() . '</div>';
                    }
                    ?>
                </div>
            </div>
        </div>

        <!-- ==================== PERMISSIONS TAB ==================== -->
        <div class="tab-pane fade" id="permissions" role="tabpanel">
            <!-- Grant Permission Form -->
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-key me-2"></i>Acordă Drept de Acces</h5>
                </div>
                <div class="card-body">
                    <form method="POST" class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Utilizator *</label>
                            <select name="utilizator_id" class="form-select" required>
                                <option value="">Alege utilizatorul</option>
                                <?php
                                try {
                                    // MODIFICAT - Query pentru a include avatar
                                    $users = $conn->query("SELECT id, username, tip, avatar FROM utilizatori WHERE activ = 1 ORDER BY username");
                                    while ($user = $users->fetch()) {
                                        echo "<option value='{$user['id']}'>{$user['username']} ({$user['tip']})</option>";
                                    }
                                } catch (Exception $e) {
                                    echo "<option value=''>Eroare la încărcare</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Pagină *</label>
                            <select name="pagina_id" class="form-select" required>
                                <option value="">Alege pagina</option>
                                <?php
                                try {
                                    $pages = $conn->query("SELECT id, nume, link FROM pagini ORDER BY nume");
                                    while ($page = $pages->fetch()) {
                                        echo "<option value='{$page['id']}'>{$page['nume']} ({$page['link']})</option>";
                                    }
                                } catch (Exception $e) {
                                    echo "<option value=''>Eroare la încărcare</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">&nbsp;</label>
                            <button name="acorda_drept" class="btn btn-primary w-100">
                                <i class="fas fa-plus"></i> Acordă
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Current Permissions -->
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-shield-alt me-2"></i>Drepturi Curente de Acces</h5>
                    <div class="col-md-4">
                        <input type="text" id="searchPermissions" class="form-control" placeholder="🔍 Caută drepturi...">
                    </div>
                </div>
                <div class="card-body p-0">
                    <?php
                    try {
                        // MODIFICAT - Query pentru a include avatar
                        $stmt = $conn->query("
                            SELECT d.*, u.username, u.tip, u.avatar, p.nume as pagina_nume, p.link 
                            FROM drepturi d 
                            JOIN utilizatori u ON d.utilizator_id = u.id 
                            JOIN pagini p ON d.pagina_id = p.id 
                            ORDER BY u.username, p.nume
                        ");
                        echo '<div class="table-responsive">
                                <table class="table table-hover mb-0" id="permissionsTable">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>Utilizator</th>
                                            <th>Tip Utilizator</th>
                                            <th>Pagină</th>
                                            <th>Link</th>
                                            <th>Acțiuni</th>
                                        </tr>
                                    </thead>
                                    <tbody>';
                        while ($row = $stmt->fetch()) {
                            $tipBadge = [
                                'admin' => 'danger',
                                'antrenor' => 'success', 
                                'jucator' => 'primary',
                                'preparator' => 'warning',
                                'manager' => 'info'
                            ];
                            $badgeColor = $tipBadge[$row['tip']] ?? 'secondary';
                            
                            // ADĂUGAT - Avatar în tabel
                            $avatarDisplay = displayAvatar($row['utilizator_id'], $row['username'], $row['avatar'], 30);
                            
                            echo "<tr>
                                    <td>
                                        <div class='d-flex align-items-center'>
                                            $avatarDisplay
                                            <div class='ms-2'>
                                                <strong>{$row['username']}</strong>
                                                <button type='button' class='btn btn-sm btn-outline-secondary ms-1' onclick='openAvatarModal({$row['utilizator_id']}, \"{$row['username']}\", \"{$row['avatar']}\")' title='Schimbă avatar'>
                                                    <i class='fas fa-camera'></i>
                                                </button>
                                            </div>
                                        </div>
                                    </td>
                                    <td><span class='badge bg-{$badgeColor}'>" . ucfirst($row['tip']) . "</span></td>
                                    <td><i class='fas fa-file-alt me-2'></i>{$row['pagina_nume']}</td>
                                    <td><code>{$row['link']}</code></td>
                                    <td>
                                        <form method='POST' class='d-inline'>
                                            <input type='hidden' name='utilizator_id' value='{$row['utilizator_id']}'>
                                            <input type='hidden' name='pagina_id' value='{$row['pagina_id']}'>
                                            <button name='revoca_drept' class='btn btn-sm btn-outline-danger' onclick='return confirm(\"Revoci dreptul pentru {$row['username']} la {$row['pagina_nume']}?\")'>
                                                <i class='fas fa-times'></i> Revocă
                                            </button>
                                        </form>
                                    </td>
                                </tr>";
                        }
                        echo '</tbody></table></div>';
                    } catch (Exception $e) {
                        echo '<div class="alert alert-danger m-3">Eroare: ' . $e->getMessage() . '</div>';
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- MODAL UPLOAD AVATAR - ADĂUGAT -->
<div class="modal fade" id="avatarModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Schimbă Avatar Utilizator</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="post" enctype="multipart/form-data" id="avatarForm">
                <input type="hidden" name="action" value="upload_avatar">
                <input type="hidden" name="user_id" id="avatar_user_id">
                <div class="modal-body text-center">
                    <div class="mb-3">
                        <div id="current_avatar" class="mb-3"></div>
                        <p class="text-muted">Avatar curent</p>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Selectează imagine nouă:</label>
                        <input type="file" name="avatar" class="form-control" accept="image/*" required>
                        <small class="text-muted">Mărime maximă: 2MB. Formate: JPG, PNG, GIF</small>
                    </div>
                    
                    <div id="preview_container" style="display: none;">
                        <p class="text-success">Preview:</p>
                        <img id="preview_image" style="width: 150px; height: 150px; object-fit: cover;" class="rounded-circle">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Anulează</button>
                    <button type="submit" class="btn btn-primary">Salvează Avatar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Search Pages
    document.getElementById('searchPages').addEventListener('keyup', function() {
        const searchValue = this.value.toLowerCase();
        const table = document.getElementById('pagesTable');
        const rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');
        
        for (let i = 0; i < rows.length; i++) {
            const row = rows[i];
            const nume = row.cells[1].textContent.toLowerCase();
            const link = row.cells[2].textContent.toLowerCase();
            
            if (nume.includes(searchValue) || link.includes(searchValue)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        }
    });
    
    // Search Permissions
    document.getElementById('searchPermissions').addEventListener('keyup', function() {
        const searchValue = this.value.toLowerCase();
        const table = document.getElementById('permissionsTable');
        const rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');
        
        for (let i = 0; i < rows.length; i++) {
            const row = rows[i];
            const username = row.cells[0].textContent.toLowerCase();
            const tip = row.cells[1].textContent.toLowerCase();
            const pagina = row.cells[2].textContent.toLowerCase();
            const link = row.cells[3].textContent.toLowerCase();
            
            if (username.includes(searchValue) || tip.includes(searchValue) || 
                pagina.includes(searchValue) || link.includes(searchValue)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        }
    });
});

// FUNCȚII AVATAR - ADĂUGAT
// Preview imagine
document.querySelector('input[name="avatar"]').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('preview_image').src = e.target.result;
            document.getElementById('preview_container').style.display = 'block';
        };
        reader.readAsDataURL(file);
    }
});

function openAvatarModal(userId, username, avatar) {
    document.getElementById('avatar_user_id').value = userId;
    document.getElementById('current_avatar').innerHTML = displayAvatarJS(userId, username, avatar, 150);
    document.getElementById('preview_container').style.display = 'none';
    new bootstrap.Modal(document.getElementById('avatarModal')).show();
}

function displayAvatarJS(userId, username, avatar, size) {
    const initials = username.substring(0, 2).toUpperCase();
    if (avatar) {
        return `<img src="uploads/avatars/${avatar}" alt="${username}" class="rounded-circle" style="width: ${size}px; height: ${size}px; object-fit: cover;">`;
    } else {
        return `<div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center mx-auto" style="width: ${size}px; height: ${size}px; font-size: ${size/2.5}px;">${initials}</div>`;
    }
}
</script>

<?php include 'footer.php'; ?>