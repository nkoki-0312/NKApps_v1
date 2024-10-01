<?php
// INPUT: {userId, groupId}
// OUTPUT: {uniqId, id, name, state, icon_file_name, groupState, myGroupState}

include("/var/www/html/src/php/db.php");

$raw = file_get_contents("php://input");
$ids = json_decode($raw);
$token = $_COOKIE["NKAppsLoginToken"];
$result = [];

$sql = "SELECT uniq_id, id, name, state, icon_file_name FROM users WHERE uniq_id = :uniq_id";
$res = $PDO->prepare($sql);
$params = array(
  ":uniq_id" => $ids[0]
);
$res->execute($params);
foreach( $res as $resFor ) {
  $result = $resFor;
}

$sql = "SELECT state FROM group_relations WHERE user_id = :user_id AND group_id = :group_id";
$res = $PDO->prepare($sql);
$params = array(
  ":user_id" => $ids[0],
  ":group_id" => $ids[1]
);
$res->execute($params);
foreach( $res as $resFor ) {
  $result[5] = $resFor[0];
}

$sql = "SELECT state FROM group_relations WHERE group_id = :group_id AND user_id = ( SELECT user_id FROM login_tokens WHERE token = :token )";
$resGetMyState = $PDO->prepare($sql);
$params = array(
  ":token" => $token,
  ":group_id" => $ids[1]
);
$resGetMyState->execute($params);
foreach( $resGetMyState as $resGetMyStateFor ) {
  $result[6] = $resGetMyStateFor[0];
}

echo json_encode($result);
?>