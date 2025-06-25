<?php 
session_start(); 
require 'db_connect.php';  

if (!isset($_SESSION['user_id']) || $_SESSION['tip'] !== 'admin') {     
    header("Location: login.php");     
    exit; 
} 
?>

<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panou Administrare - BasketProgress</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .admin-header {
            background: rgba(0,0,0,0.8);
            backdrop-filter: blur(10px);
            border-bottom: 3px solid #ffd700;
            padding: 20px 0;
            position: sticky;
            top: 0;
            z-index: 1000;
        }
        
        .admin-title {
            color: #ffd700;
            font-weight: 700;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.5);
        }
        
        .welcome-text {
            color: rgba(255,255,255,0.9);
            font-size: 1.1rem;
        }
        
        .admin-card {
            background: rgba(255,255,255,0.95);
            backdrop-filter: blur(20px);
            border: none;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.2);
            transition: all 0.4s ease;
            overflow: hidden;
            position: relative;
        }
        
        .admin-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 5px;
            background: linear-gradient(90deg, #667eea, #764ba2, #ffd700);
        }
        
        .admin-card:hover {
            transform: translateY(-10px) scale(1.02);
            box-shadow: 0 30px 60px rgba(0,0,0,0.3);
        }
        
        .card-icon {
            font-size: 4rem;
            background: linear-gradient(45deg, #667eea, #764ba2);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 20px;
        }
        
        .admin-btn {
            background: linear-gradient(45deg, #667eea, #764ba2);
            border: none;
            padding: 12px 30px;
            border-radius: 50px;
            color: white;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        
        .admin-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
            transition: left 0.5s;
        }
        
        .admin-btn:hover::before {
            left: 100%;
        }
        
        .admin-btn:hover {
            transform: scale(1.05);
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.4);
        }
        
        .stats-section {
            background: rgba(255,255,255,0.1);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 30px;
            margin-top: 40px;
            border: 1px solid rgba(255,255,255,0.2);
        }
        
        .stat-card {
            background: rgba(255,255,255,0.2);
            border: 1px solid rgba(255,255,255,0.3);
            border-radius: 15px;
            padding: 25px;
            text-align: center;
            color: white;
            transition: all 0.3s ease;
        }
        
        .stat-card:hover {
            background: rgba(255,255,255,0.3);
            transform: translateY(-5px);
        }
        
        .floating-shapes {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: -1;
        }
        
        .shape {
            position: absolute;
            opacity: 0.1;
            animation: float 6s ease-in-out infinite;
        }
        
        .shape:nth-child(1) { top: 20%; left: 10%; animation-delay: 0s; }
        .shape:nth-child(2) { top: 60%; right: 10%; animation-delay: 2s; }
        .shape:nth-child(3) { bottom: 20%; left: 20%; animation-delay: 4s; }
        
        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(180deg); }
        }
        
        .user-info {
            background: rgba(255,215,0,0.2);
            border: 2px solid #ffd700;
            border-radius: 15px;
            padding: 15px;
            color: white;
            backdrop-filter: blur(10px);
        }
        
        .logout-btn {
            background: rgba(220,53,69,0.8);
            border: 2px solid #dc3545;
            color: white;
            border-radius: 10px;
            padding: 8px 20px;
            transition: all 0.3s ease;
        }
        
        .logout-btn:hover {
            background: #dc3545;
            color: white;
            transform: scale(1.05);
        }
    </style>
</head>
<body>
    <!-- Floating Background Shapes -->
    <div class="floating-shapes">
        <i class="fas fa-basketball-ball shape" style="font-size: 100px;"></i>
        <i class="fas fa-trophy shape" style="font-size: 80px;"></i>
        <i class="fas fa-users shape" style="font-size: 120px;"></i>
    </div>

    <!-- Admin Header -->
    <div class="admin-header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <h1 class="admin-title mb-0">
                        <i class="fas fa-crown me-3"></i>Panou de Administrare
                    </h1>
                    <p class="welcome-text mb-0 mt-2">
                        Bine ai venit, <strong><?= $_SESSION['username'] ?></strong>! Controlezi întregul sistem BasketProgress.
                    </p>
                </div>
                <div class="col-lg-4 text-end">
                    <div class="user-info d-inline-block me-3">
                        <i class="fas fa-user-shield me-2"></i>
                        <strong>Administrator</strong>
                    </div>
                    <a href="logout.php" class="btn logout-btn">
                        <i class="fas fa-sign-out-alt me-2"></i>Deconectare
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container mt-5">
        <!-- Quick Access Cards -->
        <div class="row g-4 mb-5">
            <div class="col-lg-6">
                <div class="admin-card h-100">
                    <div class="card-body text-center p-5">
                        <i class="fas fa-users-cog card-icon"></i>
                        <h3 class="card-title mb-3">Gestionează Utilizatori și Echipe</h3>
                        <p class="card-text text-muted mb-4">
                            Controlul complet asupra utilizatorilor, jucătorilor și echipelor din sistem. 
                            Adaugă, editează sau șterge orice entitate.
                        </p>
                        <a href="admin-gestionare.php" class="admin-btn">
                            <i class="fas fa-tools me-2"></i>Accesează Gestionarea
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="admin-card h-100">
                    <div class="card-body text-center p-5">
                        <i class="fas fa-sitemap card-icon"></i>
                        <h3 class="card-title mb-3">Pagini Sistem</h3>
                        <p class="card-text text-muted mb-4">
                            Vizualizează și controlează toate paginile sistemului. 
                            Gestionează accesul utilizatorilor și drepturile pentru fiecare pagină.
                        </p>
                        <a href="admin-pagini.php" class="admin-btn">
                            <i class="fas fa-eye me-2"></i>Vezi Toate Paginile
                        </a>
                    </div>
                </div>
            </div>
        </div>



        <!-- Quick Actions -->
        <div class="row mt-5 mb-5">
            <div class="col-12 text-center">
                <h3 class="text-white mb-4">
                    <i class="fas fa-bolt me-2"></i>Acțiuni Rapide
                </h3>
                <a href="echipe.php" class="btn admin-btn me-3 mb-3">
                    <i class="fas fa-plus me-2"></i>Adaugă Echipă
                </a>
                <a href="index.php" class="btn admin-btn mb-3" style="background: linear-gradient(45deg, #28a745, #20c997);">
                    <i class="fas fa-home me-2"></i>Înapoi la Site
                </a>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Add some interactive effects -->
    <script>
        // Add floating animation to cards on scroll
        window.addEventListener('scroll', () => {
            const cards = document.querySelectorAll('.admin-card');
            cards.forEach((card, index) => {
                const scrolled = window.pageYOffset;
                const parallax = scrolled * 0.1;
                card.style.transform = `translateY(${parallax * (index + 1)}px)`;
            });
        });

        // Add click effect to buttons
        document.querySelectorAll('.admin-btn').forEach(btn => {
            btn.addEventListener('click', function(e) {
                let ripple = document.createElement('span');
                ripple.classList.add('ripple');
                this.appendChild(ripple);
                
                setTimeout(() => ripple.remove(), 600);
            });
        });
    </script>
</body>
</html>