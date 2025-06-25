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
        'training-log.php', 
        'coach-strategy-playbook.php'
    )
    ORDER BY p.id
");
$stmt->execute([$user_id]);
$meniu = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Obține tipurile de scheme din baza de date  
// (Nu mai este necesar pentru că nu creăm strategii noi)

// Procesare CRUD
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'create_new':
                // Creează o strategie nouă în baza de date (disponibilă pentru toți)
                $stmt = $conn->prepare("INSERT INTO scheme (nume_scheema, tip_scheema, descriere) VALUES (?, ?, ?)");
                $stmt->execute([$_POST['nume'], $_POST['tip'], $_POST['descriere']]);
                $schema_id = $conn->lastInsertId();
                
                // Asociază automat cu antrenorul care a creat-o
                $stmt = $conn->prepare("INSERT INTO scheme_antrenori (utilizator_id, schema_id, nota, data) VALUES (?, ?, '', NOW())");
                $stmt->execute([$user_id, $schema_id]);
                
                header('Location: ' . $_SERVER['PHP_SELF']);
                exit;
                
            case 'assign':
                // Asociază o schemă existentă cu antrenorul
                $stmt = $conn->prepare("
                    INSERT IGNORE INTO scheme_antrenori (utilizator_id, schema_id, nota, data) 
                    VALUES (?, ?, '', NOW())
                ");
                $stmt->execute([$user_id, $_POST['schema_id']]);
                
                header('Location: ' . $_SERVER['PHP_SELF']);
                exit;
                
            case 'remove':
                // Elimină asocierea cu antrenorul
                $stmt = $conn->prepare("DELETE FROM scheme_antrenori WHERE utilizator_id = ? AND schema_id = ?");
                $stmt->execute([$user_id, $_POST['schema_id']]);
                
                header('Location: ' . $_SERVER['PHP_SELF']);
                exit;
        }
    }
}

// Obține TOATE schemele disponibile din baza de date
$stmt = $conn->prepare("SELECT * FROM scheme ORDER BY tip_scheema, nume_scheema");
$stmt->execute();
$toate_schemele = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Obține schemele antrenorului (cele pe care le-a selectat)
$stmt = $conn->prepare("
    SELECT 
        s.id,
        s.nume_scheema,
        s.tip_scheema,
        s.descriere,
        sa.data
    FROM scheme s
    JOIN scheme_antrenori sa ON s.id = sa.schema_id
    WHERE sa.utilizator_id = ?
    ORDER BY sa.data DESC
");
$stmt->execute([$user_id]);
$strategii_antrenor = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Schemele disponibile pentru adăugare (cele pe care nu le are încă)
$id_strategii_antrenor = array_column($strategii_antrenor, 'id');
$scheme_disponibile = array_filter($toate_schemele, function($schema) use ($id_strategii_antrenor) {
    return !in_array($schema['id'], $id_strategii_antrenor);
});
?>

<?php include 'header.php'; ?>
<?php include 'navbar.php'; ?>

<div class="container-fluid mt-5">
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
            <!-- Header -->
            <div class="card shadow mb-4" style="background: linear-gradient(135deg, #ff6b00, #ff9a00); color: white;">
                <div class="card-body text-center py-4">
                    <h1 class="mb-2">Caiet Strategii</h1>
                    <p class="mb-0">Creați și gestionați strategiile ofensive și defensive ale echipei</p>
                    <p class="mb-0"><strong>Echipa:</strong> <?= htmlspecialchars($antrenor_info['nume_echipa']) ?> | <strong>Strategii:</strong> <?= count($strategii_antrenor) ?></p>
                </div>
            </div>

            <!-- Formular creare strategie nouă -->
            <div class="card shadow mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Creează Strategie Nouă</h5>
                </div>
                <div class="card-body">
                    <form method="POST">
                        <input type="hidden" name="action" value="create_new">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nume Strategie</label>
                                <input type="text" class="form-control" name="nume" required placeholder="ex. Atac prin laterale">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Tip Strategie</label>
                                <select class="form-select" name="tip" required>
                                    <option value="">Selectează tip</option>
                                    <option value="ofensiva">Ofensivă</option>
                                    <option value="defensiva">Defensivă</option>
                                </select>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Descriere Detaliată</label>
                            <textarea class="form-control" name="descriere" rows="4" required 
                                      placeholder="Descrieți strategia: poziții, mișcări, obiective..."></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>Creează și Adaugă la Planul Meu
                        </button>
                    </form>
                </div>
            </div>

            <!-- Formular adăugare strategii existente -->
            <?php if (count($scheme_disponibile) > 0): ?>
            <div class="card shadow mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">Adaugă Strategii Disponibile</h5>
                </div>
                <div class="card-body">
                    <form method="POST">
                        <input type="hidden" name="action" value="assign">
                        <div class="row align-items-end">
                            <div class="col-md-8">
                                <label class="form-label">Selectează o strategie din baza de date</label>
                                <select class="form-select" name="schema_id" required>
                                    <option value="">Alege o strategie...</option>
                                    <?php foreach ($scheme_disponibile as $schema): ?>
                                        <option value="<?= $schema['id'] ?>">
                                            <?= htmlspecialchars($schema['nume_scheema']) ?> (<?= ucfirst($schema['tip_scheema']) ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <button type="submit" class="btn btn-success w-100">
                                    <i class="fas fa-plus me-2"></i>Adaugă la Planul Meu
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <?php endif; ?>

            <!-- Lista strategii -->
            <?php if (count($strategii_antrenor) > 0): ?>
                <?php foreach ($strategii_antrenor as $strategie): ?>
                    <div class="card shadow mb-4">
                        <div class="card-header">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h4 class="mb-1"><?= htmlspecialchars($strategie['nume_scheema']) ?></h4>
                                    <span class="badge <?= $strategie['tip_scheema'] === 'ofensiva' ? 'bg-primary' : 'bg-warning' ?>">
                                        <?= ucfirst($strategie['tip_scheema']) ?>
                                    </span>
                                </div>
                                <div class="d-flex gap-2">
                                    <small class="text-muted me-3">
                                        Adăugat: <?= date('d.m.Y', strtotime($strategie['data'])) ?>
                                    </small>
                                    <form method="POST" style="display: inline;" onsubmit="return confirm('Sigur elimini această strategie din planul tău?')">
                                        <input type="hidden" name="action" value="remove">
                                        <input type="hidden" name="schema_id" value="<?= $strategie['id'] ?>">
                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                            <i class="fas fa-minus"></i> Elimină
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="p-3" style="background-color: #f8f9fa; border-radius: 8px; border-left: 4px solid #ff6b00;">
                                <div style="white-space: pre-line;"><?= htmlspecialchars($strategie['descriere']) ?></div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="card shadow">
                    <div class="card-body text-center py-5">
                        <i class="fas fa-clipboard-list fa-4x text-muted mb-3"></i>
                        <h4 class="text-muted">Nu aveți strategii selectate</h4>
                        <p class="text-muted">Adăugați strategii din lista disponibilă de mai sus.</p>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>