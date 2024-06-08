<!-- <link rel="stylesheet" type="text/css" href="/css/modal.css">
 -->

<style>
    .action_column{
        text-align: center !important;
        width: 180px;
    }
    .action_column button{
        font-size: 18px;
    }
    .action_column i{
        margin: 0;
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
    .swal-modal .swal-text {
        text-align: center;
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
                        <? if($action == "view") { ?>
                            <h5>Список пользователей сайта</h5>
                        <? } else if($action == "balance") { ?>
                            <h5>Изменение баланса пользователя <?=$element["name"]?> (id<?=$element["id"]?>)</h5>
                        <? } ?>
                    </div>
                    <div class="card-block">
                        <? echo($notify); ?>
                        <? if($action == "view") { ?>
                            <table id="simpletable" class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Имя</th>
                                        <th>Почта</th>
                                        <th>Баланс</th>
                                        <th>Тип аккаунта</th>
                                        <th class="action_column">Действия</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?  
                                    for ($i = 0; $i < count($elements); $i++) {
                                        $item = $elements[$i];
                                        $blocked = false;
                                        $activation = false;
                                        $admin = false;
                                        if($item['admin'] == 1){
                                            $account_type = "<span style='color: red;'>Админ</span>";
                                            $admin = true;
                                        } else if($item['activation'] != ""){
                                            $account_type = "<span style='color: darkgoldenrod;'>Активация ({$ACCOUNT_TYPES[$item['type']]})</span>";
                                            $activation = true;
                                        } else if($item['type'] == 0){
                                            $account_type = "<span style='color: red;'>Бан ({$ACCOUNT_TYPES[$item['base_type']]})</span>";
                                            $blocked = true;
                                        } else {
                                            $account_type = "<span style='color: green;'>{$ACCOUNT_TYPES[$item['type']]}</span>";
                                        }

                                        $edit_link = "";
                                        if(!$activation){
                                            if($item['base_type'] == 1){
                                                foreach($ankets as $anket){
                                                    if($anket["user_id"] == $item['id']){
                                                        $edit_link = "/admin/edit_anket.php?id=".$anket["id"];
                                                        break;
                                                    }
                                                }
                                            } else {
                                                foreach($companies as $company){
                                                    if($company["user_id"] == $item['id']){
                                                        $edit_link = "/admin/edit_company.php?id=".$company["id"];
                                                        break;
                                                    }
                                                }
                                            }
                                        }
                                        $type_user = 'Изменить анкету работника';
                                        if ($item['type'] == 2)
                                            $type_user = 'Изменить компанию работадателя'

                                        ?>

                                        <tr id="<?=$item['id']?>">
                                            <td><?=$item['id']?></td>
                                            <td><?=$item['name']?></td>
                                            <td><?=$item['email']?></td>
                                            <td><?=$item['balance']?> монет</td>
                                            <td><?=$account_type?></td>
                                            <td class="action_column">
                                                <a title="<?= $type_user ?>" target="_blank" <?=$activation?'disabled':"href='$edit_link'"?>>
                                                    <button class="btn btn-info btn-icon <?=$activation?'disabled':''?>"><i class="icofont icofont-edit"></i></button>
                                                </a>
                                                <a title="Изменить баланс" <?=$activation?'disabled':"href='/admin/users.php?balance={$item['id']}'"?>>
                                                    <button class="btn btn-warning btn-icon <?=$activation?'disabled':''?>"><i class="icofont icofont-cur-dollar"></i></button>
                                                </a>
                                                <button title="Активировать пользователя" class="btn <?=$activation?'btn-inverse':'btn-default'?> btn-icon <?=$activation?'':'disabled'?>" onclick="activateUser(<?=$item['id']?>);"><i class="icofont icofont-key"></i></button>
                                                <? if($blocked) { ?>
                                                    <button class="btn btn-primary btn-icon" onclick="setUserStatus(false, <?=$item['id']?>);"><i class="icofont icofont-ui-unlock"></i></button>
                                                <? } else { ?>
                                                    <button title="Заблокировать пользователя" class="btn btn-danger btn-icon <?=($activation || $admin)?'disabled':''?>" <?=($activation || $admin)?"":"onclick='setUserStatus(true, {$item['id']});'"?>><i class="icofont icofont-ui-block"></i></button>
                                                <? } ?>
                                            </td>
                                        </tr>

                                        <?
                                    }
                                    ?>
                                </tbody>
                            </table>
                        <? } else if($action == "balance") { ?>
                            <form method="POST" action="">
                                <input type="hidden" name="action" value="set_balance"/>
                                <input type="hidden" name="user_id" value="<?=((int) $_GET['balance'])?>"/>
                                <input type="hidden" name="token" value="<?=$_SESSION['token']?>"/>
                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-label">Новый баланс</label>
                                    <div class="col-sm-5">
                                        <input id="new_balance_input" name="new_balance" type="number" value="<?=$element["balance"]?>" class="form-control" placeholder="0" min="0" max="1000000" autofocus required>
                                    </div>
                                </div>
                                <p>Текущий баланс: <b><?=$element["balance"]?> монет</b></p>
                                <p><big>Изменение баланса: <b><span id="balance_diff">0</span> монет</b></big></p>
                                <button class="btn btn-success btn-round">Сохранить</button>
                                <a href="users.php"><button type="button" class="btn btn-primary btn-round">Назад</button></a>
                            </form>
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
    let startBalance = <?=$element["balance"]?>;
    $(document).ready(function() {
        // $.noConflict(true);
        table = $('#simpletable').DataTable();
        new_balance_input.oninput = function() {
            balance_diff.innerHTML = ((new_balance_input.value - startBalance) > 0 ? "+" : "") + (new_balance_input.value - startBalance);
        };
    } );
</script>