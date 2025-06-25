<?php include 'header.php'; ?>
<?php include 'navbar.php'; ?>
<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Plan Recuperare - BasketProgress</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        .recovery-timeline {
            position: relative;
            padding: 20px 0;
        }
        .recovery-item {
            border-left: 3px solid #ff6b00;
            padding-left: 20px;
            margin-bottom: 20px;
        }
        .recovery-icon {
            font-size: 2rem;
            color: #ff6b00;
            margin-right: 15px;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <div class="row">
            <div class="col-md-8">
                <h1 class="mb-4">Plan Personalizat de Recuperare</h1>
                
                <div class="recovery-timeline">
                    <div class="recovery-item">
                        <h4><i class="recovery-icon">🏥</i>Evaluare Inițială</h4>
                        <p>Diagnostic complet și stabilire strategie de recuperare</p>
                        <small class="text-muted">Data estimată: 12 Martie 2024</small>
                    </div>
                    <div class="recovery-item">
                        <h4><i class="recovery-icon">💪</i>Recuperare Fizică - Etapa 1</h4>
                        <p>Exerciții ușoare de mobilitate și tonifiere</p>
                        <small class="text-muted">12 Martie - 25 Martie 2024</small>
                    </div>
                    <div class="recovery-item">
                        <h4><i class="recovery-icon">🏀</i>Recuperare Sportivă - Etapa 2</h4>
                        <p>Reintegrare treptată în antrenamente specifice baschetului</p>
                        <small class="text-muted">26 Martie - 10 Aprilie 2024</small>
                    </div>
                </div>

                <div class="card mt-4">
                    <div class="card-header">
                        Exerciții Recomandate
                    </div>
                    <div class="card-body">
                        <ul class="list-group">
                            <li class="list-group-item">Stretching zilnic - 30 minute</li>
                            <li class="list-group-item">Exerciții de stabilitate - 3x săptămână</li>
                            <li class="list-group-item">Recuperare musculară - Ultrasunet și masaj</li>
                        </ul>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">Informații Recuperare</div>
                    <div class="card-body">
                        <h5>Status Recuperare</h5>
                        <div class="progress mb-3">
                            <div class="progress-bar bg-success" role="progressbar" style="width: 65%">65%</div>
                        </div>
                        
                        <p><strong>Medic Responsabil:</strong> Dr. Maria Popescu</p>
                        <p><strong>Estimare Revenire:</strong> 6-8 săptămâni</p>
                        
                        <button class="btn btn-primary w-100 mt-3">Discuție Online cu Medic</button>
                    </div>
                </div>

                <div class="card mt-3">
                    <div class="card-header">Monitorizare Zilnică</div>
                    <div class="card-body">
                        <form>
                            <div class="mb-3">
                                <label>Nivel Durere (0-10)</label>
                                <input type="range" class="form-range" min="0" max="10">
                            </div>
                            <div class="mb-3">
                                <label>Notițe Recuperare</label>
                                <textarea class="form-control" rows="3"></textarea>
                            </div>
                            <button class="btn btn-success w-100">Înregistrează Progres</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php include 'footer.php'; ?>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>