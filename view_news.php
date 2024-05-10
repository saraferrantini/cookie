<?php
session_start();

$default_language = 'it';

if (isset($_COOKIE['language'])) {
    $current_language = $_COOKIE['language'];
} else {
    $current_language = $default_language;
}

if (isset($_POST['language'])) {
    $current_language = $_POST['language'];
    setcookie('language', $current_language, time() + (86400 * 30), "/");
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "language";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Connessione fallita: " . $e->getMessage());
}

function translate($text) {
    global $current_language;
    
    $translations = array(
        'it' => array(
            'welcome' => 'Benvenuto',
            'news' => 'Notizie',
        ),
        'en' => array(
            'welcome' => 'Welcome',
            'news' => 'News',
        )
    );

    return isset($translations[$current_language][$text]) ? $translations[$current_language][$text] : $text;
}

function getNews($language) {
    global $conn;
    
    $table_name = 'news_' . $language;
    
    $stmt = $conn->prepare("SELECT * FROM $table_name");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

?>

<!DOCTYPE html>
<html lang="<?php echo $current_language; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo translate('news'); ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }
        h1, h2, h3 {
            margin-top: 0;
        }
        form {
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 5px;
        }
        input[type="text"], textarea, select {
            width: 100%;
            margin-bottom: 10px;
        }
        input[type="submit"] {
            padding: 5px 10px;
            background-color: #007bff;
            color: #fff;
            border: none;
            cursor: pointer;
        }
        input[type="submit"]:hover {
            background-color: #0056b3;
        }
        ul {
            list-style-type: none;
            padding: 0;
        }
        li {
            margin-bottom: 20px;
        }
        a {
            color: #007bff;
            text-decoration: none;
        }
        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <h1><?php echo translate('welcome'); ?></h1>
    <h2><?php echo translate('news'); ?></h2>

    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
        <label for="language">Seleziona Lingua:</label>
        <select id="language" name="language">
            <option value="it" <?php if ($current_language == 'it') echo 'selected'; ?>>Italiano</option>
            <option value="en" <?php if ($current_language == 'en') echo 'selected'; ?>>English</option>
        </select>
        <input type="submit" value="Cambia Lingua">
    </form>
    
    <ul>
        <?php foreach(getNews($current_language) as $item): ?>
            <li>
                <h3><?php echo $item['title']; ?></h3>
                <p><?php echo $item['content']; ?></p>
            </li>
        <?php endforeach; ?>
    </ul>
</body>
</html>

<?php
$conn = null;
?>
