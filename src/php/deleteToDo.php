<?php
// INPUT: todoId
// OUTPUT: { state }
// state: "notExistToDo": 対象の予定が存在しない
//        "cannotDeleteToDo": ユーザに権限がない
//        "success": 成功

include('/var/www/html/src/php/db.php');
include('/var/www/html/src/php/register_log.php');

$raw = file_get_contents("php://input");
$todoId = json_decode($raw);
$token = $_COOKIE["NKAppsLoginToken"];
$result = "";
$groupId = "";
$userState = "";

// Get user uniq id.
$sql = "SELECT user_id FROM login_tokens WHERE token = :token";
$res = $PDO->prepare($sql);
$params = array(
  ":token" => $token
);
$res->execute($params);
foreach( $res as $resFor ) {
  $userUniqId = $resFor[0];
}

// Check this ToDo exist and get group id.
$sql = "SELECT count(*), group_id FROM todos WHERE uniq_id = :uniq_id";
$res = $PDO->prepare($sql);
$params = array(
  ":uniq_id" => $todoId
);
$res->execute($params);
foreach( $res as $resFor ) {
  if( $resFor[0] == 0 ) {
    $result = "notExistToDo";
  } else {
    $groupId = $resFor[1];
  }
}

// Check this user can update
$sql = "SELECT count(*) FROM todos WHERE ( group_id = :group_id AND create_by = :create_by AND uniq_id = :uniq_id_0 ) OR ( uniq_id = :uniq_id_1 AND group_id IN ( SELECT group_id FROM group_relations WHERE user_id = :user_id AND state = :state ) )";
$res = $PDO->prepare($sql);
$params = array(
  ":group_id" => "self",
  ":create_by" => $userUniqId,
  ":uniq_id_0" => $todoId,
  ":uniq_id_1"=> $todoId,
  ":user_id" => $userUniqId,
  ":state" => "admin"
);
$res->execute($params);
foreach( $res as $resFor ) {
  if( $resFor[0] == 0 ) {
    $result = "cannotDeleteToDo";
  }
}

// If this user can delete ToDo, delete ToDo.
if( $result == "" ) {
  $sql = "DELETE FROM todos WHERE uniq_id = :uniq_id";
  $res = $PDO->prepare($sql);
  $params = array(
    ":uniq_id" => $todoId
  );
  $res->execute($params);
  
  $result = "success";
}

echo json_encode($result);
?>