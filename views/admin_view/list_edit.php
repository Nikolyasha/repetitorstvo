<link rel="stylesheet" type="text/css" href="../bower_components/switchery/css/switchery.min.css">
<link rel="stylesheet" type="text/css" href="../bower_components/bootstrap-tagsinput/css/bootstrap-tagsinput.css" />
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
    #simpletable thead tr th:nth-child(1){
        width: 50px;
    }
    #simpletable thead tr th:nth-child(3){
        width: 120px;
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
                        <h5>Редактирование списка</h5>
                        <span>Осторожно изменяйте эти значения</span> 
                    </div>
                    <div class="card-block">
                        <? echo($notify); ?>
                        <? if(isset($_GET['el'])) { 
                            for ($i = 0; $i < count($elements); $i++) { 
                                $item = $elements[$i];
                                $id_field = array_keys($item)[0];
                                $name_field = array_keys($item)[1];
                                if((int) $_GET['el'] == $item[$id_field]){
                                    ?>
                                    <form method="POST" action="">
                                        <input type="hidden" name="action" value="edit_element"/>
                                        <input type="hidden" name="element_id" value="<?=((int) $_GET['el'])?>"/>
                                        <input type="hidden" name="table" value="<?=htmlspecialchars(mysqli_escape_string($link, $_GET['edit']))?>"/>
                                        <input type="hidden" name="token" value="<?=$_SESSION['token']?>"/>
                                        <input type="hidden" name="value_field" value="<?=$name_field?>"/>
                                        <div class="form-group row">
                                            <label class="col-sm-1 col-form-label">ID</label>
                                            <div class="col-sm-5">
                                                <input name="new_element_id" type="number" value="<?=((int) $_GET['el'])?>" class="form-control" placeholder="0" min="0" max="1000000" autofocus required>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-sm-1 col-form-label">Значение</label>
                                            <div class="col-sm-5">
                                                <input name="<?=$name_field?>" type="text" value="<?=$item[$name_field]?>" class="form-control" required>
                                            </div>
                                        </div>
                                        <button class="btn btn-success btn-round">Сохранить</button>
                                        <a href="lists.php?edit=<?=htmlspecialchars(mysqli_escape_string($link, $_GET['edit']))?>"><button type="button" class="btn btn-primary btn-round">Назад</button></a>
                                    </form>
                                    <?
                                    break;
                                }
                            }
                        } else { ?>
                        <table id="simpletable" class="table table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Название</th>
                                    <th>Действия</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?  
                                for ($i = 0; $i < count($elements); $i++) {
                                    $item = $elements[$i];
                                    $id_field = array_keys($item)[0];
                                    $name_field = array_keys($item)[1];
                                    echo('                      
                                        <tr id="el_'.$item[$id_field].'">
                                            <td>'.$item[$id_field].'</td>
                                            <td class="td-warp">'.$item[$name_field].'</td>
                                            <td class="action_column">
                                                <a title="Изменить элемент" href="?edit='.htmlspecialchars(mysqli_escape_string($link, $_GET['edit'])).'&el='.$item[$id_field].'"><button type="button" class="btn btn-warning btn-icon" href="#">
                                                    <i class="ti-pencil-alt"></i>
                                                </button></a>
                                                <a title="Удалить элемент" onclick="removeElement('.$item[$id_field].', \''.htmlspecialchars(mysqli_escape_string($link, $_GET['edit'])).'\');"><button class="btn btn-danger btn-icon" type="button">
                                                    <i class="icofont icofont-ui-delete"></i>
                                                </button></a>
                                            </td>
                                        </tr>');
                                }
                                ?>
                            </tbody>
                        </table>
                        <form method="POST" action="">
                            <input type="hidden" name="action" value="create_element"/>
                            <input type="hidden" name="token" value="<?=$_SESSION['token']?>"/>
                            <input type="hidden" name="table" value="<?=htmlspecialchars(mysqli_escape_string($link, $_GET['edit']))?>"/>
                            <button class="btn btn-primary btn-round" ><i class="ti-plus"></i> Добавить элемент</button>
                        </from>
                        <? } ?>
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