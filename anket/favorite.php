<?
session_start(); 
include("../core/db.php");
require_once($_SERVER['DOCUMENT_ROOT']."/models/vacancy.php");
require_once($_SERVER['DOCUMENT_ROOT']."/models/company.php");
require_once($_SERVER['DOCUMENT_ROOT']."/models/user.php");

if($_SESSION['account_type'] != 2){
    header("Location: /anket/"); die();
}
$sql = Vacancy::GetTypesSQL().Company::GetCityListSQL().Company::GetFavoriteListSQL($_SESSION['id']).User::GetBalanceSQL($_SESSION['id']);
list($vacancy_types, $cities, $favorites, $current_balance) = MultiQuery($link, $sql);
$favorites = json_decode($favorites[0]['favorite']);
$current_balance = $current_balance[0]['count'];

$offset = ((int) $_GET['page']) * 10;
if($offset == "0") $offset = "";
else $offset = ",".$offset;

$order_by = "`id` DESC";
if(!empty($_GET['order'])){
    switch($_GET['order']){
        case "old":
            $order_by = "`id` ASC"; break;
        default:
            $order_by = "`id` DESC"; break;
    }
}

list($ankets, $anket_count) = MultiQuery($link, Vacancy::GetAnketFavoriteListSQL($order_by, $favorites, $offset));
$anket_count = $anket_count[0]['ankets_count'];
// echo 'AAAAAAAAAA, '.gettype($ankets).''; 
?>
<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width">
    <link rel="stylesheet" href="/css/ankets.css">
    <title>Отобарнные анкеты на 
        <? echo($_SETTINGS['site_name_option']); ?>
    </title>
    <script type="text/javascript" src="/bower_components/jquery/js/jquery.min.js"></script>
    <script src="/js/favorite.js"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <svg class="inline-svg">
        <symbol id="check" viewbox="0 0 12 10">
            <polyline points="1.5 6 4.5 9 10.5 1"></polyline>
        </symbol>
    </svg>
    <style>
        :root {
            --main-site-color: <?=$_SETTINGS['primay_color_option']?>;
        }
    </style>
</head>

<body>
    <div class="wrapper">
        <? include("../views/header.php"); ?>
        <div class="item">
            <div class="vakancy__title">
                <div class="container">
                    <div class="vakancy__row__title">
                        <div class="vakancy__main">
                            <div class="vakancy__public">
                                Найдено <? echo $anket_count;
                                switch($anket_count){
                                    case 1: echo(" анкета"); break;
                                    case 2: case 3: case 4:  echo(" анкеты"); break;
                                    default: echo(" анкет"); break;
                                } ?> 
                            </div>
                         </div>
                        <div class="vakancy__main">
                            <div class="vakancy__sorting">
                                сортировка: <a onclick="changeSort('<?= $_GET['order'] == "old" ? "new" : "old" ?>');"><?= $_GET['order'] == "old" ? "Сначала старые" : "Сначала новые" ?></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="vacancy__body">
                <div class="container">
                    <div class="vacancy__row">
                        <div class="text__left">
                            <?
                            if( is_null($ankets) ){ 
                            // if(count($ankets) == 0){ ?>
                                <div class="vacancy__block no_vacancies">
                                    Список отобранных акнет пуст<br>Добавляйте сюда понравившиеся резюме нажатием звездочки
                                </div>
                            <? }
                            else {
                                foreach($ankets as $anket){ ?>
                                    <div class="vacancy__block">
                                        <div class="vacancy__block_left">
                                            <a href="/anket/<?=  $anket['id'] ?>">
                                            <?
                                            if(empty($anket['photos'])){

                                                if ($anket['sex'] == 1) 
                                                    echo("<img class='vacancy__photo' src='/img/avatars/no_photo_female.png' alt='Нет фото'>");  
                                                else                                                       
                                                    echo("<img class='vacancy__photo' src='/img/avatars/no_photo_male.png' alt='Нет фото'>");
                                                }
                                                else {                                                                                                           
                                                    echo(" <img src='/img/avatars/".explode(",", $anket['photos'])[0]."' alt='' class='vacancy__photo'> ");                                                     
                                                }?>
                                            <!-- <img src="/img/avatars/<?= explode(",", $anket['photos'])[0] ?>" alt="" class="vacancy__photo"> -->
                                        </a>
                                        </div>
                                        <div class="vacancy__block_right">
                                            <div class="vacancy__block_right_title">
                                                <a href="/anket/<?=  $anket['user_id'] ?>" class="vacancy__block_right_title_title">
                                                    <?= $anket['first_name'] ?> <?= $_SESSION['account_type'] == 2 ? $anket['last_name'] : "***" ?>, <?= $anket['age'] ?>
                                                </a>
                                                <div class="icons__right">
                                                    <span class="calm"></span>
                                                    <?= ($_SESSION['account_type'] == 2 ? '<div onclick="setFavoriteAnket(this, '.$anket['id'].')" class="'.($favorites != null ? (in_array(strval($anket['id']), $favorites) ? "active_favorite" : "unactive_favorite") : "unactive_favorite").'"></div>' : '') ?>
                                                    <?= (!isset($_SESSION['id']) ? '<div onclick="window.location = \'/login.php\'" class="'.$anket['id'].')" class="'.($favorites != null ? (in_array(strval($anket['id']), $favorites) ? "active_favorite" : "unactive_favorite") : "unactive_favorite").'"></div>' : '') ?>
                                                </div>
                                            </div>
                                            <div class="vacancy__block_city">
                                                <div class="vacancy__block_city"><? echo(((int) $anket['city']) > count($cities) ? "-" : $cities[(int) $anket['city'] - 1]['name']); ?> </div>
                                            </div>
                                            <div class="vacancy__block_model">
                                                <?
                                                    $anket_vacancy_types = [];
                                                    foreach(explode(",", $anket['job_types']) as $pefer_vacancy){
                                                        foreach($vacancy_types as $vacancy_type){
                                                            if((int) $pefer_vacancy == (int) $vacancy_type['id']){
                                                                array_push($anket_vacancy_types, $vacancy_type['vacancy_type_name']);
                                                            }
                                                        }
                                                    }
                                                    echo(implode(", ", $anket_vacancy_types));
                                                ?>
                                            </div>
                                            <div class="vacancy__block_button_and_photo">
                                                <? 
                                                    if(((int) $anket['last_online'] + (5 * 60))  > (int) time()){
                                                        echo('<div class="vacancy__block_button online_status_active"><a class="user_status">Сейчас на сайте</a></div>');
                                                    } else {
                                                        echo('<div class="vacancy__block_button online_status_disabled"><a class="user_status">Был(а) на сайте '.gmdate("d.m.Y в H:i", $anket['last_online']).'</a></div>');
                                                    }
                                                    $reg_date = explode(":", (new DateTime($anket['reg_date']))->diff(new DateTime)->format(' %d дней: %m месяцев: %y лет'));
                                                    $reg_date = ($reg_date[0] != " 0 дней" ? $reg_date[0] : "").($reg_date[1] != " 0 месяцев" ? $reg_date[1] : "").($reg_date[2] != " 0 лет" ? $reg_date[2] : "");
                                                ?>
                                                <div class="vacancy__block_photo">
                                                    <a><?= empty($anket['photos']) ? "0" : count(explode(",", $anket['photos'])) ?> фото</a>
                                                </div>
                                            </div>
                                            <div class="vacancy__working">
                                                <? if(!isset($_SESSION['account_type'])){ ?> 
                                                <img src="/img/lock.png" alt="">
                                                <div class="contact__working">
                                                    Контактная информация в доступна только
                                                    работодателям. <a href="/signup.php" class="registration">Зарегистрироваться</a>
                                                </div>
                                                <? } else if($_SESSION['account_type'] < 2){ ?> 
                                                <img src="/img/lock.png" alt="">
                                                <div class="contact__working">
                                                    Контактная информация в анкетах доступна только работодателям.
                                                </div>
                                                <? } else if($_SESSION['account_type'] == 2) { ?>
                                                <div class="info__world">
                                                    <div class="contact__working">
                                                        <a href="/anket/<?=  $anket['user_id'] ?>#contacts" target="_blank">Смотреть анкету</a>
                                                    </div>
                                                </div>
                                                <? } ?>
                                            </div>
                                        </div>
                                    </div>
                                <? } ?>
                            <? } ?>
                            <div class="page_counter_wrapper">
                                <div class="page_counter">
                                    <?
                                        $page_count = ceil($anket_count / 10);
                                        // $page_count = 20;
                                        $current_page = 0;
                                        if(isset($_GET['page']) && (int) $_GET['page'] > 0)
                                            $current_page = (int) $_GET['page'];
                                        if($current_page > $page_count){
                                            // echo("<script>$('#filter_form')[0].page.value=0;$('#filter_form')[0].submit();</script>");
                                        }
                                        if($current_page < 4){
                                            for($i = 0; $i < ($page_count > 6 ? 8 : $page_count); $i++){
                                                if($i == 5){
                                                    echo('<div onclick="select_page(-1);" class="page_counter__item">...</div>');
                                                    echo('<div onclick="select_page('.($page_count-1).');" class="page_counter__item">'.$page_count.'</div>');
                                                    break;  
                                                }
                                                if($current_page > 5){
                                                    echo('<div onclick="select_page('.($i == 0 ? "" : ($current_page+$i-1)).');" class="page_counter__item '.($i == 0 ? "selected" : "").'">'.($current_page+$i).'</div>');
                                                }
                                                else{
                                                    echo('<div onclick="select_page('.($i == $current_page ? "" : $i).');" class="page_counter__item '.($i == $current_page ? "selected" : "").'">'.($i+1).'</div>');
                                                }
                                            }
                                        }
                                        else if($current_page < ($page_count-4)){
                                            echo('<div onclick="select_page(0);" class="page_counter__item">1</div>');
                                            echo('<div onclick="select_page(-1);" class="page_counter__item">...</div>');
                                            for($i = 0; $i < ($page_count > 6 ? 8 : $page_count); $i++){
                                                if($i == 5){
                                                    echo('<div onclick="select_page(-1);" class="page_counter__item">...</div>');
                                                    echo('<div onclick="select_page('.($page_count-1).');" class="page_counter__item">'.$page_count.'</div>');
                                                    break;  
                                                }
                                                echo('<div onclick="select_page('.(($current_page+$i-2) == $current_page ? "" : ($current_page+$i-2)).');" class="page_counter__item '.(($current_page+$i-2) == $current_page ? "selected" : "").'">'.($current_page+$i-1).'</div>');
                                            }
                                        }
                                        else{
                                            echo('<div onclick="select_page(0);" class="page_counter__item">1</div>');
                                            echo('<div onclick="select_page(-1);" class="page_counter__item">...</div>');
                                            for($i = 0; $i < 5; $i++){
                                                echo('<div onclick="select_page('.(($page_count-5)+$i == $current_page ? "" : (($page_count-5)+$i)).');" class="page_counter__item '.(($page_count-5)+$i == $current_page ? "selected" : "").'">'.(($page_count-4)+$i).'</div>');
                                            }
                                        }
                                    ?>
                                </div>
                            </div>
                        </div>
                        <div class="text__right">
                            <div class="ads" id="right_banner"></div>
                            <div class="search__vacancies" style="margin-top: 0px;">
                                <div class="search__vacancies_title">
                                    <a href="/vacancy/">поиск вакансий</a>
                                </div>
                                <ul class="search__list">
                                    <?
                                        foreach($vacancy_types as $item){
                                            echo('<li><a href="/vacancy?vacancy_types[]='.$item['id'].'">'.$item['vacancy_type_name'].'</a></li>');
                                        }
                                    ?>
                                </ul>
                            </div>
                        </div>
                        <form action="" method="GET" id="filter_form">
                            <input type="hidden" name="order" value="<?= $_GET['order'] == "old" ? "old" : "new" ?>"/>
                            <input type="hidden" name="page" value="<?= (int) $_GET['page'] > 0 ? (int) $_GET['page'] : 0 ?>"/>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <? include("../views/footer.php"); ?>
        <script type="text/javascript" src="/js/search.js"></script>
    </div>
</body>

</html>