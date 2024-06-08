<!--  -->

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
</style>
<div class="pcoded-main-container">
    <div class="pcoded-wrapper">
        <? include("../views/admin_view/menu.php"); ?>
    </div>

    <div class="pcoded-content">
        <div class="pcoded-inner-content">
            <div class="main-body">
                <div class="card">
                <div class="card-header">
                    <h5>Список редактируемых страниц сайта</h5>
                </div>
                <div class="card-block">
                    <? echo($notify); ?>
                    <table id="simpletable" class="table table-hover adaptative_table">
                        <thead>
                            <tr>
                                <th style="width: 30px">#</th>
                                <th>Файл</th>
                                <th style="width: 300px">Ссылка</th>
                                <th style="width: 100px">Действия</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?  
                            for ($i = 0; $i < count($pages); $i++) {
                                echo('                      
                                <tr id="page_'.$pages[$i]['id'].'">
                                    <td>'.($i+1).'</td>
                                    <td><b>'.$pages[$i]['name'].'</b></td>
                                    <td><b><a target="_blank" href="/'.$pages[$i]['link'].'.html">/'.$pages[$i]['link'].'.html</a></b></td>
                                    <td class="action_column">
                                        <a title="Изменить страницу" href="?edit='.$pages[$i]['id'].'"><button class="btn btn-warning btn-icon" href="#">
                                            <i class="ti-pencil-alt"></i>
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

<script type="text/javascript" src="assets/js/modal.js"></script>

<script>
    let table = null;
    $(document).ready(function() {
        // $.noConflict(true);
        table = $('#simpletable').DataTable();
    } );
</script>