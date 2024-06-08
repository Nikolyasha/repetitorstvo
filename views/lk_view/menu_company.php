<nav class="pcoded-navbar" pcoded-header-position="relative">
    <div class="sidebar_toggle"><a href="#"><i class="icon-close icons"></i></a></div>
    <div class="pcoded-inner-navbar main-menu" style="display: flex;flex-direction: column;">
		<div class="" >
			<div class="main-menu-header">
				<img class="img-40" src="/assets/images/user.png" alt="User-Profile-Image">
				<div class="user-details">
					<span><? echo($_SESSION['name']); ?></span>
					<span><? echo($ACCOUNT_TYPES[$_SESSION['account_type']]); ?></span>
				</div>
			</div>
		</div>
        <div class="pcoded-navigatio-lavel" data-i18n="nav.category.navigation" menu-title-theme="theme5">Основное</div>
            <ul class="pcoded-item pcoded-left-item">
            <li class="<? if($CURRENT_FILE == 'index') echo("active"); ?>">
                <!-- <a href="javascript:void(0)"> -->
                <a href="./">
                    <span class="pcoded-micon"><i class="ti-home"></i></span>
                    <span class="pcoded-mtext" data-i18n="nav.dash.main">Главная страница</span>
                    <span class="pcoded-mcaret"></span>
                </a>
            </li>
            <!-- <li class="<? if($CURRENT_FILE == 'company_settings') echo("active"); ?>">
                <a href="./company_settings.php">
                    <span class="pcoded-micon"><i class="ti-user"></i></span>
                    <span class="pcoded-mtext" data-i18n="nav.page_layout.main">Настройки компании</span>
                    <span class="pcoded-mcaret"></span>
                </a>
            </li> -->
            <li class="<? if($CURRENT_FILE == 'settings') echo("active"); ?>">
                <a href="./settings.php">
                    <span class="pcoded-micon"><i class="ti-settings"></i></span>
                    <span class="pcoded-mtext" data-i18n="nav.widget.main">Настройки</span>
                    <span class="pcoded-mcaret"></span>
                </a>
            </li>
            <div class="pcoded-navigatio-lavel" data-i18n="nav.category.navigation" menu-title-theme="theme5">Вакансии</div>
            <li class="<? if($CURRENT_FILE == 'vacancies') echo("active"); ?>">
                <a href="vacancies.php" >
                    <span class="pcoded-micon"><i class="ti-write"></i></span>
                    <span class="pcoded-mtext" data-i18n="nav.navigate.main">Список вакансий</span>
                    <span class="pcoded-mcaret"></span>
                </a>
            </li>
            <li class="<? if($CURRENT_FILE == 'create_vacanсy') echo("active"); ?>">
                <a href="vacancies.php?create">
                    <span class="pcoded-micon"><i class="ti-pencil-alt"></i></span>
                    <span class="pcoded-mtext" data-i18n="nav.navigate.main">Новая вакансия</span>
                    <span class="pcoded-mcaret"></span>
                </a>
            </li>
            <li class="<? if($CURRENT_FILE == 'offers') echo("active"); ?>">
                <a href="offers.php" >
                    <span class="pcoded-micon"><i class="ti-user"></i></span>
                    <span class="pcoded-mtext" data-i18n="nav.navigate.main">Отклики</span>
                    <?
                        if($new_offers_count > 0)
                            echo('<span class="pcoded-badge label label-danger">'.$new_offers_count.'</span>');
                    ?>
                    <span class="pcoded-mcaret"></span>
                </a>
            </li>
            <? if ($_SETTINGS['payment_active_option'] == "true") {?>
                <div class="pcoded-navigatio-lavel" data-i18n="nav.category.navigation" menu-title-theme="theme5">Операции</div>
                <li class="<? if($CURRENT_FILE == 'buy_money') echo("active"); ?>">
                    <a href="./buy.php" >
                        <span class="pcoded-micon"><i class="ti-shopping-cart"></i></span>
                        <span class="pcoded-mtext" data-i18n="nav.navigate.main">Купить монеты</span>
                        <span class="pcoded-mcaret"></span>
                    </a>
                </li>
            <?}?>
            <!-- <li class="<? if($CURRENT_FILE == 'settings') echo("active"); ?>">
                <a href="./account_settings.php">
                    <span class="pcoded-micon"><i class="ti-settings"></i></span>
                    <span class="pcoded-mtext" data-i18n="nav.widget.main">Настройки</span>
                    <span class="pcoded-mcaret"></span>
                </a>
            </li> -->
        </ul>
    </div>
</nav>