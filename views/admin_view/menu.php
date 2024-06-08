<nav class="pcoded-navbar" pcoded-header-position="relative">
    <div class="sidebar_toggle"><a href="#"><i class="icon-close icons"></i></a></div>
    <div class="pcoded-inner-navbar main-menu">
		<div class="">
			<div class="main-menu-header">
				<img class="img-40" src="/assets/images/user.png" alt="User-Profile-Image">
				<div class="user-details">
					<span><? echo($_SESSION['name']); ?></span>
					<span id="more-details">Администратор</span>
				</div>
			</div>
		</div>
        <ul class="pcoded-item pcoded-left-item">
            <li class="<? if($CURRENT_FILE == 'index') echo("active"); ?>">
                <a href="/admin/">
                    <span class="pcoded-micon"><i class="ti-home"></i></span>
                    <span class="pcoded-mtext" data-i18n="nav.dash.main">Главная страница</span>
                    <span class="pcoded-mcaret"></span>
                </a>
            </li>
        </ul>
        <div class="pcoded-navigatio-lavel" data-i18n="nav.category.navigation" menu-title-theme="theme5">Параметры сайта</div>
        <ul class="pcoded-item pcoded-left-item">
            <li class="<? if($CURRENT_FILE == 'settings') echo("active"); ?>">
                <a href="/admin/settings.php">
                    <span class="pcoded-micon"><i class="ti-settings"></i></span>
                    <span class="pcoded-mtext" data-i18n="nav.dash.main">Настройки сайта</span>
                    <span class="pcoded-mcaret"></span>
                </a>
            </li>
            <li class="<? if($CURRENT_FILE == 'prices') echo("active"); ?>">
                <a href="/admin/prices.php">
                    <span class="pcoded-micon"><i class="ti-shopping-cart"></i></span>
                    <span class="pcoded-mtext" data-i18n="nav.dash.main">Настройки цен</span>
                    <span class="pcoded-mcaret"></span>
                </a>
            </li>
            <li class="<? if($CURRENT_FILE == 'lists') echo("active"); ?>">
                <a href="/admin/lists.php">
                    <span class="pcoded-micon"><i class="ti-view-list-alt"></i></span>
                    <span class="pcoded-mtext" data-i18n="nav.dash.main">Настройка списков</span>
                    <span class="pcoded-mcaret"></span>
                </a>
            </li>
            <li class="<? if($CURRENT_FILE == 'edit_page') echo("active"); ?>">
                <a href="/admin/edit_page.php">
                    <span class="pcoded-micon"><i class="ti-ruler-pencil"></i></span>
                    <span class="pcoded-mtext" data-i18n="nav.dash.main">Редактор страниц</span>
                    <span class="pcoded-mcaret"></span>
                </a>
            </li>
        </ul>
        <div class="pcoded-navigatio-lavel" data-i18n="nav.category.navigation" menu-title-theme="theme5">Контент</div>
        <ul class="pcoded-item pcoded-left-item">
            <li class="<? if($CURRENT_FILE == 'users') echo("active"); ?>">
                <a href="/admin/users.php">
                    <span class="pcoded-micon"><i class="ti-user"></i></span>
                    <span class="pcoded-mtext" data-i18n="nav.dash.main">Пользователи</span>
                    <span class="pcoded-mcaret"></span>
                </a>
            </li>
        </ul>
        <div class="pcoded-navigatio-lavel" data-i18n="nav.category.navigation" menu-title-theme="theme5">Дополнительно</div>
        <ul class="pcoded-item pcoded-left-item">
            <li class="<? if($CURRENT_FILE == 'filters' || $CURRENT_FILE == 'create_filter') echo("active"); ?>">
                <a href="/admin/filters.php">
                    <span class="pcoded-micon"><i class="ti-filter"></i></span>
                    <span class="pcoded-mtext" data-i18n="nav.dash.main">Фильтры</span>
                    <span class="pcoded-mcaret"></span>
                </a>
            </li>
            <li class="<? if($CURRENT_FILE == 'logs') echo("active"); ?>">
                <a href="/admin/logs.php">
                    <span class="pcoded-micon"><i class="ti-receipt"></i></span>
                    <span class="pcoded-mtext" data-i18n="nav.dash.main">Логи</span>
                    <span class="pcoded-mcaret"></span>
                </a>
            </li>
            <li class="<? if($CURRENT_FILE == 'account_settings') echo("active"); ?>">
                <a href="/admin/admin_account_settings.php">
                    <span class="pcoded-micon"><i class="ti-key"></i></span>
                    <span class="pcoded-mtext" data-i18n="nav.dash.main">Сменить пароль</span>
                    <span class="pcoded-mcaret"></span>
                </a>
            </li>
            <? if($CURRENT_FILE == "edit_vacancy") { ?>
            <li class="active">
                <a href="">
                    <span class="pcoded-micon"><i class="ti-pencil-alt"></i></span>
                    <span class="pcoded-mtext" data-i18n="nav.dash.main">Изменение вакансии</span>
                    <span class="pcoded-mcaret"></span>
                </a>
            </li>
            <? } ?>
            <? if($CURRENT_FILE == "edit_company") { ?>
            <li class="active">
                <a href="">
                    <span class="pcoded-micon"><i class="ti-pencil-alt"></i></span>
                    <span class="pcoded-mtext" data-i18n="nav.dash.main">Изменение компании</span>
                    <span class="pcoded-mcaret"></span>
                </a>
            </li>
            <? } ?>
            <? if($CURRENT_FILE == "edit_anket") { ?>
            <li class="active">
                <a href="">
                    <span class="pcoded-micon"><i class="ti-pencil-alt"></i></span>
                    <span class="pcoded-mtext" data-i18n="nav.dash.main">Изменение анкеты</span>
                    <span class="pcoded-mcaret"></span>
                </a>
            </li>
            <? } ?>
        </ul>
    </div>
</nav>