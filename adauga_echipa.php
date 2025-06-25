<?php
require 'db_connect.php';

$mesaj = '';
$eroare = '';

// Process form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        // Validate inputs
        if (empty($_POST['nume_echipa']) || empty($_POST['oras']) || empty($_POST['antrenor']) || 
            empty($_POST['arena']) || empty($_POST['an_fondare'])) {
            throw new Exception("Toate câmpurile sunt obligatorii!");
        }
        
        // Insert new team
        $stmt = $conn->prepare("
            INSERT INTO echipe (nume_echipa, oras, antrenor, arena, an_fondare) 
            VALUES (:nume_echipa, :oras, :antrenor, :arena, :an_fondare)
        ");
        
        $stmt->bindParam(':nume_echipa', $_POST['nume_echipa']);
        $stmt->bindParam(':oras', $_POST['oras']);
        $stmt->bindParam(':antrenor', $_POST['antrenor']);
        $stmt->bindParam(':arena', $_POST['arena']);
        $stmt->bindParam(':an_fondare', $_POST['an_fondare'], PDO::PARAM_INT);
        
        $stmt->execute();
        $mesaj = "Echipa a fost adăugată cu succes!";
    } catch (Exception $e) {
        $eroare = $e->getMessage();
    }
}
?>

<?php include 'header.php'; ?>

<div class="container mt-4">
    <h1 class="text-center mb-4">Adaugă Echipă Nouă</h1>
    
    <?php if ($mesaj): ?>
        <div class="alert alert-success"><?= $mesaj ?></div>
    <?php endif; ?>
    
    <?php if ($eroare): ?>
        <div class="alert alert-danger"><?= $eroare ?></div>
    <?php endif; ?>
    
    <div class="card shadow">
        <div class="card-body">
            <form method="post" action="">
                <div class="mb-3">
                    <label for="nume_echipa" class="form-label">Nume Echipă</label>
                    <input type="text" class="form-control" id="nume_echipa" name="nume_echipa" required>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="oras" class="form-label">Oraș</label>
                        <input type="text" class="form-control" id="oras" name="oras" required>
                    </div>
                    <div class="col-md-6">
                        <label for="arena" class="form-label">Arena</label>
                        <input type="text" class="form-control" id="arena" name="arena" required>
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="antrenor" class="form-label">Antrenor</label>
                        <input type="text" class="form-control" id="antrenor" name="antrenor" required>
                    </div>
                    <div class="col-md-6">
                        <label for="an_fondare" class="form-label">An Fondare</label>
                        <input type="number" class="form-control" id="an_fondare" name="an_fondare" min="1900" max="<?= date('Y') ?>" required>
                    </div>
                </div>
                
                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <a href="echipe.php" class="btn btn-secondary me-md-2">Anulează</a>
                    <button type="submit" class="btn btn-primary">Salvează</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>