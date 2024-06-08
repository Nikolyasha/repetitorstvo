<?

$CURRENT_FILE = 'logs';
list($success, $error, $notify) = [null, null, ""];

include("init.php");

$action = "view";
$sql = "SELECT * FROM `logs`;";
$elements = mysqli_fetch_all(mysqli_query($link, $sql), MYSQLI_ASSOC);

include("../views/admin_view/header.php"); 

include("../views/admin_view/logs.php");

include("../views/admin_view/footer.php"); 

?>