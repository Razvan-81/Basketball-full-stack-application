<?php 
session_start(); 
if (!isset($_SESSION['user_id']) || $_SESSION['tip'] !== 'admin') { 
    header("Location: login.html"); 
    exit; 
} 
?>

<?php
require 'db_connect.php';

function getJucatori($conn) {
    $stmt = $conn->query("SELECT j.*, e.nume_echipa FROM jucatori j JOIN echipe e ON j.echipa_id = e.id");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getEchipe($conn) {
    $stmt = $conn->query("SELECT * FROM echipe");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$jucatori = getJucatori($conn);
$echipe = getEchipe($conn);
?>

<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <title>Vizualizare Date</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <div class="container mx-auto p-4">
        <h1 class="text-2xl font-bold mb-4">Jucători</h1>
        <table class="min-w-full bg-white border">
            <thead>
                <tr>
                    <th class="py-2 px-4 border">Nume</th>
                    <th class="py-2 px-4 border">Prenume</th>
                    <th class="py-2 px-4 border">Poziție</th>
                    <th class="py-2 px-4 border">Înălțime (cm)</th>
                    <th class="py-2 px-4 border">Greutate (kg)</th>
                    <th class="py-2 px-4 border">Data Nașterii</th>
                    <th class="py-2 px-4 border">Echipa</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($jucatori as $jucator): ?>
                <tr>
                    <td class="py-2 px-4 border"><?php echo htmlspecialchars($jucator['nume']); ?></td>
                    <td class="py-2 px-4 border"><?php echo htmlspecialchars($jucator['prenume']); ?></td>
                    <td class="py-2 px-4 border"><?php echo htmlspecialchars($jucator['pozitie']); ?></td>
                    <td class="py-2 px-4 border"><?php echo htmlspecialchars($jucator['inaltime']); ?></td>
                    <td class="py-2 px-4 border"><?php echo htmlspecialchars($jucator['greutate']); ?></td>
                    <td class="py-2 px-4 border"><?php echo htmlspecialchars($jucator['data_nastere']); ?></td>
                    <td class="py-2 px-4 border"><?php echo htmlspecialchars($jucator['nume_echipa']); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <h1 class="text-2xl font-bold mt-8 mb-4">Echipe</h1>
        <table class="min-w-full bg-white border">
            <thead>
                <tr>
                    <th class="py-2 px-4 border">Nume Echipă</th>
                    <th class="py-2 px-4 border">Oraș</th>
                    <th class="py-2 px-4 border">Antrenor</th>
                    <th class="py-2 px-4 border">Arena</th>
                    <th class="py-2 px-4 border">An Fondare</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($echipe as $echipa): ?>
                <tr>
                    <td class="py-2 px-4 border"><?php echo htmlspecialchars($echipa['nume_echipa']); ?></td>
                    <td class="py-2 px-4 border"><?php echo htmlspecialchars($echipa['oras']); ?></td>
                    <td class="py-2 px-4 border"><?php echo htmlspecialchars($echipa['antrenor']); ?></td>
                    <td class="py-2 px-4 border"><?php echo htmlspecialchars($echipa['arena']); ?></td>
                    <td class="py-2 px-4 border"><?php echo htmlspecialchars($echipa['an_fondare']); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>