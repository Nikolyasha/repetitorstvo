<? 
if(empty($_GET['page']) && empty($_GET['frame'])){
    header("Location: /");
    die();
}

session_start(); 
include("./core/db.php"); 
require_once("./models/user.php"); 

$page_link = htmlspecialchars(mysqli_real_escape_string($link, strtolower($_GET['page'])));
if(!empty($_GET['frame'])){
    $page_link = htmlspecialchars(mysqli_real_escape_string($link, strtolower($_GET['frame'])));
}

$current_balance = 0;
$page = Null;
$sql = "SELECT `name`, `html` FROM `site_pages` WHERE `link` = '$page_link';";
if($_SESSION['account_type'] > 0){
    $sql .= User::GetBalanceSQL($_SESSION['id']);
    list($page, $current_balance) = MultiQuery($link, $sql);
    $current_balance = $current_balance[0]['count'];
}
else{
    list($page) = MultiQuery($link, $sql);
}
if($page_link == "_preview" && $_SESSION['admin'] != 1 || empty($page)){
    if(!empty($_GET['frame'])){
        die();
    }
    header("Location: /");
    die();
}
$page = $page[0];

?>

<? if(!empty($_GET['frame'])) { ?>
<?= $page['html'] ?>
<? } else { ?> 
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width">
    <link rel="stylesheet" href="/css/page.css">
    <link rel="icon" href="./favicon.ico" type="image/x-icon">
    <title><?=$page['name']?> | <? echo($_SETTINGS['site_name_option']); ?></title>
    <script type="text/javascript" src="/bower_components/jquery/js/jquery.min.js"></script> 
    <script src="/js/main.js"></script>
    <script src="/js/favorite.js"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <style>
        :root {
            --main-site-color: <?=$_SETTINGS['primay_color_option']?>;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <? include("./views/header.php"); ?>
        <div class="text">
            <div class="container">
                <?= $page['html'] ?>
                <!-- <iframe src="<?=$_SERVER['REQUEST_URI']?>"></iframe> -->
            </div>
        </div>
        <? include("./views/footer.php"); ?>
    </div>
</body>
</html>
<? } ?>