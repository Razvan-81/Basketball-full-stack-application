<?php
require 'db_connect.php';

$mesaj = '';
$eroare = '';

// Get all teams for dropdown
$stmt = $conn->prepare("SELECT id, nume_echipa FROM echipe ORDER BY nume_echipa");
$stmt->execute();
$echipe = $stmt->fetchAll();

// Process form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        // Validate inputs
        if (empty($_POST['nume']) || empty($_POST['prenume']) || empty($_POST['pozitie']) || 
            empty($_POST['inaltime']) || empty($_POST['greutate']) || empty($_POST['data_nastere']) || 
            empty($_POST['echipa_id'])) {
            throw new Exception("Toate câmpurile sunt obligatorii!");
        }
        
        // Insert new player
        $stmt = $conn->prepare("
            INSERT INTO jucatori (nume, prenume, pozitie, inaltime, greutate, data_nastere, echipa_id) 
            VALUES (:nume, :prenume, :pozitie, :inaltime, :greutate, :data_nastere, :echipa_id)
        ");
        
        $stmt->bindParam(':nume', $_POST['nume']);
        $stmt->bindParam(':prenume', $_POST['prenume']);
        $stmt->bindParam(':pozitie', $_POST['pozitie']);
        $stmt->bindParam(':inaltime', $_POST['inaltime'], PDO::PARAM_INT);
        $stmt->bindParam(':greutate', $_POST['greutate'], PDO::PARAM_INT);
        $stmt->bindParam(':data_nastere', $_POST['data_nastere']);
        $stmt->bindParam(':echipa_id', $_POST['echipa_id'], PDO::PARAM_INT);
        
        $stmt->execute();
        $mesaj = "Jucătorul a fost adăugat cu succes!";
    } catch (Exception $e) {
        $eroare = $e->getMessage();
    }
}
?>

<?php include 'header.php'; ?>

<div class="container mt-4">
    <h1 class="text-center mb-4">Adaugă Jucător Nou</h1>
    
    <?php if ($mesaj): ?>
        <div class="alert alert-success"><?= $mesaj ?></div>
    <?php endif; ?>
    
    <?php if ($eroare): ?>
        <div class="alert alert-danger"><?= $eroare ?></div>
    <?php endif; ?>
    
    <div class="card shadow">
        <div class="card-body">
            <form method="post" action="">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="nume" class="form-label">Nume</label>
                        <input type="text" class="form-control" id="nume" name="nume" required>
                    </div>
                    <div class="col-md-6">
                        <label for="prenume" class="form-label">Prenume</label>
                        <input type="text" class="form-control" id="prenume" name="prenume" required>
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="pozitie" class="form-label">Poziție</label>
                        <select class="form-select" id="pozitie" name="pozitie" required>
                            <option value="">Selectează poziția</option>
                            <option value="Conducător de joc">Conducător de joc</option>
                            <option value="Aruncător">Aruncător</option>
                            <option value="Extremă">Extremă</option>
                            <option value="Pivot">Pivot</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="echipa_id" class="form-label">Echipă</label>
                        <select class="form-select" id="echipa_id" name="echipa_id" required>
                            <option value="">Selectează echipa</option>
                            <?php foreach ($echipe as $echipa): ?>
                                <option value="<?= $echipa['id'] ?>"><?= htmlspecialchars($echipa['nume_echipa']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label for="inaltime" class="form-label">Înălțime (cm)</label>
                        <input type="number" class="form-control" id="inaltime" name="inaltime" min="150" max="250" required>
                    </div>
                    <div class="col-md-4">
                        <label for="greutate" class="form-label">Greutate (kg)</label>
                        <input type="number" class="form-control" id="greutate" name="greutate" min="50" max="150" required>
                    </div>
                    <div class="col-md-4">
                        <label for="data_nastere" class="form-label">Data Nașterii</label>
                        <input type="date" class="form-control" id="data_nastere" name="data_nastere" required>
                    </div>
                </div>
                
                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <a href="jucatori.php" class="btn btn-secondary me-md-2">Anulează</a>
                    <button type="submit" class="btn btn-primary">Salvează</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>