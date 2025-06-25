<?php
require 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add_jucator'])) {
        $nume = $_POST['nume'];
        $prenume = $_POST['prenume'];
        $pozitie = $_POST['pozitie'];
        $inaltime = $_POST['inaltime'];
        $greutate = $_POST['greutate'];
        $data_nastere = $_POST['data_nastere'];
        $echipa_id = $_POST['echipa_id'];

        $stmt = $conn->prepare("INSERT INTO jucatori (nume, prenume, pozitie, inaltime, greutate, data_nastere, echipa_id) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$nume, $prenume, $pozitie, $inaltime, $greutate, $data_nastere, $echipa_id]);
    } elseif (isset($_POST['add_echipa'])) {
        $nume_echipa = $_POST['nume_echipa'];
        $oras = $_POST['oras'];
        $antrenor = $_POST['antrenor'];
        $arena = $_POST['arena'];
        $an_fondare = $_POST['an_fondare'];

        $stmt = $conn->prepare("INSERT INTO echipe (nume_echipa, oras, antrenor, arena, an_fondare) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$nume_echipa, $oras, $antrenor, $arena, $an_fondare]);
    }
    header("Location: view_data.php");
    exit();
}

$echipe = $conn->query("SELECT id, nume_echipa FROM echipe")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <title>Adaugă Date</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <div class="container mx-auto p-4">
        <h1 class="text-2xl font-bold mb-4">Adaugă Jucător</h1>
        <div class="bg-white p-4 rounded shadow">
            <form action="add_data.php" method="POST">
                <div class="mb-4">
                    <label class="block text-gray-700">Nume</label>
                    <input type="text" name="nume" class="w-full p-2 border rounded" required>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700">Prenume</label>
                    <input type="text" name="prenume" class="w-full p-2 border rounded" required>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700">Poziție</label>
                    <input type="text" name="pozitie" class="w-full p-2 border rounded" required>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700">Înălțime (cm)</label>
                    <input type="number" name="inaltime" class="w-full p-2 border rounded" required>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700">Greutate (kg)</label>
                    <input type="number" name="greutate" class="w-full p-2 border rounded" required>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700">Data Nașterii</label>
                    <input type="date" name="data_nastere" class="w-full p-2 border rounded" required>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700">Echipa</label>
                    <select name="echipa_id" class="w-full p-2 border rounded" required>
                        <?php foreach ($echipe as $echipa): ?>
                        <option value="<?php echo $echipa['id']; ?>"><?php echo htmlspecialchars($echipa['nume_echipa']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button type="submit" name="add_jucator" class="bg-blue-500 text-white p-2 rounded">Adaugă Jucător</button>
            </form>
        </div>

        <h1 class="text-2xl font-bold mt-8 mb-4">Adaugă Echipă</h1>
        <div class="bg-white p-4 rounded shadow">
            <form action="add_data.php" method="POST">
                <div class="mb-4">
                    <label class="block text-gray-700">Nume Echipă</label>
                    <input type="text" name="nume_echipa" class="w-full p-2 border rounded" required>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700">Oraș</label>
                    <input type="text" name="oras" class="w-full p-2 border rounded" required>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700">Antrenor</label>
                    <input type="text" name="antrenor" class="w-full p-2 border rounded" required>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700">Arena</label>
                    <input type="text" name="arena" class="w-full p-2 border rounded" required>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700">An Fondare</label>
                    <input type="number" name="an_fondare" class="w-full p-2 border rounded" required>
                </div>
                <button type="submit" name="add_echipa" class="bg-blue-500 text-white p-2 rounded">Adaugă Echipă</button>
            </form>
        </div>
    </div>
</body>
</html>