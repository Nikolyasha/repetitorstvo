<nav class="pcoded-navbar" pcoded-header-position="relative">
    <div class="sidebar_toggle"><a href="#"><i class="icon-close icons"></i></a></div>
    <div class="pcoded-inner-navbar main-menu" style="display: flex;flex-direction: column;">
		<div class="" >
			<div class="main-menu-header">
				<img class="img-40" src="/assets/images/user.png" alt="User-Profile-Image">
				<div class="user-details">
					<span><? echo($_SESSION['name']); ?></span>
					<span id=""><? echo($ACCOUNT_TYPES[$_SESSION['account_type']]); ?></span>
				</div>
			</div>
		</div>
        <!-- <div class="pcoded-navigatio-lavel" data-i18n="nav.category.navigation" menu-title-theme="theme5">Меню</div> -->
        <ul class="pcoded-item pcoded-left-item">
            <li class="<? if($CURRENT_FILE == 'index') echo("active"); ?>">
                <a href="/lk/">
                    <span class="pcoded-micon"><i class="ti-home"></i></span>
                    <span class="pcoded-mtext" data-i18n="nav.dash.main">Главная страница</span>
                    <span class="pcoded-mcaret"></span>
                </a>
            </li>
            <li class="<? if($CURRENT_FILE == 'anket') echo("active"); ?>">
                <a href="/lk/anket_settings.php">
                    <span class="pcoded-micon"><i class="ti-user"></i></span>
                    <span class="pcoded-mtext" data-i18n="nav.page_layout.main">Моя анкета</span>
                    <? if($user['activation'] == 0) { ?><span class="pcoded-badge label label-danger">!</span><? } ?>
                    <span class="pcoded-mcaret"></span>
                </a>
            </li>
            <li class="<? if($CURRENT_FILE == 'requests') echo("active"); ?>">
                <a href="/lk/requests.php" >
                    <span class="pcoded-micon"><i class="ti-receipt"></i></span>
                    <span class="pcoded-mtext" data-i18n="nav.navigate.main">Мои отклики</span>
                    <span class="pcoded-mcaret"></span>
                </a>
            </li>
            <? if ($_SETTINGS['payment_active_option'] == "true") {?>
                <li class="<? if($CURRENT_FILE == 'buy_money') echo("active"); ?>">
                    <a href="./buy.php" >
                        <span class="pcoded-micon"><i class="ti-shopping-cart"></i></span>
                        <span class="pcoded-mtext" data-i18n="nav.navigate.main">Купить монеты</span>
                        <span class="pcoded-mcaret"></span>
                    </a>
                </li>
            <?}?>
            <li class="<? if($CURRENT_FILE == 'settings') echo("active"); ?>">
                <a href="./account_settings.php">
                    <span class="pcoded-micon"><i class="ti-key"></i></span>
                    <span class="pcoded-mtext" data-i18n="nav.widget.main">Сменить пароль</span>
                    <span class="pcoded-mcaret"></span>
                </a>
            </li>
        </ul>
    </div>
</nav>