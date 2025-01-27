<?php
// Fonction pour charger les livres depuis un fichier JSON
function chargerLivres() {
    // Vérifie si le fichier existe
    if (!file_exists('livres.txt')) {
        return []; // Si le fichier n'existe pas, retourne un tableau vide
    }

    // Récupère le contenu du fichier
    $contenu = file_get_contents('livres.txt');

    // Vérifie si le fichier est vide
    if (trim($contenu) === '') {
        return []; // Retourne un tableau vide si le fichier est vide
    }

    // Décode le contenu JSON
    $livres = json_decode($contenu, true);

    // Vérifie si le décodage a échoué ou si ce n'est pas un tableau
    if (!is_array($livres)) {
        return []; // Retourne un tableau vide en cas d'erreur de format
    }

    return $livres;
}

// Fonction pour sauvegarder les livres dans un fichier JSON
function sauvegarderLivres($livres) {
    file_put_contents('livres.txt', json_encode($livres, JSON_PRETTY_PRINT));
}

// Gestion du formulaire d'ajout
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajouter'])) {
    $livres = chargerLivres();
    $livres[] = [
        'titre' => $_POST['titre'],
        'auteur' => $_POST['auteur'],
        'annee' => $_POST['annee'],
        'domaine' => $_POST['domaine']
    ];
    sauvegarderLivres($livres);
    header('Location: livres.php');
    exit;
}

$livres = chargerLivres();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Livres</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 text-gray-800">
<div class="container mx-auto p-4">
    <!-- Barre de navigation -->
    <nav class="flex justify-between items-center mb-4">
        <a href="index.php" class="bg-gray-700 text-white px-4 py-2 rounded hover:bg-gray-800">Menu Principal</a>
        <a href="emprunts.php" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Gestion des Emprunts</a>
    </nav>

    <h1 class="text-3xl font-bold mb-4">Gestion des Livres</h1>

    <!-- Contenu existant -->
    <form method="POST" class="bg-white p-4 rounded shadow mb-4">
        <h2 class="text-xl font-semibold mb-2">Ajouter un Livre</h2>
        <div class="mb-2">
            <label class="block">Titre :</label>
            <input type="text" name="titre" class="w-full p-2 border rounded" required>
        </div>
        <div class="mb-2">
            <label class="block">Auteur :</label>
            <input type="text" name="auteur" class="w-full p-2 border rounded" required>
        </div>
        <div class="mb-2">
            <label class="block">Année :</label>
            <input type="number" name="annee" class="w-full p-2 border rounded" required>
        </div>
        <div class="mb-2">
            <label class="block">Domaine :</label>
            <input type="text" name="domaine" class="w-full p-2 border rounded" required>
        </div>
        <button type="submit" name="ajouter" class="bg-blue-500 text-white px-4 py-2 rounded">Ajouter</button>
    </form>

    <!-- Liste des livres -->
    <h2 class="text-xl font-semibold mb-2">Liste des Livres</h2>
    <table class="table-auto w-full bg-white shadow rounded">
        <thead>
        <tr class="bg-gray-200">
            <th class="px-4 py-2">Titre</th>
            <th class="px-4 py-2">Auteur</th>
            <th class="px-4 py-2">Année</th>
            <th class="px-4 py-2">Domaine</th>
        </tr>
        </thead>
        <tbody>
        <?php if (!empty($livres) && is_array($livres)): ?>
            <?php foreach ($livres as $livre): ?>
                <tr>
                    <td class="border px-4 py-2"><?= htmlspecialchars($livre['titre']) ?></td>
                    <td class="border px-4 py-2"><?= htmlspecialchars($livre['auteur']) ?></td>
                    <td class="border px-4 py-2"><?= htmlspecialchars($livre['annee']) ?></td>
                    <td class="border px-4 py-2"><?= htmlspecialchars($livre['domaine']) ?></td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="4" class="text-center p-4">Aucun livre trouvé.</td>
            </tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>
</body>
</html>
