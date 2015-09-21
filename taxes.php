<?
require_once "db.php";
require_once "accountant.class.php";

//print_r($_POST);

$period_from = $_REQUEST["period_from"];
$period_to = $_REQUEST["period_to"];
$group = $_REQUEST['group'];
$esv_frame = $_REQUEST['esv_frame'];
$language = $_REQUEST['language'];

$accountant = new Accountant();

//$date = new DateTime("2015-04-12");
//echo $accountant->isHoliday($date);
//die;

$accountant->setGroup($group);
$accountant->setPeriod($period_from, $period_to);
$accountant->setEsvFrame($esv_frame);
$accountant->setLanguage($language);

$accountant->printTasksJSON();
//$accountant->printRawTasks();



?>
