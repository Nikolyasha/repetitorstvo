<?
session_start(); 
include("../core/db.php");
require_once($_SERVER['DOCUMENT_ROOT']."/models/vacancy.php");
require_once($_SERVER['DOCUMENT_ROOT']."/models/company.php");
require_once($_SERVER['DOCUMENT_ROOT']."/models/user.php");

$sql = Vacancy::GetTypesSQL().Company::GetCityListSQL().User::GetFilterListSQL();
if($_SESSION['account_type'] == 2){
    $sql .= Company::GetFavoriteListSQL($_SESSION['id']);
    $sql .= User::GetBalanceSQL($_SESSION['id']);
    list($vacancy_types, $cities, $filters, $favorites, $current_balance) = MultiQuery($link, $sql);
    $current_balance = $current_balance[0]['count'];
    $favorites = json_decode($favorites[0]['favorite']);
}
else if($_SESSION['account_type'] == 1){
    $sql .= User::GetBalanceSQL($_SESSION['id']);
    list($vacancy_types, $cities, $filters, $current_balance) = MultiQuery($link, $sql);
    $current_balance = $current_balance[0]['count'];
}
else{
    list($vacancy_types, $cities, $filters) = MultiQuery($link, $sql);
}
// $favorites = [];

$anketsOrVacancies = 'ankets';

$query_filters = [];
$offset = ((int) $_GET['page']) * 10;
if($offset == "0") $offset = "";
else $offset = ",".$offset;

/* #region  Statdart Filters */

$order_by = "workers.id DESC";
if(!empty($_GET['order'])){
    switch($_GET['order']){
        case "old":
            $order_by = "workers.id ASC"; break;
        default:
            $order_by = "workers.id DESC"; break;
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
    array_push($query_filters, "`workers`.`city` = ".$city_list[0]);
}
else if(count($city_list) > 1){
    array_push($query_filters, "`workers`.`city` in (".implode(",", $city_list).")");
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
if(!empty($vacancy_types_list)){
    $job_types_filter = [];
    foreach($vacancy_types_list as $job_type){
        $job_type = (int) $job_type;
        if($job_type > 0)
            array_push($job_types_filter, "(`job_types` LIKE '%,$job_type,%' OR `job_types` LIKE ('$job_type,%') OR `job_types` LIKE ('%,$job_type'))");
    }
    array_push($query_filters, implode(" OR ", $job_types_filter));
}

if(ctype_digit($_GET["gender_type"]) && ((int) $_GET["gender_type"]) > 0){
    if(((int) $_GET["gender_type"]) > 2) $_GET["gender_type"] = 2;
    array_push($query_filters, "`workers`.`sex` = ".((int) $_GET["gender_type"]));
}

if(ctype_digit($_GET["min_age"]) && ctype_digit($_GET["max_age"]) && (int) $_GET["min_age"] > (int) $_GET["max_age"]){
    list($_GET["max_age"], $_GET["min_age"]) = [$_GET["min_age"], $_GET["max_age"]];
}

if(ctype_digit($_GET["min_age"])){
    if(!((int) $_GET["min_age"]) > 13){
        $_GET["min_age"] = 14;
    }
    array_push($query_filters, "`workers`.`age` >= ".((int) $_GET["min_age"]));
}

if(ctype_digit($_GET["max_age"])){
    if(!((int) $_GET["max_age"]) > 99){
        $_GET["max_age"] = 99;
    }
    array_push($query_filters, "`workers`.`age` <= ".((int) $_GET["max_age"]));
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
                    array_push($query_filters, "JSON_OVERLAPS(`workers`.`extra_fields`, '[".(implode(",", $current_filter))."]')");
                }
            }
            break;
        case 1:
        case 2:
            if(ctype_digit($_GET["extra_{$extra_filter['name']}"]) && ((int) $_GET["extra_{$extra_filter['name']}"]) > -1){
                array_push($query_filters, "JSON_OVERLAPS(`workers`.`extra_fields`, '[{\"name\": \"{$extra_filter['name']}\", \"value\": ".((int) $_GET["extra_{$extra_filter['name']}"])."}]')");
            }
        default:
            # code...
            break;
    }
}
/* #endregion */

// print_r(User::GetAnketsListSQL($order_by, $query_filters, $offset));
list($ankets, $ankets_count) = MultiQuery($link, User::GetAnketsListSQL($order_by, $query_filters, $offset));
$ankets_count = $ankets_count[0]['ankets_count']; 
// echo User::GetFavoriteListSQL($_SESSION['id']);
?>
<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width">
    <link rel="stylesheet" href="/css/ankets.css">
    <title>Поиск анкет на
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
                            <div class="vakancy__public-container">
                                <div class="vakancy__public">
                                    Найдено <? echo $ankets_count;
                                    switch($ankets_count){
                                        case 1: echo(" анкета"); break;
                                        case 2: case 3: case 4:  echo(" анкеты"); break;
                                        default: echo(" анкет"); break;
                                    } ?> 
                                </div>
                                <div class="underline">
                                    <div class="main--line"></div>
                                    <div class="small--line"></div>
                                </div>  
                            </div>
                            <div class="vakancy__buttons-container">
                                <div class="vakancy__sorting">
                                    сортировка: <a onclick="changeSort('<?= $_GET['order'] == "old" ? "new" : "old" ?>');"><?= $_GET['order'] == "old" ? "Сначала старые" : "Сначала новые" ?></a>
                                </div>
                                <div class="vakancy__button">
                                    <a href="." class="vak__button">Сбросить все фильтры</a>
                                </div>  
                            </div>
                            
                        </div>
                    </div>
                </div>
            </div>
            <div class="vacancy__body">
                <div class="container">
                    <div class="vacancy__row">
                        <div class="text__right">
                                <script>
                                    let openList = [];
                                </script>
                                <form action="." method="GET" id="filter_form" class="form__filters">
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
                                                Последняя активность
                                            </a>
                                            <ul class="positions__border dropdown_block">
                                                <div class="pos__flex">
                                                    <li>
                                                        <div class="box">
                                                            <input id="last_activity_null" type="radio" name="last_activity" value=-1>
                                                            <span class="check"></span>
                                                            <label for="last_activity_null">Неважно</label>
                                                        </div>
                                                    </li>
                                                    <li>
                                                        <div class="box">
                                                            <input id="last_activity_0" type="radio" name="last_activity" value=0>
                                                            <span class="check"></span>
                                                            <label for="last_activity_0">Не более 3 дней назад</label>
                                                        </div>
                                                    </li>
                                                    <li>
                                                        <div class="box">
                                                            <input id="last_activity_1" type="radio" name="last_activity" value=1>
                                                            <span class="check"></span>
                                                            <label for="last_activity_1">Не более недели назад</label>
                                                        </div>
                                                    </li>
                                                    <li>
                                                        <div class="box">
                                                            <input id="last_activity_2" type="radio" name="last_activity" value=2>
                                                            <span class="check"></span>
                                                            <label for="last_activity_2">Не более месяца назад</label>
                                                        </div>
                                                    </li>
                                                </div>
                                                <script>
                                                    if (<?= (ctype_digit($_GET["last_activity"]) && ((int) $_GET["last_activity"]) > -1) ? "true" : "false" ?>) {
                                                        document.getElementsByName("last_activity")[<?= (int) $_GET["last_activity"] ?> + 1].checked = true;
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
                                            <? }?>
                                        </div>
                                        <? } ?>
                                        <!-- CUSTOM AREA -->
                                         <div class="button__wrapper">
                                            <button class="block__button">
                                                фильтровать
                                            </button>
                                         </div>
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
                                <div class="ads" id="right_banner"></div>

                        </div>

                        <div class="text__left">
                            <div class="wrapper-text__left">
                                <? 
                                if(count($ankets) == 0){ ?>
                                    <div class="vacancy__block no_vacancies">
                                        К сожалению по вашему запросу ничего не найдено<br>Попробуйте изменить параметры поиска
                                    </div>
                                <? }
                                else {
                                    foreach($ankets as $index => $anket){ 

                                        $additionalClass = '';
                                        if($index == 0) {
                                            $additionalClass .= 'first';
                                        }
                                        if($index == count($ankets) - 1) {
                                            $additionalClass .= 'last';
                                        }
                                        ?>
                                        <div class="vacancy__block <?php echo $additionalClass  ?>">
                                            <div class="wrapper_block">
                                                <div class="vacancy__block_left">
                                                    <? if(empty($anket['photos'])) { ?>
                                                        <? if ($anket['sex'] == 1) { ?>
                                                            <a href="/anket/<?=  $anket['user_id'] ?>"><img src='/img/avatars/no_photo_female_anket.png' alt='Нет фото' class="vacancy__photo"></a>
                                                        <? } else { ?>                                                    
                                                            <a href="/anket/<?=  $anket['user_id'] ?>"><img src='/img/avatars/no_photo_male_anket.png' alt='Нет фото' class="vacancy__photo"></a>
                                                        <? } ?>
                                                        
                                                    <? } else { ?>
                                                        <a href="/anket/<?=  $anket['user_id'] ?>"><img src="/img/avatars/<?= explode(",", $anket['photos'])[0] ?>" alt="" class="vacancy__photo"></a>
                                                    <? } ?>
                                                </div>
                                                <div class="vacancy__block_right">
                                                    <div class="vacancy__block_right-info">
                                                        <div class="vacancy__block_right_title">
                                                            <a href="/anket/<?=  $anket['user_id'] ?>" class="vacancy__block_right_title_title">
                                                                <?= $anket['first_name'] ?> <?= $_SESSION['account_type'] == 2 ? $anket['last_name'] : "***" ?>, <?= $anket['age'] ?>
                                                            </a>
                                                            <? 
                                                                if(((int) $anket['last_online'] + (5 * 60))  > (int) time()){
                                                                    echo('<div class="vacancy__block_button online_status_active"><a class="user_status">Сейчас на сайте</a></div>');
                                                                } else {
                                                                    echo('<div class="vacancy__block_button online_status_disabled"><a class="user_status">в сети <strong>'.gmdate("d.m.Y в H:i", $anket['last_online']).'</strong></a></div>');
                                                                }
                                                                $reg_date = explode(":", (new DateTime($anket['reg_date']))->diff(new DateTime)->format(' %d дней: %m месяцев: %y лет'));
                                                                $reg_date = ($reg_date[0] != " 0 дней" ? $reg_date[0] : "").($reg_date[1] != " 0 месяцев" ? $reg_date[1] : "").($reg_date[2] != " 0 лет" ? $reg_date[2] : "");
                                                            ?>

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
                                                    </div>
                                                    <div class="vacancy__working">
                                                        <? if(!isset($_SESSION['account_type'])){ ?> 
                                                        <img src="/img/lock.png" alt="">
                                                        <div class="contact__working">
                                                            Контактная информация доступна только
                                                            работодателям. <a href="/signup.php" class="registration">Зарегистрироваться</a>
                                                        </div>
                                                        <? } else if($_SESSION['account_type'] < 2){ ?> 
                                                        <img src="/img/lock.png" alt="">
                                                        <div class="contact__working">
                                                            Контактная информация в анкетах доступна только работодателям.
                                                        </div>
                                                        <? } else if($_SESSION['account_type'] == 2) { ?>
                                                        <div class="info__world">
                                                            <div class="contact__working-view">
                                                                <a href="/anket/<?=  $anket['user_id'] ?>" target="_blank">Смотреть</a>
                                                            </div>
                                                            <div class="icons__right">
                                                                <!-- <span class="calm"></span> -->
                                                                <?= ($_SESSION['account_type'] == 2 ? '
                                                                <div onclick="setFavoriteAnket(this, '.$anket['id'].')" class="'.($favorites != null ? (in_array(strval($anket['id']), $favorites) ? "active_favorite" : "unactive_favorite") : "unactive_favorite").'">
                                                                    <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <path d="M4.45067 13.9082L11.4033 20.4395C11.6428 20.6644 11.7625 20.7769 11.9037 20.8046C11.9673 20.8171 12.0327 20.8171 12.0963 20.8046C12.2375 20.7769 12.3572 20.6644 12.5967 20.4395L19.5493 13.9082C21.5055 12.0706 21.743 9.0466 20.0978 6.92607L19.7885 6.52734C17.8203 3.99058 13.8696 4.41601 12.4867 7.31365C12.2913 7.72296 11.7087 7.72296 11.5133 7.31365C10.1304 4.41601 6.17972 3.99058 4.21154 6.52735L3.90219 6.92607C2.25695 9.0466 2.4945 12.0706 4.45067 13.9082Z" stroke="var(--main-site-color)" stroke-width="1"></path> </g></svg>

                                                                </div>' : '') 
                                                                ?>
                                                                <?= (!isset($_SESSION['id']) ? '<div onclick="window.location = \'/login.php\'" class="'.$anket['id'].')" class="'.($favorites != null ? (in_array(strval($anket['id']), $favorites) ? "active_favorite" : "unactive_favorite") : "unactive_favorite").'">
                                                                    <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <path d="M4.45067 13.9082L11.4033 20.4395C11.6428 20.6644 11.7625 20.7769 11.9037 20.8046C11.9673 20.8171 12.0327 20.8171 12.0963 20.8046C12.2375 20.7769 12.3572 20.6644 12.5967 20.4395L19.5493 13.9082C21.5055 12.0706 21.743 9.0466 20.0978 6.92607L19.7885 6.52734C17.8203 3.99058 13.8696 4.41601 12.4867 7.31365C12.2913 7.72296 11.7087 7.72296 11.5133 7.31365C10.1304 4.41601 6.17972 3.99058 4.21154 6.52735L3.90219 6.92607C2.25695 9.0466 2.4945 12.0706 4.45067 13.9082Z" fill="#ffffff" fill-opacity="1" stroke="var(--main-site-color)" stroke-width="1.4"></path> </g></svg>
                                                                </div>' : '') ?>
                                                            </div>
                                                        </div>
                                            
                                                        <? } ?>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="line__block">
                                                <div class="line_main"></div>
                                            </div>
                                        </div>
                                    <? } ?>
                                <? } ?>
                            </div>
                            <div class="page_counter_wrapper">
                                <div class="page_counter">
                                    <?
                                        $page_count = ceil($ankets_count / 10);
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
                    </div>
                </div>
            </div>
        </div>
        <? include("../views/footer.php"); ?>
        <script type="text/javascript" src="/js/search.js"></script>
    </div>
</body>

</html>