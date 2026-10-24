<?php
require_once __DIR__ . '/bootstrap.php';

$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $capacitorId = (int) ($_POST['capacitor_id'] ?? 0);
    $name  = trim($_POST['name'] ?? '');
    $value = trim($_POST['value'] ?? '');

    if ($name === '' || $value === '' || $repository->findById($capacitorId) === null) {
        $error = 'Заповніть усі поля коректно.';
    } elseif (preg_match('/[|:]/', $name) || str_contains($value, '|')) {
        $error = 'Назва не може містити символи « | » і « : », значення — символ « | ».';
    } elseif (mb_strlen($name) > 50 || mb_strlen($value) > 100) {
        $error = 'Занадто довгий текст (назва — до 50, значення — до 100 символів).';
    } else {
        $repository->addParameterToCapacitor($capacitorId, $name, $value);
        header('Location: index.php');   // PRG-патерн
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <title>Додати параметр маркування</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>Новий параметр маркування</h1>
    <?php if ($error): ?>
        <p class="error"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>
    <form method="post" action="parameter_form.php">
        <label>Конденсатор:
            <select name="capacitor_id" required>
                <?php foreach ($repository->findAll() as $c): ?>
                    <option value="<?= $c->getId() ?>"
                        <?= ((int) ($_POST['capacitor_id'] ?? 0)) === $c->getId() ? 'selected' : '' ?>>
                        <?= htmlspecialchars('#' . $c->getId() . ' ' . $c->getDesignation()) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </label>
        <label>Назва параметра: <input type="text" name="name" required
                   value="<?= htmlspecialchars($_POST['name'] ?? '') ?>"></label>
        <label>Значення: <input type="text" name="value" required
                   value="<?= htmlspecialchars($_POST['value'] ?? '') ?>"></label>
        <button type="submit">Зберегти</button>
    </form>
    <p><a href="index.php">← Назад до каталогу</a></p>
</body>
</html>
