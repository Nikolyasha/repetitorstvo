<!-- <link rel="stylesheet" type="text/css" href="/css/modal.css">
 -->

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
</style>
<div class="pcoded-main-container">
    <div class="pcoded-wrapper">
        <? switch($_SESSION['account_type']){
            case 1:
                include("../views/lk_view/menu_worker.php"); 
                break;
            case 2:
                include("../views/lk_view/menu_company.php"); 
                break;
            default:
                include("../views/lk_view/menu_hide.php"); 
                break;
        }
        ?>
    </div>

    <div class="pcoded-content">
        <div class="pcoded-inner-content">
            <div class="main-body">
                <div class="card">
                    <div class="card-header">
                        <h5>Список вакансий</h5>
                        <span>Управляйте ими кнопками справа</span>
                    </div>
                    <div class="card-block">
                        <? echo($notify); ?>
                        <table id="simpletable" class="table table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Название</th>
                                    <th>Краткое описание</th>
                                    <th>Зарплата</th>
                                    <th>Мест</th>
                                    <th>Отклики</th>
                                    <th>Просмотры</th>
                                    <th>Опубликовано</th>
                                    <th>Действия</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?  
                                for ($i = 0; $i < count($vacancy_list); $i++) {
                                    $item = $vacancy_list[$i];
                                    $salary = $item['salary_per_hour']." тг/час";
                                    if($item['salary_per_hour'] == 0){
                                        if($item['salary_per_day'] == 0){
                                            $salary = $item['salary_per_month']." тг/месяц";
                                        }
                                        else{
                                            $salary = $item['salary_per_day']." тг/день";
                                        }
                                    }
                                    echo('                      
                                        <tr id="vacancy_'.$item['id'].'">
                                            <td>'.($i+1).'</td>
                                            <td class="td-warp"><a target="blank" href="/vacancy/'.$item['id'].'"><b>'.$item['name'].'</b></a></td>
                                            <td class="td-warp">'.$item['desc_min'].'</td>
                                            <td>'.$salary.'</td>
                                            <td>'.$item['workplace_count'].'</td>
                                            <td>'.$response_count[$i]['resp_count'].(
                                                (int) $response_count[$i]['new_resp_count'] == 0 ? "" : " (".$response_count[$i]['new_resp_count'].")"
                                            ).'</td>
                                            <td>'.$item['views'].'</td>
                                            <td>'.$item['public_date'].'</td>
                                            <td class="action_column">
                                                '.(
                                                    ((int) $response_count[$i]['resp_count']) > 0 ? '<a title="Просмотреть" href="?view='.$item['id'].'"><button class="btn btn-success btn-icon">
                                                    <i class="icofont icofont-eye-alt"></i>' : '<a><button class="btn btn-success btn-icon disabled">
                                                    <i class="icofont icofont-eye-alt"></i>'
                                                ).'
                                                </button></a>
                                                <a title="Изменить вакансию" href="?edit='.$item['id'].'"><button class="btn btn-warning btn-icon" href="#">
                                                    <i class="ti-pencil-alt"></i>
                                                </button></a>
                                                <a title="Удалить вакансию" onclick="removeVacancy('.$item['id'].');"><button class="btn btn-danger btn-icon" href="#">
                                                    <i class="icofont icofont-ui-delete"></i>
                                                </button></a>
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

<script type="text/javascript" src="../assets/js/modal.js"></script>

<script src="/js/vacancy_list.js"></script>

<script>
    let table = null;
    $(document).ready(function() {
        // $.noConflict(true);
        table = $('#simpletable').DataTable();
    } );
</script>