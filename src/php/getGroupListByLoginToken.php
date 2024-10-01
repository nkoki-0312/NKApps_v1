<?php
// INPUT: None
// OUTPUT: [uniqId, id, name]

include("/var/www/html/src/php/db.php");

$token = $_COOKIE["NKAppsLoginToken"];
$result = [];

$sql = "SELECT uniq_id, id, name FROM groups WHERE uniq_id IN ( SELECT group_id FROM group_relations WHERE user_id = ( SELECT user_id FROM login_tokens WHERE token = :token ) )";
$res = $PDO->prepare($sql);
$params = array(
  ":token" => $token
);
$res->execute($params);
foreach( $res as $resFor ) {
  array_push( $result, $resFor );
}

echo json_encode($result);
?>