<?php
// INPUT: {userId, groupId}
// OUTPUT: {state}
// state:  "notExistOtherAdmin": 他の管理者が存在しない
//         "success": 成功

include('/var/www/html/src/php/db.php');
include('/var/www/html/src/php/register_log.php');

$raw = file_get_contents("php://input");
$ids = json_decode($raw);
$token = $_COOKIE["NKAppsLoginToken"];
$result = "";

$sql = "SELECT user_id FROM login_tokens WHERE token = :token";
$resGetUserUniqId = $PDO->prepare($sql);
$params = array(
  ":token" => $token
);
$resGetUserUniqId->execute($params);
foreach( $resGetUserUniqId as $resGetUserUniqIdFor ) {
  $sql = "SELECT count(*) FROM group_relations WHERE user_id <> :user_id AND group_id = :group_id AND state = :state";
  $resCheckOtherAdmin = $PDO->prepare($sql);
  $params = array(
    ":user_id" => $ids[0],
    ":group_id" => $ids[1],
    ":state" => "admin"
  );
  $resCheckOtherAdmin->execute($params);
  foreach( $resCheckOtherAdmin as $resCheckOtherAdminFor ) {
    if( $resCheckOtherAdminFor[0] == 0 ) {
      $result = "notExistOtherAdmin";
    } else {
      $sql = "DELETE FROM group_relations WHERE user_id = :user_id AND group_id = :group_id";
      $res = $PDO->prepare($sql);
      $params = array(
        ":user_id" => $ids[0],
        ":group_id" => $ids[1]
      );
      $res->execute($params);
    
      register_log("disjoin_group", $ids[0], $resGetUserUniqIdFor[0], $ids[1], "");
      $result = "success";
    }
  }
}


echo json_encode($result);
?>