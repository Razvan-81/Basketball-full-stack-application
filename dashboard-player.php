<?php 
session_start(); 
require 'db_connect.php';
require 'avatar-helper.php'; //  avatare

if (!isset($_SESSION['user_id']) || $_SESSION['tip'] !== 'jucator') {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];

// ADĂUGAT - Obține avatar utilizator
$stmt = $conn->prepare("SELECT avatar FROM utilizatori WHERE id = ?");
$stmt->execute([$user_id]);
$user_avatar = $stmt->fetchColumn();

// Meniu filtrat
$stmt = $conn->prepare("
    SELECT p.nume, p.link 
    FROM pagini p
    JOIN drepturi d ON p.id = d.pagina_id
    WHERE d.utilizator_id = ? AND p.link IN ('dashboard-player.php', 'training-log.php', 'player-profile.php')
    ORDER BY p.id
");
$stmt->execute([$user_id]);
$meniu = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Informații echipă și antrenor (safe check)
$stmt = $conn->prepare("
    SELECT e.nume_echipa, e.antrenor 
    FROM utilizatori u 
    LEFT JOIN echipe e ON u.echipa_id = e.id 
    WHERE u.id = ?
");
$stmt->execute([$user_id]);
$echipa = $stmt->fetch(PDO::FETCH_ASSOC);

// Total antrenamente
$stmt = $conn->prepare("SELECT COUNT(*) FROM antrenament_jucator WHERE utilizator_id = ?");
$stmt->execute([$user_id]);
$total_antrenamente = $stmt->fetchColumn();

// Ultimul antrenament
$stmt = $conn->prepare("
    SELECT a.nume_exercitiu, aj.data 
    FROM antrenament_jucator aj 
    JOIN antrenamente a ON aj.antrenament_id = a.id 
    WHERE aj.utilizator_id = ?
    ORDER BY aj.data DESC LIMIT 1
");
$stmt->execute([$user_id]);
$ultim = $stmt->fetch(PDO::FETCH_ASSOC);

// ADĂUGAT - Statistici profil jucător
$stmt = $conn->prepare("
    SELECT data_nasterii, inaltime, greutate, nationalitate, puncte_meci, recuperari_meci, pase_meci,
           TIMESTAMPDIFF(YEAR, data_nasterii, CURDATE()) as varsta
    FROM profil_jucator 
    WHERE utilizator_id = ?
");
$stmt->execute([$user_id]);
$profil = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<?php include 'header.php'; ?>
<?php include 'navbar.php'; ?>

<div class="d-flex flex-column min-vh-100">
    <div class="container-fluid flex-grow-1 mt-4">
        <div class="row">
            <!-- Sidebar -->
            <nav class="col-md-3 mb-4">
                <div class="list-group shadow-sm">
                    <?php foreach ($meniu as $pagina): ?>
                        <a href="<?= htmlspecialchars($pagina['link']) ?>" class="list-group-item list-group-item-action<?= basename($_SERVER['PHP_SELF']) === basename($pagina['link']) ? ' active' : '' ?>">
                            <?= htmlspecialchars($pagina['nume']) ?>
                        </a>
                    <?php endforeach; ?>
                    <a href="logout.php" class="list-group-item list-group-item-action text-danger">
                        <i class="fas fa-sign-out-alt me-1"></i>Deconectare
                    </a>
                </div>
            </nav>

            <!-- Conținut principal -->
            <main class="col-md-9">
                <!-- HEADER CU AVATAR - MODIFICAT -->
                <div class="card shadow mb-4" style="background: linear-gradient(135deg, #007bff, #0056b3); color: white;">
                    <div class="card-body py-4">
                        <div class="row align-items-center">
                            <div class="col-md-2 text-center">
                                <!-- Avatar mare în header -->
                                <?= displayAvatar($user_id, $username, $user_avatar, 100) ?>
                            </div>
                            <div class="col-md-10">
                                <div class="row">
                                    <div class="col-md-8">
                                        <h1 class="h2 mb-2">
                                            <i class="fas fa-basketball-ball text-warning me-2"></i>
                                            Bine ai venit, <?= htmlspecialchars($username) ?>!
                                        </h1>
                                        <p class="mb-1"><strong>Echipa:</strong> <?= $echipa && $echipa['nume_echipa'] ? htmlspecialchars($echipa['nume_echipa']) : 'Nedefinit' ?></p>
                                        <p class="mb-0"><strong>Antrenor:</strong> <?= $echipa && $echipa['antrenor'] ? htmlspecialchars($echipa['antrenor']) : 'N/A' ?></p>
                                    </div>
                                    <div class="col-md-4 text-end">
                                        <a href="player-profile.php" class="btn btn-light btn-sm">
                                            <i class="fas fa-user-edit me-1"></i>Editează Profil
                                        </a>
                                        <br>
                                        <small class="text-light mt-2 d-block">
                                            <i class="fas fa-clock me-1"></i>
                                            Ultima activitate: <?= $ultim ? date('d.m.Y H:i', strtotime($ultim['data'])) : 'Niciodată' ?>
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Carduri statistici - ÎMBUNĂTĂȚITE -->
                <div class="row mb-4">
                    <div class="col-lg-3 col-md-6 mb-3">
                        <div class="card border-left-primary shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                            Total Antrenamente
                                        </div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $total_antrenamente ?></div>
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
                                            Puncte / Meci
                                        </div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                                            <?= $profil && $profil['puncte_meci'] ? number_format($profil['puncte_meci'], 1) : '0.0' ?>
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-chart-line fa-2x text-gray-300"></i>
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
                                            Recuperări / Meci
                                        </div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                                            <?= $profil && $profil['recuperari_meci'] ? number_format($profil['recuperari_meci'], 1) : '0.0' ?>
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-hands fa-2x text-gray-300"></i>
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
                                            Pase / Meci
                                        </div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                                            <?= $profil && $profil['pase_meci'] ? number_format($profil['pase_meci'], 1) : '0.0' ?>
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-exchange-alt fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Informații profil - ADĂUGAT -->
                <?php if ($profil): ?>
                <div class="row mb-4">
                    <div class="col-md-8">
                        <div class="card shadow">
                            <div class="card-header bg-primary text-white">
                                <h6 class="m-0 font-weight-bold">
                                    <i class="fas fa-user-athlete me-2"></i>Informații Jucător
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <p class="mb-2"><strong>Vârsta:</strong> <?= $profil['varsta'] ? $profil['varsta'] . ' ani' : 'N/A' ?></p>
                                        <p class="mb-2"><strong>Înălțime:</strong> <?= $profil['inaltime'] ? number_format($profil['inaltime'], 2) . ' m' : 'N/A' ?></p>
                                        <p class="mb-2"><strong>Greutate:</strong> <?= $profil['greutate'] ? $profil['greutate'] . ' kg' : 'N/A' ?></p>
                                    </div>
                                    <div class="col-md-6">
                                        <p class="mb-2"><strong>Naționalitate:</strong> <?= $profil['nationalitate'] ? htmlspecialchars($profil['nationalitate']) : 'N/A' ?></p>
                                        <p class="mb-2"><strong>Data nașterii:</strong> <?= $profil['data_nasterii'] ? date('d.m.Y', strtotime($profil['data_nasterii'])) : 'N/A' ?></p>
                                        
                                        <!-- Indicator performanță -->
                                        <?php 
                                        $total_puncte = $profil['puncte_meci'] + $profil['recuperari_meci'] + $profil['pase_meci'];
                                        if ($total_puncte > 30) {
                                            $nivel = 'Excelent';
                                            $culoare = 'success';
                                        } elseif ($total_puncte > 20) {
                                            $nivel = 'Bun';
                                            $culoare = 'warning';
                                        } elseif ($total_puncte > 0) {
                                            $nivel = 'În dezvoltare';
                                            $culoare = 'info';
                                        } else {
                                            $nivel = 'Nedefinit';
                                            $culoare = 'secondary';
                                        }
                                        ?>
                                        <p class="mb-0">
                                            <strong>Nivel performanță:</strong> 
                                            <span class="badge bg-<?= $culoare ?> ms-1"><?= $nivel ?></span>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="card shadow">
                            <div class="card-header bg-success text-white">
                                <h6 class="m-0 font-weight-bold">
                                    <i class="fas fa-trophy me-2"></i>Acțiuni Rapide
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="d-grid gap-2">
                                    <a href="training-log.php" class="btn btn-primary btn-sm">
                                        <i class="fas fa-plus me-1"></i>Adaugă Antrenament
                                    </a>
                                    <a href="player-profile.php" class="btn btn-info btn-sm">
                                        <i class="fas fa-edit me-1"></i>Actualizează Profil
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Ultimul antrenament - ÎMBUNĂTĂȚIT -->
                <?php if ($ultim): ?>
                <div class="card shadow mb-4">
                    <div class="card-header bg-warning text-dark">
                        <h6 class="m-0 font-weight-bold">
                            <i class="fas fa-clock me-2"></i>Ultimul Antrenament
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <h5 class="mb-1"><?= htmlspecialchars($ultim['nume_exercitiu']) ?></h5>
                                <p class="text-muted mb-0">
                                    <i class="fas fa-calendar me-1"></i>
                                    <?= date('d.m.Y H:i', strtotime($ultim['data'])) ?>
                                </p>
                            </div>
                            <div class="col-md-4 text-end">
                                <a href="training-log.php" class="btn btn-outline-primary">
                                    <i class="fas fa-eye me-1"></i>Vezi Toate
                                </a>
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

<!-- CSS local - ÎMBUNĂTĂȚIT -->
<style>
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

/* Stiluri pentru carduri statistici */
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
</style>