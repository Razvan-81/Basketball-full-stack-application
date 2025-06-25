<?php
require 'db_connect.php';

function getTopJucatori($conn) {
    $stmt = $conn->query("SELECT j.*, e.nume_echipa FROM jucatori j JOIN echipe e ON j.echipa_id = e.id LIMIT 3");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$jucatori = getTopJucatori($conn);
?>

<?php include 'header.php'; ?>
<?php include 'navbar.php'; ?>

</body>
</html>
<?php ob_end_flush(); ?>
<!DOCTYPE html>
<html lang="ro">

<body>
    <!-- Secțiune Hero -->
    <div class="hero-section">
        <div class="container">
            <h1>BasketProgress</h1>
            <p>Urmărește-ți performanța, devino un campion</p>
            <a href="sign-up.php" class="btn btn-light btn-lg">Începe Acum</a>
        </div>
    </div>

    <!-- Roluri și Caracteristici -->
    <div class="container mt-5">
        <h2 class="text-center mb-4">Platformă Integrată pentru toate rolurile</h2>
        <div class="row">
            <div class="col-md-3">
                <div class="card role-card">
                    <div class="card-body text-center">
                        <div class="feature-icon">🏀</div>
                        <h3>Jucător</h3>
                        <p>Monitorizează-ți progresul, antrenamentele și performanța individuală.</p>
                        <a href="player-profile.php" class="btn btn-outline-primary">Detalii</a>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card role-card">
                    <div class="card-body text-center">
                        <div class="feature-icon">👥</div>
                        <h3>Antrenor</h3>
                        <p>Gestionează echipa, planifică antrenamente și analizează performanța.</p>
                        <a href="coach-dashboard.php" class="btn btn-outline-primary">Detalii</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Caracteristici Principale -->
    <div class="container mt-5">
        <div class="row text-center">
            <div class="col-md-4 mb-4">
                <div class="feature-icon">📊</div>
                <h2>Monitorizare Performanță</h2>
                <p>Urmărește fiecare aspect al performanței tale cu instrumente precise și ușor de utilizat.</p>
            </div>
            <div class="col-md-4 mb-4">
                <div class="feature-icon">🏀</div>
                <h2>Antrenament Personalizat</h2>
                <p>Planuri de antrenament adaptate nevoilor tale individuale și obiectivelor tale specifice.</p>
            </div>
            <div class="col-md-4 mb-4">
                <div class="feature-icon">📈</div>
                <h2>Progres Continuu</h2>
                <p>Vizualizează progresul tău prin grafice detaliate și statistici comprehensive.</p>
            </div>
        </div>
    </div>

    <!-- Cookie Consent Banner -->
    <div class="position-fixed bottom-0 start-0 end-0 p-3 bg-dark text-white" id="cookieConsent" style="z-index: 1050; display: none;">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <p class="mb-md-0">Acest site folosește cookie-uri pentru a oferi o experiență mai bună. Prin continuarea navigării, sunteți de acord cu <a href="cookies.php" class="text-warning">Politica noastră de Cookies</a>.</p>
                </div>
                <div class="col-md-4 text-md-end">
                    <button class="btn btn-sm btn-outline-light me-2" onclick="document.getElementById('cookieConsent').style.display='none';">Refuz</button>
                    <button class="btn btn-sm btn-warning" onclick="document.getElementById('cookieConsent').style.display='none';">Accept</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-dark text-white text-center py-3 mt-5">
        <div class="container">
            <div class="row">
                <div class="col-md-3">
                    <h5>BasketProgress</h5>
                    <p>Tehnologie pentru performanță în baschet</p>
                </div>
                <div class="col-md-3">
                    <h5>Navigare Rapidă</h5>
                    <ul class="list-unstyled">
                        <li><a href="index.php" class="text-white">Acasă</a></li>
                        <li><a href="faq.php" class="text-white">Întrebări Frecvente</a></li>
                        <li><a href="login.php" class="text-white">Autentificare</a></li>
                    </ul>
                </div>
                <div class="col-md-3">
                    <h5>Informații Legale</h5>
                    <ul class="list-unstyled">
                        <li><a href="terms&conditions.php" class="text-white">Termeni și Condiții</a></li>
                        <li><a href="cookies.php" class="text-white">Politica de Cookies</a></li>
                    </ul>
                </div>
                <div class="col-md-3">
                    <h5>Contact</h5>
                    <p>office@basketprogress.ro<br>+40 721 234 567</p>
                </div>
            </div>
            <hr class="my-2">
            <p>&copy; 2024 BasketProgress. Toate drepturile rezervate.</p>
        </div>
    </footer>

    <!-- Bootstrap JS și dependențe -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
    
    <!-- Script pentru afișarea banner-ului de cookie -->
    <script>
        window.onload = function() {
            // Verificăm dacă utilizatorul a acceptat deja cookie-urile
            if(!localStorage.getItem('cookieConsent')) {
                // Dacă nu, afișăm banner-ul
                setTimeout(function() {
                    document.getElementById('cookieConsent').style.display = 'block';
                }, 1000);
            }
            
            // Setăm handler pentru butonul de acceptare
            document.querySelectorAll('#cookieConsent .btn-warning').forEach(function(button) {
                button.addEventListener('click', function() {
                    localStorage.setItem('cookieConsent', 'true');
                    document.getElementById('cookieConsent').style.display = 'none';
                });
            });
        }
    </script>
</body>
</html>