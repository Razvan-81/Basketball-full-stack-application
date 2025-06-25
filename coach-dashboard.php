<?php
session_start();
require 'db_connect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['tip'] !== 'antrenor') {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];

// Obține date echipă și antrenor
$stmt = $conn->prepare("
    SELECT e.nume_echipa, e.oras, u.echipa_id
    FROM echipe e
    JOIN utilizatori u ON u.echipa_id = e.id
    WHERE u.id = ?
");
$stmt->execute([$user_id]);
$antrenor_info = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$antrenor_info) {
    echo "<script>alert('Eroare: nu s-a găsit echipa antrenorului.'); window.location.href='login.php';</script>";
    exit;
}

$echipa_id = $antrenor_info['echipa_id'];

// Meniu dinamic pentru antrenor
$stmt = $conn->prepare("
    SELECT p.nume, p.link 
    FROM pagini p
    JOIN drepturi d ON p.id = d.pagina_id
    WHERE d.utilizator_id = ? 
    AND p.link IN (
        'coach-dashboard.php', 
        'player-management.php', 
        'coach-strategy-playbook.php'
    )
    ORDER BY p.id
");
$stmt->execute([$user_id]);
$meniu = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Număr total jucători activi din echipă
$stmt = $conn->prepare("SELECT COUNT(*) FROM utilizatori WHERE echipa_id = ? AND tip = 'jucator'");
$stmt->execute([$echipa_id]);
$total_jucatori = $stmt->fetchColumn();
?>

<?php include 'header.php'; ?>
<?php include 'navbar.php'; ?>

<div class="d-flex flex-column min-vh-100">
    <div class="container-fluid flex-grow-1 mt-4">
        <div class="row">
            <!-- Meniu lateral -->
            <div class="col-md-3 mb-4">
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
            </div>

            <!-- Conținut principal -->
            <div class="col-md-9">
                <div class="card shadow mb-4">
                    <div class="card-header bg-dark text-white">
                        <h4 class="mb-0"><i class="fas fa-chalkboard-teacher me-2"></i> Panou Antrenor</h4>
                    </div>
                    <div class="card-body">
                        <h5 class="mb-3">Salut, <?= htmlspecialchars($username) ?>!</h5>
                        <p><strong>Echipa:</strong> <?= htmlspecialchars($antrenor_info['nume_echipa']) ?> (<?= htmlspecialchars($antrenor_info['oras']) ?>)</p>
                        <p><strong>Jucători activi în echipă:</strong> <?= $total_jucatori ?></p>
                    </div>
                </div>

                <!-- Alte acțiuni rapide -->
                <div class="card shadow mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-users me-2"></i> Gestionare Jucători</h5>
                    </div>
                    <div class="card-body">
                        <p>Accesează și gestionează jucătorii echipei tale.</p>
                        <a href="player-management.php" class="btn btn-primary">
                            <i class="fas fa-cogs me-2"></i> Mergi la gestionare
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include 'footer.php'; ?>
</div>