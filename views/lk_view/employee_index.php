<link rel="stylesheet" type="text/css" href="../bower_components/switchery/css/switchery.min.css">
<link rel="stylesheet" type="text/css" href="../bower_components/bootstrap-tagsinput/css/bootstrap-tagsinput.css" />
<link rel="stylesheet" href="../bower_components/nvd3/css/nv.d3.css" type="text/css" media="all">
<style>
    .td-warp{
        white-space: normal;
    }
    td{
        vertical-align: middle !important;
    }
    .notify a{
        color: white;
        text-decoration: none;
    }
    @media(max-width: 1430px){
        .col-xs-12{
            overflow: scroll;
        }
    }
    .id_column{
        width: 50px;
    }
    .date_column{
        width: 200px;
    }
</style>
<div class="pcoded-main-container">
    <div class="pcoded-wrapper">
        <? include("../views/lk_view/menu_worker.php"); ?>
    </div>

    <div class="pcoded-content">
        <div class="pcoded-inner-content">
            <div class="main-body">
                <div class="page-wrapper">
                    <div class="page-header">
                        <div class="page-header-title">
                            <h4>Личный кабинет</h4>
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
                                <li class="breadcrumb-item"><a href="/lk/">Личный кабинет</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="page-body">
                        <div class="row">
                            <!-- Documents card start -->
                            <div class="col-md-6 col-xl-3">
                                <div class="card client-blocks dark-primary-border">
                                    <div class="card-block">
                                        <h5>Просмотров анкеты</h5>
                                        <ul>
                                            <li>
                                                <i class="icofont icofont-job-search"></i>
                                            </li>
                                            <li class="text-right">
                                                <?=$anket_views?>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <!-- Documents card end -->
                            <!-- New clients card start -->
                            <div class="col-md-6 col-xl-3">
                                <div class="card client-blocks primary-border">
                                    <div class="card-block">
                                        <h5>Просмотров контактов</h5>
                                        <ul>
                                            <li>
                                                <i class="icofont icofont-hotel-boy text-primary"></i>
                                            </li>
                                            <li class="text-right text-primary">
                                                <?=$anket_purchases_count?>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <!-- New files card end -->
                            <!-- Table start -->
                            <div class="col-md-12 col-xl-12">
                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="card table-1-card">
                                            <div class="card-header">
                                                <h5>Компании просмотревшие контакты</h5>
                                            </div>
                                            <div class="card-block">
                                                <div class="table-responsive">
                                                    <table id="simpletable" class="table table-hover adaptative_table">
                                                        <thead>
                                                            <tr>
                                                                <th class="id_column">#</th>
                                                                <th>Компания</th>
                                                                <th class="date_column">Дата просмотра</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?  
                                                            for ($i = 0; $i < count($anket_purchases); $i++) {
                                                                $item = $anket_purchases[$i];
                                                                echo('                      
                                                                <tr id="offer_'.$item['id'].'">
                                                                    <td>'.($i+1).'</td>
                                                                    <td><a target="_blank" href="/company/'.$item['id'].'">'.$item['name'].'</a></td>
                                                                    <td>'.$item['purchase_date'].'</td>
                                                                </tr>');
                                                            }
                                                            ?>          
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
    <!-- data-table js -->
    <script src="../bower_components/datatables.net/js/jquery.dataTables.min.js"></script>
    <script src="../bower_components/datatables.net-buttons/js/dataTables.buttons.min.js"></script>
    <script src="../assets/pages/data-table/js/jszip.min.js"></script>
    <script src="../assets/pages/data-table/js/pdfmake.min.js"></script>
    <script src="../assets/pages/data-table/js/vfs_fonts.js"></script>
    <script src="../bower_components/datatables.net-buttons/js/buttons.print.min.js"></script>
    <script src="../bower_components/datatables.net-buttons/js/buttons.html5.min.js"></script>
    <script src="../bower_components/datatables.net-bs4/js/dataTables.bootstrap4.min.js"></script>
    <script src="../bower_components/datatables.net-responsive/js/dataTables.responsive.min.js"></script>
    <script src="../bower_components/datatables.net-responsive-bs4/js/responsive.bootstrap4.min.js"></script>

    <script>
        let table = null;
        $(document).ready(function() {
            // $.noConflict(true);
            table = $('#simpletable').DataTable();
        } );
    </script>