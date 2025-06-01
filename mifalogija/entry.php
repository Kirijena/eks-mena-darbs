<?php
// Запускаем сессию в самом начале файла, до вывода любого HTML или пробелов
session_start();

// Если не задана переменная текущей страницы, устанавливаем пустую строку
if (!isset($current_page)) {
    $current_page = '';
}

// Проверяем, находимся ли мы в папке mythologies или mifalogija, чтобы корректно указывать пути к файлам
$is_mythology_page = strpos($_SERVER['PHP_SELF'], '/mythologies/') !== false || strpos($_SERVER['PHP_SELF'], '/mifalogija/') !== false;
$base_path = $is_mythology_page ? '../' : '';

// Подключаемся к базе данных
$serveris = "localhost";
$lietotajs = "grobina1_belovinceva";
$parole = "U9R1@kvzL";
$datubaze = "grobina1_belovinceva";

$savienojums = mysqli_connect($serveris, $lietotajs, $parole, $datubaze);

// Проверяем успешность соединения
if (!$savienojums) {
    die("Savienojuma kļūda: " . mysqli_connect_error());
}

// Получаем ID записи из GET параметра, если нет - по умолчанию 1
$id = isset($_GET['id']) ? (int)$_GET['id'] : 1;

// Проверяем, авторизован ли пользователь (есть ли в сессии admin_id)
$is_logged_in = isset($_SESSION['admin_id']);

// Если пользователь авторизован и пришёл POST-запрос с лайком — обрабатываем лайк
if ($is_logged_in) {
    // Если массив лайкнутых записей в сессии ещё не создан — создаём
    if (!isset($_SESSION['liked_entries'])) {
        $_SESSION['liked_entries'] = [];
    }

    // Обработка POST-запроса лайка
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['like_entry_id'])) {
        $like_id = (int)$_POST['like_entry_id'];
        // Если ещё не лайкал эту запись — добавляем её в список лайков
        if (!in_array($like_id, $_SESSION['liked_entries'])) {
            $_SESSION['liked_entries'][] = $like_id;
        }
        // Чтобы избежать повторной отправки формы при обновлении страницы — делаем редирект на эту же страницу
        header("Location: " . $_SERVER['REQUEST_URI']);
        exit();
    }
}

// Получаем данные записи из таблицы eksamens_entries
$stmt = mysqli_prepare($savienojums, "SELECT id, title, type_id, category_id, description, country, first_mention_date, description_text, published FROM eksamens_entries WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

// Проверяем, что запись существует
if ($row = mysqli_fetch_assoc($result)) {

    // Получаем название типа из таблицы eksamens_categories по type_id
    $type_id = $row['type_id'];
    $type_nosaukums = "Nezināms"; // "Неизвестно" по умолчанию

    $type_stmt = mysqli_prepare($savienojums, "SELECT Nosaukums FROM eksamens_categories WHERE id = ?");
    mysqli_stmt_bind_param($type_stmt, "i", $type_id);
    mysqli_stmt_execute($type_stmt);
    $type_result = mysqli_stmt_get_result($type_stmt);
    if ($type_row = mysqli_fetch_assoc($type_result)) {
        $type_nosaukums = $type_row['Nosaukums'];
    }
    mysqli_stmt_close($type_stmt);

    // Получаем название категории из таблицы eksamens_kategorija по category_id
    $category_id = $row['category_id'];
    $category_nosaukums = "Nezināms"; // "Неизвестно" по умолчанию

    $cat_stmt = mysqli_prepare($savienojums, "SELECT Kategorija FROM eksamens_kategorija WHERE id_kat = ?");
    mysqli_stmt_bind_param($cat_stmt, "i", $category_id);
    mysqli_stmt_execute($cat_stmt);
    $cat_result = mysqli_stmt_get_result($cat_stmt);
    if ($cat_row = mysqli_fetch_assoc($cat_result)) {
        $category_nosaukums = $cat_row['Kategorija'];
    }
    mysqli_stmt_close($cat_stmt);

    // Проверяем, поставил ли текущий авторизованный пользователь лайк на эту запись
    $liked = $is_logged_in && in_array($id, $_SESSION['liked_entries']);
    ?>

    <!DOCTYPE html>
    <html lang="lv">
    <head>
        <meta charset="UTF-8">
        <title><?php echo htmlspecialchars($row['title']); ?></title>
        <style>
            :root {
                --color-primary: #92400e;
                --color-secondary: #b45309;
                --color-background: #fef3c7;
                --color-surface: rgba(255, 255, 255, 0.9);
                --color-text: #78350f;
                --color-text-light: #92400e;
                --color-border: #d97706;
                --color-success: #059669;
                --color-error: #dc2626;
                --color-warning: #d97706;
            }
            body {
                font-family: 'Segoe UI', system-ui, sans-serif;
                background: linear-gradient(to bottom, #fee0c7, #e9aa75);
                min-height: 100vh;
                color: var(--color-text);
                padding-top: 80px;
            }
            .entry-container {
                max-width: 1000px;
                margin: 0 auto;
                background: var(--color-surface);
                padding: 30px;
                border-radius: 8px;
                box-shadow: 0 2px 6px rgba(0,0,0,0.1);
            }
            .entry-header {
                text-align: center;
                margin-bottom: 30px;
            }
            .entry-header h1 {
                color: var(--color-primary);
                font-family: 'Cinzel', serif;
                font-size: 2.5rem;
                margin: 0;
            }
            .entry-details {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 20px;
                margin-bottom: 30px;
            }
            .entry-field {
                background: white;
                border-radius: 12px;
                padding: 25px;
                margin-bottom: 30px;
                box-shadow: 0 4px 12px rgba(139, 69, 19, 0.1);
                border-left: 4px solid var(--color-secondary);
                font-size: 1.2rem;
            }
            .entry-field .label {
                color: var(--color-text-light);
                font-weight: bold;
                margin-bottom: 5px;
                font-size: 1.5rem;
            }
            .entry-description {
                background: white;
                border-radius: 12px;
                padding: 25px;
                margin-bottom: 30px;
                box-shadow: 0 4px 12px rgba(139, 69, 19, 0.1);
                border-left: 4px solid var(--color-secondary);
                font-size: 1.2rem;
            }
            .entry-description .label {
                color: var(--color-text-light);
                font-weight: bold;
                margin-bottom: 5px;
                font-size: 1.5rem;
            }
            .back-link {
                display: inline-block;
                margin-top: 20px;
                padding: 10px 20px;
                background: var(--color-primary);
                color: white;
                text-decoration: none;
                border-radius: 6px;
                transition: background 0.3s;
                cursor: pointer;
            }
            .back-link:hover {
                background: var(--color-secondary);
            }
            @media (max-width: 768px) {
                .entry-details {
                    grid-template-columns: 1fr;
                }
            }
        </style>
    </head>
    <body>
    <div class="entry-container">
        <div class="entry-header">
            <h1><?php echo htmlspecialchars($row['title']); ?></h1>
        </div>
        <div class="entry-details">
            <div class="entry-field">
                <div class="label">Tips</div>
                <div><?php echo htmlspecialchars($type_nosaukums); ?></div>
            </div>
            <div class="entry-field">
                <div class="label">Kategorija</div>
                <div><?php echo htmlspecialchars($category_nosaukums); ?></div>
            </div>
            <div class="entry-field">
                <div class="label">Valsts</div>
                <div><?php echo htmlspecialchars($row['country']); ?></div>
            </div>
            <div class="entry-field">
                <div class="label">Pirmās pieminēšanas datums</div>
                <div><?php echo htmlspecialchars($row['first_mention_date']); ?></div>
            </div>
        </div>
        <div class="entry-description">
            <div class="label">Apraksts:</div>
            <p><?php echo nl2br(htmlspecialchars($row['description'])); ?></p>
            <div class="label" style="margin-top: 15px;">Detalizēts apraksts:</div>
            <p><?php echo nl2br(htmlspecialchars($row['description_text'])); ?></p>
        </div>
        <div style="text-align: center; margin-top: 20px;">
            <?php if ($is_logged_in): ?>
                <?php if (!$liked): ?>
                    <form method="post" action="">
                        <input type="hidden" name="like_entry_id" value="<?php echo $id; ?>">
                        <button type="submit" style="padding: 10px 20px; background: var(--color-success); color: white; border: none; border-radius: 6px; cursor: pointer;">
                            👍 Нравится
                        </button>
                    </form>
                <?php else: ?>
                    <p style="color: var(--color-primary); font-weight: bold;">Вы уже отметили "Нравится"!</p>
                <?php endif; ?>
            <?php else: ?>
                <p>Для того, чтобы поставить отметку "Нравится", пожалуйста, <a href="<?php echo $base_path; ?>login.php">войдите в аккаунт</a>.</p>
            <?php endif; ?>
        </div>
        <a class="back-link" onclick="history.back()">← Atpakaļ</a>
    </div>
    </body>
    </html>

<?php
} else {
    // Если запись с таким ID не найдена — выводим сообщение
    echo "Ieraksts ar šo ID nav atrasts.";
}

// Закрываем подготовленные запросы и соединение
mysqli_stmt_close($stmt);
mysqli_close($savienojums);
?>
