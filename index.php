<? 

session_start(); 
require_once("./models/vacancy.php"); 
require_once("./models/user.php"); 
include("./core/db.php");


$sql = User::GetWorkersCountSQL().Vacancy::GetTypesSQL().Vacancy::GetLastVacancySQL();
$current_balance = 0;
if($_SESSION['account_type'] == 2){
    $sql .= User::GetBalanceSQL($_SESSION['id']).User::GetLastWorkerPhotos($link);
    list($users_count, $vacancy_types, $vacancy_list, $current_balance, $workers_photos_list) = MultiQuery($link, $sql);
    $current_balance = $current_balance[0]['count'];
}
else if($_SESSION['account_type'] == 1){
    $sql .= User::GetBalanceSQL($_SESSION['id']);
    $sql .= User::GetFavoriteListSQL($_SESSION['id']);
    list($users_count, $vacancy_types, $vacancy_list, $current_balance, $favorites) = MultiQuery($link, $sql);
    $favorites = json_decode($favorites[0]['favorite']);
    $current_balance = $current_balance[0]['count'];
}
else{
    $sql .= User::GetLastWorkerPhotos($link);
    list($users_count, $vacancy_types, $vacancy_list, $workers_photos_list) = MultiQuery($link, $sql);
}

$workers_photos = [];
if(!empty($workers_photos_list)){
    foreach($workers_photos_list as $users_photo){
        array_push($workers_photos, [$users_photo['user_id'], explode(",", $users_photo['photos'])[0]]);
    }
    if(count($workers_photos) < 22){
        while(count($workers_photos) < 22){
            foreach($workers_photos as $users_photo){
                array_push($workers_photos, $users_photo);
                if(count($workers_photos) >= 22){
                    break;
                }
            }
        }
    }
}

if($favorites == null) $favorites = [];



$users = User::Users($link);

?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width">

    <link rel="stylesheet" type="text/css" href="../bower_components/switchery/css/switchery.min.css">
    <link rel="stylesheet" type="text/css" href="../bower_components/bootstrap-tagsinput/css/bootstrap-tagsinput.css" />
    <link rel="stylesheet" href="../bower_components/nvd3/css/nv.d3.css" type="text/css" media="all">

    <link rel="stylesheet" href="/css/style.css">
    <link rel="icon" href="./favicon.ico" type="image/x-icon">
    <title><? echo($_SETTINGS['site_name_option']); ?></title>
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
        <? if($_SESSION['account_type'] != "1") { ?>
            <div class="people">
                <div class="container" id="people_container">
                    <div class="people__col" onclick="hidePeopleBlock();">
                        <div class="people__col__row">
                            <div class="people__col__text">
                                <span class="text1"><? print_r($users_count[0]['COUNT(*)']); ?></span> ЧЕЛОВЕК ИЩУТ РАБОТУ
                            </div>
                            <div class="people__col__but">
                                <? if(!isset($_SESSION['id'])) echo('<a href="/signup.php" class="but__p">СОЗДАТЬ СВОЮ АНКЕТУ</a>'); ?>
                            </div>
                        </div>
                        <div class="people__col__row_2">
                        <? if(!isset($_SESSION['id']) || $_SESSION['account_type'] == "2") echo('<a href="/anket/">Найти подходящего кандитата</a>'); ?>
                        </div>
                    </div>
                    <div class="people__row" <? if(isset($_SESSION['id'])) echo('style="height: 0;"');  ?>>
                        <div class="people__img">
                            <? foreach($workers_photos as $photo) { ?>
                                <a href="/anket/<?=$photo[0]?>"><img src="/img/avatars/<?=$photo[1]?>" alt="Worker Photo"></a>
                            <? } ?>
                        </div>
                    </div>
                </div>
            </div>  
        <? } ?>
        <div style="display: none" class="worked">
            <!-- ? if(True || $_SESSION['account_type'] == "2") echo('style="display: none"'); ?> -->
            <div class="container">
                <div class="worked__str">
                    <div class="worked__text_1">
                        <a href="#">работодатели</a>
                    </div>
                    <div class="worked__text_2">
                        <a href="#">поиск работодателей</a>
                    </div>  
                </div>
                <div class="worked_img">
                    <div class="worked__img">
                        <a href="#" class="worked__strel"></a>
                        <a class="work1" href="#"><img src="/img/01.png" alt=""></a>
                        <a class="work2" href="#"><img src="/img/02.png" alt=""></a>
                        <a class="work3" href="#"><img src="/img/03.png" alt=""></a>
                        <a class="work4" href="#"><img src="/img/04.png" alt=""></a>
                        <a href="#" class="worked__strel_2"></a>
                    </div>
                </div>
            </div>
        </div>        
        <div class="vacancy">
            <div class="container_vacancies">
                <div class="vacancy_list">
                    <div class="glav__title">
                        <div class="glav__text">
                            <a href="#">Вакансии</a>
                        </div>
                        <div class="glav__text_op">
                            Вакансии
                        </div>
                    </div>
                    <? 
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
                            $gen_logo = "no_photo"; 
                            $post_date = date_parse($item['public_date']);                            

                            echo('
                                <div class="vacancy_item">
                                    <div class="vacancy__row">
                                        <div class="vacancy__data">
                                        '.($_SESSION['account_type'] == 1 ? '<div onclick="setFavoriteVacancy(this, '.$item['id'].')" class="'.(in_array(strval($item['id']), $favorites) ? "active_favorite" : "unactive_favorite").'"></div>' : '').'
                                            <div class="data__text">
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


                                                        <!-- <img style="width: 27px; height: 27px; marhin-left: 7px" src="/img/avatars/no_photo_female.png" alt="" class="vacancy__photo"> --> 
                                                        <!-- <ins class="gender_icon '.$GENDERS[$item['sex']].'"></ins> -->                                                      
                                                    </a>
                                                </div>
                                                <div class="vacancy__desc">
                                                    <p><span class="anket">'.($item['type_id'] == 105 ? $item['type_another'] : $item['type']).'. </span>'.$item['desc_min'].' ('.$item['city'].')</p>
                                                    <p class="time">'.$DURABILITY[$item['time_type']].$employment_days.'</p>
                                                </div>
                                                <div class="vacancy__company">
                                                    <a href="/company/'.$item['owner_id'].'">'.$item['company'].', '.$item['company_type'].'</a>
                                                </div>
                                            </div>
                                            <div class="img__router">
                                                <a href="/company/'.$item['owner_id'].'">
                                                <a href="/company/'.$item['owner_id'].'"><img src="/img/companies/'.$item['logo'].'" alt=""></a>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>'
                            );
                        }
                    ?>
                    <a href="/vacancy" class="finalbut">Посмотреть остальные вакансии</a>
                
                </div>
                <div class="vakancii__two">
                    <div class="ads" id="right_banner"></div>
                    <div class="glav__title__two">
                        <div class="two__text">
                            <a href="#">Поиск ванансий</a>
                        </div>
                    </div>
                    <div class="two__text_ul">
                        <ul class="two__list">
                            <?
                                foreach($vacancy_types as $item){
                                    echo('<a href="/vacancy?vacancy_types[]='.$item['id'].'"><li>'.$item['vacancy_type_name'].'</li></a>');
                                }
                            ?>
                        </ul>
                    </div>
                    <!-- <div class="glav__list">
                        <div class="two__text">
                            <a href="#">Новости и статьи</a>
                        </div>
                    </div>
                    <div class="news">
                        <div class="news__text">
                            <div class="news__title">
                                <a href="#">Оформление мест продаж</a>
                            </div>
                            <div class="news__texted">
                                <a href="#">Джумби, мобили, воблер и другие безмолвные продавцы в местах продаж</a>
                            </div>
                        </div>
                        <div class="news__text_2">
                            <div class="news__title">
                                <a href="#">7 интересных фактов о BTL-рекламе</a>
                            </div>
                            <div class="news__texted">
                                <a href="#">Чем хороша BTL-реклама и почему она "под чертой"</a>
                            </div>
                        </div>
                        <div class="new__img">
                            <div class="new__title__row">
                                <a href="#">Нерадивые сотрудники: поймать и обезвредить. </a>
                            </div>
                            <div class="new__row__img">
                                <img src="/img/soup.png" alt="">
                            </div>
                            <div class="new__row__text">
                                <a href="#">На тренингах промоутерам  часто говорят о том, что  с момента, как они надели промо-форму и приступили к работе на акции, они становятся лицом рекламируемого бренда и должны вести себя в соответствии с этим фактом. Ведь теперь  любое действие, которое совершает промоутер, ассоциируется  с продуктом, который он  рекламирует. </a>
                            </div>
                        </div>
                        <div class="new__button">
                            <a href="#">Все новости и статьи</a>
                        </div>
                    </div> -->
                </div>
            </div>
        </div>
        <? include("./views/footer.php"); ?>
    </div>

</body>
</html>
