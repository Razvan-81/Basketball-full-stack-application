<?php
session_start();
require 'db_connect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['tip'] !== 'jucator') {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];

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

$stmt = $conn->prepare("SELECT p.id, p.nume, p.link FROM pagini p JOIN drepturi d ON p.id = d.pagina_id WHERE d.utilizator_id = ?");
$stmt->execute([$user_id]);
$meniu = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $conn->prepare("SELECT * FROM antrenamente");
$stmt->execute();
$antrenamente = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $conn->prepare("SELECT a.nume_exercitiu, aj.data FROM antrenament_jucator aj JOIN antrenamente a ON aj.antrenament_id = a.id WHERE aj.utilizator_id = ? ORDER BY aj.data DESC");
$stmt->execute([$user_id]);
$loguri = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<?php include 'header.php'; ?>
<?php include 'navbar.php'; ?>

<div class="container-fluid mt-5">
    <div class="row">
        <!-- Meniu lateral dinamic -->
        <div class="col-md-3 mb-4">
            <div class="list-group shadow-sm">
                <?php foreach ($meniu as $item): ?>
                    <a href="<?= htmlspecialchars($item['link']) ?>" class="list-group-item list-group-item-action<?= basename($_SERVER['PHP_SELF']) === basename($item['link']) ? ' active' : '' ?>">
                        <?= htmlspecialchars($item['nume']) ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Conținut principal -->
        <div class="col-md-9">
            <h2 class="mb-4">Jurnal de Antrenamente</h2>
            <div class="row">
                <!-- Adăugare antrenament -->
                <div class="col-md-6">
                    <form method="post" class="card card-body shadow mb-4">
                        <h5>Adaugă Antrenament</h5>
                        <select name="antrenament_id" class="form-select mb-3" required>
                            <option value="">Selectează antrenamentul</option>
                            <?php foreach ($antrenamente as $a): ?>
                                <option value="<?= $a['id'] ?>">
                                    <?= htmlspecialchars($a['nume_exercitiu']) ?> (<?= ucfirst($a['tip_exercitiu']) ?>, <?= $a['durata'] ?> min)
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <button type="submit" name="log" class="btn btn-primary">Adaugă</button>
                    </form>
                </div>

                <!-- Statistici rapide -->
                <div class="col-md-6">
                    <div class="card shadow mb-4">
                        <div class="card-header bg-success text-white">Statistici</div>
                        <div class="card-body">
                            <?php
                            $total = count($loguri);
                            $saptamana = 0;
                            $curenta = date('W');
                            foreach ($loguri as $l) {
                                if (date('W', strtotime($l['data'])) == $curenta) $saptamana++;
                            }
                            ?>
                            <p><strong>Total:</strong> <?= $total ?></p>
                            <p><strong>Săptămâna aceasta:</strong> <?= $saptamana ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabel loguri -->
            <div class="card shadow">
                <div class="card-header bg-info text-white">Istoric Antrenamente</div>
                <div class="card-body">
                    <?php if (!$loguri): ?>
                        <p class="text-muted">Nu ai înregistrat încă niciun antrenament.</p>
                    <?php else: ?>
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Exercițiu</th>
                                    <th>Data</th>
                                    <th>Ora</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($loguri as $log): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($log['nume_exercitiu']) ?></td>
                                        <td><?= date('d.m.Y', strtotime($log['data'])) ?></td>
                                        <td><?= date('H:i', strtotime($log['data'])) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
