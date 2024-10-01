<?php
// INPUT: {groupId, name, id, password, text, usePassword}
// OUTPUT: state
// state: "cannotEditGroup": 編集権がない
//        "success": 成功

include("/var/www/html/src/php/db.php");
include("/var/www/html/src/php/register_log.php");

$raw = file_get_contents("php://input");
$datas = json_decode($raw);
$token = $_COOKIE["NKAppsLoginToken"];
$result = "";

// Check this user can edit group.
$sql = "SELECT count(*) FROM group_relations WHERE group_id = :group_id AND user_id IN ( SELECT user_id FROM login_tokens WHERE token = :token )";
$res = $PDO->prepare($sql);
$params = array(
  ":group_id" => $datas[0],
  ":token" => $token
);
$res->execute($params);
foreach( $res as $resFor ) {
  if( $resFor[0] == 0 ) {
    $result = "cannotEditGroup";
  }
}

// If this use can edit group, edit group.
if( $result == "" ) {
  if( $datas[3] == "" && $datas[5] ) {
    $sql = "UPDATE groups SET id = :id, name = :name, text = :text WHERE uniq_id = :uniq_id";
    $res = $PDO->prepare($sql);
    $params = array(
      ":id" => $datas[2],
      ":name" => $datas[1],
      ":text" => $datas[4],
      ":uniq_id" => $datas[0]
    );
  } else if( $datas[3] == "" && !$datas[5] ) {
    $sql = "UPDATE groups SET id = :id, name = :name, password = :password, text = :text WHERE uniq_id = :uniq_id";
    $res = $PDO->prepare($sql);
    $params = array(
      ":id" => $datas[2],
      ":name" => $datas[1],
      ":password" => "",
      ":text" => $datas[4],
      ":uniq_id" => $datas[0]
    );
  } else {
    $sql = "UPDATE groups SET id = :id, name = :name, password = :password, text = :text WHERE uniq_id = :uniq_id";
    $res = $PDO->prepare($sql);
    $params = array(
      ":id" => $datas[2],
      ":name" => $datas[1],
      ":password" => password_hash($datas[3], PASSWORD_DEFAULT),
      ":text" => $datas[4],
      ":uniq_id" => $datas[0]
    );
  }
  $res->execute($params);

  $sql = "SELECT user_id FROM login_tokens WHERE token = :token";
  $res = $PDO->prepare($sql);
  $params = array(
    ":token" => $token
  );
  $res->execute($params);
  foreach( $res as $resFor ) {
    register_log("edit_group", $datas[0], $resFor[0], "", "");
  }

  $result = "success";
}

echo json_encode($result);
?>