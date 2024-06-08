<nav class="pcoded-navbar" pcoded-header-position="relative">
    <div class="sidebar_toggle"><a href="#"><i class="icon-close icons"></i></a></div>
    <div class="pcoded-inner-navbar main-menu" style="display: flex;flex-direction: column;">
		<div class="" >
			<div class="main-menu-header">
				<img class="img-40" src="/assets/images/user.png" alt="User-Profile-Image">
				<div class="user-details">
					<span><? echo($_SESSION['name']); ?></span>
					<span id="more-details"><? echo($ACCOUNT_TYPES[$_SESSION['account_type']]); ?></span>
				</div>
			</div>

			<div class="main-menu-content">
				<ul>
					<li class="more-details">
						<a href="#!"><i class="ti-settings"></i>Настройки</a>
						<a href="/logout.php"><i class="ti-layout-sidebar-left"></i>Выход</a>
					</li>
				</ul>
			</div>
		</div>
        <div class="pcoded-navigatio-lavel" data-i18n="nav.category.navigation" menu-title-theme="theme5">Меню</div>
        <ul class="pcoded-item pcoded-left-item">
            <li class="active">
                <a href="javascript:void(0)">
                    <span class="pcoded-micon"><i class="ti-home"></i></span>
                    <span class="pcoded-mtext" data-i18n="nav.dash.main">Главная страница</span>
                    <span class="pcoded-mcaret"></span>
                </a>
            </li>
            <li class=" ">
                <a href="./account_settings.php">
                    <span class="pcoded-micon"><i class="ti-settings"></i></span>
                    <span class="pcoded-mtext" data-i18n="nav.widget.main">Настройки</span>
                    <span class="pcoded-mcaret"></span>
                </a>
            </li>
        </ul>
    </div>
</nav>