<?php
// index.php — головна сторінка: актуальні списки всіх сутностей (крок 6 завдання)
require_once __DIR__ . '/bootstrap.php';

$counts = [];
foreach ($repository->findAll() as $c) {
    $id = $c->getType()->getId();
    $counts[$id] = ($counts[$id] ?? 0) + 1;
}
?>
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <title>Каталог конденсаторів</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>Типи конденсаторів</h1>
    <ul>
        <?php foreach ($repository->getTypes() as $type): ?>
            <li><?= htmlspecialchars($type->getName()) ?> (конденсаторів: <?= $counts[$type->getId()] ?? 0 ?>)</li>
        <?php endforeach; ?>
    </ul>
    <p class="links"><a href="type_form.php">+ Додати тип</a></p>

    <h1>Конденсатори</h1>
    <table>
        <thead>
            <tr><th>Фото</th><th>№</th><th>Позначення</th><th>Тип</th><th>Ємність, мкФ</th><th>Напруга, В</th><th>Параметри маркування</th></tr>
        </thead>
        <tbody>
        <?php foreach ($repository->findAll() as $c): ?>
            <tr>
                <td>
                    <?php if ($c->getPhoto()): ?>
                        <img class="thumb" src="uploads/<?= htmlspecialchars($c->getPhoto()) ?>" alt="<?= htmlspecialchars($c->getDesignation()) ?>">
                    <?php else: ?>—<?php endif; ?>
                </td>
                <td><?= $c->getId() ?></td>
                <td><?= htmlspecialchars($c->getDesignation()) ?></td>
                <td><?= htmlspecialchars($c->getType()->getName()) ?></td>
                <td><?= htmlspecialchars((string) $c->getCapacitance()) ?></td>
                <td><?= htmlspecialchars((string) $c->getVoltage()) ?></td>
                <td>
                    <?= htmlspecialchars(implode('; ', array_map(fn(Parameter $p) => (string) $p, $c->getParameters()))) ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <p class="links">
        <a href="capacitor_form.php">+ Додати конденсатор</a>
        <a href="parameter_form.php">+ Додати параметр маркування</a>
    </p>
</body>
</html>
