<?php
// INPUT: uniqId
// OUTPUT: name

include('/var/www/html/src/php/db.php');

$raw = file_get_contents("php://input");
$groupId = json_decode($raw);

$sql = "SELECT name FROM groups WHERE uniq_id = :uniq_id";
$res = $PDO->prepare($sql);
$params = array(
  ":uniq_id" => $groupId
);
$res->execute($params);
foreach( $res as $resFor ) {
  echo json_encode($resFor[0]);
}
?>