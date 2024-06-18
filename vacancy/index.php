<?
$anketsOrVacancies = 'vacancies';
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
$vacancy_id = (int) $_GET['id'];

$payout_types = Array("Каждый день", "Каждую неделю", "Каждый месяц");
$job_type = Array("Постоянная", "Временная", "Единоразовая");

session_start(); 
include("../core/db.php");
require_once($_SERVER['DOCUMENT_ROOT']."/models/vacancy.php");
require_once($_SERVER['DOCUMENT_ROOT']."/models/user.php");

$result = Vacancy::GetVacancy($link, $vacancy_id);

if($result['vacancy'] === false || $result['company'] === false){
    header("Location: /vacancy/");
    die();
}

$vacancy = $result['vacancy'][0];
$company = $result['company'][0];
$filters = json_encode($result['filters']);

if(!isset($_COOKIE['cookie_dmlld19oaXN0b3J5'])){
    setcookie('cookie_dmlld19oaXN0b3J5', base64_encode(json_encode(["anket" => [], "vacancy" => [$vacancy['id']], "company" => []])));
    Vacancy::CountView($link, (int) $vacancy['id']);
} else {
    $view_history = json_decode(base64_decode($_COOKIE['cookie_dmlld19oaXN0b3J5']), true);
    if(!in_array((string) $vacancy['id'], $view_history['vacancy'])){
        array_push($view_history['vacancy'], $vacancy['id']);
        setcookie('cookie_dmlld19oaXN0b3J5', base64_encode(json_encode($view_history)));
        Vacancy::CountView($link, (int) $vacancy['id']);
    }
}

$salary = $vacancy['salary_per_hour']." тг./час";
if($vacancy['salary_per_hour'] == 0){
    if($vacancy['salary_per_day'] == 0){
        $salary = $vacancy['salary_per_month']." тг./месяц";
    }
    else{
        $salary = $vacancy['salary_per_day']." тг./день";
    }
}
if($vacancy['time_type'] == 0){
    $employment_days = $vacancy['days'];
    if($employment_days == "1,2,3,4,5,6,7"){
        $employment_days = ": каждый день";
    }
    else if($employment_days == "1,2,3,4,5"){
        $employment_days = ": в будние дни";
    }
    else if($employment_days == "6,7"){
        $employment_days = ": на выходных";
    }
    else{
        $employment_days = ":".str_replace(
            Array('1', '2', '3', '4', '5', '6', '7'),
            Array(' Пн', ' Вт', ' Ср', ' Чт', ' Пт', ' Сб', ' Вс'),
            $vacancy['days']
        );
    }
}
else if($vacancy['time_type'] == 1){
    $dates = explode(":", $vacancy['days']);
    $dates[0] = date_parse($dates[0]);
    $dates[0] = (strlen($dates[0]['day']) == 1 ? "0".$dates[0]['day'] : $dates[0]['day']).' '.$MONTHES[$dates[0]['month']-1];
    $dates[1] = date_parse($dates[1]);
    $dates[1] = (strlen($dates[1]['day']) == 1 ? "0".$dates[1]['day'] : $dates[1]['day']).' '.$MONTHES[$dates[1]['month']-1];
    $employment_days = ": с ".$dates[0]." по ".$dates[1];
}
else{
    $employment_days = ": ".$vacancy['days'];
}
$post_date = date_parse($vacancy['public_date']);

$sql = Vacancy::GetTypesSQL();
if(isset($_SESSION['id'])){
    $sql .= User::GetBalanceSQL($_SESSION['id']);
    list($vacancy_types, $current_balance) = MultiQuery($link, $sql);
    $current_balance = $current_balance[0]['count'];
}
else{
    list($vacancy_types) = MultiQuery($link, $sql);
}

function echo_br($text){
    echo(str_replace(array("\r\n", "\r", "\n"), '<br>', $text));
}

?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width">
    <link rel="stylesheet" href="/css/vacancy.css">
    <!-- for video -->
    <link href="https://vjs.zencdn.net/8.10.0/video-js.css" rel="stylesheet" />
    <title>Вакансия <? echo($vacancy['name'] . " " . $_SETTINGS['site_name_option']); ?></title>
    <script type="text/javascript" src="/bower_components/jquery/js/jquery.min.js"></script>
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
        <? include("../views/header.php"); ?>
        <? include("../views/search.php"); ?>
        <div class="container">
            <div class="vakancy__title">
                <div class="vakancy__title__item">
                    <div class="vakancy__title_item">
                        <h3 class="vakancy__vakancy">Вакансия</h3>
                        <div class="underline">
                            <div class="main--line"></div>
                            <div class="small--line"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="vacancy--name">
            <div class="container container--vacancy__name">
                <p class="vakancy__prom"><? echo($vacancy['name'].", ".$salary); ?></p>
                <a class="company" href="/company/<? echo($company['id']); ?>">
                    <h4 class="name__company"><? echo($company['company_name']); ?></h4>
                    <span class="type__company"><? echo($company['company_type']); ?></span>
                </a>
            </div>
        </div>
        <div class="container text__container">
            <div class="text">
                <div class="text__left">
                    <div class="text_title">
                        <div class="text__title">
                            <? echo($vacancy['desc_min']); ?>
                        </div>
                        <p class="vacant__place">
                            <? echo($vacancy['workplace_count']); 
                            echo((int) $vacancy['workplace_count'] == 1 ? " вакантное" : " вакантных");
                            echo(
                                (int) $vacancy['workplace_count'] == 1 ? " место" :
                                ((int) $vacancy['workplace_count'] < 5 ? " места" : " мест")
                            );?>
                        </p>
                        <div class="text__flex">
                            <div class="text__flex1">
                                <p class="city">Город</p>
                                <p class="theme item--block">Тематика</p>
                                <p class="schedule item--block">
                                    <? echo($job_type[$vacancy['time_type']]); ?> работа 
                                </p>
                                <p class="time item--block">часы работы</p>
                            </div>
                            <div class="text__flex2">
                                <p class="city--info"><? echo($vacancy['city']); ?></p>
                                <p class="theme--info info--block"><? if ($vacancy['type_id'] == 105) echo($vacancy['type_another']); else echo($vacancy['type']); ?></p>
                                <p class="schedule--info info--block">
                                    <?
                                        switch ($vacancy['time_type']) {
                                            case 1:
                                                $dates = explode(":", $item['days']);
                                                $dates[0] = date_parse($dates[0]);
                                                $dates[0] = (strlen($dates[0]['day']) == 1 ? "0".$dates[0]['day'] : $dates[0]['day']).' '.$MONTHES[$dates[0]['month']-1];
                                                $dates[1] = date_parse($dates[1]);
                                                $dates[1] = (strlen($dates[1]['day']) == 1 ? "0".$dates[1]['day'] : $dates[1]['day']).' '.$MONTHES[$dates[1]['month']-1];
                                                echo(": с ".$dates[0]." по ".$dates[1]);
                                                break;
                                            case 2:
                                                $date = date_parse($vacancy['days']);
                                                $date = (strlen($date['day']) == 1 ? "0".$date['day'] : $date['day']).' '.$MONTHES[$date['month']-1].' '.$date['year'];
                                                echo($date);
                                                break;
                                            default:
                                                $employment_days = $vacancy['days'];
                                                if($employment_days == "1,2,3,4,5,6,7"){
                                                    $employment_days = "Каждый день";
                                                }
                                                else if($employment_days == "1,2,3,4,5"){
                                                    $employment_days = "В будние дни";
                                                }
                                                else if($employment_days == "6,7"){
                                                    $employment_days = "На выходных";
                                                }
                                                else{
                                                    $employment_days = ":".str_replace(
                                                        Array('1', '2', '3', '4', '5', '6', '7'),
                                                        Array(' Пн', ' Вт', ' Ср', ' Чт', ' Пт', ' Сб', ' Вс'),
                                                        $vacancy['days']
                                                    );
                                                }
                                                echo($employment_days);
                                                break;
                                        }
                                    ?>
                                </p>
                                <p class="time--info info--block">
                                    <? echo($vacancy['time_from']." до ".$vacancy['time_to']); ?>
                                </p>

                            </div>
                        </div>
                        <div class="text__text">
                            <? echo_br($vacancy['description']); ?>
                        </div>
                        <div class="last__but">
                            <? if($_SESSION['account_type'] != 2) { ?>
                            <div class="last__button">
                                <a href="/lk/send_offer.php?vacancy=<? echo $vacancy_id; ?>" class="but__p">откликнуться</a>
                            </div>
                            <? } ?>
                            <div class="but__text">
                                <? if($vacancy['request_count'] > 0){
                                    switch($vacancy['request_count']){
                                        case 1:
                                            echo("1 человек откликнулся");
                                            break;
                                        case 2:
                                        case 3:
                                        case 4:
                                            echo("{$vacancy['request_count']} человека откликнулись");
                                            break;
                                        default:
                                            echo("{$vacancy['request_count']} человек откликнулись");
                                    }
                                }
                                else{
                                    echo("Никто не откликнулся");
                                } ?>
                            </div>
                        </div>
                    </div>
                    <div class="vakancyy">
                        <div class="text__vakancy">
                            <p class="vakancy__treb__title">требования к кандидату</p>
                            <div class="vakancy__treb__text">
                                <p class="treb__text__title">основные</p>
                                <div class="treb__text__text">
                                    <div class="pol">
                                        <p class="poll">
                                            Пол
                                        </p>
                                        <p class="poll">
                                            Возраст
                                        </p>
                                    </div>
                                    <div class="pol2">
                                        <? 
                                        switch($vacancy['sex']){
                                            case 1:
                                                echo('<div class="pol__text pol__text1 info">Мужской</div>');
                                                break;
                                            case 2:
                                                echo('<div class="pol__text pol__text1 info">Женский</div>');
                                                break;
                                            case 0:
                                            default:
                                                echo('<div class="pol__text pol__text1">не важен</div>');
                                                break;
                                        } 
                                        echo($vacancy['age_min'] == 0 && $vacancy['age_max'] == 0 ? 
                                            '<div class="pol__text pol__text2">не важен</div>' : 
                                            '<div class="pol__text pol__text2 info">'.(
                                                ($vacancy['age_min'] == 0 ? "" : "от ".$vacancy['age_min']." ").
                                                ($vacancy['age_max'] == 0 ? "" : "до ".$vacancy['age_max'])
                                            ).'</div>'
                                        );
                                        ?>
                                    </div>
                                </div>
                                <p class="treb__text__title2">
                                    другие
                                </p>
                                <div class="pol treb__text__text2">
                                    <table id="extra_params">
                                    <?
                                    if(strlen($vacancy['extra_params']) > 0 && $vacancy['extra_params'] != "null"){
                                        foreach(json_decode($vacancy['extra_params'], true) as $field){
                                            foreach(json_decode($filters, true) as $filter){
                                                if($filter['name'] == $field['name']){ ?>
                                                    <? if ($filter['type'] == 0 && count(explode(';', $filter['options'])) > 2)  { 
                                                        $str = "";?>                                                        
                                                        <?foreach(explode(";", $filter['options']) as $option) {
                                                            if ($field[$option] == 1) {
                                                                $str .= ''.$option.'; ';
                                                             }
                                                        }?>

                                                        <tr>
                                                            <td><? echo $filter['display']; ?></td>
                                                            <td><? echo $str; ?></td>
                                                        </tr>                                                                                                                                                                                                                                                        
                                                               
                                                    <?} else if ($filter['type'] == 3) {?>
                                                        <tr>
                                                        
                                                        <td><? echo $filter['display']; ?></td>
                                                           
                                                                <div style="display: flex;">                                                                
                                                                <?$ph =  $field['value'];  $i = 0; foreach( explode(",", $field['value']) as $val) {
                                                                    if (strripos($val, "/") == true){
                                                                        ?>
                                                                    <td>
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
                                                                    </td>
                                                                        
                                                                    <? continue;
                                                                    }
                                                                    // echo $val; ?>
                                                                    <td>
                                                                    <img onclick='showPopupFilter(<?echo $i?>, "<?echo $filter["display"];?>", "<?=$ph?>".split(",")); photosFilter = "<?=$ph?>".split(",");' src="/img/filter_photos/<? echo $val; ?>" class="mini_photo">
                                                                    </td>
                                                                    
                                                                <?$i++;} ?>
                                                                
                                                            
                                                                <? echo explode(";", $filter['options'])[$field['value']]; ?>
                                                                </div>
                                                        </tr>
                                                            
                                                    <?} else {?>
                                                        <tr>
                                                            <td><? echo $filter['display']; ?></td>
                                                            <td><? echo explode(";", $filter['options'])[$field['value']]; ?></td>
                                                        </tr>                                                                                                            
                                                    
                                                    <?}
                                                }
                                            }
                                        }
                                    }
                                    ?>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="zp__vakancy">
                            <div class="zp__title">Зарплата</div>
                            <div class="zp__text">
                                <div class="zp__zp">
                                    <p class="zp__chill">
                                        <? echo($vacancy['salary_per_hour'] == 0 ? "-" : $vacancy['salary_per_hour']); ?>
                                    </p>
                                    <p class="zp__chill">
                                        <? echo($vacancy['salary_per_day'] == 0 ? "-" : $vacancy['salary_per_day']); ?>   
                                    </p>
                                    <p class="zp__chill">
                                        <? echo($vacancy['salary_per_month'] == 0 ? "-" : $vacancy['salary_per_month']); ?>
                                    </p>
                                </div>
                                <div class="zp__zp2">
                                    <p class="zp__texting1 zp__texting">
                                        ₸/час
                                    </p>
                                    <p class="zp__texting2 zp__texting">
                                        ₸/день
                                    </p>
                                    <p class="zp__texting3 zp__texting">
                                        ₸/месяц
                                    </p>
                                </div>
                            </div>
                            <div class="zp__footer">
                                <p class="pol vip">
                                    выплата
                                </p>
                                <div class="ned">
                                    <? echo($payout_types[$vacancy['payment_type']]); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- <div class="data">
                        <div class="data__title">Дата и время работы</div>
                        <div class="data__text">
                            <div class="data__text1">
                                <div class="data__time">
                                    <? echo($job_type[$vacancy['time_type']]); ?> работа 
                                </div>
                            </div>
                            <div class="data__text2">
                                <?
                                    switch ($vacancy['time_type']) {
                                        case 1:
                                            $dates = explode(":", $item['days']);
                                            $dates[0] = date_parse($dates[0]);
                                            $dates[0] = (strlen($dates[0]['day']) == 1 ? "0".$dates[0]['day'] : $dates[0]['day']).' '.$MONTHES[$dates[0]['month']-1];
                                            $dates[1] = date_parse($dates[1]);
                                            $dates[1] = (strlen($dates[1]['day']) == 1 ? "0".$dates[1]['day'] : $dates[1]['day']).' '.$MONTHES[$dates[1]['month']-1];
                                            echo(": с ".$dates[0]." по ".$dates[1]);
                                            break;
                                        case 2:
                                            $date = date_parse($vacancy['days']);
                                            $date = (strlen($date['day']) == 1 ? "0".$date['day'] : $date['day']).' '.$MONTHES[$date['month']-1].' '.$date['year'];
                                            echo("Дата проведения: ".$date);
                                            break;
                                        default:
                                            $employment_days = $vacancy['days'];
                                            if($employment_days == "1,2,3,4,5,6,7"){
                                                $employment_days = "Каждый день";
                                            }
                                            else if($employment_days == "1,2,3,4,5"){
                                                $employment_days = "В будние дни";
                                            }
                                            else if($employment_days == "6,7"){
                                                $employment_days = "На выходных";
                                            }
                                            else{
                                                $employment_days = ":".str_replace(
                                                    Array('1', '2', '3', '4', '5', '6', '7'),
                                                    Array(' Пн', ' Вт', ' Ср', ' Чт', ' Пт', ' Сб', ' Вс'),
                                                    $vacancy['days']
                                                );
                                            }
                                            echo($employment_days);
                                            break;
                                    }
                                ?>
                            </div>
                            <div class="data__text3">
                                Часы работы
                            </div>
                            <div class="data__text4">
                                c <? echo($vacancy['time_from']." до ".$vacancy['time_to']); ?> 
                            </div>
                        </div>
                    </div> -->
                    <div class="vakancy__last">
                        <!-- <div class="last__but">
                            <? if($_SESSION['account_type'] != 2) { ?>
                            <div class="last__button">
                                <a href="/lk/send_offer.php?vacancy=<? echo $vacancy_id; ?>" class="but__p">откликнуться на вакансию</a>
                            </div>
                            <? } ?>
                            <div class="but__text">
                                <? if($vacancy['request_count'] > 0){
                                    switch($vacancy['request_count']){
                                        case 1:
                                            echo("1 человек уже откликнулся на эту вакансию");
                                            break;
                                        case 2:
                                        case 3:
                                        case 4:
                                            echo("{$vacancy['request_count']} человека уже откликнулись на эту вакансию");
                                            break;
                                        default:
                                            echo("{$vacancy['request_count']} человек уже откликнулись на эту вакансию");
                                    }
                                }
                                else{
                                    echo("Еще никто не откликнулся на эту вакансию");
                                } ?>
                            </div>
                        </div> -->
                        <?
                        isset($_SESSION['id']) ? "" : '<div class="last__text">
                            Чтобы иметь возможность откликаться на вакансии
                            и видеть контактную информацию работодателей необходимо <a href="#" class="last">создать свою анкету</a>
                        </div>'
                        ?>
                    </div>
                    <div class="vakancy__footer">
                        <ul class="vak__footer">
                            <li>Автор вакансии<a href="/company/<? echo($company['id']); ?>"><? echo($company['company_name']); ?> (<? echo($company['company_type']); ?>)</a></li>
                            <li>Дата публикации<span>
                            <? 
                            $date = date_parse($vacancy['public_date']);
                            echo(
                                (strlen($date['day']) == 1 ? "0".$date['day'] : $date['day']).' '.$MONTHES[$date['month']-1].' '.$date['year']." в ".$date['hour'].":".$date['minute']
                            ); ?>
                            </span></li>
                        </ul>
                    </div>
                </div>        
                <div class="text__right">
                    <a class="link_company" href="/company/<? echo($company['id']); ?>">
                        <div class="company_information">
                            <div class="company__content">
                                <div class="company_image">
                                    <img src="/img/companies/<?php echo($company['logo'])?>" alt="">
                                </div>
                                <p class="name-of-company"><? echo($company['company_name']); ?></p>
                                <p class="type-of-company"><? echo($company['company_type']); ?></p>
                                <p class="type-of-company">Описание: <? echo explode(";", $company['company_desc'])[0]?></p>
                            </div>
                        </div>
                    </a>
                    <? $val = explode(";", $company['company_desc'])[1]; if (strripos($val, "/") == true) { ?>
                        <p class="type-of-company">Видео о компании:</p>

                        <video id="my-video1" width="600" controls class="video-js 1" data-setup='
                            {
                                "techOrder": ["youtube"],
                                "sources": [{
                                    "type": "video/youtube",
                                    "src": "<?echo $val?>"
                                }]
                            }
                        '>
                        </video>
                    <?}?>


                    <? if($_SESSION['admin']) { ?> 
                        <div class="edit_button">
                            <a href="/admin/edit_vacancy.php?id=<? echo($vacancy_id); ?>">Редактировать вакансию</a>
                        </div>
                    <? } else { ?>
                        <div class="ads" id="right_banner">                      
                        </div>
                    <? } ?>
                    <!-- <div class="search">
                        <div class="search__title">
                            <a href="/vacancy/">поиск вакансий</a>
                        </div>
                        <div class="search__text">
                            <?
                            foreach($vacancy_types as $item){
                                echo('<a href="/vacancy?vacancy_types[]='.$item['id'].'"><span>'.$item['vacancy_type_name'].'</span></a>');
                            }
                            ?>
                        </div>
                    </div> -->
                </div>
            </div>
        </div>



    </div>
        <? include("../views/footer.php"); ?>
        <script type="text/javascript" src="/js/vacacny.js"></script> 
        <!-- for video -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/video.js/8.15.0/video.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/videojs-youtube/3.0.1/Youtube.min.js"></script>
        <script src="https://unpkg.com/youtube-video-id@latest/dist/youtube-video-id.min.js"></script>
    </div>
</body>
<script>let current_balance = '<?=$current_balance?>';</script>
</html>