<?php include 'header.php'; ?>
<?php include 'navbar.php'; ?>
<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Performanță Echipă Avansat - BasketProgress</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/chart.js/dist/chart.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f4f4f4;
        }
        .performance-header {
            background: linear-gradient(135deg, #ff6b00, #ff9a00);
            color: white;
            padding: 30px 0;
            margin-bottom: 20px;
        }
        .performance-card {
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            margin-bottom: 20px;
            transition: transform 0.3s;
        }
        .performance-card:hover {
            transform: scale(1.02);
        }
        .performance-metric {
            background-color: #f8f9fa;
            border-radius: 5px;
            padding: 15px;
            margin-bottom: 15px;
        }
        .team-stat-badge {
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
        .match-result {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 15px;
            padding: 10px;
            background-color: #ffffff;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <div class="performance-header text-center">
        <div class="container">
            <h1>Performanță Echipă BC Urban</h1>
            <p>Analiză completă a performanței și statisticilor</p>
        </div>
    </div>

    <div class="container-fluid">
        <div class="row">
            <div class="col-md-8">
                <div class="card performance-card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h3>Statistici Principale</h3>
                        <select class="form-select form-select-sm w-auto">
                            <option>Sezon Curent</option>
                            <option>Sezon Precedent</option>
                        </select>
                    </div>
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-md-3">
                                <h4>Victorii</h4>
                                <p class="display-6">12</p>
                            </div>
                            <div class="col-md-3">
                                <h4>Înfrângeri</h4>
                                <p class="display-6">5</p>
                            </div>
                            <div class="col-md-3">
                                <h4>Medie Puncte</h4>
                                <p class="display-6">85.6</p>
                            </div>
                            <div class="col-md-3">
                                <h4>Procentaj Victorii</h4>
                                <p class="display-6">70%</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="card performance-card">
                            <div class="card-header">Performanță Aruncări</div>
                            <div class="card-body">
                                <canvas id="shootingChart"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card performance-card">
                            <div class="card-header">Performanță pe Posturi</div>
                            <div class="card-body">
                                <canvas id="positionPerformanceChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card performance-card">
                    <div class="card-header">Rezultate Meciuri Recente</div>
                    <div class="card-body">
                        <div class="match-result">
                            <div>
                                <strong>BC Urban</strong> vs CSM București
                            </div>
                            <div class="team-stat-badge bg-success text-white">
                                Victorie 85-78
                            </div>
                        </div>
                        <div class="match-result">
                            <div>
                                <strong>Dinamo</strong> vs BC Urban
                            </div>
                            <div class="team-stat-badge bg-danger text-white">
                                Înfrângere 82-76
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card performance-card">
                    <div class="card-header">Top Performeri</div>
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <div class="flex-shrink-0">
                                <img src="/api/placeholder/50/50" class="rounded-circle" alt="Jucător">
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h5 class="mb-1">Mihai Popescu</h5>
                                <p class="mb-0">22.5 puncte/meci</p>
                            </div>
                            <span class="team-stat-badge bg-primary text-white">MVP</span>
                        </div>
                        <div class="d-flex align-items-center mb-3">
                            <div class="flex-shrink-0">
                                <img src="/api/placeholder/50/50" class="rounded-circle" alt="Jucător">
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h5 class="mb-1">Adrian Radu</h5>
                                <p class="mb-0">10.2 recuperări/meci</p>
                            </div>
                            <span class="team-stat-badge bg-success text-white">Defensiv</span>
                        </div>
                    </div>
                </div>

                <div class="card performance-card">
                    <div class="card-header">Urmează</div>
                    <div class="card-body">
                        <div class="alert alert-primary">
                            <strong>Meci Următor</strong>
                            <p>BC Urban vs U Cluj</p>
                            <p>20 Martie 2024, 19:00</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS și dependențe -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Grafic Aruncări
        const shootingCtx = document.getElementById('shootingChart').getContext('2d');
        new Chart(shootingCtx, {
            type: 'bar',
            data: {
                labels: ['2 Puncte', '3 Puncte', 'Aruncări Libere'],
                datasets: [{
                    label: 'Procentaj Aruncări',
                    data: [52, 38, 82],
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.6)',
                        'rgba(54, 162, 235, 0.6)',
                        'rgba(255, 206, 86, 0.6)'
                    ],
                    borderColor: [
                        'rgb(255, 99, 132)',
                        'rgb(54, 162, 235)',
                        'rgb(255, 206, 86)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    title: {
                        display: true,
                        text: 'Eficiență Aruncări'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100
                    }
                }
            }
        });

        // Grafic Performanță pe Posturi
        const positionCtx = document.getElementById('positionPerformanceChart').getContext('2d');
        new Chart(positionCtx, {
            type: 'radar',
            data: {
                labels: ['Pivot', 'Extremă', 'Pivot secund', 'Coordonator'],
                datasets: [{
                    label: 'Performanță pe Posturi',
                    data: [75, 85, 70, 80],
                    backgroundColor: 'rgba(255, 107, 0, 0.2)',
                    borderColor: 'rgb(255, 107, 0)',
                    pointBackgroundColor: 'rgb(255, 107, 0)'
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    title: {
                        display: true,
                        text: 'Performanță Posturi'
                    }
                }
            }
        });
    </script>
</body>
</html>
<?php include 'footer.php'; ?>