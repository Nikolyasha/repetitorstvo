<?
session_start(); 
include("../core/db.php");
require_once($_SERVER['DOCUMENT_ROOT']."/models/vacancy.php");
require_once($_SERVER['DOCUMENT_ROOT']."/models/company.php");
require_once($_SERVER['DOCUMENT_ROOT']."/models/user.php");

if($_SESSION['account_type'] != 1){
    header("Location: /vacancy/"); die();
}
$sql = Vacancy::GetTypesSQL().Company::GetCityListSQL().User::GetFavoriteListSQL($_SESSION['id']).User::GetBalanceSQL($_SESSION['id']);;
list($vacancy_types, $cities, $favorites, $current_balance) = MultiQuery($link, $sql);
$favorites = json_decode($favorites[0]['favorite']);
$current_balance = $current_balance[0]['count'];

$offset = ((int) $_GET['page']) * 10;
if($offset == "0") $offset = "";
else $offset = ",".$offset;

$order_by = "vacancies.id DESC";
if(!empty($_GET['order'])){
    switch($_GET['order']){
        case "old":
            $order_by = "vacancies.id ASC"; break;
        default:
            $order_by = "vacancies.id DESC"; break;
    }
}

list($vacancies, $vacancy_count) = MultiQuery($link, Vacancy::GetVacancyFavoriteListSQL($order_by, $favorites, $offset));
$vacancy_count = $vacancy_count[0]['vacancies_count']; 

?>
<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width">
    <link rel="stylesheet" href="/css/vacancies.css">
    <title>Отобарнные ваканси на 
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
                            <div class="vakancy__public-container">
                                <div class="vakancy__public">
                                     <?
                                     if(is_null($vacancy_count)){
                                        echo(" Вакансий не найдено");
                                     }else{
                                     ?>
                                    Найдено <? echo $vacancy_count;
                                    switch($vacancy_count){
                                        case 1: echo(" вакансия"); break;
                                        case 2: case 3: case 4:  echo(" вакансии"); break;
                                        default: echo(" вакансий"); break;
                                    } }?> 
                                </div>
                                <div class="underline">
                                    <div class="main--line"></div>
                                    <div class="small--line"></div>
                                </div>                                                
                            </div>  
                         </div>
                        <!-- <div class="vakancy__main">
                            <div class="vakancy__sorting">
                                сортировка: <a onclick="changeSort('<?= $_GET['order'] == "old" ? "new" : "old" ?>');"><?= $_GET['order'] == "old" ? "Сначала старые" : "Сначала новые" ?></a>
                            </div>
                        </div> -->
                    </div>
                </div>
            </div>
            <div class="vacancy__body">
                <div class="container">
                    <div class="vacancy__row">
                        <div class="text__left" >
                            <div class="wrapper-text__left" style="margin-left:0;">
                            <? 
                            if(is_null($vacancies)){ ?>
                                <div class="vacancy__block no_vacancies">
                                    Список отобранных вакансий пуст<br>Добавляйте сюда понравившиеся вакансии нажатием сердечка
                                </div>
                            <? }
                            else {
                                foreach ($vacancies as $key=>$item) {
                                    $class = ($key === 0 && count($vacancies) === 1) ? 'last' : (($key === count($vacancies) - 1) ? 'last' : '');
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
                                    <div class="vacancy__block '.$class.'">
                                    
                                        <div class="wrapper_block">
                                            <div class="vakancy__block_right">
                                                <div class="vakancy__block_right_text">
                                                    <div class="vakancy__block_right_text_title">
                                                        <a href="/vacancy/'.$item['id'].'">'.$item['name'].', '.$salary.'
                                                        '.($item['sex'] == 2 ? '<img style="width: 25px; height:25px; margin-left:7px" src="/img/avatars/no_photo_female.png" alt="" class="vacancy__photo">' : '').'
                                                        '.($item['sex'] == 1 ? '<img style="width: 25px; height:25px; margin-left:7px" src="/img/avatars/no_photo_male.png" alt="" class="vacancy__photo">' : " ").'
                                                        '.($item['sex'] == 0 ? '<img style="width: 25px; height:25px; margin-left:7px" src="/img/avatars/no_photo_female.png" alt="" class="vacancy__photo"> <img style="width: 25px; height:25px;" src="/img/avatars/no_photo_male.png" alt="" class="vacancy__photo">' : " ").'
                                                        
                                                        </a>
                                                    </div>
                                                    <div class="vakancy__block_right_text_body">
                                                        <span>'.$item['type'].'.</span>'.$item['desc_min'].' ('.$item['city'].')
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
                                            <div class="button_footer">
                                            '.($_SESSION['account_type'] != 2 ? '
                                                <div class="wrap__footer--buttons">
                                                    ' : '' ).'
                                                    '.($_SESSION['account_type'] == 1 ? '<div onclick="setFavoriteVacancy(this, '.$item['id'].')" class="'.(in_array(strval($item['id']), $favorites) ? "active_favorite" : "unactive_favorite").'" style="margin-left:0;">
                                                        <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <path d="M4.45067 13.9082L11.4033 20.4395C11.6428 20.6644 11.7625 20.7769 11.9037 20.8046C11.9673 20.8171 12.0327 20.8171 12.0963 20.8046C12.2375 20.7769 12.3572 20.6644 12.5967 20.4395L19.5493 13.9082C21.5055 12.0706 21.743 9.0466 20.0978 6.92607L19.7885 6.52734C17.8203 3.99058 13.8696 4.41601 12.4867 7.31365C12.2913 7.72296 11.7087 7.72296 11.5133 7.31365C10.1304 4.41601 6.17972 3.99058 4.21154 6.52735L3.90219 6.92607C2.25695 9.0466 2.4945 12.0706 4.45067 13.9082Z" stroke="var(--main-site-color)" stroke-width="1"></path> </g></svg>

                                                    </div> </div>' : '').'
                                                    '.(!isset($_SESSION['id']) ? '<div onclick="window.location = \'/login.php\'" class="'.(in_array(strval($anket['id']), $favorites) ? "active_favorite" : "unactive_favorite").'">
                                                        <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <path d="M4.45067 13.9082L11.4033 20.4395C11.6428 20.6644 11.7625 20.7769 11.9037 20.8046C11.9673 20.8171 12.0327 20.8171 12.0963 20.8046C12.2375 20.7769 12.3572 20.6644 12.5967 20.4395L19.5493 13.9082C21.5055 12.0706 21.743 9.0466 20.0978 6.92607L19.7885 6.52734C17.8203 3.99058 13.8696 4.41601 12.4867 7.31365C12.2913 7.72296 11.7087 7.72296 11.5133 7.31365C10.1304 4.41601 6.17972 3.99058 4.21154 6.52735L3.90219 6.92607C2.25695 9.0466 2.4945 12.0706 4.45067 13.9082Z" fill="#ffffff" fill-opacity="1" stroke="var(--main-site-color)" stroke-width="1.4"></path> </g></svg>

                                                    </div>
                                                    
                                                </div>
                                                <div class="vacancy__block_left_where">
                                                    '.(strlen($post_date['day']) == 1 ? "0".$post_date['day'] : $post_date['day']).' '.$MONTHES[$post_date['month']-1].'
                                                </div>
                                                ' : '').'
                                                </div>
                                                
                                            </div>
                                            <div class="line__block"><div class="line_main"></div></div>
                                        </div>
                                        
                                        ');
                                }
                            }
                            ?>
                            
                            </div>
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