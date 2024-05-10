<?php
session_start();

// Funzione per impostare la lingua dei cookie
function setLanguageCookie($language) {
    setcookie('language', $language, time() + (86400 * 30), "/"); // Cookie valido per 30 giorni
}

// Funzione per ottenere la lingua corrente dai cookie o impostare la lingua predefinita
function getCurrentLanguage() {
    $default_language = 'it';
    return isset($_COOKIE['language']) ? $_COOKIE['language'] : $default_language;
}

// Imposta la lingua corrente
$current_language = getCurrentLanguage();

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "language";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    // Setta PDO in modalità di errore eccezioni
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Connessione fallita: " . $e->getMessage());
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if(isset($_POST['title_it']) && isset($_POST['content_it'])) {
        $title_it = $_POST['title_it'];
        $content_it = $_POST['content_it'];
        
        $sql = "INSERT INTO news_it (title, content) VALUES (:title, :content)";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':title', $title_it);
        $stmt->bindParam(':content', $content_it);
        
        if($stmt->execute()) {
            echo "Notizia in italiano inserita con successo.";
        } else {
            echo "Errore durante l'inserimento della notizia in italiano.";
        }
    }
    
    if(isset($_POST['title_en']) && isset($_POST['content_en'])) {
        $title_en = $_POST['title_en'];
        $content_en = $_POST['content_en'];
        
        $sql = "INSERT INTO news_en (title, content) VALUES (:title, :content)";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':title', $title_en);
        $stmt->bindParam(':content', $content_en);
        
        if($stmt->execute()) {
            echo "News in English inserted successfully.";
        } else {
            echo "Error inserting news in English.";
        }
    }
}

function translate($text) {
    global $current_language;
    
    $translations = array(
        'it' => array(
            'welcome' => 'Benvenuto',
            'news' => 'Notizie',
            'read_more' => 'Leggi di più',
            'insert_news' => 'Inserisci Notizia',
            'view_news' => 'Visualizza Notizie'
        ),
        'en' => array(
            'welcome' => 'Welcome',
            'news' => 'News',
            'read_more' => 'Read More',
            'insert_news' => 'Insert News',
            'view_news' => 'View News'
        )
    );

    return isset($translations[$current_language][$text]) ? $translations[$current_language][$text] : $text;
}

?>

<!DOCTYPE html>
<html lang="<?php echo $current_language; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo translate('news'); ?></title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container">
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo translate('news'); ?> - <?php echo translate('it'); ?></h5>
                        <?php renderNewsForm('it'); ?>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo translate('news'); ?> - <?php echo translate('en'); ?></h5>
                        <?php renderNewsForm('en'); ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="row mt-3">
            <div class="col-md-12 text-center">
                <a href="view_news.php" class="btn btn-primary"><?php echo translate('view_news'); ?></a>
            </div>
        </div>
    </div>

</body>
</html>

<?php
$conn = null;

function renderNewsForm($language) {
    $language_prefix = ($language == 'it') ? '_it' : '_en';
    $title_label = ($language == 'it') ? 'Titolo (Italiano):' : 'Title (English):';
    $content_label = ($language == 'it') ? 'Contenuto (Italiano):' : 'Content (English):';
    $submit_text = ($language == 'it') ? translate('insert_news') . ' (' . translate('it') . ')' : translate('insert_news') . ' (' . translate('en') . ')';
    ?>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
        <label for="title<?php echo $language_prefix; ?>"><?php echo $title_label; ?></label><br>
        <input type="text" id="title<?php echo $language_prefix; ?>" name="title<?php echo $language_prefix; ?>"><br>
        <label for="content<?php echo $language_prefix; ?>"><?php echo $content_label; ?></label><br>
        <textarea id="content<?php echo $language_prefix; ?>" name="content<?php echo $language_prefix; ?>"></textarea><br>
        <button type="submit" class="btn btn-primary"><?php echo $submit_text; ?></button>
    </form>
<?php } ?>
