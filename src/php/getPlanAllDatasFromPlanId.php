<?php
// INPUT: planId
// OUTPUT: [{state}, [orininal_id, uniq_id, group_id, ttl, bg_clr, font_clr, start_at, end_at, detail, create_at, update_at, create_by, update_by, groupName, createUserName, updateUserName, groupState ], memberList]
// state: "cannotGetPlan": 対象外の予定のため、取得できない
//        "notExistPlan" 対象の予定が存在しない
//        "success": 成功

include("/var/www/html/src/php/db.php");

$raw = file_get_contents("php://input");
$planId = json_decode($raw);
$token = $_COOKIE["NKAppsLoginToken"];
$result = ["", [], []];

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

// Check plan exist
$sql = "SELECT count(*) FROM plans WHERE uniq_id = :uniq_id";
$res = $PDO->prepare($sql);
$params = array(
  ":uniq_id" => $planId
);
$res->execute($params);
foreach( $res as $resFor ) {
  if( $resFor[0] == 0 ) {
    $result[0] = "notExistPlan";
  }
}

// Check plan exist
if( $result[0] == "" ) {
  $sql = "SELECT count(*) FROM plans WHERE uniq_id = :uniq_id AND ( ( group_id = :group_id AND create_by = :create_by ) OR ( group_id IN ( SELECT group_id FROM group_relations WHERE user_id = :user_id ) ) )";
  $res = $PDO->prepare($sql);
  $params = array(
    ":uniq_id" => $planId,
    ":group_id"=> "self",
    ":create_by" => $userUniqId,
    ":user_id" => $userUniqId
  );
  $res->execute($params);
  foreach( $res as $resFor ) {
    if( $resFor[0] == 0 ) {
      $result[0] = "cannotGetPlan";
    }
  }
}

// Get plan datas
if( $result[0] == "" ) {
  $result[0] = "success";

  $sql = "SELECT original_id, uniq_id, group_id, ttl, bg_clr, font_clr, start_at, end_at, detail, create_at, update_at, create_by, update_by FROM plans WHERE uniq_id = :uniq_id";
  $res = $PDO->prepare($sql);
  $params = array(
    ":uniq_id" => $planId
  );
  $res->execute($params);
  foreach( $res as $resFor ) {
    $result[1] = $resFor;
  }

  // Get group name
  if( $result[1][2] == "self" ) {
    $result[1][13] = "自分のみ";
  } else {
    $sql = "SELECT name FROM groups WHERE uniq_id = :uniq_id";
    $res = $PDO->prepare($sql);
    $params = array(
      ":uniq_id" => $result[1][2]
    );
    $res->execute($params);
    foreach( $res as $resFor ) {
      $result[1][13] = $resFor[0];
    }
  }

  // Get create user name
  $sql = "SELECT name FROM users WHERE uniq_id = :uniq_id";
  $res = $PDO->prepare($sql);
  $params = array(
    ":uniq_id" => $result[1][11]
  );
  $res->execute($params);
  foreach( $res as $resFor ) {
    $result[1][14] = $resFor[0];
  }
  
  // Get update user name
  $sql = "SELECT name FROM users WHERE uniq_id = :uniq_id";
  $res = $PDO->prepare($sql);
  $params = array(
    ":uniq_id" => $result[1][12]
  );
  $res->execute($params);
  foreach( $res as $resFor ) {
    $result[1][15] = $resFor[0];
  }

  // Get user state to group
  if( $result[1][2] == "self" ) {
    $result[1][16] = "self";
  } else {
    $sql = "SELECT state FROM group_relations WHERE user_id = :user_id AND group_id = :group_id";
    $res = $PDO->prepare($sql);
    $params = array(
      ":user_id" => $userUniqId,
      ":group_id" => $result[1][2]
    );
    $res->execute($params);
    foreach( $res as $resFor ) {
      $result[1][16] = $resFor[0];
    }
  }

  $sql = "SELECT uniq_id, id, name, icon_file_name FROM users WHERE uniq_id IN ( SELECT user_id FROM group_relations WHERE group_id = :group_id )";
  $res = $PDO->prepare($sql);
  $params = array(
    ":group_id" => $result[1][2]
  );
  $res->execute($params);
  foreach( $res as $resFor ) {
    array_push($result[2], $resFor);
  }
}

echo json_encode($result);

?>