<?php
// INPUT: groupId
// OUTPUT: {state}
// state: "notExistGroupId": グループIDが存在しない
//        "alreadyJoin": すでにグループに参加している
//        "success": 正しい

include("/var/www/html/src/php/db.php");
include("/var/www/html/src/php/register_log.php");

$raw = file_get_contents("php://input");
$groupId = json_decode($raw);
$token = $_COOKIE["NKAppsLoginToken"];
$datetime = date("Y-m-d H:i:s");
$result = "";

$sql = "SELECT user_id FROM login_tokens WHERE token = :token";
$resGetUniqId = $PDO->prepare($sql);
$params = array(
  ":token" => $token
);
$resGetUniqId->execute($params);
foreach( $resGetUniqId as $resGetUniqIdFor ) {
  // Check already join group
  $sql = "SELECT count(*) FROM group_relations WHERE user_id = :user_id AND group_id = ( SELECT uniq_id FROM groups WHERE id = :id )";
  $resCheckAlreadyJoinGroup = $PDO->prepare($sql);
  $params = array(
    ":id" => $groupId,
    ":user_id" => $resGetUniqIdFor[0]
  );
  $resCheckAlreadyJoinGroup->execute($params);
  foreach( $resCheckAlreadyJoinGroup as $resCheckAlreadyJoinGroupFor ) {
    if( $resCheckAlreadyJoinGroupFor[0] != 0 ) {
      $result = "alreadyJoin";
    }
  }

  // Check exist group id
  $sql = "SELECT count(*), uniq_id, name, password FROM groups WHERE id = :id";
  $resCheckExistGroupId = $PDO->prepare($sql);
  $params = array(
    ":id" => $groupId
  );
  $resCheckExistGroupId->execute($params);
  foreach( $resCheckExistGroupId as $resCheckExistGroupIdFor ) {
    if( $resCheckExistGroupIdFor[0] == 0 ) {
      $result = "notExistGroupId";
    } else {
      if( $result == "" ) {
        $result = $resCheckExistGroupIdFor;
      }
    }
  }
}

echo json_encode($result);
?>