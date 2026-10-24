<?php
// type_form.php — форма додавання типу конденсатора (без файлу), метод POST
require_once __DIR__ . '/bootstrap.php';

$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');

    if ($name === '') {
        $error = 'Назва типу обов’язкова.';
    } elseif (mb_strlen($name) > 50) {
        $error = 'Назва типу — не більше 50 символів.';
    } elseif ($repository->typeNameExists($name)) {
        $error = 'Такий тип уже існує.';
    } else {
        $repository->createType($name);
        header('Location: index.php');   // PRG-патерн
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <title>Додати тип конденсатора</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>Новий тип конденсатора</h1>
    <?php if ($error): ?>
        <p class="error"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>
    <form method="post" action="type_form.php">
        <label>Назва: <input type="text" name="name" required
            value="<?= htmlspecialchars($_POST['name'] ?? '') ?>"></label>
        <button type="submit">Зберегти</button>
    </form>
    <p><a href="index.php">← Назад до каталогу</a></p>
</body>
</html>
