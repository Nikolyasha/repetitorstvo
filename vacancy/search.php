<?
session_start();
$anketsOrVacancies='vacancies';
include("../core/db.php");
require_once($_SERVER['DOCUMENT_ROOT']."/models/vacancy.php");
require_once($_SERVER['DOCUMENT_ROOT']."/models/company.php");
require_once($_SERVER['DOCUMENT_ROOT']."/models/user.php");

$sql = Vacancy::GetTypesSQL().Company::GetCityListSQL().Vacancy::GetFilterListSQL($link);
if($_SESSION['account_type'] == 2){
    $sql .= User::GetBalanceSQL($_SESSION['id']);
    list($vacancy_types, $cities, $filters, $current_balance) = MultiQuery($link, $sql);
    $current_balance = $current_balance[0]['count'];
}
else if($_SESSION['account_type'] == 1){
    $sql .= User::GetFavoriteListSQL($_SESSION['id']);
    $sql .= User::GetBalanceSQL($_SESSION['id']);
    list($vacancy_types, $cities, $filters, $favorites, $current_balance) = MultiQuery($link, $sql);
    $favorites = json_decode($favorites[0]['favorite']);
    $current_balance = $current_balance[0]['count'];
}
else{
    list($vacancy_types, $cities, $filters) = MultiQuery($link, $sql);
}

if(!isset($favorites) || $favorites == null)
    $favorites = [];

$query_filters = [];
$offset = ((int) $_GET['page']) * 10;
if($offset == "0") $offset = "";
else $offset = ",".$offset;
/* #region  Statdart Filters */

$order_by = "vacancies.id DESC";
if(!empty($_GET['order'])){
    switch($_GET['order']){
        case "old":
            $order_by = "vacancies.id ASC"; break;
        default:
            $order_by = "vacancies.id DESC"; break;
    }
}

$city_list = [];
if(!empty($_GET["cities"])){
    foreach($_GET["cities"] as $city){
        foreach($cities as $check_city){
            if((int) $city == $check_city["id"]){
                array_push($city_list, (int) $city);
            }
        }
    }
}
$_GET["cities"] = $city_list;
if(count($city_list) == 1){
    array_push($query_filters, "`vacancies`.`city_id` = ".$city_list[0]);
}
else if(count($city_list) > 1){
    array_push($query_filters, "`vacancies`.`city_id` in (".implode(",", $city_list).")");
}

$vacancy_types_list = [];
if(!empty($_GET["vacancy_types"])){
    foreach($_GET["vacancy_types"] as $vacancy){
        foreach($vacancy_types as $check_vacancy){
            if((int) $vacancy == $check_vacancy["id"]){
                array_push($vacancy_types_list, (int) $vacancy);
            }
        }
    }
}
$_GET["vacancy_types"] = $vacancy_types_list;
if(count($vacancy_types_list) == 1){
    array_push($query_filters, "`vacancies`.`type_id` = ".((int) $vacancy_types_list[0]));
}
else if(count($vacancy_types_list) > 1){
    array_push($query_filters, "`vacancies`.`type_id` in (".implode(",", $vacancy_types_list).")");
}

if(ctype_digit($_GET["salary_type"]) && ((int) $_GET["salary_type"]) > -1){
    if(((int) $_GET["salary_type"]) > 2) $_GET["salary_type"] = 2;
    array_push($query_filters, "`vacancies`.`payment_type` = ".((int) $_GET["salary_type"]));
    if(ctype_digit($_GET["salary_value"]) && ((int) $_GET["salary_value"]) > 0){
        if(((int) $_GET["salary_value"]) > 1000000) $_GET["salary_value"] = 1000000;
        switch ((int) $_GET["salary_type"]) {
            case 0:
                array_push($query_filters, "`vacancies`.`salary_per_hour` >= ".((int) $_GET["salary_value"]));
                break;
            case 1:
                array_push($query_filters, "`vacancies`.`salary_per_day` >= ".((int) $_GET["salary_value"]));
                break;
            case 2:
                array_push($query_filters, "`vacancies`.`salary_per_month` >= ".((int) $_GET["salary_value"]));
                break;
            default: break;
        }
    }
}

if(ctype_digit($_GET["employment_type"]) && ((int) $_GET["employment_type"]) > -1){
    if(((int) $_GET["employment_type"]) > 2) $_GET["employment_type"] = 2;
    array_push($query_filters, "`vacancies`.`time_type` = ".((int) $_GET["employment_type"]));
}

if(ctype_digit($_GET["gender_type"]) && ((int) $_GET["gender_type"]) > 0){
    if(((int) $_GET["gender_type"]) > 2) $_GET["gender_type"] = 2;
    array_push($query_filters, "`vacancies`.`sex` = ".((int) $_GET["gender_type"]));
}

if(ctype_digit($_GET["min_age"]) && ctype_digit($_GET["max_age"]) && (int) $_GET["min_age"] > (int) $_GET["max_age"]){
    list($_GET["max_age"], $_GET["min_age"]) = [$_GET["min_age"], $_GET["max_age"]];
}

if(ctype_digit($_GET["min_age"])){
    if(!((int) $_GET["min_age"]) > 13){
        $_GET["min_age"] = 14;
    }
    array_push($query_filters, "`vacancies`.`age_min` <= ".((int) $_GET["min_age"]));
}

if(ctype_digit($_GET["max_age"])){
    if(!((int) $_GET["max_age"]) > 99){
        $_GET["max_age"] = 99;
    }
    array_push($query_filters, "`vacancies`.`age_max` >= ".((int) $_GET["max_age"]));
}

/* #endregion */
/* #region  Extra Filters */

$extra_filters = [];
foreach(array_keys($_GET) as $param){
    if(explode("_", $param)[0] == "extra"){
        foreach($filters as $filter){
            if("extra_" . $filter['name'] == $param){
                $extra_filters[$param] = $filter;
            }
        }
    }
}

foreach($extra_filters as $extra_filter){
    $current_filter = [];
    switch ($extra_filter['type']) {
        case 0:
            if(!empty($_GET["extra_{$extra_filter['name']}"])){
                foreach($_GET["extra_{$extra_filter['name']}"] as $param){
                    if((int) $param > -1 && (int) $param < count(explode(";", $extra_filter['options']))){
                        array_push($current_filter, "{\"name\": \"{$extra_filter['name']}\", \"value\": ".((int) $param)."}");
                    }
                }
                if(count($current_filter) > 0){
                    array_push($query_filters, "JSON_OVERLAPS(`vacancies`.`extra_params`, '[".(implode(",", $current_filter))."]')");
                }
            }
            break;
        case 1:
        case 2:
            if(ctype_digit($_GET["extra_{$extra_filter['name']}"]) && ((int) $_GET["extra_{$extra_filter['name']}"]) > -1){
                array_push($query_filters, "JSON_OVERLAPS(`vacancies`.`extra_params`, '[{\"name\": \"{$extra_filter['name']}\", \"value\": ".((int) $_GET["extra_{$extra_filter['name']}"])."}]')");
            }
        default:
            # code...
            break;
    }
}

/* #endregion */

list($vacancies, $vacancy_count) = MultiQuery($link, Vacancy::GetVacancyListSQL($order_by, $query_filters, $offset));
$vacancy_count = $vacancy_count[0]['vacancies_count']; 
// print_r($favorites);
// echo User::GetFavoriteListSQL($_SESSION['id']);
?>
<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width">
    <link rel="stylesheet" href="/css/vacancies.css">
    <title>Поиск вакансий на
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
        <? include("../views/search.php"); ?>
        <div class="item">
            <div class="vakancy__title">
                <div class="container">
                    <div class="vakancy__row__title">
                        <div class="vakancy__main">
                            <div class="vakancy__public">
                                Найдено <? echo $vacancy_count;
                                switch($vacancy_count){
                                    case 1: echo(" вакансия"); break;
                                    case 2: case 3: case 4:  echo(" вакансии"); break;
                                    default: echo(" вакансий"); break;
                                } ?> 
                            </div>
                            <div class="vakancy__button">
                                <a href="." class="vak__button">Сбросить все фильтры</a>
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
                            if(count($vacancies) == 0){ ?>
                                <div class="vacancy__block no_vacancies">
                                    К сожалению по вашему запросу ничего не найдено<br>Попробуйте изменить параметры поиска
                                </div>
                            <? }
                            else {
                                foreach ($vacancies as $item) {
                                    $salary = $item['salary_per_hour']." тг./час";
                                    if($item['salary_per_hour'] == 0){
                                        if($item['salary_per_day'] == 0){
                                            $salary = $item['salary_per_month']." тг./месяц";
                                        }
                                        else{
                                            $salary = $item['salary_per_day']." тг./день";
                                        }
                                    }
                                    if($item['time_type'] == 0){
                                        $employment_days = $item['days'];
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
                                                $item['days']
                                            );
                                        }
                                    }
                                    else if($item['time_type'] == 1){
                                        $dates = explode(":", $item['days']);
                                        $dates[0] = date_parse($dates[0]);
                                        $dates[0] = (strlen($dates[0]['day']) == 1 ? "0".$dates[0]['day'] : $dates[0]['day']).' '.$MONTHES[$dates[0]['month']-1];
                                        $dates[1] = date_parse($dates[1]);
                                        $dates[1] = (strlen($dates[1]['day']) == 1 ? "0".$dates[1]['day'] : $dates[1]['day']).' '.$MONTHES[$dates[1]['month']-1];
                                        $employment_days = ": с ".$dates[0]." по ".$dates[1];
                                    }
                                    else{
                                        $date = date_parse($item['days']);
                                        $date = (strlen($date['day']) == 1 ? "0".$date['day'] : $date['day']).' '.$MONTHES[$date['month']-1].' '.$date['year'];
                                        $employment_days = ": ".$date;
                                    }
                                    $post_date = date_parse($item['public_date']);
                                    echo('                                  
                                        <div class="vacancy__block">
                                            <div class="vacancy__block_left">
                                                '.($_SESSION['account_type'] == 1 ? '<div onclick="setFavoriteVacancy(this, '.$item['id'].')" class="'.(in_array(strval($item['id']), $favorites) ? "active_favorite" : "unactive_favorite").'"></div>' : '').'
                                                '.(!isset($_SESSION['id']) ? '<div onclick="window.location = \'/login.php\'" class="'.(in_array(strval($anket['id']), $favorites) ? "active_favorite" : "unactive_favorite").'"></div>' : '').'
                                                <div class="vacancy__block_left_where">
                                                    '.(strlen($post_date['day']) == 1 ? "0".$post_date['day'] : $post_date['day']).' '.$MONTHES[$post_date['month']-1].'
                                                </div>
                                                <div class="vacancy__block_left_where">
                                                    В '.(strlen($post_date['hour']) == 1 ? "0".$post_date['hour'] : $post_date['hour']).':'.(strlen($post_date['minute']) == 1 ? "0".$post_date['minute'] : $post_date['minute']).'
                                                </div>
                                            </div>
                                            <div class="vakancy__block_right">
                                                <div class="vakancy__block_right_text">
                                                    <div class="vakancy__block_right_text_title">
                                                        <a href="/vacancy/'.$item['id'].'">'.$item['name'].', '.$salary.'
                                                        '.($item['sex'] == 2 ? '<img style="width: 25px; height:25px; margin-left:5px" src="/img/avatars/no_photo_female.png" alt="" class="vacancy__photo">' : '').'
                                                        '.($item['sex'] == 1 ? '<img style="width: 25px; height:25px; margin-left:5px" src="/img/avatars/no_photo_male.png" alt="" class="vacancy__photo">' : " ").'
                                                        '.($item['sex'] == 0 ? '<img style="width: 25px; height:25px; margin-left:5px" src="/img/avatars/no_photo_female.png" alt="" class="vacancy__photo"> <img style="width: 25px; height:25px;" src="/img/avatars/no_photo_male.png" alt="" class="vacancy__photo">' : " ").'
                                                           
                                                        </a>
                                                    </div>
                                                    <div class="vakancy__block_right_text_body">
                                                        <span>'.($item['type_id'] == 105 ? $item['type_another'] : $item['type']) .'.</span>'.$item['desc_min'].' ('.$item['city'].')
                                                        <br>
                                                        '.$DURABILITY[$item['time_type']].$employment_days.'
                                                    </div>
                                                    <div class="vakancy__block_right_text_footer">
                                                        <a href="/company/'.$item['owner_id'].'">'.$item['company'].', '.$item['company_type'].'</a>
                                                    </div>
                                                </div>
                                                <div class="vakancy__block_right_img">
                                                    <img src="/img/companies/'.$item['logo'].'" alt="">
                                                </div>
                                            </div>
                                        </div>');
                                }
                            }
                            ?>
                            <div class="page_counter_wrapper">
                                <div class="page_counter">
                                    <?
                                        $page_count = ceil($vacancy_count / 10);
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
                            <script>
                                let openList = [];
                            </script>
                            <form action="." method="GET" id="filter_form">
                                <input type="hidden" name="order" value="<?= $_GET['order'] == "old" ? "old" : "new" ?>"/>
                                <input type="hidden" name="page" value="<?= (int) $_GET['page'] > 0 ? (int) $_GET['page'] : 0 ?>"/>
                                <div class="positions__block">
                                    <div class="block_positions">
                                        <a class="positions" onclick="openFilter(this);">
                                            Город
                                        </a>
                                        <ul class="positions__border dropdown_block">
                                            <? foreach($cities as $city) { ?>
                                            <li><input class="inp-cbx" id="city_<? echo $city['id']; ?>" type="checkbox" name="cities[]" value="<? echo $city['id']; ?>" />
                                                <label class="cbx" for="city_<? echo $city['id']; ?>"><span>
                                                        <svg width="12px" height="10px">
                                                            <use xlink:href="#check"></use>
                                                        </svg></span><span>
                                                        <? echo $city['name']; ?>
                                                    </span>
                                                </label>
                                            </li>
                                            <? } ?>
                                        </ul>
                                        <script>
                                            if (<?= !empty($_GET["cities"]) ? "true" : "false" ?>) {
                                                [<?= implode(", ", array_values($_GET["cities"])) ?>].forEach(el => {
                                                    document.getElementById("city_" + el).checked = true;
                                                });
                                                openList.push(document.getElementsByClassName("positions")[document.getElementsByClassName("positions").length - 1]);
                                            }
                                        </script>
                                    </div>
                                    <div class="block_positions">
                                        <a class="positions" onclick="openFilter(this);">
                                            должности
                                        </a>
                                        <ul class="positions__border dropdown_block">
                                            <? foreach($vacancy_types as $vacancy_type) { ?>
                                            <li><input class="inp-cbx" id="vacancy_type_<? echo $vacancy_type['id']; ?>" type="checkbox" name="vacancy_types[]" value="<? echo $vacancy_type['id']; ?>" />
                                                <label class="cbx" for="vacancy_type_<? echo $vacancy_type['id']; ?>"><span>
                                                        <svg width="12px" height="10px">
                                                            <use xlink:href="#check"></use>
                                                        </svg></span><span>
                                                        <? echo $vacancy_type['vacancy_type_name']; ?>
                                                    </span>
                                                </label>
                                            </li>
                                            <? } ?>
                                        </ul>

                                        <script>
                                            if (<?= !empty($_GET["vacancy_types"]) ? "true" : "false" ?>) {
                                                [<?= implode(", ", array_values($_GET["vacancy_types"])) ?>].forEach(el => {
                                                    document.getElementById("vacancy_type_" + el).checked = true;
                                                });
                                                openList.push(document.getElementsByClassName("positions")[document.getElementsByClassName("positions").length - 1]);
                                            }
                                        </script>
                                    </div>
                                    <div class="block_positions">
                                        <a class="positions" onclick="openFilter(this);">
                                            тип занятости
                                        </a>
                                        <ul class="positions__border dropdown_block">
                                            <li>
                                                <div class="box">
                                                    <input id="employment_type_null" type="radio" name="employment_type" value=-1>
                                                    <span class="check"></span>
                                                    <label for="employment_type_null">Неважно</label>
                                                </div>
                                            </li>
                                            <li>
                                                <div class="box">
                                                    <input id="employment_type_0" type="radio" name="employment_type" value=0>
                                                    <span class="check"></span>
                                                    <label for="employment_type_0">Постоянная работа</label>
                                                </div>
                                            </li>
                                            <li>
                                                <div class="box">
                                                    <input id="employment_type_1" type="radio" name="employment_type" value=1>
                                                    <span class="check"></span>
                                                    <label for="employment_type_1">Временная работа</label>
                                                </div>
                                            </li>
                                            <li>
                                                <div class="box">
                                                    <input id="employment_type_2" type="radio" name="employment_type" value=2>
                                                    <span class="check"></span>
                                                    <label for="employment_type_2">Единоразовое мероприятие</label>
                                                </div>
                                            </li>
                                        </ul>
                                        <script>
                                            if (<?= (ctype_digit($_GET["employment_type"]) && ((int) $_GET["employment_type"]) > -1) ? "true" : "false" ?>) {
                                                document.getElementsByName("employment_type")[<?= (int) $_GET["employment_type"] ?>].checked = true;
                                                openList.push(document.getElementsByClassName("positions")[document.getElementsByClassName("positions").length - 1]);
                                            }
                                        </script>
                                    </div>
                                    <div class="block_positions">
                                        <a class="positions" onclick="openFilter(this);">
                                            пол
                                        </a>
                                        <ul class="positions__border dropdown_block">
                                            <li>
                                                <div class="box">
                                                    <input id="gender_type_0" type="radio" name="gender_type" value=0>
                                                    <span class="check"></span>
                                                    <label for="gender_type_0">Неважно</label>
                                                </div>
                                            </li>
                                            <li>
                                                <div class="box">
                                                    <input id="gender_type_1" type="radio" name="gender_type" value=1>
                                                    <span class="check"></span>
                                                    <label for="gender_type_1">Мужской</label>
                                                </div>
                                            </li>
                                            <li>
                                                <div class="box">
                                                    <input id="gender_type_2" type="radio" name="gender_type" value=2>
                                                    <span class="check"></span>
                                                    <label for="gender_type_2">Женский</label>
                                                </div>
                                            </li>
                                        </ul>
                                        <script>
                                            if (<?= (ctype_digit($_GET["gender_type"]) && ((int) $_GET["gender_type"]) > 0) ? "true" : "false" ?>) {
                                                document.getElementsByName("gender_type")[<?= (int) $_GET["gender_type"] ?>].checked = true;
                                                openList.push(document.getElementsByClassName("positions")[document.getElementsByClassName("positions").length - 1]);
                                            }
                                        </script>
                                    </div>
                                    <div class="block_positions">
                                        <a class="positions" onclick="openFilter(this);">
                                            возраст
                                        </a>
                                        <div class="positions__select dropdown_block">
                                            <select name="min_age" class="select_one1" id="">
                                                <option value="-1">-----</option>
                                                <? for($i = 14; $i < 100; $i++) { echo("<option value='$i'>от $i</option>"); } ?>
                                            </select>
                                            <div class="tire">
                                                -
                                            </div>
                                            <select name="max_age" class="select_one1" id="">
                                                <option value="-1">-----</option>
                                                <? for($i = 14; $i < 100; $i++) { echo("<option value='$i'>до $i</option>"); } ?>
                                            </select>
                                        </div>
                                        <script>
                                            if (<?= (ctype_digit($_GET["min_age"]) && ((int) $_GET["min_age"]) > -1) || (ctype_digit($_GET["max_age"]) && ((int) $_GET["max_age"]) > -1) ? "true" : "false" ?>) {
                                                document.getElementsByName("min_age")[0].value = <?= (int) $_GET["min_age"] ?>;
                                                document.getElementsByName("max_age")[0].value = <?= (int) $_GET["max_age"] ?>;
                                                openList.push(document.getElementsByClassName("positions")[document.getElementsByClassName("positions").length - 1]);
                                            }
                                        </script>
                                    </div>
                                    <div class="block_positions">
                                        <a class="positions" onclick="openFilter(this);">
                                            зарплата
                                        </a>
                                        <ul class="positions__border dropdown_block">
                                            <div class="pos__flex">
                                                <li>
                                                    <div class="box">
                                                        <input id="salary_type_null" type="radio" name="salary_type" value=-1>
                                                        <span class="check"></span>
                                                        <label for="salary_type_null">Неважно</label>
                                                    </div>
                                                </li>
                                                <li>
                                                    <div class="box">
                                                        <input id="salary_type_0" type="radio" name="salary_type" value=0>
                                                        <span class="check"></span>
                                                        <label for="salary_type_0">В час</label>
                                                    </div>
                                                </li>
                                                <li>
                                                    <div class="box">
                                                        <input id="salary_type_1" type="radio" name="salary_type" value=1>
                                                        <span class="check"></span>
                                                        <label for="salary_type_1">В день</label>
                                                    </div>
                                                </li>
                                                <li>
                                                    <div class="box">
                                                        <input id="salary_type_2" type="radio" name="salary_type" value=2>
                                                        <span class="check"></span>
                                                        <label for="salary_type_2">В месяц</label>
                                                    </div>
                                                </li>
                                            </div>
                                            <div class="salary_input_block">
                                                <span>от </span>
                                                <input type="number" value="0" name="salary_value" class="salary_input">
                                            </div>
                                            <script>
                                                if (<?= (ctype_digit($_GET["salary_type"]) && ((int) $_GET["salary_type"]) > -1) ? "true" : "false" ?>) {
                                                    document.getElementsByName("salary_type")[<?= (int) $_GET["salary_type"] ?> + 1].checked = true;
                                                    document.getElementsByName("salary_value")[0].value = <?= (int) $_GET["salary_value"] ?>;
                                                    openList.push(document.getElementsByClassName("positions")[document.getElementsByClassName("positions").length - 1]);
                                                }
                                            </script>
                                        </ul>
                                    </div>
                                    <!-- CUSTOM AREA -->
                                    <? foreach($filters as $filter) { $checked = False; ?>
                                    <div class="block_positions">
                                        <a class="positions" onclick="openFilter(this);">
                                            <? echo $filter['display']; ?>
                                        </a>
                                        <? switch($filter['type']){  
                                                case 0: ?>
                                        <ul class="positions__border dropdown_block">
                                            <? $i = 0;
                                            foreach(explode(";", $filter['options']) as $filter_option) { ?>
                                            <li>
                                                <input class="inp-cbx" id="extra_<? echo($filter['name'] . '_' . $i); ?>" type="checkbox" name="extra_<? echo($filter['name']); ?>[]" value="<? echo $i; ?>" />
                                                <label class="cbx" for="extra_<? echo($filter['name'] . '_' . $i); ?>"><span>
                                                        <svg width="12px" height="10px">
                                                            <use xlink:href="#check"></use>
                                                        </svg></span><span>
                                                        <? echo($filter_option); ?>
                                                    </span>
                                                </label>
                                            </li>
                                            <? $i++; } ?>
                                        </ul>
                                        <script>
                                            if (<?= (!empty($_GET["extra_{$filter['name']}"]) && ((int) $_GET["extra_{$filter['name']}"]) > -1) ? "true" : "false" ?>) openList.push(document.getElementsByClassName("positions")[document.getElementsByClassName("positions").length - 1]);
                                        </script>
                                        <script>
                                            if (<?= !empty("extra_{$filter['name']}") ? "true" : "false" ?>) {
                                                [<?= implode(", ", array_values($_GET["extra_{$filter['name']}"])) ?>].forEach(el => {
                                                    document.getElementById("extra_<? echo($filter['name'] . '_'); ?>" + el).checked = true;
                                                });
                                                openList.push(document.getElementsByClassName("positions")[document.getElementsByClassName("positions").length - 1]);
                                            }
                                        </script>
                                        <? break;
                                                case 1:  ?>
                                        <ul class="positions__border dropdown_block">
                                            <li>
                                                <div class="box">
                                                    <input id="extra_<? echo($filter['name'] . '_null'); ?>" type="radio" name="extra_<? echo($filter['name']); ?>" value="-1" checked>
                                                    <span class="check"></span>
                                                    <label for="extra_<? echo($filter['name'] . '_null'); ?>">Неважно</label>
                                                </div>
                                            </li>
                                            <? $i = 0;
                                                        foreach(explode(";", $filter['options']) as $filter_option) { ?>
                                            <li>
                                                <div class="box">
                                                    <input id="extra_<? echo($filter['name'] . '_' . $i); ?>" type="radio" name="extra_<? echo($filter['name']); ?>" value="<? echo $i; ?>" <? if(!$checked && ctype_digit($_GET["extra_{$filter['name']}"]) && (int) $_GET["extra_{$filter['name']}"]==$i){ echo "checked" ; $checked=True; } ?>>
                                                    <span class="check"></span>
                                                    <label for="extra_<? echo($filter['name'] . '_' . $i); ?>">
                                                        <? echo($filter_option); ?>
                                                    </label>
                                                </div>
                                            </li>
                                            <? $i++; } ?>
                                        </ul>
                                        <script>
                                            if (<?= (ctype_digit($_GET["extra_{$filter['name']}"]) && ((int) $_GET["extra_{$filter['name']}"]) > -1) ? "true" : "false" ?>) openList.push(document.getElementsByClassName("positions")[document.getElementsByClassName("positions").length - 1]);
                                        </script>
                                        <? break;
                                                case 2: ?>
                                        <div class="positions__select dropdown_block">
                                            <select name="extra_<? echo($filter['name']); ?>" class="extra_select" id="extra_<? echo($filter['name']); ?>">
                                                <option value="-1">-----</option>
                                                <? $i = 0;
                                                            foreach(explode(";", $filter['options']) as $filter_option) { ?>
                                                <option value="<? echo $i; ?>">
                                                    <? echo($filter_option); ?>
                                                </option>
                                                <? $i++; } ?>
                                            </select>
                                        </div>
                                        <script>
                                            extra_<? echo($filter['name']); ?> .value = <?= ctype_digit($_GET["extra_{$filter['name']}"]) ? $_GET["extra_{$filter['name']}"] : -1; ?>;
                                            if (<?= (ctype_digit($_GET["extra_{$filter['name']}"]) && ((int) $_GET["extra_{$filter['name']}"]) > -1) ? "true" : "false" ?>) openList.push(document.getElementsByClassName("positions")[document.getElementsByClassName("positions").length - 1]);
                                        </script>
                                        <?break; default: break;?>
                                        <? } print_r((int) $_GET["extra_{$filter['name']}"]); ?>
                                    </div>
                                    <? } ?>
                                    <!-- CUSTOM AREA -->

                                    <button class="block__button">
                                        фильтровать
                                    </button>
                                </div>
                            </form>
                            <div class="search__vacancies">
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
                    </div>
                </div>
            </div>
        </div>
        <? include("../views/footer.php"); ?>
        <script type="text/javascript" src="/js/search.js"></script>
        
    </div>
</body>

</html>