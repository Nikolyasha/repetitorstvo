<?
$anketsOrVacancies = 'ankets';
if(!isset($_GET['id'])){
    include("./search.php");
    die();
}
if($_GET['id'] == "favorite/" || $_GET['id'] == "favorite"){
    include("./favorite.php");
    die();
}
if((int) $_GET['id'] < 1){
    header("Location: /");
}
$user_id = (int) $_GET['id'];

session_start(); 
include("../core/db.php");
require_once($_SERVER['DOCUMENT_ROOT']."/models/user.php");
require_once($_SERVER['DOCUMENT_ROOT']."/models/vacancy.php");
require_once($_SERVER['DOCUMENT_ROOT']."/models/company.php");

list($user, $extra_fields) = User::GetUser($link, $user_id, $_SESSION['id']);
if(!$user || (int) $user['activation'] < 1){
    header("Location: /");
}

if(!isset($_COOKIE['cookie_dmlld19oaXN0b3J5'])){
    setcookie('cookie_dmlld19oaXN0b3J5', base64_encode(json_encode(["anket" => [$user['id']], "vacancy" => [], "company" => []])));
    User::CountView($link, (int) $user['id']);
} else {
    $view_history = json_decode(base64_decode($_COOKIE['cookie_dmlld19oaXN0b3J5']), true);
    if(!in_array((string) $user['id'], $view_history['anket'])){
        array_push($view_history['anket'], $user['id']);
        setcookie('cookie_dmlld19oaXN0b3J5', base64_encode(json_encode($view_history)));
        User::CountView($link, (int) $user['id']);
    }
}

$sql = Vacancy::GetTypesSQL().Company::GetCityListSQL();
if(isset($_SESSION['id'])){
    $sql .= User::GetBalanceSQL($_SESSION['id']);
    list($vacancy_types, $cities_arr, $current_balance) = MultiQuery($link, $sql);
    $current_balance = $current_balance[0]['count'];
}
else{
    list($vacancy_types, $cities_arr) = MultiQuery($link, $sql);
}
$cities = ["-"];
foreach($cities_arr as $city){
    array_push($cities, $city['name']);
}

?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width">
    <link rel="stylesheet" href="/css/user.css">
    <!-- for video -->
    <link href="https://vjs.zencdn.net/8.10.0/video-js.css" rel="stylesheet" />
    <title><? echo  $user['first_name']; ?>  <? if($_SESSION['account_type'] == 2){ echo $user['last_name']; } else { echo("***"); }?> на <? echo($_SETTINGS['site_name_option']); ?></title>
    <script type="text/javascript" src="/bower_components/jquery/js/jquery.min.js"></script> 
    <script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
    <script src="//malsup.github.io/min/jquery.form.min.js"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <style>
        :root {
            --main-site-color: <?=$_SETTINGS['primay_color_option']?>;
        }
        .mini_photo {
        width: 200px;
        height: 200px;
        border-radius: 5px;
        object-fit: cover;
        /* margin-top: 10px; */
        margin-right: 10px;
        }
        .modal_photo {
        width: 800px;
        height: 480px;
        border-radius: 5px;
        object-fit: cover;
        }
    </style>
</head>
<body>
    <div class="modal" id="popup">
        <div class="window">
            <div class="modal_header">
                <div class="modal_header__title">Фотографии анкеты</div>
                <div class="modal_header__close" onclick="hidePopup();"><i class="fas fa-times"></i></div>
            </div>
            <div class="modal_content">
                <div class="modal_content__wrapper">
                    <img src="/img/avatars/<? echo explode(",", $user['photos'])[0]; ?>" id="popup_img" onclick="nextPhoto();">
                </div>
                <p class="modal_nav">
                    <i class="fas fa-chevron-left" onclick="previousPhoto();"></i>
                    <span id="modal_photo_id">Фото * из *</span>
                    <i class="fas fa-chevron-right" onclick="nextPhoto();"></i>
                </p>
            </div>
        </div>
    </div>
    <!-- for filter photo -->
    <div class="modal" id="popupFilter">
        <div class="window">
            <div class="modal_header">
                <div id="txtHeader" class="modal_headertitle">Фотографии анкеты</div>
                <div class="modal_headerclose" onclick="hidePopup();"><i class="fas fa-times"></i></div>
            </div>
            <div class="modal_content">
                <div class="modal_content__wrapper">
                    <img class="modal_photo" src="" id="popup_imgFilter" onclick="nextPhotoFilter();">
                </div>
                <p class="modal_nav">
                    <i class="fas fa-chevron-left" onclick="previousPhotoFilter();"></i>
                    <span id="modal_photo_idFilter">Фото * из *</span>
                    <i class="fas fa-chevron-right" onclick="nextPhotoFilter();"></i>
                </p>
            </div>
        </div>
    </div>

    <div class="wrapper">
        <? include("../views/header.php"); ?>
        <? include("../views/search.php"); ?>
        <div class="container container-anket">
            <div class="item">
                <div class="title">
                <div class="title__body">
                        <div class="vakancy__title_item">
                            <h3 class="vakancy__vakancy">Анкета</h3>
                            <div class="underline">
                                <div class="main--line"></div>
                                <div class="small--line"></div>
                            </div>
                        </div>

                        <div class="title__item2">
                        <?php
                            if(((int) $user['last_online'] + (5 * 60)) > (int) time()){
                                echo('<a class="user_status-online online_status_active">Сейчас на сайте</a>');
                            } else {
                                echo('<a class="user_status-online online_status_disabled">в сети <strong>'.gmdate("d.m.Y в H:i", $user['last_online']).'</strong></a>');
                            }

                            $reg_date_diff = (new DateTime($user['reg_date']))->diff(new DateTime);
                            $days = $reg_date_diff->d;
                            $months = $reg_date_diff->m;
                            $years = $reg_date_diff->y;

                            function format_duration($value, $forms) {
                                if ($value == 0) {
                                    return "";
                                }

                                $n = $value % 100;
                                if ($n >= 11 && $n <= 19) {
                                    $form = $forms[2];
                                } else {
                                    $n = $value % 10;
                                    if ($n == 1) {
                                        $form = $forms[0];
                                    } elseif ($n >= 2 && $n <= 4) {
                                        $form = $forms[1];
                                    } else {
                                        $form = $forms[2];
                                    }
                                }

                                return "$value $form";
                            }

                            $days_form = ["день", "дня", "дней"];
                            $months_form = ["месяц", "месяца", "месяцев"];
                            $years_form = ["год", "года", "лет"];

                            $reg_date = trim(
                                format_duration($years, $years_form). " " .
                                format_duration($months, $months_form) . " " .
                                format_duration($days, $days_form) 
                            );

                            echo '<a class="user_status-online prof1">на платформе <strong>'. ($reg_date == "" ? " меньше дня" : " $reg_date") . '</strong></a>';

                            if ($_SESSION['admin']) {
                                echo '<a class="user_status-online edit_anket_button" href="/admin/edit_anket.php?id=' . $user['id'] . '">Редактировать анкету</a>';
                            }
                            ?>
                        </div>
                    </div>
                </div>
                <div class="block">
                    <div class="block__row">
                            <div class="port__row">
                                <div class="port__img">
                                    <? $i = 0;
                                    // die(var_dump($user['photos']));
                                    if(empty($user['photos'])){

                                        if ($user['sex'] == 1) 
                                            echo("<img class='vacancy__photo' src='/img/avatars/no_photo_female_anket.png' alt='Нет фото'>");  
                                        else                                                       
                                            echo("<img class='vacancy__photo' src='/img/avatars/no_photo_male_anket.png' alt='Нет фото'>");
                                    }
                                    else {
                                        foreach(explode(",", $user['photos']) as $photo) {
                                            echo("<img onclick='showPopup($i);' src='/img/avatars/$photo' alt='Фотография ".(explode(" ", $user['name'])[0])."'>");
                                            $i++;
                                        } 
                                    }?>
                                </div>
                                <div class="main-info">
                                    <h4 class="main-info_title">
                                        Основная
                                    </h4>
                                    <div class="main-info_content">
                                        <div class="main-info_content-left main-info_content_item">
                                            <p>город</p>
                                            <p>пол</p>
                                            <p>Возраст</p>
                                        </div>
                                        <div class="main-info_content-right main-info_content_item">
                                            <p>
                                                <? echo(((int) $user['city']) > count($cities) ? $cities[0] : $cities[(int) $user['city']]); ?> 
                                            </p>
                                            <p>
                                                <? echo(["Мужской", "Женский"][(int) $user['sex']]); ?> 
                                            </p>
                                            <p>
                                                <? echo($user['age']); ?>  лет
                                            </p>
                                        </div>
                                    </div>
                                    <h4 class="main-info_title main-info_title-contacts">
                                        Контакты
                                    </h4>
                                    <div class="contacts-info">
                                        <div class="info__contact_text">
                                            <? if(!isset($_SESSION['account_type'])){ ?> 
                                            <div class="contact__text_flex">
                                                <div class="info__contact_onetext">
                                                    Контактная информация в анкетах доступна только
                                                    зарегистрированным работодателям. <a href="/signup.php" class="registration">Зарегистрироваться</a>
                                                </div>
                                            </div>
                                            <? } else if($_SESSION['account_type'] < 2){ ?> 
                                            <div class="contact__text_flex">
                                                <img src="/img/lock.png" alt="">
                                                <div class="info__contact_onetext">
                                                    Контактная информация в анкетах доступна только работодателям.
                                                </div>
                                            </div> 
                                            <? } else if($user['purchased'] == 1 || $_SETTINGS['worker_contact_price'] == 0 || $_SESSION['id'] == $user['user_id']) { ?> 
                                            <div class="contact__text_flex">
                                                <div class="info__contact_type">
                                                    Телефон
                                                </div>
                                                <div class="info__contact_link">
                                                    <a href="tel:<? echo($user['phone']); ?>"><? echo($user['phone']); ?></a>
                                                </div>
                                            </div>
                                            <? if(!empty($user['viber'])) { ?>
                                                <div class="contact__text_flex">
                                                    <div class="info__contact_type">
                                                        Viber
                                                    </div>
                                                    <div class="info__contact_link">
                                                        <a tagret="_blank" href="viber://add?number=<? echo($user['viber']); ?>"><? echo($user['viber']); ?></a>
                                                    </div>
                                                </div>
                                            <? } ?>
                                            <? if(!empty($user['telegram'])) { ?>
                                                <div class="contact__text_flex">
                                                    <div class="info__contact_type">
                                                        Telegram
                                                    </div>
                                                    <div class="info__contact_link">
                                                        <a tagret="_blank" href="https://t.me/<? echo(str_replace("@", "", $user['telegram'])); ?>"><? echo($user['telegram']); ?></a>
                                                    </div>
                                                </div>
                                            <? } ?>
                                            <? if(!empty($user['whatsapp'])) { ?>
                                                <div class="contact__text_flex">
                                                    <div class="info__contact_type">
                                                        WhatsApp
                                                    </div>
                                                    <div class="info__contact_link">
                                                        <a tagret="_blank" href="https://wa.me/<? echo(str_replace("+", "", $user['whatsapp'])); ?>"><? echo($user['whatsapp']); ?></a>
                                                    </div>
                                                </div>
                                            <? } ?>
                                            <? } else { ?> 
                                            <div class="contact__text_flex">
                                                <img src="/img/lock.png" alt="">
                                                <div class="info__contact_onetext">
                                                    <a class="buy_contacts" onclick="buyUserContacts(<?=$user['id']?>);">Приобрести контакты работника</a>
                                                </div>
                                            </div>
                                            <? } ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="ads__row">
                                    <div class="ads" id="right_banner"></div>
                                </div>
                            </div>
                            <div class="info__row">
                                <div class="info__name">
                                    <h4>
                                        <? echo $user['first_name']; ?>  <? if($_SESSION['account_type'] == 2){ echo($user['last_name']); } else { echo("***"); }?> 
                                    </h4>
                                    <?php
                                        $status = (int) $user['status'];
                                        $class = $status == 1 ? "prof_status_active" : "prof_status_disabled";
                                        $text = $status == 1 ? "ищу работу" : "занят";
                                    ?>
                                    <a class="user_status <?php echo $class; ?>"><?php echo $text; ?></a>
                                </div>
                                <div class="line"></div>
                                <div class="info__work">
                                    <div class="info__title">
                                        интересующая работа
                                    </div>
                                    <div class="info__job">
                                        <table>
                                            <tr>
                                                <td>
                                                    Ожидаемая ставка
                                                </td>
                                                <td>
                                                    <? echo($user['min_salary']); ?> тг./час.
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    Профессия
                                                </td>
                                                <td>
                                                    <? 
                                                        $prefer_vacancy_types = [];
                                                        $str = "";
                                                        foreach(explode(",", $user['job_types']) as $pefer_vacancy){
                                                            foreach($vacancy_types as $vacancy_type){
                                                                if((int) $pefer_vacancy == (int) $vacancy_type['id']){
                                                                    array_push($prefer_vacancy_types, $vacancy_type['vacancy_type_name']);
                                                                    $str .= ''.$vacancy_type['vacancy_type_name'].'; ';
                                                                }
                                                            }
                                                        }
                                                        echo $str; 
                                                    ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    Удобное время
                                                </td>
                                                <td>
                                                    <? echo(str_replace("-", " - ", $user['time_range'])); ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    Удобные дни
                                                </td>
                                                <td>
                                                    <? 
                                                        $days = [1 => "Понедельник", 2 => "Вторник", 3 => "Среда", 4 => "Четверг", 5 => "Пятница", 6 => "Суббота", 7 => "Воскресенье",];
                                                        $str = "";
                                                        foreach(explode(",", $user['week_days']) as $job_day){
                                                            $str .= ''.$days[$job_day].'; ';
                                                        }
                                                        echo $str;
                                                    ?>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                                <div class="info__xp">
                                    <div class="info__title">
                                        Подробнее о себе
                                    </div>
                                    <? if(strlen($user['about']) > 0) { ?>
                                    <div class="info__true">
                                        <? echo($user['about']); ?>
                                    </div>
                                    <? } else { ?>
                                    <div class="info__true appearance__two_false">
                                        не указано
                                    </div>
                                    <? } ?>
                                </div>
                                <div class="info__glav">
                                    <div class="info__title">
                                        основная информация
                                    </div>
                                    <div class="info__info">
                                        <div class="info__block">
                                            <div class="info__world">
                                                город
                                            </div>
                                            <div class="info__city">
                                                <? echo(((int) $user['city']) > count($cities) ? $cities[0] : $cities[(int) $user['city']]); ?> 
                                            </div>
                                        </div>
                                        <div class="info__block info__block1">
                                            <div class="info__world">
                                                пол
                                            </div>
                                            <div class="info__city">
                                                <? echo(["Мужской", "Женский"][(int) $user['sex']]); ?> 
                                            </div>
                                        </div>
                                        <div class="info__block info__block1">
                                            <div class="info__world">
                                                Возраст
                                            </div>
                                            <div class="info__city">
                                                <? echo($user['age']); ?>  лет
                                            </div>
                                        </div>
                                        <?
                                        if(strlen($user['extra_fields']) > 0 && $user['extra_fields'] != "null"){
                                            foreach(json_decode($user['extra_fields'], true) as $field){
                                                foreach($extra_fields as $extra_field){
                                                    if($extra_field['name'] == $field['name']){ ?>
                                                        <? if ($extra_field['type'] == 0 && count(explode(';', $extra_field['options'])) > 2)  { 
                                                            $str = "";?>                                                        
                                                            <?foreach(explode(";", $extra_field['options']) as $option) {
                                                                if ($field[$option] == 1) {
                                                                    $str .= ''.$option.'; ';
                                                                }
                                                            }?>
                                                                <div class="info__block info__block1">
                                                                    <div class="info__world">
                                                                        <? echo $extra_field['display']; ?>
                                                                    </div>
                                                                    <div class="info__city">
                                                                        <? echo $str; ?>
                                                                    </div>
                                                                </div>
                                                                <?} else if ($extra_field['type'] == 3) {?>
                                                                    <div class="info__block info__block1">
                                                                        <div class="info__world">
                                                                            <? echo $extra_field['display']; ?>
                                                                        </div>
                                                                        <div class="info__city">
                                                                            <div style="display: flex;">                                                                
                                                                            <?$ph =  $field['value'];  $i = 0; foreach( explode(",", $field['value']) as $val) {
                                                                                if (strripos($val, "/") == true){?>

                                                                                <video id="my-video" width="600" controls class="video-js" data-setup='
                                                                                {                                                                
                                                                                    "techOrder": ["youtube"],
                                                                                    "sources": [{
                                                                                        "type": "video/youtube",
                                                                                        "src": "<?echo $val?>"
                                                                                        }]
                                                                                }
                                                                                '>                                                                
                                                                                </video>
                                                                                    
                                                                                <? continue;
                                                                                }
                                                                                // echo $val; ?>
                                                                                <img onclick='showPopupFilter(<?echo $i?>, "<?echo $extra_field["display"];?>", "<?=$ph?>".split(",")); photosFilter = "<?=$ph?>".split(",");' src="/img/filter_photos/<? echo $val; ?>" class="mini_photo">
                                                                            <?$i++;} ?>
                                                                            
                                                                        
                                                                            <? echo explode(";", $extra_field['options'])[$field['value']]; ?>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                        <?} else {?>
                                                        
                                                        <div class="info__block info__block1">
                                                            <div class="info__world">
                                                                <? echo $extra_field['display']; ?>
                                                            </div>
                                                            <div class="info__city">
                                                                <? echo explode(";", $extra_field['options'])[$field['value']]; ?>
                                                            </div>
                                                        </div>
                                                        <?}
                                                    }
                                                }
                                            }
                                        }
                                        ?>
                                    </div>
                                </div>
                                <div class="info__contact" id="contacts">
                                    <div class="info__title">
                                        контактная информация
                                    </div>
                                </div>
                                <div class="info__xp">
                                    <div class="info__title">
                                    опыт работы
                                    </div>
                                    <div class="xp__text">
                                        <? if(strlen($user['experience']) > 0) { ?>
                                        <div class="xp__text_true">
                                            <? echo($user['experience']); ?>
                                        </div>
                                        <? } else { ?>
                                        <div class="info__true xp__text_false">
                                            не указан
                                        </div>
                                        <? } ?>
                                    </div>
                                </div>
                                <div class="info__self">
                                    <div class="info__hobby">
                                        <div class="info__title">
                                            внешние параметры
                                        </div>
                                        <? if(strlen($user['view']) > 0) { ?>
                                        <div class="info__true">
                                            <? echo($user['view']); ?>
                                        </div>
                                        <? } else { ?>
                                        <div class="info__true appearance__two_false">
                                            не указаны
                                        </div>
                                        <? } ?>
                                    </div>
                                    <div class="info__hobby_xp">
                                        <div class="info__title">
                                            особые навыки
                                        </div>
                                        <? if(!empty($user['special'])) { ?>
                                            <div class="info__true appearance__two_true">
                                                <? echo($user['special']); ?>
                                            </div>
                                        <? } else { ?>
                                            <div class="info__true appearance__two_false">
                                                не указаны
                                            </div>
                                        <? } ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                </div>
            </div>
        </div>

        <? include("../views/footer.php"); ?>
        <script type="text/javascript" src="/js/user.js"></script> 
        <!-- for video -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/video.js/8.15.0/video.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/videojs-youtube/3.0.1/Youtube.min.js"></script>
        <script src="https://unpkg.com/youtube-video-id@latest/dist/youtube-video-id.min.js"></script>
        <script type="text/javascript">
            let photos = "<? echo str_replace(" ", "", $user['photos']); ?>".split(",");
            
            let vacancy_price = <? if ($_SETTINGS['payment_active_option'] == 'false') 
                                       echo 0;
                                    else
                                        echo $_SETTINGS['worker_contact_price']; ?>
            
            
            let current_balance = '<?=$current_balance?>';
            let payment_active = '<?= $_SETTINGS['payment_active_option']?>';
        </script>
    </div>
    <div class="title__item1">
                                <div class="title__text1">
                                    Анкета 
                                </div>
                                <div class="title__text2">
                                    <? echo $user['first_name']; ?>  <? if($_SESSION['account_type'] == 2){ echo($user['last_name']); } else { echo("***"); }?> 
                                </div>
                            </div>
                                                        
</body>
</html>