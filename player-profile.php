<?php
session_start();
require 'db_connect.php';
require 'avatar-helper.php'; // ADĂUGAT pentru avatare

if (!isset($_SESSION['user_id']) || $_SESSION['tip'] !== 'jucator') {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];

// PROCESARE UPLOAD AVATAR - ADĂUGAT
if (isset($_POST['action']) && $_POST['action'] === 'upload_avatar') {
    try {
        $user_id_avatar = $_POST['user_id'];
        
        // Verifică permisiunile
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
        
        $mesaj_avatar = "Avatar actualizat cu succes!";
        
    } catch (Exception $e) {
        $eroare_avatar = $e->getMessage();
    }
}

// Meniu lateral
$stmt = $conn->prepare("
    SELECT p.nume, p.link
    FROM pagini p
    JOIN drepturi d ON p.id = d.pagina_id
    WHERE d.utilizator_id = ? AND p.link IN ('dashboard-player.php', 'training-log.php', 'player-profile.php')
    ORDER BY p.id
");
$stmt->execute([$user_id]);
$meniu = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Verifică dacă se cere editarea
$editare = isset($_GET['edit']);

// Prelucrare form profil
$mesaj = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['action'])) {
    $data = [
        'data_nasterii' => $_POST['data_nasterii'],
        'inaltime' => $_POST['inaltime'],
        'greutate' => $_POST['greutate'],
        'nationalitate' => $_POST['nationalitate'],
        'puncte_meci' => $_POST['puncte_meci'],
        'recuperari_meci' => $_POST['recuperari_meci'],
        'pase_meci' => $_POST['pase_meci']
    ];

    $stmt = $conn->prepare("SELECT id FROM profil_jucator WHERE utilizator_id = ?");
    $stmt->execute([$user_id]);
    $exists = $stmt->fetch();

    if ($exists) {
        $stmt = $conn->prepare("
            UPDATE profil_jucator SET
                data_nasterii = ?, inaltime = ?, greutate = ?, nationalitate = ?,
                puncte_meci = ?, recuperari_meci = ?, pase_meci = ?
            WHERE utilizator_id = ?
        ");
        $stmt->execute([
            $data['data_nasterii'], $data['inaltime'], $data['greutate'], $data['nationalitate'],
            $data['puncte_meci'], $data['recuperari_meci'], $data['pase_meci'], $user_id
        ]);
        $mesaj = 'Profil actualizat cu succes.';
    } else {
        $stmt = $conn->prepare("
            INSERT INTO profil_jucator
                (utilizator_id, data_nasterii, inaltime, greutate, nationalitate, puncte_meci, recuperari_meci, pase_meci)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $user_id, $data['data_nasterii'], $data['inaltime'], $data['greutate'], $data['nationalitate'],
            $data['puncte_meci'], $data['recuperari_meci'], $data['pase_meci']
        ]);
        $mesaj = 'Profil creat cu succes.';
    }

    // După salvare nu mai editează
    $editare = false;
}

// Citim profilul și avatarul - MODIFICAT
$stmt = $conn->prepare("SELECT * FROM profil_jucator WHERE utilizator_id = ?");
$stmt->execute([$user_id]);
$profil = $stmt->fetch(PDO::FETCH_ASSOC);

// Obține avatar curent - ADĂUGAT
$stmt = $conn->prepare("SELECT avatar FROM utilizatori WHERE id = ?");
$stmt->execute([$user_id]);
$user_avatar = $stmt->fetchColumn();
?>

<?php include 'header.php'; ?>
<?php include 'navbar.php'; ?>

<div class="d-flex flex-column min-vh-100">
    <div class="container-fluid flex-grow-1 mt-4">
        <div class="row">
            <!-- Meniu lateral -->
            <nav class="col-md-3 mb-4">
                <div class="list-group shadow-sm">
                    <?php foreach ($meniu as $item): ?>
                        <a href="<?= $item['link'] ?>" class="list-group-item list-group-item-action<?= basename($_SERVER['PHP_SELF']) === basename($item['link']) ? ' active' : '' ?>">
                            <?= htmlspecialchars($item['nume']) ?>
                        </a>
                    <?php endforeach; ?>
                    <a href="logout.php" class="list-group-item list-group-item-action text-danger">
                        <i class="fas fa-sign-out-alt me-1"></i>Deconectare
                    </a>
                </div>
            </nav>

            <!-- Conținut principal -->
            <main class="col-md-9">
                <div class="pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">
                        <i class="fas fa-user-circle text-info me-2"></i>
                        Profilul Meu
                    </h1>
                </div>

                <!-- SECȚIUNE AVATAR - ADĂUGAT -->
                <div class="card shadow p-4 mb-4">
                    <div class="text-center">
                        <?= displayAvatar($user_id, $username, $user_avatar, 150) ?>
                        <h4 class="mt-3 mb-1"><?= htmlspecialchars($username) ?></h4>
                        <p class="text-muted">Jucător</p>
                        <button type="button" class="btn btn-outline-primary mt-2" onclick="openAvatarModal(<?= $user_id ?>, '<?= htmlspecialchars($username) ?>', '<?= $user_avatar ?? '' ?>')">
                            <i class="fas fa-camera me-1"></i>Schimbă Avatar
                        </button>
                    </div>
                </div>

                <!-- Mesaje Avatar - ADĂUGAT -->
                <?php if (isset($mesaj_avatar)): ?>
                    <div class="alert alert-success"><?= $mesaj_avatar ?></div>
                <?php endif; ?>
                <?php if (isset($eroare_avatar)): ?>
                    <div class="alert alert-danger"><?= $eroare_avatar ?></div>
                <?php endif; ?>

                <?php if ($mesaj): ?>
                    <div class="alert alert-success"><?= $mesaj ?></div>
                <?php endif; ?>

                <?php if (!$profil || $editare): ?>
                    <!-- Formular completare sau editare -->
                    <form method="post" class="card shadow p-4 mb-4">
                        <h5 class="mb-3">Completează sau editează profilul</h5>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label>Data Nașterii</label>
                                <input type="date" name="data_nasterii" value="<?= $profil['data_nasterii'] ?? '' ?>" class="form-control">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label>Înălțime (m)</label>
                                <input type="text" name="inaltime" value="<?= $profil['inaltime'] ?? '' ?>" class="form-control">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label>Greutate (kg)</label>
                                <input type="text" name="greutate" value="<?= $profil['greutate'] ?? '' ?>" class="form-control">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label>Naționalitate</label>
                            <input type="text" name="nationalitate" value="<?= $profil['nationalitate'] ?? '' ?>" class="form-control">
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label>Puncte / Meci</label>
                                <input type="text" name="puncte_meci" value="<?= $profil['puncte_meci'] ?? '' ?>" class="form-control">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label>Recuperări / Meci</label>
                                <input type="text" name="recuperari_meci" value="<?= $profil['recuperari_meci'] ?? '' ?>" class="form-control">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label>Pase / Meci</label>
                                <input type="text" name="pase_meci" value="<?= $profil['pase_meci'] ?? '' ?>" class="form-control">
                            </div>
                        </div>
                        <button type="submit" class="btn btn-success">Salvează</button>
                        <a href="player-profile.php" class="btn btn-secondary">Renunță</a>
                    </form>
                <?php else: ?>
                    <!-- Afișare profil -->
                    <div class="card shadow p-4 mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5>🏀 Datele Tale</h5>
                            <a href="player-profile.php?edit=1" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-edit me-1"></i>Modifică
                            </a>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Data Nașterii:</strong> <?= htmlspecialchars($profil['data_nasterii']) ?></p>
                                <p><strong>Naționalitate:</strong> <?= htmlspecialchars($profil['nationalitate']) ?></p>
                                <p><strong>Înălțime:</strong> <?= $profil['inaltime'] ?> m</p>
                                <p><strong>Greutate:</strong> <?= $profil['greutate'] ?> kg</p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Puncte / Meci:</strong> <?= $profil['puncte_meci'] ?></p>
                                <p><strong>Recuperări / Meci:</strong> <?= $profil['recuperari_meci'] ?></p>
                                <p><strong>Pase / Meci:</strong> <?= $profil['pase_meci'] ?></p>
                                <div class="mt-4">
                                    <span class="badge bg-info">Nivel Performanță Estimat</span>
                                    <h4 class="mt-2 text-success">
                                        <?php
                                        $score = $profil['puncte_meci'] + $profil['recuperari_meci'] + $profil['pase_meci'];
                                        echo $score > 30 ? 'Excelent' : ($score > 20 ? 'Bun' : 'În dezvoltare');
                                        ?>
                                    </h4>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </main>
        </div>
    </div>

    <?php include 'footer.php'; ?>
</div>

<!-- MODAL UPLOAD AVATAR - ADĂUGAT -->
<div class="modal fade" id="avatarModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Schimbă Avatar</h5>
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