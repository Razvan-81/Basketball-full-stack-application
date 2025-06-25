<?php
session_start();
require 'db_connect.php';
require 'avatar-helper.php'; // ADĂUGAT pentru avatare

if (!isset($_SESSION['user_id']) || $_SESSION['tip'] !== 'jucator') {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];

// ADĂUGAT - Obține avatar utilizator
$stmt = $conn->prepare("SELECT avatar FROM utilizatori WHERE id = ?");
$stmt->execute([$user_id]);
$user_avatar = $stmt->fetchColumn();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['log'])) {
        $antrenament_id = $_POST['antrenament_id'];
        $data = date('Y-m-d H:i:s');
        $stmt = $conn->prepare("INSERT INTO antrenament_jucator (utilizator_id, antrenament_id, data) VALUES (?, ?, ?)");
        $stmt->execute([$user_id, $antrenament_id, $data]);
        header('Location: training-log.php');
        exit;
    }
}

// Meniu dinamic pentru jucător
$stmt = $conn->prepare("
    SELECT p.nume, p.link
    FROM pagini p
    JOIN drepturi d ON p.id = d.pagina_id
    WHERE d.utilizator_id = ? AND p.link IN ('dashboard-player.php', 'training-log.php', 'player-profile.php')
    ORDER BY p.id
");
$stmt->execute([$user_id]);
$meniu = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $conn->prepare("SELECT * FROM antrenamente");
$stmt->execute();
$antrenamente = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $conn->prepare("SELECT a.nume_exercitiu, a.tip_exercitiu, a.durata, aj.data FROM antrenament_jucator aj JOIN antrenamente a ON aj.antrenament_id = a.id WHERE aj.utilizator_id = ? ORDER BY aj.data DESC");
$stmt->execute([$user_id]);
$loguri = $stmt->fetchAll(PDO::FETCH_ASSOC);

// ADĂUGAT - Statistici îmbunătățite
$total = count($loguri);
$saptamana = 0;
$luna = 0;
$curenta = date('W');
$luna_curenta = date('Y-m');

foreach ($loguri as $l) {
    if (date('W', strtotime($l['data'])) == $curenta) $saptamana++;
    if (date('Y-m', strtotime($l['data'])) == $luna_curenta) $luna++;
}

// Calculează tipul de exercițiu preferat
$tipuri_exercitii = [];
foreach ($loguri as $l) {
    $tip = $l['tip_exercitiu'];
    if (!isset($tipuri_exercitii[$tip])) {
        $tipuri_exercitii[$tip] = 0;
    }
    $tipuri_exercitii[$tip]++;
}
arsort($tipuri_exercitii);
$exercitiu_preferat = !empty($tipuri_exercitii) ? array_key_first($tipuri_exercitii) : 'N/A';
?>

<?php include 'header.php'; ?>
<?php include 'navbar.php'; ?>

<div class="d-flex flex-column min-vh-100">
    <div class="container-fluid flex-grow-1 mt-4">
        <div class="row">
            <!-- Meniu lateral dinamic -->
            <nav class="col-md-3 mb-4">
                <div class="list-group shadow-sm">
                    <?php foreach ($meniu as $item): ?>
                        <a href="<?= htmlspecialchars($item['link']) ?>" class="list-group-item list-group-item-action<?= basename($_SERVER['PHP_SELF']) === basename($item['link']) ? ' active' : '' ?>">
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
                <!-- HEADER CU AVATAR - ADĂUGAT -->
                <div class="card shadow mb-4" style="background: linear-gradient(135deg, #28a745, #20c997); color: white;">
                    <div class="card-body py-3">
                        <div class="row align-items-center">
                            <div class="col-md-2 text-center">
                                <!-- Avatar în header -->
                                <?= displayAvatar($user_id, $username, $user_avatar, 80) ?>
                            </div>
                            <div class="col-md-10">
                                <div class="row">
                                    <div class="col-md-8">
                                        <h1 class="h2 mb-2">
                                            <i class="fas fa-clipboard-list me-2"></i>
                                            Jurnal de Antrenamente
                                        </h1>
                                        <p class="mb-0">Monitorizează și înregistrează progresul tău, <?= htmlspecialchars($username) ?>!</p>
                                    </div>
                                    <div class="col-md-4 text-end">
                                        <div class="text-white">
                                            <small><i class="fas fa-calendar me-1"></i>Astăzi: <?= date('d.m.Y') ?></small><br>
                                            <small><i class="fas fa-clock me-1"></i><?= date('H:i') ?></small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Statistici îmbunătățite - MODIFICAT -->
                <div class="row mb-4">
                    <div class="col-lg-3 col-md-6 mb-3">
                        <div class="card border-left-primary shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                            Total Antrenamente
                                        </div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $total ?></div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-dumbbell fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-3 col-md-6 mb-3">
                        <div class="card border-left-success shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                            Săptămâna Aceasta
                                        </div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $saptamana ?></div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-calendar-week fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-3 col-md-6 mb-3">
                        <div class="card border-left-info shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                            Luna Aceasta
                                        </div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $luna ?></div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-calendar-alt fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-3 col-md-6 mb-3">
                        <div class="card border-left-warning shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                            Tip Preferat
                                        </div>
                                        <div class="h6 mb-0 font-weight-bold text-gray-800"><?= ucfirst($exercitiu_preferat) ?></div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-heart fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <!-- Adăugare antrenament - ÎMBUNĂTĂȚIT -->
                    <div class="col-md-6">
                        <div class="card shadow mb-4">
                            <div class="card-header bg-primary text-white">
                                <h5 class="mb-0">
                                    <i class="fas fa-plus me-2"></i>Adaugă Antrenament Nou
                                </h5>
                            </div>
                            <div class="card-body">
                                <form method="post">
                                    <div class="mb-3">
                                        <label class="form-label">Selectează exercițiul:</label>
                                        <select name="antrenament_id" class="form-select" required>
                                            <option value="">Alege antrenamentul...</option>
                                            <?php foreach ($antrenamente as $a): ?>
                                                <option value="<?= $a['id'] ?>">
                                                    <?= htmlspecialchars($a['nume_exercitiu']) ?> 
                                                    <small>(<?= ucfirst($a['tip_exercitiu']) ?>, <?= $a['durata'] ?> min)</small>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <small class="text-muted">
                                            <i class="fas fa-info-circle me-1"></i>
                                            Antrenamentul va fi înregistrat cu data și ora curentă.
                                        </small>
                                    </div>
                                    
                                    <button type="submit" name="log" class="btn btn-primary w-100">
                                        <i class="fas fa-save me-1"></i>Înregistrează Antrenament
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Progres și motivație - ADĂUGAT -->
                    <div class="col-md-6">
                        <div class="card shadow mb-4">
                            <div class="card-header bg-success text-white">
                                <h5 class="mb-0">
                                    <i class="fas fa-chart-line me-2"></i>Progresul Tău
                                </h5>
                            </div>
                            <div class="card-body">
                                <?php if ($total > 0): ?>
                                    <div class="mb-3">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <span>Obiectiv săptămânal (5 antrenamente):</span>
                                            <span class="badge bg-<?= $saptamana >= 5 ? 'success' : 'warning' ?>">
                                                <?= $saptamana ?>/5
                                            </span>
                                        </div>
                                        <div class="progress">
                                            <div class="progress-bar bg-<?= $saptamana >= 5 ? 'success' : 'primary' ?>" 
                                                 style="width: <?= min(($saptamana / 5) * 100, 100) ?>%"></div>
                                        </div>
                                    </div>
                                    
                                    <div class="text-center">
                                        <?php if ($saptamana >= 5): ?>
                                            <p class="text-success mb-2">
                                                <i class="fas fa-trophy me-1"></i>
                                                Felicitări! Ai atins obiectivul săptămânal!
                                            </p>
                                        <?php else: ?>
                                            <p class="text-info mb-2">
                                                <i class="fas fa-bullseye me-1"></i>
                                                Încă <?= 5 - $saptamana ?> antrenamente până la obiectiv!
                                            </p>
                                        <?php endif; ?>
                                        
                                        <small class="text-muted">
                                            Ultimul antrenament: 
                                            <?= !empty($loguri) ? date('d.m.Y H:i', strtotime($loguri[0]['data'])) : 'Niciodată' ?>
                                        </small>
                                    </div>
                                <?php else: ?>
                                    <div class="text-center py-3">
                                        <i class="fas fa-rocket fa-3x text-muted mb-3"></i>
                                        <h6>Începe călătoria ta!</h6>
                                        <p class="text-muted">Înregistrează primul tău antrenament și începe să-ți urmărești progresul.</p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tabel loguri - ÎMBUNĂTĂȚIT -->
                <div class="card shadow">
                    <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="fas fa-history me-2"></i>Istoric Antrenamente
                        </h5>
                        <span class="badge bg-light text-dark"><?= $total ?> înregistrări</span>
                    </div>
                    <div class="card-body">
                        <?php if (!$loguri): ?>
                            <div class="text-center py-5">
                                <i class="fas fa-clipboard-list fa-4x text-muted mb-3"></i>
                                <h5 class="text-muted">Nu ai înregistrat încă niciun antrenament</h5>
                                <p class="text-muted">Începe prin a adăuga primul tău antrenament folosind formularul de mai sus!</p>
                                <a href="#" onclick="document.querySelector('select[name=antrenament_id]').focus()" class="btn btn-primary">
                                    <i class="fas fa-plus me-1"></i>Adaugă primul antrenament
                                </a>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-hover table-striped">
                                    <thead class="table-dark">
                                        <tr>
                                            <th><i class="fas fa-dumbbell me-1"></i>Exercițiu</th>
                                            <th><i class="fas fa-tag me-1"></i>Tip</th>
                                            <th><i class="fas fa-clock me-1"></i>Durată</th>
                                            <th><i class="fas fa-calendar me-1"></i>Data</th>
                                            <th><i class="fas fa-time me-1"></i>Ora</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($loguri as $index => $log): ?>
                                            <tr>
                                                <td>
                                                    <strong><?= htmlspecialchars($log['nume_exercitiu']) ?></strong>
                                                </td>
                                                <td>
                                                    <span class="badge bg-secondary">
                                                        <?= ucfirst($log['tip_exercitiu']) ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="text-info">
                                                        <i class="fas fa-stopwatch me-1"></i>
                                                        <?= $log['durata'] ?> min
                                                    </span>
                                                </td>
                                                <td><?= date('d.m.Y', strtotime($log['data'])) ?></td>
                                                <td>
                                                    <span class="text-muted">
                                                        <?= date('H:i', strtotime($log['data'])) ?>
                                                    </span>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                            
                            <?php if ($total > 10): ?>
                                <div class="text-center mt-3">
                                    <small class="text-muted">
                                        Se afișează toate <?= $total ?> antrenamentele înregistrate
                                    </small>
                                </div>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <?php include 'footer.php'; ?>
</div>

<!-- CSS local - ADĂUGAT -->
<style>
/* Stiluri pentru carduri statistici */
.card.border-left-primary {
    border-left: 5px solid #007bff;
}
.card.border-left-success {
    border-left: 5px solid #28a745;
}
.card.border-left-info {
    border-left: 5px solid #17a2b8;
}
.card.border-left-warning {
    border-left: 5px solid #ffc107;
}

.text-xs {
    font-size: 0.7rem;
}

.text-gray-800 {
    color: #5a5c69;
}

.text-gray-300 {
    color: #dddfeb;
}

/* Hover effects pentru carduri */
.card {
    transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
}

.card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 20px rgba(0,0,0,0.1);
}

/* Stiluri pentru tabel */
.table-hover tbody tr:hover {
    background-color: rgba(0,123,255,0.1);
}

/* Progress bar animată */
.progress-bar {
    transition: width 0.6s ease;
}

/* Responsive pentru avatar în header */
@media (max-width: 768px) {
    .col-md-2.text-center {
        margin-bottom: 1rem;
    }
    
    .col-md-4.text-end {
        text-align: start !important;
        margin-top: 1rem;
    }
}

/* Animații pentru butoane */
.btn {
    transition: all 0.3s ease;
}

.btn:hover {
    transform: translateY(-1px);
}

/* Stiluri pentru badge-uri în tabel */
.badge {
    font-size: 0.75em;
}
</style>