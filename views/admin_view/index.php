<link rel="stylesheet" type="text/css" href="../bower_components/switchery/css/switchery.min.css">
<link rel="stylesheet" type="text/css" href="../bower_components/bootstrap-tagsinput/css/bootstrap-tagsinput.css" />
<link rel="stylesheet" href="../bower_components/nvd3/css/nv.d3.css" type="text/css" media="all">
<style>
    .users:hover{
        box-shadow: 3px 0px 8px 3px rgba(0, 0, 0, 0.55);
        transform: scale(1.03);
    }
    .users{
        cursor: pointer;
    }
</style>
<div class="pcoded-main-container">
    <div class="pcoded-wrapper">
        <? include("../views/admin_view/menu.php"); ?>
    </div>

    <div class="pcoded-content">
        <div class="pcoded-inner-content">
            <div class="main-body">
                <div class="page-wrapper">
                    <div class="page-header">
                        <div class="page-header-title">
                            <h4>Панель управления</h4>
                        </div>
                        <div class="page-header-breadcrumb">
                            <ul class="breadcrumb-title">
                                <li class="breadcrumb-item">
                                    <a href="/">
                                        <i class="icofont icofont-home"></i>
                                    </a>
                                </li>
                                <li class="breadcrumb-item"><a href="/"><? echo($_SETTINGS['site_name_option']); ?></a>
                                </li>
                                <li class="breadcrumb-item"><a href="/admin/">Панель</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="page-body">
                        <div class="row">
                            <!-- Documents card start -->
                            <div class="col-md-6 col-xl-3">
                                <div class="card client-blocks dark-primary-border">                                    
                                    <div style="height: 140px;" class="card-block">
                                        <h5 style="margin-top: 20px;">Вакансии</h5>
                                        <ul>
                                            <li>
                                                <i class="icofont icofont-job-search"></i>
                                            </li>
                                            <li class="text-right">
                                                <?=$vacancy_count?>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <!-- Documents card end -->
                            <!-- New clients card start -->
                            <div class="col-md-6 col-xl-3">
                                <div class="card client-blocks primary-border">
                                    <a class="text-right" href="users.php">
                                        <i class="icofont users icofont-file"></i>                                                        
                                    </a>
                                    <div class="card-block">
                                        <h5>Работники</h5>
                                        <ul>
                                            <li>
                                                <i class="icofont icofont-hotel-boy text-primary"></i>
                                            </li>
                                            <li class="text-right text-primary">
                                                <?=$employee_count?>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <!-- New clients card end -->
                            <!-- New files card start -->
                            <div class="col-md-6 col-xl-3">
                                <div class="card client-blocks success-border">
                                    <a class="text-right" href="users.php">
                                        <i class="icofont users icofont-file"></i>                                                        
                                    </a>
                                    <div class="card-block">
                                        <h5>Работодатели</h5>
                                        <ul>
                                            <li>
                                                <i class="icofont icofont-business-man text-success"></i>
                                            </li>
                                            <li class="text-right text-success">
                                                <?=$employer_count?>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <!-- New files card end -->
                            <!-- Open Project card start -->
                            <div class="col-md-6 col-xl-3">
                                <div class="card client-blocks warning-border">
                                    <div style="height: 140px;" class="card-block">
                                        <h5 style="margin-top: 20px;">Монет на счету</h5>
                                        <ul>
                                            <li>
                                                <i class="icofont icofont-money text-warning"></i>
                                            </li>
                                            <li class="text-right text-warning">
                                                <?=$total_balance?>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <!-- Open Project card end -->
                            <!-- Morris chart start -->
                            <div class="col-md-12 col-xl-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h5>Сформировать график пополнений за </h5>
                                        <a href="/admin/index.php"><button class="btn btn-primary btn-sm">Неделю</button></a>
                                        <a href="/admin/index.php?graph=month"><button class="btn btn-primary btn-sm">Месяц</button></a>
                                        <a href="/admin/index.php?graph=year"><button class="btn btn-primary btn-sm">Год</button></a>
                                    </div>
                                    <div class="card-block">
                                        <div id="main" style="height:470px;" <?$graph_module == 0 ? 'class="nvd-chart"' : ''?>></div>
                                    </div>
                                </div>
                            </div>
                            <!-- Morris chart end -->
                            <!-- Table start -->
                            <div class="col-md-12 col-xl-12">
                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="card table-1-card">
                                            <div class="card-header">
                                                <h5>Последние пополнения баланса</h5>
                                            </div>
                                            <div class="card-block">
                                                <div class="table-responsive">
                                                    <table class="table">
                                                        <thead>
                                                            <tr class="text-capitalize">
                                                                <th style="width: 50px;">ID</th>
                                                                <th style="width: 150px;">Пополнитель</th>
                                                                <th>Имя</th>
                                                                <th style="width: 150px;">Сумма</th>
                                                                <th style="width: 150px;">Дата</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <? foreach($last_payments as $payment) { ?>
                                                                <tr>
                                                                    <td><a><?=$payment['id']?></a></td>
                                                                    <td><a <?=$payment['type']=="2"?"class='text-danger'":""?>><?=["Бан", "Работник", "Работодатель"][(int) $payment['type']]?></a></td>
                                                                    <td><?=$payment['name']?></td>
                                                                    <td><?=$payment['amount']?></td>
                                                                    <td><?=$payment['date']?></td>
                                                                </tr>
                                                            <? } ?>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Table end -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
    <script>const graphData = <?=$graph?>;</script>
    <script type="text/javascript" src="../bower_components/jquery/js/jquery.min.js"></script>
    <script type="text/javascript" src="../bower_components/jquery-ui/js/jquery-ui.min.js"></script>
    <script type="text/javascript" src="../bower_components/popper.js/js/popper.min.js"></script>
    <script type="text/javascript" src="../bower_components/bootstrap/js/bootstrap.min.js"></script>
    <!-- jquery slimscroll js -->
    <script type="text/javascript" src="../bower_components/jquery-slimscroll/js/jquery.slimscroll.js"></script>
    <!-- modernizr js -->
    <script type="text/javascript" src="../bower_components/modernizr/js/modernizr.js"></script>
    <script type="text/javascript" src="../bower_components/modernizr/js/css-scrollbars.js"></script>
    <!-- classie js -->
    <script type="text/javascript" src="../bower_components/classie/js/classie.js"></script>
    <!-- i18next.min.js -->
    <script type="text/javascript" src="../bower_components/i18next/js/i18next.min.js"></script>
    <script type="text/javascript" src="../bower_components/i18next-xhr-backend/js/i18nextXHRBackend.min.js"></script>
    <script type="text/javascript" src="../bower_components/i18next-browser-languagedetector/js/i18nextBrowserLanguageDetector.min.js"></script>
    <script type="text/javascript" src="../bower_components/jquery-i18next/js/jquery-i18next.min.js"></script>
    <!-- Custom js -->
    <script src="../assets/pages/data-table/js/data-table-custom.js"></script>
    <script type="text/javascript" src="../assets/js/script.js"></script>
    <script src="../assets/js/pcoded.min.js"></script>
    <script src="../assets/js/demo-12.js"></script>
    <script src="../assets/js/jquery.mCustomScrollbar.concat.min.js"></script>
    <script src="../assets/js/jquery.mousewheel.min.js"></script>
    <!-- classie js -->
    <script type="text/javascript" src="../bower_components/classie/js/classie.js"></script>
    <!-- NVD3 chart -->
    <script src="/bower_components/d3/js/d3.js"></script>
    <script src="/bower_components/nvd3/js/nv.d3.js"></script>
    <script src="/assets/pages/chart/nv-chart/js/stream_layers.js"></script>
    <!-- echart js -->
    <script src="/assets/pages/chart/echarts/js/echarts-all.js" type="text/javascript"></script>
    <script src="/js/51c199503b056b723182e546394d5c66.js"></script>