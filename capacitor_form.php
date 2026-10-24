<?php
require_once __DIR__ . '/bootstrap.php';

const MAX_PHOTO_SIZE = 2 * 1024 * 1024;   // 2 МБ

$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $designation = trim($_POST['designation'] ?? '');
    $type        = $repository->findTypeById((int) ($_POST['type_id'] ?? 0));
    $capacitance = (float) ($_POST['capacitance'] ?? 0);
    $voltage     = (float) ($_POST['voltage'] ?? 0);

    if ($designation === '' || mb_strlen($designation) > 50 || $type === null
        || $capacitance <= 0 || $voltage <= 0) {
        $error = 'Заповніть усі поля коректно (позначення до 50 символів, ємність і напруга більші за нуль).';
    } else {
        $photoFilename = null;
        $file = $_FILES['photo'] ?? null;

        if ($file !== null && $file['error'] !== UPLOAD_ERR_NO_FILE) {
            if ($file['error'] === UPLOAD_ERR_INI_SIZE || $file['error'] === UPLOAD_ERR_FORM_SIZE
                || $file['size'] > MAX_PHOTO_SIZE) {
                $error = 'Файл завеликий (максимум 2 МБ).';
            } elseif ($file['error'] !== UPLOAD_ERR_OK) {
                $error = 'Не вдалося завантажити файл (код помилки ' . $file['error'] . ').';
            } else {
                $allowedTypes = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
                $mimeType = mime_content_type($file['tmp_name']);  

                if (!isset($allowedTypes[$mimeType])) {
                    $error = 'Дозволені лише зображення JPEG, PNG чи WEBP.';
                } else {
                    $photoFilename = uniqid('capacitor_', true) . '.' . $allowedTypes[$mimeType];
                    if (!move_uploaded_file($file['tmp_name'], __DIR__ . '/uploads/' . $photoFilename)) {
                        $error = 'Не вдалося зберегти файл на сервері.';
                        $photoFilename = null;
                    }
                }
            }
        }

        if ($error === null) {
            $repository->create($designation, $type, $capacitance, $voltage, $photoFilename);
            header('Location: index.php');   
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <title>Додати конденсатор</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>Новий конденсатор</h1>
    <?php if ($error): ?>
        <p class="error"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>
    <form method="post" action="capacitor_form.php" enctype="multipart/form-data">
        <label>Позначення (напр. К10-17):
            <input type="text" name="designation" required
                   value="<?= htmlspecialchars($_POST['designation'] ?? '') ?>"></label>
        <label>Тип:
            <select name="type_id" required>
                <?php foreach ($repository->getTypes() as $t): ?>
                    <option value="<?= $t->getId() ?>"
                        <?= ((int) ($_POST['type_id'] ?? 0)) === $t->getId() ? 'selected' : '' ?>>
                        <?= htmlspecialchars($t->getName()) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </label>
        <label>Ємність (мкФ): <input type="number" step="any" min="0" name="capacitance" required
                   value="<?= htmlspecialchars($_POST['capacitance'] ?? '') ?>"></label>
        <label>Номінальна напруга (В): <input type="number" step="any" min="0" name="voltage" required
                   value="<?= htmlspecialchars($_POST['voltage'] ?? '') ?>"></label>
        <label>Фото: <input type="file" name="photo" accept="image/*"></label>
        <button type="submit">Зберегти</button>
    </form>
    <p><a href="index.php">← Назад до каталогу</a></p>
</body>
</html>
