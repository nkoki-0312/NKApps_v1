<?php
// INPUT: None
// OUTPUT: [uniq_id, group_id, user_id, name, type, amount, clr], [uniq_id, group_id, user_id, name, type, clr]]

include("/var/www/html/src/php/db.php");
include("/var/www/html/src/php/register_log.php");

$token = $_COOKIE["NKAppsLoginToken"];
$result = [[], []];

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

// Get asset cats
$sql = "SELECT uniq_id, group_id, user_id, name, type, amount, clr FROM asset_cats WHERE type <> :type AND ( ( group_id = :group_id AND user_id = :user_id_self ) OR ( group_id IN ( SELECT group_id FROM group_relations WHERE user_id = :user_id ) ) )";
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
  array_push($result[0], $resFor); 

  // Get group name
  if( $resFor[1] == "self" ) {
    $result[0][$count][7] = "自分のみ";
  } else {
    $sql = "SELECT name FROM groups WHERE uniq_id = :uniq_id";
    $resGetGroupName = $PDO->prepare($sql);
    $params = array(
      ":uniq_id" => $resFor[1]
    );
    $resGetGroupName->execute($params);
    foreach( $resGetGroupName as $resGetGroupNameFor ) {
      $result[0][$count][7] = $resGetGroupNameFor[0];
    }
  }

  $count++;
}

// Get bop cats
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
  array_push($result[1], $resFor); 

  // Get group name
  if( $resFor[1] == "self" ) {
    $result[1][$count][6] = "自分のみ";
  } else {
    $sql = "SELECT name FROM groups WHERE uniq_id = :uniq_id";
    $resGetGroupName = $PDO->prepare($sql);
    $params = array(
      ":uniq_id" => $resFor[1]
    );
    $resGetGroupName->execute($params);
    foreach( $resGetGroupName as $resGetGroupNameFor ) {
      $result[1][$count][6] = $resGetGroupNameFor[0];
    }
  }

  $count++;
}

echo json_encode($result);
?>