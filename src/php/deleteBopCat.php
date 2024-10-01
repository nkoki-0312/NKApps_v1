<?php
// INPUT: uniqId
// OUTPUT: state
// state: "notExistBopCat": 対象の収支カテゴリーが存在しない
//        "cannotDeleteBopCat": 収支カテゴリーの編集権限がない
//        "success": 成功

include("/var/www/html/src/php/db.php");
include("/var/www/html/src/php/register_log.php");

$raw = file_get_contents("php://input");
$bopUniqId = json_decode($raw);
$token = $_COOKIE["NKAppsLoginToken"];
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

// Check this bop cat is exits
$sql = "SELECT count(*) FROM bop_cats WHERE uniq_id = :uniq_id";
$res = $PDO->prepare($sql);
$params = array(
  ":uniq_id" => $bopUniqId
);
$res->execute($params);
foreach( $res as $resFor ) {
  if( $resFor[0] == 0 ) {
    $result = "notExistBopCat";
  }
}

// Check this user can delete
if( $result == "" ) {
  $sql = "SELECT count(*) FROM bop_cats WHERE uniq_id = :uniq_id AND ( ( group_id = :group_id ) AND ( user_id = :user_id_self ) OR ( group_id IN ( SELECT group_id FROM group_relations WHERE user_id = :user_id AND state = :state ) ) )";
  $res = $PDO->prepare($sql);
  $params = array(
    ":uniq_id" => $bopUniqId,
    ":group_id" => "self",
    ":user_id_self" => $userUniqId,
    ":user_id" => $userUniqId,
    ":state" => "admin"
  );
  $res->execute($params);
  foreach( $res as $resFor ) {
    if( $resFor[0] == 0 ) {
      $result = "cannotDeleteBopCat";
    }
  }
}

// Delete bop cats
if( $result == "" ) {
  $sql = "UPDATE bop_cats SET type = :type, update_at = :update_at WHERE uniq_id = :uniq_id";
  $res = $PDO->prepare($sql);
  $params = array(
    ":type" => "deleted",
    ":update_at" => date("Y-m-d H:i:s"),
    ":uniq_id" => $bopUniqId
  );
  $res->execute($params);

  $result = "success";
}

echo json_encode($result);
?>