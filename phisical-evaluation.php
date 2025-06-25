<?php include 'header.php'; ?>
<?php include 'navbar.php'; ?>
<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BasketProgress - Evaluare Fizică</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <nav class="col-md-2 d-md-block bg-dark sidebar">
                <div class="position-sticky pt-3">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link active text-white" href="#">Evaluare Fizică</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="recovery-plan.html">Plan Recuperare</a>
                        </li>
                    </ul>
                </div>
            </nav>

            <main class="col-md-10 ms-sm-auto px-md-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Evaluare Fizică Jucători</h1>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#newAssessmentModal">
                        Evaluare Nouă
                    </button>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-4">
                        <div class="card">
                            <div class="card-header">Indicatori Fizici</div>
                            <div class="card-body">
                                <canvas id="physicalIndicatorsChart"></canvas>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6 mb-4">
                        <div class="card">
                            <div class="card-header">Statistici Medicale</div>
                            <div class="card-body">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Indicator</th>
                                            <th>Valoare</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>Indice Masă Corporală</td>
                                            <td>23.5</td>
                                            <td><span class="badge bg-success">Normal</span></td>
                                        </tr>
                                        <tr>
                                            <td>Procent Grăsime</td>
                                            <td>12.3%</td>
                                            <td><span class="badge bg-success">Optim</span></td>
                                        </tr>
                                        <tr>
                                            <td>Masă Musculară</td>
                                            <td>42 kg</td>
                                            <td><span class="badge bg-warning">Mediu</span></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">Istoric Evaluări</div>
                            <div class="card-body">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Dată</th>
                                            <th>Jucător</th>
                                            <th>Înălțime</th>
                                            <th>Greutate</th>
                                            <th>% Grăsime</th>
                                            <th>Acțiuni</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>15 Mar 2024</td>
                                            <td>Andrei Popescu</td>
                                            <td>190 cm</td>
                                            <td>85 kg</td>
                                            <td>12.5%</td>
                                            <td>
                                                <button class="btn btn-sm btn-info">Detalii</button>
                                                <button class="btn btn-sm btn-warning">Editează</button>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>01 Feb 2024</td>
                                            <td>Maria Ionescu</td>
                                            <td>178 cm</td>
                                            <td>72 kg</td>
                                            <td>14.2%</td>
                                            <td>
                                                <button class="btn btn-sm btn-info">Detalii</button>
                                                <button class="btn btn-sm btn-warning">Editează</button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Modal Evaluare Nouă -->
    <div class="modal fade" id="newAssessmentModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Evaluare Fizică Nouă</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Jucător</label>
                                <select class="form-select">
                                    <option>Andrei Popescu</option>
                                    <option>Mihai Ionescu</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Dată Evaluare</label>
                                <input type="date" class="form-control" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Înălțime (cm)</label>
                                <input type="number" class="form-control" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Greutate (kg)</label>
                                <input type="number" class="form-control" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Procent Grăsime</label>
                                <input type="number" step="0.1" class="form-control" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Test Forță</label>
                                <input type="number" class="form-control" placeholder="Kg ridicare">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Test Rezistență</label>
                                <input type="number" class="form-control" placeholder="Minute alergare">
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Anulează</button>
                    <button type="button" class="btn btn-primary">Salvează Evaluare</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS și dependențe -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>

    <script>
        // Grafic Indicatori Fizici
        const physicalCtx = document.getElementById('physicalIndicatorsChart').getContext('2d');
        new Chart(physicalCtx, {
            type: 'radar',
            data: {
                labels: ['Forță', 'Viteză', 'Rezistență', 'Flexibilitate', 'Coordonare'],
                datasets: [{
                    label: 'Performanță Fizică',
                    data: [80, 75, 82, 70, 78],
                    backgroundColor: 'rgba(255, 107, 0, 0.2)',
                    borderColor: '#ff6b00'
                }]
            },
            options: {
                responsive: true,
                scale: {
                    ticks: {
                        beginAtZero: true,
                        max: 100
                    }
                }
            }
        });
    </script>
</body>
</html>
<?php include 'footer.php'; ?>