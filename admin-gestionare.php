<?php
session_start();
require 'db_connect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['tip'] !== 'admin') {
    header("Location: login.php");
    exit;
}

// Handle form submissions
$mesaj = '';
$eroare = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        // ADAUGA UTILIZATOR
        if (isset($_POST['adauga_utilizator'])) {
            if (empty($_POST['username']) || empty($_POST['password']) || empty($_POST['tip'])) {
                throw new Exception("Username, parolă și tip sunt obligatorii!");
            }
            
            $stmt = $conn->prepare("INSERT INTO utilizatori (username, password, tip, email, activ) VALUES (:username, :password, :tip, :email, 1)");
            $stmt->bindParam(':username', $_POST['username']);
            $stmt->bindParam(':password', $_POST['password']);
            $stmt->bindParam(':tip', $_POST['tip']);
            $stmt->bindParam(':email', $_POST['email']);
            $stmt->execute();
            $mesaj = "Utilizator adăugat cu succes!";
        }
        
        // ADAUGA ECHIPA
        elseif (isset($_POST['adauga_echipa'])) {
            if (empty($_POST['nume_echipa']) || empty($_POST['oras'])) {
                throw new Exception("Numele echipei și orașul sunt obligatorii!");
            }
            
            $stmt = $conn->prepare("INSERT INTO echipe (nume_echipa, oras, antrenor, arena, an_fondare) VALUES (:nume_echipa, :oras, :antrenor, :arena, :an_fondare)");
            $stmt->bindParam(':nume_echipa', $_POST['nume_echipa']);
            $stmt->bindParam(':oras', $_POST['oras']);
            $stmt->bindParam(':antrenor', $_POST['antrenor']);
            $stmt->bindParam(':arena', $_POST['arena']);
            $stmt->bindParam(':an_fondare', $_POST['an_fondare']);
            $stmt->execute();
            $mesaj = "Echipa adăugată cu succes!";
        }
        
        // ADAUGA JUCATOR
        elseif (isset($_POST['adauga_jucator'])) {
            if (empty($_POST['nume']) || empty($_POST['prenume']) || empty($_POST['pozitie']) || empty($_POST['echipa_id'])) {
                throw new Exception("Numele, prenumele, poziția și echipa sunt obligatorii!");
            }
            
            $stmt = $conn->prepare("INSERT INTO jucatori (nume, prenume, pozitie, inaltime, greutate, data_nastere, echipa_id) VALUES (:nume, :prenume, :pozitie, :inaltime, :greutate, :data_nastere, :echipa_id)");
            $stmt->bindParam(':nume', $_POST['nume']);
            $stmt->bindParam(':prenume', $_POST['prenume']);
            $stmt->bindParam(':pozitie', $_POST['pozitie']);
            $stmt->bindParam(':inaltime', $_POST['inaltime']);
            $stmt->bindParam(':greutate', $_POST['greutate']);
            $stmt->bindParam(':data_nastere', $_POST['data_nastere']);
            $stmt->bindParam(':echipa_id', $_POST['echipa_id']);
            $stmt->execute();
            $mesaj = "Jucător adăugat cu succes!";
        }
        
        // STERGE UTILIZATOR
        elseif (isset($_POST['sterge_utilizator'])) {
            $stmt = $conn->prepare("UPDATE utilizatori SET activ = 0 WHERE id = :id");
            $stmt->bindParam(':id', $_POST['user_id']);
            $stmt->execute();
            $mesaj = "Utilizator dezactivat cu succes!";
        }
        
        // STERGE ECHIPA
        elseif (isset($_POST['sterge_echipa'])) {
            $stmt = $conn->prepare("DELETE FROM echipe WHERE id = :id");
            $stmt->bindParam(':id', $_POST['echipa_id']);
            $stmt->execute();
            $mesaj = "Echipa ștearsă cu succes!";
        }
        
        // STERGE JUCATOR
        elseif (isset($_POST['sterge_jucator'])) {
            $stmt = $conn->prepare("DELETE FROM jucatori WHERE id = :id");
            $stmt->bindParam(':id', $_POST['jucator_id']);
            $stmt->execute();
            $mesaj = "Jucător șters cu succes!";
        }
        
    } catch (Exception $e) {
        $eroare = $e->getMessage();
    }
}
?>
<?php include 'header.php'; ?>

<div class="container mt-4">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>
                    <i class="fas fa-users-cog me-2"></i>Gestionare Completă - Utilizatori, Echipe și Jucători
                </h2>
                <a href="admin-panel.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Înapoi la Panoul Admin
                </a>
            </div>
            
            <?php if ($mesaj): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i><?= $mesaj ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            
            <?php if ($eroare): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i><?= $eroare ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <ul class="nav nav-tabs" id="adminTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="utilizatori-tab" data-bs-toggle="tab" data-bs-target="#utilizatori" type="button">
                <i class="fas fa-users me-2"></i>Utilizatori
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="echipe-tab" data-bs-toggle="tab" data-bs-target="#echipe" type="button">
                <i class="fas fa-basketball-ball me-2"></i>Echipe
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="jucatori-tab" data-bs-toggle="tab" data-bs-target="#jucatori" type="button">
                <i class="fas fa-running me-2"></i>Jucători
            </button>
        </li>
    </ul>

    <div class="tab-content pt-4" id="adminTabsContent">
        <!-- ==================== UTILIZATORI TAB ==================== -->
        <div class="tab-pane fade show active" id="utilizatori" role="tabpanel">
            <!-- ADD USER FORM -->
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-user-plus me-2"></i>Adaugă Utilizator Nou</h5>
                </div>
                <div class="card-body">
                    <form method="POST" class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">Username *</label>
                            <input name="username" class="form-control" placeholder="Username unic" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Email</label>
                            <input name="email" type="email" class="form-control" placeholder="email@example.com">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Parolă *</label>
                            <input name="password" type="password" class="form-control" placeholder="Parolă sigură" required>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Tip *</label>
                            <select name="tip" class="form-select" required>
                                <option value="jucator">Jucător</option>
                                <option value="antrenor">Antrenor</option>
                                <option value="preparator">Preparator</option>
                                <option value="manager">Manager</option>
                                <option value="admin">Admin</option>
                            </select>
                        </div>
                        <div class="col-md-1">
                            <label class="form-label">&nbsp;</label>
                            <button name="adauga_utilizator" class="btn btn-success w-100">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- USERS LIST -->
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-list me-2"></i>Lista Utilizatori</h5>
                    <div class="col-md-4">
                        <input type="text" id="searchUsers" class="form-control" placeholder="🔍 Caută utilizatori...">
                    </div>
                </div>
                <div class="card-body p-0">
                    <?php
                    try {
                        $stmt = $conn->query("SELECT * FROM utilizatori ORDER BY id DESC");
                        echo '<div class="table-responsive">
                                <table class="table table-hover mb-0" id="usersTable">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>ID</th>
                                            <th>Username</th>
                                            <th>Email</th>
                                            <th>Tip</th>
                                            <th>Status</th>
                                            <th>Acțiuni</th>
                                        </tr>
                                    </thead>
                                    <tbody>';
                        while ($row = $stmt->fetch()) {
                            $activStatus = isset($row['activ']) ? ($row['activ'] ? 'Activ' : 'Inactiv') : 'Activ';
                            $badgeClass = $activStatus === 'Activ' ? 'bg-success' : 'bg-danger';
                            
                            echo "<tr>
                                    <td><strong>#{$row['id']}</strong></td>
                                    <td><i class='fas fa-user me-2'></i>{$row['username']}</td>
                                    <td>" . (isset($row['email']) ? $row['email'] : '<span class="text-muted">Fără email</span>') . "</td>
                                    <td><span class='badge bg-primary'>" . ucfirst($row['tip']) . "</span></td>
                                    <td><span class='badge {$badgeClass}'>{$activStatus}</span></td>
                                    <td>
                                        <form method='POST' class='d-inline'>
                                            <input type='hidden' name='user_id' value='{$row['id']}'>
                                            <button name='sterge_utilizator' class='btn btn-sm btn-outline-danger' onclick='return confirm(\"Dezactivezi utilizatorul {$row['username']}?\")'>
                                                <i class='fas fa-user-times'></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>";
                        }
                        echo '</tbody></table></div>';
                    } catch (Exception $e) {
                        echo '<div class="alert alert-danger m-3">Eroare: ' . $e->getMessage() . '</div>';
                    }
                    ?>
                </div>
            </div>
        </div>

        <!-- ==================== ECHIPE TAB ==================== -->
        <div class="tab-pane fade" id="echipe" role="tabpanel">
            <!-- ADD TEAM FORM -->
            <div class="card mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-plus-circle me-2"></i>Adaugă Echipă Nouă</h5>
                </div>
                <div class="card-body">
                    <form method="POST" class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">Nume Echipă *</label>
                            <input name="nume_echipa" class="form-control" placeholder="Ex: Lakers Cluj" required>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Oraș *</label>
                            <input name="oras" class="form-control" placeholder="Cluj-Napoca" required>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Antrenor</label>
                            <input name="antrenor" class="form-control" placeholder="Nume antrenor">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Arena</label>
                            <input name="arena" class="form-control" placeholder="Sala Sporturilor">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">An Fondare</label>
                            <input name="an_fondare" type="number" class="form-control" placeholder="2020" min="1900" max="<?= date('Y') ?>">
                        </div>
                        <div class="col-md-1">
                            <label class="form-label">&nbsp;</label>
                            <button name="adauga_echipa" class="btn btn-success w-100">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- TEAMS LIST -->
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-list me-2"></i>Lista Echipe</h5>
                    <div class="col-md-4">
                        <input type="text" id="searchTeams" class="form-control" placeholder="🔍 Caută echipe...">
                    </div>
                </div>
                <div class="card-body p-0">
                    <?php
                    try {
                        $stmt = $conn->query("SELECT * FROM echipe ORDER BY id DESC");
                        echo '<div class="table-responsive">
                                <table class="table table-hover mb-0" id="teamsTable">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>ID</th>
                                            <th>Nume Echipă</th>
                                            <th>Oraș</th>
                                            <th>Antrenor</th>
                                            <th>Arena</th>
                                            <th>An Fondare</th>
                                            <th>Acțiuni</th>
                                        </tr>
                                    </thead>
                                    <tbody>';
                        while ($row = $stmt->fetch()) {
                            echo "<tr>
                                    <td><strong>#{$row['id']}</strong></td>
                                    <td><i class='fas fa-basketball-ball me-2 text-warning'></i><strong>{$row['nume_echipa']}</strong></td>
                                    <td><i class='fas fa-map-marker-alt me-2 text-primary'></i>{$row['oras']}</td>
                                    <td>" . ($row['antrenor'] ?? '<span class="text-muted">Nespecificat</span>') . "</td>
                                    <td>" . ($row['arena'] ?? '<span class="text-muted">Nespecificat</span>') . "</td>
                                    <td>" . ($row['an_fondare'] ?? '<span class="text-muted">N/A</span>') . "</td>
                                    <td>
                                        <form method='POST' class='d-inline'>
                                            <input type='hidden' name='echipa_id' value='{$row['id']}'>
                                            <button name='sterge_echipa' class='btn btn-sm btn-outline-danger' onclick='return confirm(\"Ștergi echipa {$row['nume_echipa']}?\")'>
                                                <i class='fas fa-trash'></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>";
                        }
                        echo '</tbody></table></div>';
                    } catch (Exception $e) {
                        echo '<div class="alert alert-danger m-3">Eroare: ' . $e->getMessage() . '</div>';
                    }
                    ?>
                </div>
            </div>
        </div>

        <!-- ==================== JUCATORI TAB ==================== -->
        <div class="tab-pane fade" id="jucatori" role="tabpanel">
            <!-- ADD PLAYER FORM -->
            <div class="card mb-4">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="fas fa-user-plus me-2"></i>Adaugă Jucător Nou</h5>
                </div>
                <div class="card-body">
                    <form method="POST" class="row g-3">
                        <div class="col-md-2">
                            <label class="form-label">Nume *</label>
                            <input name="nume" class="form-control" placeholder="Popescu" required>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Prenume *</label>
                            <input name="prenume" class="form-control" placeholder="Ion" required>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Poziție *</label>
                            <select name="pozitie" class="form-select" required>
                                <option value="">Alege poziția</option>
                                <option value="PG">Point Guard (PG)</option>
                                <option value="SG">Shooting Guard (SG)</option>
                                <option value="SF">Small Forward (SF)</option>
                                <option value="PF">Power Forward (PF)</option>
                                <option value="C">Center (C)</option>
                            </select>
                        </div>
                        <div class="col-md-1">
                            <label class="form-label">Înălțime (m)</label>
                            <input name="inaltime" type="number" step="0.01" class="form-control" placeholder="1.85">
                        </div>
                        <div class="col-md-1">
                            <label class="form-label">Greutate (kg)</label>
                            <input name="greutate" type="number" step="0.1" class="form-control" placeholder="75.5">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Data nașterii</label>
                            <input name="data_nastere" type="date" class="form-control">
                        </div>
                        <div class="col-md-1">
                            <label class="form-label">Echipa *</label>
                            <select name="echipa_id" class="form-select" required>
                                <option value="">Alege</option>
                                <?php
                                try {
                                    $echipe = $conn->query("SELECT id, nume_echipa FROM echipe ORDER BY nume_echipa");
                                    while ($echipa = $echipe->fetch()) {
                                        echo "<option value='{$echipa['id']}'>{$echipa['nume_echipa']}</option>";
                                    }
                                } catch (Exception $e) {
                                    echo "<option value=''>Eroare la încărcare</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="col-md-1">
                            <label class="form-label">&nbsp;</label>
                            <button name="adauga_jucator" class="btn btn-info w-100">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- PLAYERS LIST -->
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-list me-2"></i>Lista Jucători</h5>
                    <div class="col-md-4">
                        <input type="text" id="searchPlayers" class="form-control" placeholder="🔍 Caută jucători...">
                    </div>
                </div>
                <div class="card-body p-0">
                    <?php
                    try {
                        $stmt = $conn->query("
                            SELECT j.*, e.nume_echipa 
                            FROM jucatori j 
                            LEFT JOIN echipe e ON j.echipa_id = e.id 
                            ORDER BY j.id DESC
                        ");
                        echo '<div class="table-responsive">
                                <table class="table table-hover mb-0" id="playersTable">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>ID</th>
                                            <th>Nume Complet</th>
                                            <th>Poziție</th>
                                            <th>Înălțime</th>
                                            <th>Greutate</th>
                                            <th>Vârsta</th>
                                            <th>Echipă</th>
                                            <th>Acțiuni</th>
                                        </tr>
                                    </thead>
                                    <tbody>';
                        while ($row = $stmt->fetch()) {
                            $varsta = '';
                            if ($row['data_nastere']) {
                                $varsta = date_diff(date_create($row['data_nastere']), date_create('today'))->y . ' ani';
                            }
                            
                            echo "<tr>
                                    <td><strong>#{$row['id']}</strong></td>
                                    <td><i class='fas fa-user me-2'></i><strong>{$row['nume']} {$row['prenume']}</strong></td>
                                    <td><span class='badge bg-info'>{$row['pozitie']}</span></td>
                                    <td>" . ($row['inaltime'] ? $row['inaltime'] . ' m' : '<span class="text-muted">N/A</span>') . "</td>
                                    <td>" . ($row['greutate'] ? $row['greutate'] . ' kg' : '<span class="text-muted">N/A</span>') . "</td>
                                    <td>" . ($varsta ?: '<span class="text-muted">N/A</span>') . "</td>
                                    <td>" . ($row['nume_echipa'] ?? '<span class="text-muted">Fără echipă</span>') . "</td>
                                    <td>
                                        <form method='POST' class='d-inline'>
                                            <input type='hidden' name='jucator_id' value='{$row['id']}'>
                                            <button name='sterge_jucator' class='btn btn-sm btn-outline-danger' onclick='return confirm(\"Ștergi jucătorul {$row['nume']} {$row['prenume']}?\")'>
                                                <i class='fas fa-trash'></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>";
                        }
                        echo '</tbody></table></div>';
                    } catch (Exception $e) {
                        echo '<div class="alert alert-danger m-3">Eroare: ' . $e->getMessage() . '</div>';
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Search functionality
document.addEventListener('DOMContentLoaded', function() {
    // Search Users
    document.getElementById('searchUsers').addEventListener('keyup', function() {
        const searchValue = this.value.toLowerCase();
        const table = document.getElementById('usersTable');
        const rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');
        
        for (let i = 0; i < rows.length; i++) {
            const row = rows[i];
            const username = row.cells[1].textContent.toLowerCase();
            const email = row.cells[2].textContent.toLowerCase();
            const tip = row.cells[3].textContent.toLowerCase();
            
            if (username.includes(searchValue) || email.includes(searchValue) || tip.includes(searchValue)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        }
    });
    
    // Search Teams
    document.getElementById('searchTeams').addEventListener('keyup', function() {
        const searchValue = this.value.toLowerCase();
        const table = document.getElementById('teamsTable');
        const rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');
        
        for (let i = 0; i < rows.length; i++) {
            const row = rows[i];
            const nume = row.cells[1].textContent.toLowerCase();
            const oras = row.cells[2].textContent.toLowerCase();
            const antrenor = row.cells[3].textContent.toLowerCase();
            const arena = row.cells[4].textContent.toLowerCase();
            
            if (nume.includes(searchValue) || oras.includes(searchValue) || antrenor.includes(searchValue) || arena.includes(searchValue)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        }
    });
    
    // Search Players
    document.getElementById('searchPlayers').addEventListener('keyup', function() {
        const searchValue = this.value.toLowerCase();
        const table = document.getElementById('playersTable');
        const rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');
        
        for (let i = 0; i < rows.length; i++) {
            const row = rows[i];
            const nume = row.cells[1].textContent.toLowerCase();
            const pozitie = row.cells[2].textContent.toLowerCase();
            const echipa = row.cells[6].textContent.toLowerCase();
            
            if (nume.includes(searchValue) || pozitie.includes(searchValue) || echipa.includes(searchValue)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        }
    });
});
</script>

<?php include 'footer.php'; ?>