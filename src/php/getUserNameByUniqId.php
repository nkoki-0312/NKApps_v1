<?php
// INPUT: uniqId
// OUTPUT: name

include("/var/www/html/src/php/db.php");

$raw = file_get_contents("php://input");
$uniq_id = json_decode($raw);

$sql = "SELECT name FROM users WHERE uniq_id = :uniq_id";
$res = $PDO->prepare($sql);
$params = array(
  ":uniq_id" => $uniq_id
);
$res->execute($params);
foreach( $res as $resFor ) {
  echo json_encode($resFor[0]);
}
?>