<?php
// INPUT: {userId, groupId, newState}
// OUTPUT: {state}
// state: "notExistAnotherAdminUser": このユーザ以外に管理者のユーザが存在しない
//        "cannotEdit": 操作する権限がない
//        "success": 成功

include("/var/www/html/src/php/db.php");
include("/var/www/html/src/php/register_log.php");

$raw = file_get_contents("php://input");
$datas = json_decode($raw);
$result = "";
$token = $_COOKIE["NKAppsLoginToken"];

$sql = "SELECT user_id FROM login_tokens WHERE token = :token";
$resGetMyUserId = $PDO->prepare($sql);
$params = array(
  ":token" => $token
);
$resGetMyUserId->execute($params);
foreach( $resGetMyUserId as $resGetMyUserIdFor ) {
  $uniqId = $resGetMyUserIdFor[0];
}

// Am I "admin"?
$sql = "SELECT count(*) FROM group_relations WHERE user_id = :user_id AND group_id = :group_id AND state = :state";
$resCheckAdmin = $PDO->prepare($sql);
$params = array(
  ":user_id" => $uniqId,
  ":group_id" => $datas[1],
  ":state" => "admin"
);
$resCheckAdmin->execute($params);
foreach( $resCheckAdmin as $resCheckAdminFor ) {
  if( $resCheckAdminFor[0] == 0 ) {
    $result = "cannotEdit";
  }
}

// If newState is "member", check exist another "admin" user.
if( $datas[2] == "member" ) {
  $sql = "SELECT count(*) FROM group_relations WHERE user_id <> :user_id AND group_id = :group_id AND state = :state";
  $res = $PDO->prepare($sql);
  $params = array(
    ":user_id" => $datas[0],
    ":group_id" => $datas[1],
    ":state" => "admin"
  );
  $res->execute($params);
  foreach( $res as $resFor ) {
    if( $resFor[0] == 0 ) {
      $result = "notExistAnotherAdminUser";
    }
  }
}

if( $result == "" ) {
  $sql = "UPDATE group_relations SET state = :state WHERE user_id = :user_id AND group_id = :group_id";
  $res = $PDO->prepare($sql);
  $params = array(
    ":state" => $datas[2],
    ":user_id" => $datas[0],
    ":group_id" => $datas[1]
  );
  $res->execute($params);

  $result = "success";
  register_log("changeUserGroupState", $datas[0], $uniqId, $datas[1], $datas[2]);
}

echo json_encode($result);

?>