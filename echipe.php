<?php
require 'db_connect.php';

$mesaj = '';
$eroare = '';

// Procesare ștergere
if (isset($_GET['sterge_id'])) {
    try {
        // Verificăm mai întâi dacă echipa are jucători
        $stmt_check = $conn->prepare("SELECT COUNT(*) as total FROM jucatori WHERE echipa_id = :id");
        $stmt_check->bindParam(':id', $_GET['sterge_id'], PDO::PARAM_INT);
        $stmt_check->execute();
        $result = $stmt_check->fetch();
        
        if ($result['total'] > 0) {
            $eroare = "Nu poți șterge echipa deoarece are jucători asociați!";
        } else {
            $stmt = $conn->prepare("DELETE FROM echipe WHERE id = :id");
            $stmt->bindParam(':id', $_GET['sterge_id'], PDO::PARAM_INT);
            $stmt->execute();
            
            if ($stmt->rowCount() > 0) {
                $mesaj = "Echipa a fost ștearsă cu succes!";
            } else {
                $eroare = "Echipa nu a fost găsită sau a fost deja ștearsă!";
            }
        }
    } catch (Exception $e) {
        $eroare = "Eroare la ștergere: " . $e->getMessage();
    }
}

// Procesare căutare
$cautare = '';
if (isset($_GET['cautare']) && !empty($_GET['cautare'])) {
    $cautare = $_GET['cautare'];
    $stmt = $conn->prepare("SELECT * FROM echipe WHERE nume_echipa LIKE :cautare OR oras LIKE :cautare OR antrenor LIKE :cautare OR arena LIKE :cautare ORDER BY nume_echipa");
    $stmt->bindValue(':cautare', '%' . $cautare . '%');
} else {
    $stmt = $conn->prepare("SELECT * FROM echipe ORDER BY nume_echipa");
}
$stmt->execute();
$echipe = $stmt->fetchAll();
?>

<?php include 'header.php'; ?>

<div class="container mt-4">
    <h1 class="text-center mb-4">Listă Echipe</h1>
    
    <?php if ($mesaj): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= $mesaj ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    
    <?php if ($eroare): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= $eroare ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    
    <!-- Formular de căutare -->
    <div class="card shadow mb-3">
        <div class="card-body">
            <form method="GET" action="">
                <div class="row">
                    <div class="col-md-8">
                        <input type="text" name="cautare" class="form-control" 
                               value="<?= htmlspecialchars($cautare) ?>" 
                               placeholder="Caută echipă după nume, oraș, antrenor sau arena...">
                    </div>
                    <div class="col-md-4">
                        <div class="d-grid gap-2 d-md-flex">
                            <button type="submit" class="btn btn-primary">Caută</button>
                            <a href="echipe.php" class="btn btn-secondary">Resetează</a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
    
    <div class="d-flex justify-content-end mb-3">
        <a href="adauga_echipa.php" class="btn btn-primary">Adaugă Echipă Nouă</a>
    </div>
    
    <div class="card shadow">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>Nume Echipă</th>
                            <th>Oraș</th>
                            <th>Antrenor</th>
                            <th>Arena</th>
                            <th>An Fondare</th>
                            <th>Acțiuni</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($echipe) > 0): ?>
                            <?php foreach ($echipe as $echipa): ?>
                                <tr>
                                    <td><?= htmlspecialchars($echipa['nume_echipa']) ?></td>
                                    <td><?= htmlspecialchars($echipa['oras']) ?></td>
                                    <td><?= htmlspecialchars($echipa['antrenor']) ?></td>
                                    <td><?= htmlspecialchars($echipa['arena']) ?></td>
                                    <td><?= htmlspecialchars($echipa['an_fondare']) ?></td>
                                    <td>
                                        <a href="echipe.php?sterge_id=<?= $echipa['id'] ?>" 
                                           class="btn btn-sm btn-danger" 
                                           onclick="return confirm('Sigur vrei să ștergi echipa <?= htmlspecialchars($echipa['nume_echipa']) ?>? Această acțiune nu poate fi anulată!')">
                                            Șterge
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center">
                                    <?php if ($cautare): ?>
                                        Nu există echipe care să corespundă căutării tale.
                                    <?php else: ?>
                                        Nu există echipe înregistrate.
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>