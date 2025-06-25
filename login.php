<?php 
session_start(); 
require 'db_connect.php'; 

$eroare = ''; 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $parola = trim($_POST['password']); 

    $stmt = $conn->prepare("SELECT * FROM utilizatori WHERE username = ? AND parola = ?");
    $stmt->execute([$username, $parola]);
    $utilizator = $stmt->fetch(PDO::FETCH_ASSOC); 

    if ($utilizator && isset($utilizator['tip'])) {
        $_SESSION['user_id'] = $utilizator['id'];
        $_SESSION['username'] = $utilizator['username'];
        $_SESSION['tip'] = $utilizator['tip']; 

        switch ($utilizator['tip']) {
            case 'admin':
                header("Location: admin-panel.php");
                break;
            case 'jucator':
                header("Location: dashboard-player.php");
                break;
            case 'antrenor':
                header("Location: coach-dashboard.php");
                break;
            case 'preparator':
                header("Location: physical-evaluation.php");
                break;
            case 'manager':
                header("Location: team-statistics.php");
                break;
            default:
                header("Location: index.php");
                break;
        }
        exit;
    } else {
        $eroare = 'Utilizator sau parolă incorecte!';
    }
} 
?>

<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <title>Autentificare - BasketProgress</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container d-flex justify-content-center align-items-center" style="min-height: 100vh;">
    <div class="card p-4 shadow" style="width: 100%; max-width: 400px;">
        <h3 class="text-center mb-4">Autentificare</h3>

        <?php if ($eroare): ?>
            <div class="alert alert-danger text-center"><?= htmlspecialchars($eroare) ?></div>
        <?php endif; ?>

        <form method="post" id="loginForm">
            <div class="mb-3">
                <label for="username" class="form-label">Utilizator</label>
                <input type="text" class="form-control" name="username" id="username" required autofocus
                       value="<?= htmlspecialchars($_POST['username'] ?? '') ?>">
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Parolă</label>
                <input type="password" class="form-control" name="password" id="password" required>
                <div id="passwordFeedback" class="form-text text-danger" style="display: none;">
                    Parola trebuie să aibă minimum 5 caractere
                </div>
            </div>
            <button type="submit" class="btn btn-primary w-100">Autentificare</button>
        </form>
        <div class="mt-3 text-center">
            <a href="sign-up.php">Nu ai cont? Înregistrează-te</a>
        </div>
    </div>
</div>

<script>
// Validare JavaScript pentru parola - live feedback
document.getElementById('password').addEventListener('input', function() {
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
document.getElementById('loginForm').addEventListener('submit', function(e) {
    const parola = document.getElementById('password').value;
    
    if (parola.length < 5) {
        e.preventDefault();
        document.getElementById('passwordFeedback').style.display = 'block';
        document.getElementById('password').classList.add('is-invalid');
        document.getElementById('password').focus();
    }
});
</script>

</body>
</html>