<link rel="stylesheet" type="text/css" href="../bower_components/switchery/css/switchery.min.css">
<link rel="stylesheet" type="text/css" href="../bower_components/bootstrap-tagsinput/css/bootstrap-tagsinput.css" />
<link rel="stylesheet" href="../bower_components/nvd3/css/nv.d3.css" type="text/css" media="all">
<style>
    .action_column{
        text-align: center !important;
    }
    .action_column button{
        font-size: 16px;
    }
    .action_column i{
        padding-left: 5px;
    }
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
    .company_response{
        overflow: hidden;
        height: 18px;    
    }
    .company_response_title{
        cursor: pointer;
    }
    .vacancy:hover{
        box-shadow: 3px 0px 8px 3px rgba(0, 0, 0, 0.55);
        transform: scale(1.03);
    }
    .offer:hover{
        box-shadow: 3px 0px 8px 3px rgba(0, 0, 0, 0.55);
        transform: scale(1.03);
    }
    .new-offer:hover{
        box-shadow: 3px 0px 8px 3px rgba(0, 0, 0, 0.55);
        transform: scale(1.03);
    }
    .views-vacancy:hover{
        box-shadow: 3px 0px 8px 3px rgba(0, 0, 0, 0.55);
        transform: scale(1.03);
    }
    .vacancy, .offer, .new-offer, .views-vacancy{
        cursor: pointer;
    }
    .new-offer-cell{
        background-color: #F1C40F;
    }
</style>
<div class="pcoded-main-container">
    <div class="pcoded-wrapper">
        <? include("../views/lk_view/menu_company.php"); ?>
    </div>

    <div class="pcoded-content">
        <div class="pcoded-inner-content">
            <div class="main-body">
                <div class="page-wrapper">
                    <div class="page-header">
                        <div class="page-header-title">
                            <h4>Личный кабинет работодателя</h4>
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
                            
                                    <div class="card client-blocks dark-primary-border ">
                                        <a class="text-right" href="../../lk/vacancies.php">
                                            <i class="icofont vacancy icofont-file"></i>                                                        
                                        </a> 

                                        <div  class="card-block">                                        
                                            <h5>Вакансии</h5>
                                                                                       
                                            <ul>
                                                <li>
                                                    <i class="icofont icofont-job-search"></i>
                                                    
                                                </li>
                                                <li class="text-right">
                                                    <?=$vacancies_count?>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                
                                
                            </div>
                            <!-- Documents card end -->
                            <!-- New clients card start -->
                            <div class="col-md-6 col-xl-3">
                                <!-- <a href="../../lk/offers.php"> -->
                                    <div class="card client-blocks primary-border ">
                                        <a class="text-right" href="../../lk/offers.php">
                                            <i class="icofont offer icofont-file"></i>                                                        
                                        </a>
                                        <div class="card-block">
                                            <h5>Отклики</h5>
                                            <ul>
                                                <li>
                                                    <i class="icofont icofont-hotel-boy text-primary"></i>
                                                </li>
                                                <li class="text-right text-primary">
                                                    <?=$offers_count?>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                <!-- </a> -->
                                
                            </div>
                            <div class="col-md-6 col-xl-3">
                                <div class="card client-blocks warning-border ">
                                <a class="text-right" href="../../lk/offers.php">
                                            <i class="icofont new-offer icofont-file"></i>                                                        
                                        </a>
                                    <div class="card-block">
                                        <h5>Новых откликов</h5>
                                        <ul>
                                            <li>
                                                <i class="icofont icofont-warning text-warning"></i>
                                            </li>
                                            <li class="text-right text-warning">
                                                <?=$new_offers_count?>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <!-- New clients card end -->
                            <!-- New files card start -->
                            <div class="col-md-6 col-xl-3" style="margin-top: px;">
                                <div class="card client-blocks success-border" >
                                    <div style="height: 140px;" class="card-block" >
                                        <h5 style="margin-top: 15px;">Просмотров вакансий</h5>
                                        <ul>
                                            <li>
                                                <i class="icofont icofont-eye-alt text-success"></i>
                                            </li>
                                            <li class="text-right text-success">
                                                <?=$vacancies_views?>
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
                                                <h5>Последние отклики</h5>
                                            </div>
                                            <div class="card-block">
                                                <div class="table-responsive">
                                                    <table id="simpletable" class="table table-hover adaptative_table">
                                                        <thead>
                                                            <tr>
                                                                <th>#</th>
                                                                <th>Отправитель</th>
                                                                <th>Вакансия</th>
                                                                <th>Содержание</th>
                                                                <th>Дата отправки</th>
                                                                <th>Статус</th>
                                                                <th>Действия</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?  
                                                            for ($i = 0; $i < count($responses); $i++) {
                                                                $item = $responses[$i];
                                                                if ($item['status'] == 0)
                                                                    $cell = "new-offer-cell";
                                                                else
                                                                    $cell = "";
                                                                echo('                      
                                                                <tr class="'.$cell.'" id="offer_'.$item['id'].'">
                                                                    <td>'.($i+1).'</td>
                                                                    <td>'.$item['worker_name']. '</td>
                                                                    <td><a target="blank" href="/vacancy/'.$item['vacancy_id'].'">'.$item['vacancy_name'].'</a></td>
                                                                    '.(
                                                                        ($item['status'] != 0 && strlen($item['response']) != 0) ?
                                                                        '<td class="td-warp">'.$item['offer'].'<br><br><div class="company_response"><b class="company_response_title" onclick="openCompanyResponse(event);">Ваш ответ: ...</b><br>'.str_replace(array("\r\n", "\r", "\n"), '<br>', $item['response']).'</div></td>' :
                                                                        '<td class="td-warp">'.$item['offer'].'</td>'
                                                                    ).'
                                                                    <td>'.$item['date'].'</td>
                                                                    <td class="action_column">'.$OFFER_STATUS[$item['status']].'</td>
                                                                    <td class="action_column">
                                                                        <a title="К анкете" target="blank" href="/anket/'.$item['user_id'].'"><button class="btn btn-info btn-icon">
                                                                            <i class="icofont icofont-user-alt-3"></i>
                                                                        </button></a>
                                                                        '.(
                                                                            $item['status'] != 0 ?
                                                                                '<a><button class="btn btn-success btn-icon disabled">
                                                                                    <i  class="icofont icofont-reply"></i>
                                                                                </button></a>
                                                                                <a title="Удалить отклик" onclick="removeOffer('.$item['id'].', '.$item['vacancy_id'].');"><button class="btn btn-danger btn-icon">
                                                                                    <i class="icofont icofont-ui-delete"></i>
                                                                                </button></a>' :
                                                                                '<a title="Ответить" href="offers.php?reply='.$item['id'].'&vacancy='.$item['vacancy_id'].'"><button class="btn btn-success btn-icon">
                                                                                    <i class="icofont icofont-reply"></i>
                                                                                </button></a>
                                                                                <a title="Отклонить" onclick="rejectOffer('.$item['id'].', '.$item['vacancy_id'].');"><button class="btn btn-danger btn-icon">
                                                                                    <i class="icofont icofont-ui-close"></i>
                                                                                </button></a>'
                                                                        ).'
                                                                    </td>
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