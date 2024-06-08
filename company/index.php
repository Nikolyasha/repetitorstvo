<?

if(!isset($_GET['id']) || (int) $_GET['id'] < 1){
    header("Location: /");
}
$company_id = (int) $_GET['id'];

$show_contacts = count(explode("/", $_GET['id'])) > 1;

session_start(); 
include("../core/db.php");
require_once($_SERVER['DOCUMENT_ROOT']."/models/company.php");
require_once($_SERVER['DOCUMENT_ROOT']."/models/user.php");

$result = new Company($link, $company_id, $user_id = $_SESSION['id']);

if($result->id == -1){
    header("Location: /company/1");
    die();
}

$sql = User::GetBalanceSQL($_SESSION['id']).User::GetFavoriteListSQL($_SESSION['id']);
list($current_balance, $favorites) = MultiQuery($link, $sql);
$current_balance = $current_balance[0]['count'];
$favorites = json_decode($favorites[0]['favorite']);
if($favorites == null) $favorites = [];

$company_info = $result->info;
$vacancy_list = $result->vacancies;
$filters = json_encode($result->filters);

// print_r($company_info);

function echo_br($text){
    echo(str_replace(array("\r\n", "\r", "\n"), '<br>', $text));
}

?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width">
    <link rel="stylesheet" href="/css/company.css">
    <title><? echo($company_info['company_name']); ?> (<? echo($company_info['company_type']); ?>) <? echo($_SETTINGS['site_name_option']); ?></title>
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
        <div class="title">
            <div class="container">
                <div class="title__body">
                    <div class="title__item1">
                        <div class="title__text1">
                            <? echo($company_info['company_type']); ?>
                        </div>
                        <div class="title__text2">
                            <? echo($company_info['company_name']); ?>
                        </div>
                    </div>
                    <div class="title__item2">
                        <? if($company_info['company_status'] == 1) { ?><a class="prof prof1">профиль активен</a><? } else {  ?>
                            <a class="prof prof2">профиль неактивен</a><? } ?>
                        <? if($_SESSION['admin']) { ?> <a class="prof prof2" href="/admin/edit_company.php?id=<? echo($company_info['id']); ?>">Редактировать компанию</a><? } ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="text">
            <div class="container">
                <div class="text__row">
                    <div class="text__left">
                        <div class="adress">
                            <div class="adress__body">
                                <div class="adress__title">
                                    Адрес 
                                </div>
                                <div class="adress__text">
                                    <div class="city__left">
                                        <div class="city">
                                            <div class="city1">
                                                Город
                                            </div>
                                            <div class="city2">
                                                <? echo($company_info['city']); ?>
                                            </div>
                                        </div>
                                        <div class="info">
                                            <div class="office">
                                                Адрес офиса
                                            </div>
                                            <div class="metro">
                                                <? echo($company_info['office_adress']); ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="adress__img">
                                        <img src="../<?=$show_contacts?"../":""?>img/companies/<? echo($company_info['logo']); ?>" alt="">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="company_info">
                            <div class="container">
                                <div class="company_info__col">
                                    <div class="company_info__title">
                                        <a onclick="showCompanyDescTab();" class="company_info__button active">о компании</a>
                                        <a onclick="showCompanyContactTab();" class="company_info__button">контактная информация</a>
                                    </div>
                                    <div class="company_info_content">
                                        <div class="company_desc_text_block">
                                            <div class="company_desc company_desc_text" id="company_desc">
                                                <? echo_br($company_info['company_desc']); ?>
                                            </div>
                                            <script>
                                                let filters = JSON.parse('<? echo($filters); ?>');
                                                let extraParams = JSON.parse('<? echo($company_info['extra_params']); ?>');
                                                if(extraParams.length != 0){
                                                    document.getElementById("company_desc").innerHTML += "<br><br><b>Дополнительная информация</b><br>";
                                                    filters.forEach(filter => {
                                                        let exit = false;
                                                        extraParams.forEach(param => {
                                                            if(!exit && param['name'] == filter['name']){
                                                                document.getElementById("company_desc").innerHTML += 
                                                                    "<span>" + filter['display'] + "</span>: " + filter['options'].split(";")[param['value']] + "<br>";
                                                                exit = true;
                                                            }
                                                        });
                                                    });
                                                }
                                            </script>
                                            <div class="company_desc_readmore">
                                                <a id="company_desc_readmore_button" onclick="showFullDescription();">Читать полностью</a>
                                                <a id="company_desc_hide_button" onclick="hideFullDescription();">Скрыть</a>
                                            </div>
                                        </div>
                                        <div class="company_desc company_desc_contact">
                                            <? 

                                            if(isset($_SESSION['account_type']) && $_SESSION['account_type'] == 2){
                                                echo('<div class="contact__text_flex">
                                                    <img src="/img/lock.png" alt="">
                                                    <div class="info__contact_onetext">
                                                        Контактная информация работодателей доступна только работникам
                                                    </div>
                                                </div>');
                                            }
                                            else if($company_info['company_status'] == 0){
                                                echo('<div class="contact__text_flex">
                                                    <img src="/img/lock.png" alt="">
                                                    <div class="info__contact_onetext">
                                                        Контактная информация неактивных компаний недоступна
                                                    </div>
                                                </div>');
                                            }
                                            else if(isset($_SESSION['account_type']) && $_SESSION['account_type'] == 1){
                                                if($company_info['purchased'] > 0 || $_SETTINGS['company_contact_price'] < 1 || $company_info['company_owner_id'] == $_SESSION['id']){
                                                    // echo_br($company_info['company_contacts']);
                                                    echo('
                                                    <div class="contact__text_flex">
                                                        <div class="info__contact_type">
                                                            Телефон
                                                        </div>
                                                        <div class="info__contact_link">
                                                            <a style="text-decoration: none;" href="tel:'.$company_info['company_contacts'].'">'.$company_info['company_contacts'].'</a>
                                                        </div>
                                                    </div>'); 
                                                }
                                                else{
                                                    echo('<div class="contact__text_flex">
                                                            <img src="/img/lock.png" alt="">
                                                            <div class="info__contact_onetext">
                                                                <a class="buy_contacts" onclick="buyCompanyContacts('.$company_info['id'].');" >Приобрести контакты компании</a>
                                                            </div>
                                                        </div>');
                                                }
                                            }
                                            else{
                                                echo('<div class="contact__text_flex">
                                                        <img src="/img/lock.png" alt="">
                                                        <div class="info__contact_onetext">
                                                            Чтобы видеть контактную информацию работодателей необходимо создать <a href="/signup.php">анкету</a>
                                                        </div>
                                                    </div>');
                                            }
                                            
                                            ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="vakancyosn">
                            <div class="container">
                                <div class="vakancyosn__col">
                                    <?
                                    if(count($vacancy_list) > 0 && $company_info['company_status'] == 1){
                                        echo('<div class="vakancyosn__title">
                                                основные вакансии
                                            </div>');
                                        foreach ($vacancy_list as $item) {
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
                                            echo('<div class="vacancy">
                                            <div class="container_vacancies">
                                                <div class="vacancy_list">
                                                    <div class="vacancy_item">
                                                        <div class="vacancy__row">
                                                            <div class="vacancy__data">
                                                            <div class="data__text">
                                                                    '.($_SESSION['account_type'] == 1 ? '<div onclick="setFavoriteVacancy(this, '.$item['id'].')" class="'.(in_array(strval($item['id']), $favorites) ? "active_favorite" : "unactive_favorite").'"></div>' : '').'
                                                                    '.(!isset($_SESSION['id']) ? '<div onclick="window.location = \'/login.php\'" class="'.(in_array(strval($anket['id']), $favorites) ? "active_favorite" : "unactive_favorite").'"></div>' : '').'
                                                                    '.(strlen($post_date['day']) == 1 ? "0".$post_date['day'] : $post_date['day']).' '.$MONTHES[$post_date['month']-1].'
                                                                    <span class="splitter"></span>
                                                                    в '.(strlen($post_date['hour']) == 1 ? "0".$post_date['hour'] : $post_date['hour']).':'.(strlen($post_date['minute']) == 1 ? "0".$post_date['minute'] : $post_date['minute']).'
                                                                </div>
                                                            </div>
                                                            <div class="vacancy__blocking">
                                                                <div class="vacancy__block">
                                                                    <div class="vacancy__title">
                                                                        <a href="/vacancy/'.$item['id'].'" class="str__text">
                                                                            '.$item['name'].', '.$salary.' 
                                                                            '.($item['sex'] == 2 ? '<img style="width: 25px; height:25px; margin-left:5px" src="/img/avatars/no_photo_female.png" alt="" class="vacancy__photo">' : '').'
                                                                            '.($item['sex'] == 1 ? '<img style="width: 25px; height:25px; margin-left:5px" src="/img/avatars/no_photo_male.png" alt="" class="vacancy__photo">' : " ").'
                                                                            '.($item['sex'] == 0 ? '<img style="width: 25px; height:25px; margin-left:5px" src="/img/avatars/no_photo_female.png" alt="" class="vacancy__photo"> <img style="width: 25px; height:25px;" src="/img/avatars/no_photo_male.png" alt="" class="vacancy__photo">' : " ").'

                                                                            
                                                                        </a>
                                                                    </div>
                                                                    <div class="vacancy__desc">
                                                                        <p><span class="anket">'.$item['type'].'. </span>'.$item['desc_min'].' ('.$item['city'].')</p>
                                                                        <p class="time">'.$DURABILITY[$item['time_type']].$employment_days.'</p>
                                                                    </div>
                                                                </div>
                                                                <div class="img__router">
                                                                    <img src="/img/companies/'.$item['logo'].'" alt="">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>');

                                        }
                                    }

                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="text__right">
                        <div class="ads" id="right_banner">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <? include("../views/footer.php"); ?>
        <script type="text/javascript" src="/js/company.js"></script> 
        <script type="text/javascript" src="/js/favorite.js"></script> 
        
        <script type="text/javascript">
            let company_price = <? if ($_SETTINGS['payment_active_option'] == 'false') 
                                       echo 0;
                                    else
                                        echo $_SETTINGS['company_contact_price']; ?>

            


            let current_balance = '<?=$current_balance?>';
            <?=$show_contacts?"showCompanyContactTab();":""?>
            let payment_active = '<?= $_SETTINGS['payment_active_option']?>';

            // let company_price = <?=$_SETTINGS['company_contact_price']?>;
        </script>
    </div>
</body>
</html>