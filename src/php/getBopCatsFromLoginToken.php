<?php
// INPUT: None
// OUTPUT: [uniq_id, group_id, user_id, name, type, clr, group_name, user_state]
//

include("/var/www/html/src/php/db.php");
include("/var/www/html/src/php/register_log.php");

$token = $_COOKIE["NKAppsLoginToken"];
$result = [];

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

// Get bop categorys
$sql = "SELECT uniq_id, group_id, user_id, name, type, clr FROM bop_cats WHERE type <> :type AND ( ( group_id = :group_id AND user_id = :user_id_self ) OR ( group_id IN ( SELECT group_id FROM group_relations WHERE user_id = :user_id ) ) )";
$res = $PDO->prepare($sql);
$params = array(
  ":type" => "deleted",
  ":group_id" => "self",
  ":user_id_self" => $userUniqId,
  ":user_id" => $userUniqId
);
$res->execute($params);
$count = 0;
foreach( $res as $resFor ) {
  $result[$count] = $resFor;

  if( $resFor[1] == "self" ) {
    $result[$count][6] = "自分のみ";
    $result[$count][7] = "admin";
  } else {
    $sql = "SELECT name FROM groups WHERE uniq_id = :uniq_id";
    $resGetGroupName = $PDO->prepare($sql);
    $params = array(
      ":uniq_id" => $resFor[1]
    );
    $resGetGroupName->execute($params);
    foreach( $resGetGroupName as $resGetGroupNameFor ) {
      $result[$count][6] = $resGetGroupNameFor[0];
    }

    $sql = "SELECT state FROM group_relations WHERE group_id = :group_id AND user_id = :user_id";
    $resGetState = $PDO->prepare($sql);
    $params = array(
      ":group_id" => $resFor[1],
      ":user_id" => $userUniqId
    );
    $resGetState->execute($params);
    foreach( $resGetState as $resGetStateFor ) {
      $result[$count][7] = $resGetStateFor[0];
    }
  }

  $count++;
}

echo json_encode($result);
?>