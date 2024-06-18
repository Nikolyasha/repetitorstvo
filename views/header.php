<?
$path_access = "/lk";
if(isset($current_balance)){
    $balance_count_label = "";
    switch($current_balance){
        case 1:
            $balance_count_label = "монетка";
            break;
        case 2:
        case 3:
        case 4:
            $balance_count_label = "монеты";
            break;
        default:
            $balance_count_label = "монет";
            break;
    }
}
?>
<div class="top_menu">
    <div class="top_menu--wrapper">
        <div class="top_menu--city">
            <span>г.Алматы</span>
        </div>
        <div class="top_menu--mail">
            <?
            if (!is_null($current_balance)){
            echo ($_SETTINGS['payment_active_option'] == "false" ? '' :
                '<a href="/lk/buy.php" class="money-btn">
                    '.$current_balance.' '.$balance_count_label.' 
                </a>'
                
            );};
            ?>
            <span>somemail@mail.com</span>
        </div>
    </div>
</div>
<div class="header">
    <script type="text/javascript" src="/js/header.js"></script> 
    <? if(isset($_SESSION['account_type']) && $_SESSION['account_type'] == 1) { ?>
        <script type="text/javascript" src="/js/online.js"></script> 
    <? } ?>
    <script>let ctoken = '<?=$_SESSION['token']?>';</script> 
    <script src="https://kit.fontawesome.com/6a81150198.js" crossorigin="anonymous"></script>
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <div class="container_header">
        <?
        
        if(!isset($_SESSION['account_type'])){
            echo('
            <div class="header__logo">
                <div class="header__icon">
                    <a href="/"><h1>Logo</h1></a>
                    <i class="fas fa-bars menu_icon" onclick="mobileMenu();"></i>
                </div>
            </div>
            <div class="header__menu">
                <div class="button--menu"><a href="/anket/" class="button_1">Анкеты</a></div>
                <div class="button--menu"><a href="/vacancy/" class="button_2">Вакансии</a></div>
                <div class="button--menu"><a href="/about.php" class="button_3">О проекте</a></div>
            </div>

            <div class="header__right">
                <div class="buti">
                    <a href="/login.php" class="but3 login--btn">
                        <span class="vhod">Войти</span>
                    </a>
                    <a href="/signup.php" class="but2 reg--button">
                        <img src="/img/menu/registration.svg" alt="">
                        <span class="reg">Регистрация</span>
                    </a>
                </div>
            </div>');
        }
        else if($_SESSION['account_type'] < 2){
            echo('
            <div class="header__logo">
                <div class="header__icon">
                    <a href="/"><h1>Logo</h1></a>
                    <i class="fas fa-bars menu_icon" onclick="mobileMenu();"></i>
                </div>
            </div>
            <div class="header__menu">
                <div class="button--menu"><a href="/vacancy/" class="menu_button">Вакансии</a></div>
                <div class="button--menu"><a href="/vacancy/favorite/" class="menu_button">Отобранные вакансии</a></div>   
            </div>
            <div class="header__right">
                <div class="buti">           
                    <a href="/lk/" class="but2 reg--button">
                        <img src="/img/menu/user.svg" alt="">
                        <span class="reg">Кабинет</span>
                    </a>
                </div>
            </div>'); 
        }
        else{
        
            if($_SESSION['admin'] == 1) { 
                $path_access = "/admin";
                echo('
                <div class="header__left">
                    <div class="header__icon">
                        <a href="/"><img src="/img/logo.png" alt=""></a>
                        <i class="fas fa-bars menu_icon" onclick="mobileMenu();"></i>
                    </div>
                    <div class="button1"><a href="/anket/" class="button_1">Анкеты</a></div>
                    <div class="button2"><a href="/anket/favorite/" class="button_2">Отобранные анкеты</a></div>
                    
                    
                </div>
                <div class="header__right">
                    
                    <div class="buti">
                        <a href="'.$path_access.'" class="but2 reg--button">
                            <img src="/img/join.sv" alt="">
                            <span class="reg">Кабинет</span>
                        </a>
                    </div>
                </div>');
            }
            else {
                // echo $path_access
                echo('
                <div class="header__logo">
                    <div class="header__icon">
                        <a href="/"><h1>Logo</h1></a>
                        <i class="fas fa-bars menu_icon" onclick="mobileMenu();"></i>
                    </div>
                </div>

                <div class="header__menu">
                    <div class="button--menu"><a href="/anket/" class="button_1">Анкеты</a></div>
                    <div class="button--menu"><a href="/anket/favorite/" class="button_2">Отобранные анкеты</a></div>
                    <div class="button--menu"><a href="/lk/offers.php" class="button_3">Отклики</a></div>
                </div>

                <div class="header__right">
                    <div class="buti">
                        <a href="/lk/vacancies.php?create" class=" outline-btn">Разместить вакансию</a>
                        <a href="'.$path_access.'" class="reg--button">
                            <img src="/img/menu/user.svg" alt="">
                            <span class="reg">Кабинет</span>
                        </a>
                    </div>
                </div>'); 
            }
                
            
        }
        
        ?>
    </div>
</div>




