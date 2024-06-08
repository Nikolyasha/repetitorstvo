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
    <div class="wrapper">
        <? include("../views/header.php"); ?>
        <? include("../views/search.php"); ?>
        <div class="item">
            <div class="title">
                <div class="container">
                    <div class="title__body">
                        <div class="title__item1">
                            <div class="title__text1">
                                Анкета №<? echo($user['id']); ?> 
                            </div>
                            <div class="title__text2">
                                <? echo $user['first_name']; ?>  <? if($_SESSION['account_type'] == 2){ echo($user['last_name']); } else { echo("***"); }?> 
                            </div>
                        </div>
                        <div class="title__item2">
                            <a class="user_status<? echo((int) $user['status'] == 1 ? " prof_status_active\">ищу работу" : " prof_status_disabled\">занят"); ?></a>
                            <? 
                                if(((int) $user['last_online'] + (5 * 60))  > (int) time()){
                                    echo('<a class="user_status online_status_active">Сейчас на сайте</a>');
                                } else {
                                    echo('<a class="user_status online_status_disabled">Был(а) на сайте '.gmdate("d.m.Y в H:i", $user['last_online']).'</a>');
                                }
                                $reg_date = explode(":", (new DateTime($user['reg_date']))->diff(new DateTime)->format(' %d дней: %m месяцев: %y лет'));
                                $reg_date = ($reg_date[0] != " 0 дней" ? $reg_date[0] : "").($reg_date[1] != " 0 месяцев" ? $reg_date[1] : "").($reg_date[2] != " 0 лет" ? $reg_date[2] : "");
                            ?>
                            <a class="user_status prof1">в промобанке<? echo($reg_date == "" ? " меньше дня" : $reg_date); ?></a>
                            <? if($_SESSION['admin']) { ?> <a class="user_status edit_anket_button" href="/admin/edit_anket.php?id=<? echo($user['id']); ?>">Редактировать анкету</a><? } ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="block">
                <div class="container">
                    <div class="block__row">
                        <div class="port__row">
                            <div class="port__title">
                                портфолио
                            </div>
                            <div class="port__img">
                                <? $i = 0;
                                // die(var_dump($user['photos']));
                                if(empty($user['photos'])){

                                    if ($user['sex'] == 1) 
                                        echo("<img class='vacancy__photo' src='/img/avatars/no_photo_female.png' alt='Нет фото'>");  
                                    else                                                       
                                        echo("<img class='vacancy__photo' src='/img/avatars/no_photo_male.png' alt='Нет фото'>");
                                }
                                else {
                                    foreach(explode(",", $user['photos']) as $photo) {
                                        echo("<img onclick='showPopup($i);' src='/img/avatars/$photo' alt='Фотография ".(explode(" ", $user['name'])[0])."'>");
                                        $i++;
                                    } 
                                }?>
                            </div>
                        </div>
                        <div class="info__row">
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
                                <div class="info__contact_text">
                                    <? if(!isset($_SESSION['account_type'])){ ?> 
                                    <div class="contact__text_flex">
                                        <img src="/img/lock.png" alt="">
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
                            <div class="info__work">
                                <div class="info__title">
                                    интересующая работа
                                </div>
                                <div class="info__job">
                                    <div class="job__title">
                                        Ожидаемая ставка от <? echo($user['min_salary']); ?> тг./час.
                                    </div>
                                    <div class="job_info">
                                        <div class="info__block info__block1">
                                            <div class="info__world">
                                                Профессия
                                            </div>
                                            <div class="info__city info__city_what">
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
                                                // echo(implode($prefer_vacancy_types, ","));                                                
                                                ?>
                                            </div>
                                        </div>
                                        <div class="info__block info__block1">
                                            <div class="info__world">
                                                Удобное время
                                            </div>
                                            <div class="info__city info__city_what">
                                                <? echo(str_replace("-", " - ", $user['time_range'])); ?>
                                            </div>
                                        </div>
                                        <div class="info__block info__block1">
                                            <div class="info__world">
                                                Удобные дни
                                            </div>
                                            <div class="info__city info__city_what">
                                            <? 
                                                $days = [1 => "Понедельник", 2 => "Вторник", 3 => "Среда", 4 => "Четверг", 5 => "Пятница", 6 => "Суббота", 7 => "Воскресенье",];
                                                $str = "";
                                                foreach(explode(",", $user['week_days']) as $job_day){
                                                    $str .= ''.$days[$job_day].'; ';
                                                //     foreach($vacancy_types as $vacancy_type){
                                                //         if((int) $pefer_vacancy == (int) $vacancy_type['id']){
                                                //             array_push($prefer_vacancy_types, $vacancy_type['vacancy_type_name']);
                                                //             $str .= ''.$vacancy_type['vacancy_type_name'].'; ';
                                                //         }
                                                //     }
                                                }
                                                echo $str;
                                                //echo $days[1]; 
                                                // echo(implode($prefer_vacancy_types, ","));                                                
                                                ?>
                                            </div>
                                        </div>
                                    </div>
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
                        <div class="ads__row">
                            <div class="ads" id="right_banner"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <? include("../views/footer.php"); ?>
        <script type="text/javascript" src="/js/user.js"></script> 
        
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
</body>
</html>