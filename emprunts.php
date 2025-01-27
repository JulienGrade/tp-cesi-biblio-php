<?php
// Fonction pour charger les emprunts depuis un fichier JSON
function chargerEmprunts() {
    return file_exists('emprunts.txt') ? json_decode(file_get_contents('emprunts.txt'), true) : [];
}

// Fonction pour sauvegarder les emprunts dans un fichier JSON
function sauvegarderEmprunts($emprunts) {
    file_put_contents('emprunts.txt', json_encode($emprunts, JSON_PRETTY_PRINT));
}

// Fonction pour charger les livres (déjà définie dans livres.php)
include_once 'livres.php';

// Gestion de l'emprunt d'un livre
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['emprunter'])) {
    $livres = chargerLivres();
    $emprunts = chargerEmprunts();

    $titreLivre = $_POST['titre'];
    $nomEmprunteur = $_POST['nom'];

    // Trouver le livre dans la liste des livres disponibles
    $indexLivre = array_search($titreLivre, array_column($livres, 'titre'));
    if ($indexLivre !== false) {
        // Ajouter le livre à la liste des emprunts avec toutes ses informations
        $emprunts[] = [
            'titre' => $livres[$indexLivre]['titre'],
            'auteur' => $livres[$indexLivre]['auteur'],
            'annee' => $livres[$indexLivre]['annee'],
            'domaine' => $livres[$indexLivre]['domaine'],
            'nom' => $nomEmprunteur,
            'date' => date('Y-m-d')
        ];

        // Retirer le livre des livres disponibles
        unset($livres[$indexLivre]);

        // Sauvegarder les modifications
        sauvegarderLivres(array_values($livres));
        sauvegarderEmprunts(array_values($emprunts));

        header('Location: emprunts.php');
        exit;
    } else {
        echo "Erreur : Le livre n'est pas disponible.";
    }
}


// Gestion du retour d'un livre
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['retourner'])) {
    $livres = chargerLivres();
    $emprunts = chargerEmprunts();

    $titreLivre = $_POST['titreRetour'];

    // Trouver le livre dans la liste des emprunts
    $indexEmprunt = array_search($titreLivre, array_column($emprunts, 'titre'));
    if ($indexEmprunt !== false) {
        // Récupérer toutes les informations du livre depuis l'emprunt
        $livreRetourne = $emprunts[$indexEmprunt];

        // Ajouter le livre à la liste des livres disponibles
        $livres[] = [
            'titre' => $livreRetourne['titre'],
            'auteur' => $livreRetourne['auteur'],
            'annee' => $livreRetourne['annee'],
            'domaine' => $livreRetourne['domaine']
        ];

        // Supprimer le livre des emprunts
        unset($emprunts[$indexEmprunt]);

        // Sauvegarder les modifications
        sauvegarderLivres(array_values($livres));
        sauvegarderEmprunts(array_values($emprunts));

        header('Location: emprunts.php');
        exit;
    } else {
        echo "Erreur : Le livre à retourner n'a pas été trouvé dans les emprunts.";
    }
}
$emprunts = chargerEmprunts();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Emprunts</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 text-gray-800">
<div class="container mx-auto p-4">
    <!-- Barre de navigation -->
    <nav class="flex justify-between items-center mb-4">
        <a href="index.php" class="bg-gray-700 text-white px-4 py-2 rounded hover:bg-gray-800">Menu Principal</a>
        <a href="livres.php" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Gestion des Livres</a>
    </nav>

    <h1 class="text-3xl font-bold mb-4">Gestion des Emprunts</h1>

    <!-- Formulaire d'emprunt -->
    <form method="POST" class="bg-white p-4 rounded shadow mb-4">
        <h2 class="text-xl font-semibold mb-2">Emprunter un Livre</h2>
        <div class="mb-2">
            <label class="block">Titre du Livre :</label>
            <input type="text" name="titre" class="w-full p-2 border rounded" required>
        </div>
        <div class="mb-2">
            <label class="block">Nom de l'Emprunteur :</label>
            <input type="text" name="nom" class="w-full p-2 border rounded" required>
        </div>
        <button type="submit" name="emprunter" class="bg-green-500 text-white px-4 py-2 rounded">Emprunter</button>
    </form>

    <!-- Formulaire de retour -->
    <form method="POST" class="bg-white p-4 rounded shadow mb-4">
        <h2 class="text-xl font-semibold mb-2">Retourner un Livre</h2>
        <div class="mb-2">
            <label class="block">Titre du Livre :</label>
            <label>
                <input type="text" name="titreRetour" class="w-full p-2 border rounded" required>
            </label>
        </div>
        <button type="submit" name="retourner" class="bg-red-500 text-white px-4 py-2 rounded">Retourner</button>
    </form>

    <!-- Liste des emprunts -->
    <h2 class="text-xl font-semibold mb-2">Livres Empruntés</h2>
    <table class="table-auto w-full bg-white shadow rounded">
        <thead>
        <tr class="bg-gray-200">
            <th class="px-4 py-2">Titre</th>
            <th class="px-4 py-2">Nom de l'Emprunteur</th>
            <th class="px-4 py-2">Date</th>
        </tr>
        </thead>
        <tbody>
        <?php if (!empty($emprunts) && is_array($emprunts)): ?>
            <?php foreach ($emprunts as $emprunt): ?>
                <tr>
                    <td class="border px-4 py-2"><?= htmlspecialchars($emprunt['titre']) ?></td>
                    <td class="border px-4 py-2"><?= htmlspecialchars($emprunt['nom']) ?></td>
                    <td class="border px-4 py-2"><?= htmlspecialchars($emprunt['date']) ?></td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="3" class="text-center p-4">Aucun emprunt trouvé.</td>
            </tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>
</body>
</html>
