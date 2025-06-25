<?php include 'header.php'; ?>
<?php include 'navbar.php'; ?>
<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Analiză Meci - BasketProgress</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .match-analysis-card {
            margin-bottom: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .stat-badge {
            margin-right: 10px;
            margin-bottom: 10px;
        }
        .player-performance {
            background-color: #f8f9fa;
            border-radius: 5px;
            padding: 10px;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <div class="container mt-4">
        <div class="row">
            <div class="col-md-8">
                <div class="card match-analysis-card">
                    <div class="card-header">
                        <h2>Analiză Meci: BC Urban București vs CSM București</h2>
                        <p class="text-muted">Data: 15 Februarie 2024</p>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h4>Scor Final</h4>
                                <h3>85 - 78</h3>
                                <p><strong>Rezultat:</strong> Victorie</p>
                            </div>
                            <div class="col-md-6">
                                <h4>Statistici Cheie</h4>
                                <span class="badge bg-primary stat-badge">Recuperări: 42</span>
                                <span class="badge bg-success stat-badge">Aruncări de 3: 12/28</span>
                                <span class="badge bg-warning stat-badge">Tururi de minge: 15</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card match-analysis-card">
                    <div class="card-header">Performanța Jucătorilor</div>
                    <div class="card-body">
                        <div class="player-performance">
                            <h5>Mihai Popescu</h5>
                            <div class="row">
                                <div class="col-md-4">
                                    <strong>Puncte:</strong> 22
                                </div>
                                <div class="col-md-4">
                                    <strong>Recuperări:</strong> 8
                                </div>
                                <div class="col-md-4">
                                    <strong>Pase Decisive:</strong> 5
                                </div>
                            </div>
                        </div>
                        <div class="player-performance">
                            <h5>Adrian Radu</h5>
                            <div class="row">
                                <div class="col-md-4">
                                    <strong>Puncte:</strong> 15
                                </div>
                                <div class="col-md-4">
                                    <strong>Recuperări:</strong> 6
                                </div>
                                <div class="col-md-4">
                                    <strong>Pase Decisive:</strong> 3
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card match-analysis-card">
                    <div class="card-header">Analiză Strategică</div>
                    <div class="card-body">
                        <h4>Puncte Forte</h4>
                        <ul>
                            <li>Apărare solidă în ultimul sfert</li>
                            <li>Recuperări ofensive decisive</li>
                            <li>Eficiență ridicată la aruncări de 3 puncte</li>
                        </ul>

                        <h4>Zone de Îmbunătățit</h4>
                        <ul>
                            <li>Reducerea tururilor de minge</li>
                            <li>Îmbunătățirea procentajului de aruncări libere</li>
                            <li>Mai multă mișcare fără minge</li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">Statistici Comparative</div>
                    <div class="card-body">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Indicator</th>
                                    <th>Noi</th>
                                    <th>Adversar</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Puncte</td>
                                    <td>85</td>
                                    <td>78</td>
                                </tr>
                                <tr>
                                    <td>Recuperări</td>
                                    <td>42</td>
                                    <td>38</td>
                                </tr>
                                <tr>
                                    <td>Pase Decisive</td>
                                    <td>18</td>
                                    <td>15</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php include 'footer.php'; ?>