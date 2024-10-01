<?php
// INPUT: [todoId, isCheck]
// OUTPUT: state
// state: "notExistTodo": 対象のToDoが存在しない
//        "success": 成功

include("/var/www/html/src/php/db.php");
include("/var/www/html/src/php/register_log.php");

$raw = file_get_contents("php://input");
$todoDatas = json_decode($raw);
$token = $_COOKIE["NKAppsLoginToken"];
$datetime = date("Y-m-d H:i:s");
$result = "";

// Get user uniq id
$sql = "SELECT user_id FROM login_tokens WHERE token = :token";
$res = $PDO->prepare($sql);
$params = array(
  ":token" => $token
);
$res->execute($params);
foreach( $res as $resFor ) {
  $userUniqId = $resFor[0];
}

// Check ToDo exist
$sql = "SELECT count(*) FROM todos WHERE ( uniq_id = :uniq_id ) AND ( ( group_id = :group_id AND create_by = :create_by ) OR ( group_id IN ( SELECT uniq_id FROM groups WHERE uniq_id IN ( SELECT group_id FROM group_relations WHERE user_id = :user_id AND state = :state ) ) ) )";
$res = $PDO->prepare($sql);
$params = array(
  ":uniq_id" => $todoDatas[0],
  ":group_id" => "self",
  ":create_by" => $userUniqId,
  ":user_id" => $userUniqId,
  ":state" => "admin"
);
$res->execute($params);
foreach( $res as $resFor ) {
  if( $resFor[0] == 0 ) {
    $result = "notExistTodo";
  }
}

// Update todo state
if( $result == "" ) {
  $sql = "UPDATE todos SET is_checked = :is_checked, check_at = :check_at, check_by = :check_by WHERE uniq_id = :uniq_id";
  $res = $PDO->prepare($sql);
  $params = array(
    ":is_checked" => $todoDatas[1],
    ":check_at" => $datetime,
    ":check_by" => $userUniqId,
    ":uniq_id" => $todoDatas[0]
  );
  $res->execute($params);

  $result = "success";
}

echo json_encode($result);
?>