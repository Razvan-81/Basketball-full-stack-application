<?php
require 'db_connect.php';

$mesaj = '';
$eroare = '';

// Procesare ștergere
if (isset($_GET['sterge_id'])) {
    try {
        $stmt = $conn->prepare("DELETE FROM jucatori WHERE id = :id");
        $stmt->bindParam(':id', $_GET['sterge_id'], PDO::PARAM_INT);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            $mesaj = "Jucătorul a fost șters cu succes!";
        } else {
            $eroare = "Jucătorul nu a fost găsit sau a fost deja șters!";
        }
    } catch (Exception $e) {
        $eroare = "Eroare la ștergere: " . $e->getMessage();
    }
}

// Procesare căutare
$cautare = '';
if (isset($_GET['cautare']) && !empty($_GET['cautare'])) {
    $cautare = $_GET['cautare'];
    $stmt = $conn->prepare("
        SELECT j.*, e.nume_echipa 
        FROM jucatori j 
        LEFT JOIN echipe e ON j.echipa_id = e.id 
        WHERE j.nume LIKE :cautare 
           OR j.prenume LIKE :cautare 
           OR j.pozitie LIKE :cautare 
           OR e.nume_echipa LIKE :cautare
           OR CONCAT(j.nume, ' ', j.prenume) LIKE :cautare
        ORDER BY j.nume
    ");
    $stmt->bindValue(':cautare', '%' . $cautare . '%');
} else {
    $stmt = $conn->prepare("
        SELECT j.*, e.nume_echipa 
        FROM jucatori j 
        LEFT JOIN echipe e ON j.echipa_id = e.id 
        ORDER BY j.nume
    ");
}
$stmt->execute();
$jucatori = $stmt->fetchAll();

// Obținem echipele pentru filtrul dropdown
$stmt_echipe = $conn->prepare("SELECT id, nume_echipa FROM echipe ORDER BY nume_echipa");
$stmt_echipe->execute();
$echipe = $stmt_echipe->fetchAll();

// Procesare filtrare după echipă
$echipa_selectata = '';
if (isset($_GET['echipa_id']) && !empty($_GET['echipa_id'])) {
    $echipa_selectata = $_GET['echipa_id'];
    $stmt = $conn->prepare("
        SELECT j.*, e.nume_echipa 
        FROM jucatori j 
        LEFT JOIN echipe e ON j.echipa_id = e.id 
        WHERE j.echipa_id = :echipa_id
        ORDER BY j.nume
    ");
    $stmt->bindParam(':echipa_id', $_GET['echipa_id'], PDO::PARAM_INT);
    $stmt->execute();
    $jucatori = $stmt->fetchAll();
}
?>

<?php include 'header.php'; ?>

<div class="container mt-4">
    <h1 class="text-center mb-4">Listă Jucători</h1>
    
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
    
    <!-- Formular de căutare și filtrare -->
    <div class="card shadow mb-3">
        <div class="card-body">
            <form method="GET" action="">
                <div class="row">
                    <div class="col-md-6">
                        <input type="text" name="cautare" class="form-control" 
                               value="<?= htmlspecialchars($cautare) ?>" 
                               placeholder="Caută jucător după nume, prenume, poziție sau echipă...">
                    </div>
                    <div class="col-md-4">
                        <select name="echipa_id" class="form-select">
                            <option value="">Toate echipele</option>
                            <?php foreach ($echipe as $echipa): ?>
                                <option value="<?= $echipa['id'] ?>" 
                                        <?= $echipa_selectata == $echipa['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($echipa['nume_echipa']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">Filtrează</button>
                        </div>
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="col-md-12">
                        <a href="jucatori.php" class="btn btn-secondary">Resetează filtrele</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
    
    <div class="d-flex justify-content-end mb-3">
        <a href="adauga_jucator.php" class="btn btn-primary">Adaugă Jucător Nou</a>
    </div>
    
    <div class="card shadow">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>Nume</th>
                            <th>Prenume</th>
                            <th>Poziție</th>
                            <th>Înălțime (cm)</th>
                            <th>Greutate (kg)</th>
                            <th>Data Nașterii</th>
                            <th>Echipă</th>
                            <th>Acțiuni</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($jucatori) > 0): ?>
                            <?php foreach ($jucatori as $jucator): ?>
                                <tr>
                                    <td><?= htmlspecialchars($jucator['nume']) ?></td>
                                    <td><?= htmlspecialchars($jucator['prenume']) ?></td>
                                    <td><?= htmlspecialchars($jucator['pozitie']) ?></td>
                                    <td><?= htmlspecialchars($jucator['inaltime']) ?></td>
                                    <td><?= htmlspecialchars($jucator['greutate']) ?></td>
                                    <td><?= htmlspecialchars(date('d.m.Y', strtotime($jucator['data_nastere']))) ?></td>
                                    <td><?= htmlspecialchars($jucator['nume_echipa']) ?></td>
                                    <td>
                                        <a href="jucatori.php?sterge_id=<?= $jucator['id'] ?><?= $cautare ? '&cautare=' . urlencode($cautare) : '' ?><?= $echipa_selectata ? '&echipa_id=' . $echipa_selectata : '' ?>" 
                                           class="btn btn-sm btn-danger" 
                                           onclick="return confirm('Sigur vrei să ștergi jucătorul <?= htmlspecialchars($jucator['prenume'] . ' ' . $jucator['nume']) ?>? Această acțiune nu poate fi anulată!')">
                                            Șterge
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" class="text-center">
                                    <?php if ($cautare || $echipa_selectata): ?>
                                        Nu există jucători care să corespundă criteriilor de căutare.
                                    <?php else: ?>
                                        Nu există jucători înregistrați.
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