<?php include 'header.php'; ?>
<?php include 'navbar.php'; ?>
<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Plan Nutriție Personalizat Avansat - BasketProgress</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/chart.js/dist/chart.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f4f4f4;
        }
        .nutrition-header {
            background: linear-gradient(135deg, #ff6b00, #ff9a00);
            color: white;
            padding: 30px 0;
            margin-bottom: 20px;
        }
        .nutrition-card {
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            margin-bottom: 20px;
            transition: transform 0.3s;
        }
        .nutrition-card:hover {
            transform: scale(1.02);
        }
        .nutrition-metric {
            background-color: #f8f9fa;
            border-radius: 5px;
            padding: 15px;
            margin-bottom: 15px;
        }
        .performance-badge {
            font-weight: bold;
            padding: 5px 10px;
            border-radius: 20px;
        }
        .badge-green {
            background-color: #28a745;
            color: white;
        }
        .badge-yellow {
            background-color: #ffc107;
            color: black;
        }
        .badge-red {
            background-color: #dc3545;
            color: white;
        }
        .meal-icon {
            font-size: 2rem;
            margin-right: 10px;
        }
        .supplement-card {
            transition: transform 0.3s;
        }
        .supplement-card:hover {
            transform: scale(1.05);
        }
    </style>
</head>
<body>
    <div class="nutrition-header text-center">
        <div class="container">
            <h1>Plan Nutriție Personalizat</h1>
            <p>Optimizează-ți performanța prin nutriție inteligentă</p>
        </div>
    </div>

    <div class="container">
        <div class="row">
            <div class="col-md-4">
                <div class="card nutrition-card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h3>Profil Fizic</h3>
                        <span class="performance-badge badge-green">Optimizat</span>
                    </div>
                    <div class="card-body">
                        <div class="nutrition-metric">
                            <h5>Date Personale</h5>
                            <p><strong>Vârstă:</strong> 22 ani</p>
                            <p><strong>Înălțime:</strong> 1.90 m</p>
                            <p><strong>Greutate:</strong> 85 kg</p>
                        </div>
                        <div class="nutrition-metric">
                            <h5>Obiectiv Nutrițional</h5>
                            <p>Creștere masă musculară</p>
                            <div class="progress">
                                <div class="progress-bar bg-success" role="progressbar" style="width: 70%;" aria-valuenow="70" aria-valuemin="0" aria-valuemax="100">70%</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card nutrition-card">
                    <div class="card-header">Nevoi Nutriționale</div>
                    <div class="card-body">
                        <div class="nutrition-metric">
                            <h5>Calorii Zilnice</h5>
                            <p class="display-6">2300</p>
                            <small>pentru creștere masă musculară</small>
                        </div>
                        <div class="nutrition-metric">
                            <h5>Distribuție Macronutrienți</h5>
                            <canvas id="macronutrientsChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-8">
                <div class="card nutrition-card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h3>Meniu Zilnic Recomandat</h3>
                        <select class="form-select form-select-sm w-auto">
                            <option>Zi de Antrenament</option>
                            <option>Zi de Odihnă</option>
                        </select>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3 text-center">
                                <div class="meal-icon">🍳</div>
                                <h4>Mic Dejun</h4>
                                <p>Omletă, Ovăz, Fructe</p>
                                <p><strong>600 cal</strong> | <strong>35g</strong> proteine</p>
                            </div>
                            <div class="col-md-3 text-center">
                                <div class="meal-icon">🍗</div>
                                <h4>Prânz</h4>
                                <p>Pui, Orez, Legume</p>
                                <p><strong>750 cal</strong> | <strong>45g</strong> proteine</p>
                            </div>
                            <div class="col-md-3 text-center">
                                <div class="meal-icon">🐟</div>
                                <h4>Cină</h4>
                                <p>Pește, Cartofi, Salată</p>
                                <p><strong>650 cal</strong> | <strong>40g</strong> proteine</p>
                            </div>
                            <div class="col-md-3 text-center">
                                <div class="meal-icon">🥤</div>
                                <h4>Gustări</h4>
                                <p>Shake Proteic, Fructe</p>
                                <p><strong>300 cal</strong> | <strong>25g</strong> proteine</p>
                            </div>
                        </div>
                        <hr>
                        <div class="text-center">
                            <p><strong>Total Zilnic:</strong> 2300 calorii | 145g proteine</p>
                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#mealPlanDetailsModal">
                                Detalii Plan Masă
                            </button>
                        </div>
                    </div>
                </div>

                <div class="card nutrition-card">
                    <div class="card-header">Recomandări Suplimente</div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="card supplement-card mb-3 bg-light">
                                    <div class="card-body text-center">
                                        <h5>Proteină Whey</h5>
                                        <p>30g înainte/după antrenament</p>
                                        <span class="badge bg-success">Recuperare</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card supplement-card mb-3 bg-light">
                                    <div class="card-body text-center">
                                        <h5>Creatină</h5>
                                        <p>5g zilnic pentru recuperare</p>
                                        <span class="badge bg-primary">Performanță</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card supplement-card mb-3 bg-light">
                                    <div class="card-body text-center">
                                        <h5>Multivitamine</h5>
                                        <p>Supplement zilnic dimineața</p>
                                        <span class="badge bg-warning text-dark">Sănătate</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="text-center">
                            <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#supplementDetailsModal">
                                Informații Suplimentare
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Jurnal Nutriție -->
    <div class="modal fade" id="nutritionLogModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Jurnal Nutriție</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Dată</label>
                                <input type="date" class="form-control">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Masă</label>
                                <select class="form-select">
                                    <option>Mic Dejun</option>
                                    <option>Prânz</option>
                                    <option>Cină</option>
                                    <option>Gustare</option>
                                </select>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Alimente Consumate</label>
                            <textarea class="form-control" rows="3" placeholder="Descrie alimentele și cantitățile"></textarea>
                        </div>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Calorii</label>
                                <input type="number" class="form-control">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Proteine (g)</label>
                                <input type="number" class="form-control">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Carbohidrați (g)</label>
                                <input type="number" class="form-control">
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">Salvează Intrare</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Detalii Plan Masă -->
    <div class="modal fade" id="mealPlanDetailsModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Detalii Plan Masă</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h5>Mic Dejun Detaliat</h5>
                            <ul>
                                <li>3 ouă întregi</li>
                                <li>50g ovăz</li>
                                <li>1 banană</li>
                                <li>Lingură de unt de arahide</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h5>Beneficii Nutriționale</h5>
                            <p>Mic dejun echilibrat cu proteine, carbohidrați complecși și grăsimi sănătoase pentru energie susținută.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Detalii Suplimente -->
    <div class="modal fade" id="supplementDetailsModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Informații Suplimente</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-4">
                            <h5>Proteină Whey</h5>
                            <p>Susține recuperarea musculară și creșterea masei musculare.</p>
                        </div>
                        <div class="col-md-4">
                            <h5>Creatină</h5>
                            <p>Îmbunătățește performanța și facilitează creșterea masei musculare.</p>
                        </div>
                        <div class="col-md-4">
                            <h5>Multivitamine</h5>
                            <p>Asigură aportul zilnic de vitamine și minerale esențiale.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Grafic Macronutrienți
        const macronutrientsCtx = document.getElementById('macronutrientsChart').getContext('2d');
        new Chart(macronutrientsCtx, {
            type: 'pie',
            data: {
                labels: ['Proteine', 'Carbohidrați', 'Grăsimi'],
                datasets: [{
                    data: [35, 45, 20],
                    backgroundColor: [
                        '#ff6b00',   // Portocaliu pentru proteine
                        '#28a745',   // Verde pentru carbohidrați
                        '#dc3545'    // Roșu pentru grăsimi
                    ]
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    title: {
                        display: true,
                        text: 'Distribuție Macronutrienți'
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const value = context.parsed;
                                const percentage = ((value / total) * 100).toFixed(1);
                                return `${context.label}: ${value}% (${percentage}%)`;
                            }
                        }
                    }
                }
            }
        });
    </script>
</body>
</html>
<?php include 'footer.php'; ?>