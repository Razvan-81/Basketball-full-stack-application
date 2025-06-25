<?php
session_start();
require 'db_connect.php';

$mesaj = '';
$eroare = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $username = trim($_POST['username']);
        $email = trim($_POST['email']);
        $parola = trim($_POST['parola']);
        $tip = trim($_POST['tip']); // jucator sau antrenor
        $echipa_id = $_POST['echipa_id'];

        if (!$username || !$email || !$parola || !$tip || !$echipa_id) {
            throw new Exception("Toate câmpurile sunt obligatorii!");
        }

        // Verificăm dacă emailul este deja folosit
        $check = $conn->prepare("SELECT COUNT(*) FROM utilizatori WHERE email = ?");
        $check->execute([$email]);
        if ($check->fetchColumn() > 0) {
            throw new Exception("Emailul este deja folosit!");
        }

        // Verificăm dacă username-ul este deja folosit
        $check = $conn->prepare("SELECT COUNT(*) FROM utilizatori WHERE username = ?");
        $check->execute([$username]);
        if ($check->fetchColumn() > 0) {
            throw new Exception("Username-ul este deja folosit!");
        }

        // Inserăm utilizatorul în tabela utilizatori
        $stmt = $conn->prepare("
            INSERT INTO utilizatori (username, parola, tip, email, echipa_id)
            VALUES (?, ?, ?, ?, ?)
        ");
        $stmt->execute([$username, $parola, $tip, $email, $echipa_id]);
        $user_id = $conn->lastInsertId();

        // Atribuire drepturi în funcție de tipul utilizatorului
        if ($tip === 'jucator') {
            // Drepturi pentru jucător - paginile specifice jucătorilor
            $pagini_jucator_ids = [1, 2, 3, 4, 5, 6]; // ID-urile corecte din tabela pagini
            
            foreach ($pagini_jucator_ids as $pagina_id) {
                $stmt = $conn->prepare("INSERT INTO drepturi (utilizator_id, pagina_id) VALUES (?, ?)");
                $stmt->execute([$user_id, $pagina_id]);
            }
            
        } elseif ($tip === 'antrenor') {
            // Drepturi pentru antrenor - paginile specifice antrenorilor
            $pagini_antrenor_ids = [7, 8, 9, 10, 11, 12]; // ID-urile corecte din tabela pagini
            
            foreach ($pagini_antrenor_ids as $pagina_id) {
                $stmt = $conn->prepare("INSERT INTO drepturi (utilizator_id, pagina_id) VALUES (?, ?)");
                $stmt->execute([$user_id, $pagina_id]);
            }
        }

        $mesaj = "Cont creat cu succes! Poți să te autentifici acum.";
        
    } catch (Exception $e) {
        $eroare = $e->getMessage();
    }
}

// Preluăm echipele pentru dropdown
$echipe = $conn->query("SELECT id, nume_echipa FROM echipe ORDER BY nume_echipa")->fetchAll(PDO::FETCH_ASSOC);
?>

<?php include 'header.php'; ?>
<?php include 'navbar.php'; ?>

<div class="d-flex flex-column min-vh-100">
    <div class="container flex-grow-1 mt-4">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <h2 class="text-center mb-4">Creează Cont Nou</h2>

                <?php if ($mesaj): ?>
                    <div class="alert alert-success"><?= $mesaj ?></div>
                <?php elseif ($eroare): ?>
                    <div class="alert alert-danger"><?= $eroare ?></div>
                <?php endif; ?>

                <form method="post" class="card shadow p-4" id="registerForm">
                    <div class="mb-3">
                        <label class="form-label">Username</label>
                        <input type="text" name="username" class="form-control" required 
                               value="<?= htmlspecialchars($_POST['username'] ?? '') ?>">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" required
                               value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Parolă</label>
                        <input type="password" name="parola" id="parola" class="form-control" required>
                        <div id="passwordFeedback" class="form-text text-danger" style="display: none;">
                            Parola trebuie să aibă minimum 5 caractere
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Tip utilizator</label>
                        <select name="tip" class="form-select" required>
                            <option value="jucator" <?= ($_POST['tip'] ?? '') === 'jucator' ? 'selected' : '' ?>>Jucător</option>
                            <option value="antrenor" <?= ($_POST['tip'] ?? '') === 'antrenor' ? 'selected' : '' ?>>Antrenor</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Echipă</label>
                        <select name="echipa_id" class="form-select" required>
                            <option value="">-- Selectează echipa --</option>
                            <?php foreach ($echipe as $echipa): ?>
                                <option value="<?= $echipa['id'] ?>" 
                                        <?= ($_POST['echipa_id'] ?? '') == $echipa['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($echipa['nume_echipa']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">Creează Cont</button>
                    </div>
                    
                    <div class="text-center mt-3">
                        <a href="login.php" class="text-muted">Ai deja cont? Autentifică-te aici</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <?php include 'footer.php'; ?>
</div>

<script>
// Validare JavaScript pentru parola - live feedback
document.getElementById('parola').addEventListener('input', function() {
    const parola = this.value;
    const feedback = document.getElementById('passwordFeedback');
    
    if (parola.length < 5) {
        feedback.style.display = 'block';
        this.classList.add('is-invalid');
        this.classList.remove('is-valid');
    } else {
        feedback.style.display = 'none';
        this.classList.remove('is-invalid');
        this.classList.add('is-valid');
    }
});

// Validare la submit
document.getElementById('registerForm').addEventListener('submit', function(e) {
    const parola = document.getElementById('parola').value;
    
    if (parola.length < 5) {
        e.preventDefault();
        document.getElementById('passwordFeedback').style.display = 'block';
        document.getElementById('parola').classList.add('is-invalid');
        document.getElementById('parola').focus();
    }
});
</script>