<?php include 'header.php'; ?>
<?php include 'navbar.php'; ?>
<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Statistici Echipă Avansat - BasketProgress</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.css" rel="stylesheet">
    
    <style>
        body {
            background-color: #f4f4f4;
        }
        .team-stats-header {
            background: linear-gradient(135deg, #ff6b00, #ff9a00);
            color: white;
            padding: 30px 0;
            margin-bottom: 20px;
        }
        .stats-card {
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            margin-bottom: 20px;
            transition: transform 0.3s;
        }
        .stats-card:hover {
            transform: scale(1.02);
        }
        .player-highlight {
            background-color: #f8f9fa;
            border-radius: 5px;
            padding: 10px;
            margin-bottom: 10px;
        }
        .performance-badge {
            font-weight: bold;
            padding: 5px 10px;
            border-radius: 20px;
        }
        .logo {
            font-weight: bold;
            color: #ff6b00;
            text-decoration: none;
            font-size: 1.5rem;
        }
        .logo:hover {
            color: #ff9a00;
        }
    </style>
</head>
<body>
    <div class="team-stats-header text-center">
        <div class="container">
            <h1>Statistici Echipă</h1>
            <p>Analiză performanță și progres</p>
        </div>
    </div>

    <div class="container-fluid">
        <div class="row">
            <div class="col-md-8">
                <div class="card stats-card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h3>Performanță Generală</h3>
                        <select class="form-select form-select-sm w-auto" id="performanceSelector">
                            <option>Sezon Curent</option>
                            <option>Sezon Precedent</option>
                        </select>
                    </div>
                    <div class="card-body">
                        <div class="row text-center" id="performanceStats">
                            <div class="col-md-3">
                                <h3 class="display-6">12</h3>
                                <p>Meciuri Jucate</p>
                            </div>
                            <div class="col-md-3">
                                <h3 class="display-6">8</h3>
                                <p>Victorii</p>
                            </div>
                            <div class="col-md-3">
                                <h3 class="display-6">4</h3>
                                <p>Înfrângeri</p>
                            </div>
                            <div class="col-md-3">
                                <h3 class="display-6">66.7%</h3>
                                <p>Rată Succes</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card stats-card">
                    <div class="card-header">Statistici Jucători Top</div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 player-highlight">
                                <h5>Mihai Popescu</h5>
                                <div class="row">
                                    <div class="col-4">
                                        <strong>Puncte</strong>
                                        <p class="display-6">22.5</p>
                                    </div>
                                    <div class="col-4">
                                        <strong>Recuperări</strong>
                                        <p class="display-6">7.3</p>
                                    </div>
                                    <div class="col-4">
                                        <strong>Pase</strong>
                                        <p class="display-6">4.2</p>
                                    </div>
                                </div>
                                <span class="performance-badge bg-success text-white">MVP</span>
                            </div>
                            <div class="col-md-6 player-highlight">
                                <h5>Adrian Ionescu</h5>
                                <div class="row">
                                    <div class="col-4">
                                        <strong>Puncte</strong>
                                        <p class="display-6">18.7</p>
                                    </div>
                                    <div class="col-4">
                                        <strong>Recuperări</strong>
                                        <p class="display-6">6.5</p>
                                    </div>
                                    <div class="col-4">
                                        <strong>Pase</strong>
                                        <p class="display-6">5.1</p>
                                    </div>
                                </div>
                                <span class="performance-badge bg-primary text-white">Top Performer</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card stats-card">
                    <div class="card-header">Evoluție Performanță</div>
                    <div class="card-body">
                        <canvas id="performanceChart"></canvas>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card stats-card">
                    <div class="card-header">Istoric Competițional</div>
                    <div class="card-body">
                        <div class="card mb-3 bg-light">
                            <div class="card-body">
                                <h5>Campionat Național</h5>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span>Poziție</span>
                                    <span class="badge bg-primary">Locul 3</span>
                                </div>
                            </div>
                        </div>
                        <div class="card bg-light">
                            <div class="card-body">
                                <h5>Cupă Europeană</h5>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span>Status</span>
                                    <span class="badge bg-success">Calificare</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card stats-card">
                    <div class="card-header">Statistici Defensive</div>
                    <div class="card-body">
                        <canvas id="defensiveStatsChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Comparație Lunară -->
    <div class="modal fade" id="compareModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Comparație Performanță Lunară</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <select class="form-select mb-3">
                                <option>Februarie</option>
                                <option>Martie</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <select class="form-select mb-3">
                                <option>Martie</option>
                                <option>Februarie</option>
                            </select>
                        </div>
                    </div>
                    <canvas id="monthlyComparisonChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Raport Detaliat -->
    <div class="modal fade" id="detailedReportModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Raport Performanță Detailat</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h5>Statistici Cheie</h5>
                            <ul class="list-group">
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    Puncte pe Meci
                                    <span class="badge bg-primary rounded-pill">85.6</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    Recuperări pe Meci
                                    <span class="badge bg-success rounded-pill">42.3</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    Pase Decisive
                                    <span class="badge bg-warning rounded-pill">22.7</span>
                                </li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h5>Analză Performanță</h5>
                            <p>Echipa a demonstrat o performanță constantă, cu îmbunătățiri semnificative în atacul pozițional și apărare.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    
    <script>
        // Grafic Evoluție Performanță
        const performanceCtx = document.getElementById('performanceChart').getContext('2d');
        new Chart(performanceCtx, {
            type: 'line',
            data: {
                labels: ['Meci 1', 'Meci 2', 'Meci 3', 'Meci 4', 'Meci 5', 'Meci 6'],
                datasets: [{
                    label: 'Puncte Marcate',
                    data: [85, 92, 78, 88, 95, 90],
                    borderColor: 'rgba(255, 107, 0, 0.8)',
                    backgroundColor: 'rgba(255, 107, 0, 0.2)',
                    tension: 0.3
                }, {
                    label: 'Puncte Primite',
                    data: [80, 85, 82, 86, 90, 87],
                    borderColor: 'rgba(54, 162, 235, 0.8)',
                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                    tension: 0.3
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    title: {
                        display: true,
                        text: 'Evoluție Puncte Meci'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // Grafic Statistici Defensive
        const defensiveCtx = document.getElementById('defensiveStatsChart').getContext('2d');
        new Chart(defensiveCtx, {
            type: 'radar',
            data: {
                labels: ['Blocaje', 'Recuperări', 'Intercepții', 'Fault-uri', 'Apărare Zonă'],
                datasets: [{
                    label: 'Statistici Defensive',
                    data: [4.2, 8.6, 6.3, 2.1, 7.5],
                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                    borderColor: 'rgb(54, 162, 235)',
                    pointBackgroundColor: 'rgb(54, 162, 235)'
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    title: {
                        display: true,
                        text: 'Performanță Defensivă'
                    }
                }
            }
        });

        // Grafic Comparație Lunară
        const monthlyComparisonCtx = document.getElementById('monthlyComparisonChart').getContext('2d');
        new Chart(monthlyComparisonCtx, {
            type: 'bar',
            data: {
                labels: ['Puncte', 'Recuperări', 'Pase', 'Blocaje'],
                datasets: [
                    {
                        label: 'Februarie',
                        data: [82, 40, 20, 4],
                        backgroundColor: 'rgba(255, 107, 0, 0.6)'
                    },
                    {
                        label: 'Martie',
                        data: [85, 42, 22, 4.2],
                        backgroundColor: 'rgba(54, 162, 235, 0.6)'
                    }
                ]
            },
            options: {
                responsive: true,
                plugins: {
                    title: {
                        display: true,
                        text: 'Comparație Performanță Lunară' }
                    },
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
    
            // Funcție pentru schimbare selector performanță
            document.getElementById('performanceSelector').addEventListener('change', function() {
                const performanceStats = document.getElementById('performanceStats');
                if (this.value === 'Sezon Precedent') {
                    // Date mock pentru sezonul precedent
                    performanceStats.innerHTML = `
                        <div class="col-md-3">
                            <h3 class="display-6">12</h3>
                            <p>Meciuri Jucate</p>
                        </div>
                        <div class="col-md-3">
                            <h3 class="display-6">6</h3>
                            <p>Victorii</p>
                        </div>
                        <div class="col-md-3">
                            <h3 class="display-6">6</h3>
                            <p>Înfrângeri</p>
                        </div>
                        <div class="col-md-3">
                            <h3 class="display-6">50%</h3>
                            <p>Rată Succes</p>
                        </div>
                    `;
                } else {
                    // Revenire la datele curente
                    performanceStats.innerHTML = `
                        <div class="col-md-3">
                            <h3 class="display-6">12</h3>
                            <p>Meciuri Jucate</p>
                        </div>
                        <div class="col-md-3">
                            <h3 class="display-6">8</h3>
                            <p>Victorii</p>
                        </div>
                        <div class="col-md-3">
                            <h3 class="display-6">4</h3>
                            <p>Înfrângeri</p>
                        </div>
                        <div class="col-md-3">
                            <h3 class="display-6">66.7%</h3>
                            <p>Rată Succes</p>
                        </div>
                    `;
                }
            });
    
            // Funcție pentru comparație lunară
            const monthlyCompareSelects = document.querySelectorAll('#compareModal .form-select');
            monthlyCompareSelects.forEach((select, index) => {
                select.addEventListener('change', () => {
                    const month1 = monthlyCompareSelects[0].value;
                    const month2 = monthlyCompareSelects[1].value;
                    
                    // Actualizare date grafic
                    monthlyComparisonChart.data.datasets[0].label = month1;
                    monthlyComparisonChart.data.datasets[1].label = month2;
                    
                    // Mock date pentru diferite luni
                    const monthData = {
                        'Februarie': [82, 40, 20, 4],
                        'Martie': [85, 42, 22, 4.2],
                        'Ianuarie': [80, 38, 18, 3.8]
                    };
    
                    monthlyComparisonChart.data.datasets[0].data = monthData[month1];
                    monthlyComparisonChart.data.datasets[1].data = monthData[month2];
                    
                    monthlyComparisonChart.update();
                });
            });
        </script>
    </body>
</html>
<?php include 'footer.php'; ?>