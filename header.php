<?php
// Pornește output buffering și sesiunea (o singură dată!)
ob_start();
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BasketProgress - Platforma Integrată de Management Baschet</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        body {
            background-color: #f4f4f4;
        }
        .hero-section {
            background: linear-gradient(135deg, #ff6b00, #ff9a00);
            color: white;
            padding: 100px 0;
            text-align: center;
        }
        .feature-icon {
            font-size: 3rem;
            margin-bottom: 15px;
            color: #ff6b00;
        }
        .role-card {
            transition: transform 0.3s;
            margin-bottom: 20px;
        }
        .role-card:hover {
            transform: scale(1.05);
        }
    </style>
</head>
<body>
