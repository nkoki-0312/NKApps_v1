<?php
// INPUT: bop_id
// OUTPUT: [state, [uniq_id, asset_cat_id, bop_cat_id, group_id, type, amount, detail, happen_at, create_at, update_at, create_by, update_by, asset_cat_name, bop_cat_name, group_name, group_state, create_user_name, update_user_name], memberList]
// state: "notExistBop": 対象の取引が存在しない
//        "cannotGetBop": 対象外の取引のため、取得できない
//        "success": 成功

include("/var/www/html/src/php/db.php");
include("/var/www/html/src/php/register_log.php");

$raw = file_get_contents("php://input");
$bopId = json_decode($raw);
$token = $_COOKIE["NKAppsLoginToken"];
$result = ["", [], []];

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

// Check exist bop
$sql = "SELECT count(*) FROM bops WHERE uniq_id = :uniq_id";
$res = $PDO->prepare($sql);
$params = array(
  ":uniq_id" => $bopId
);
$res->execute($params);
foreach( $res as $resFor ) {
  if( $resFor[0] == 0 ) {
    $result[0] = "notExistBop";
  }
}

// Check this user can get bop datas
if( $result[0] == "" ) {
  $sql = "SELECT count(*) FROM bops WHERE uniq_id = :uniq_id AND ( ( group_id = :group_id AND create_by = :create_by ) OR ( group_id IN ( SELECT group_id FROM group_relations WHERE user_id = :user_id ) ) )";
  $res = $PDO->prepare($sql);
  $params = array(
    ":uniq_id" => $bopId,
    ":group_id"=> "self",
    ":create_by" => $userUniqId,
    ":user_id" => $userUniqId
  );
  $res->execute($params);
  foreach( $res as $resFor ) {
    if( $resFor[0] == 0 ) {
      $result[0] = "cannotGetBop";
    }
  }
}

// Get bop datas
if( $result[0] == "" ) {
  $sql = "SELECT * FROM bops WHERE uniq_id = :uniq_id";
  $res = $PDO->prepare($sql);
  $params = array(
    ":uniq_id" => $bopId
  );
  $res->execute($params);
  foreach( $res as $resFor ) {
    $result[1] = $resFor;
  }

  // Get asset cat name
  $sql = "SELECT name FROM asset_cats WHERE uniq_id = :uniq_id";
  $res = $PDO->prepare($sql);
  $params = array(
    ":uniq_id" => $result[1][1]
  );
  $res->execute($params);
  foreach( $res as $resFor ) {
    $result[1][12] = $resFor[0];
  }
  
  // Get bop cat name
  $sql = "SELECT name FROM bop_cats WHERE uniq_id = :uniq_id";
  $res = $PDO->prepare($sql);
  $params = array(
    ":uniq_id" => $result[1][2]
  );
  $res->execute($params);
  foreach( $res as $resFor ) {
    $result[1][13] = $resFor[0];
  }

  // Get group name
  if( $result[1][3] == "self" ) {
    $result[1][14] = "自分のみ";
  } else {
    $sql = "SELECT name FROM groups WHERE uniq_id = :uniq_id";
    $res = $PDO->prepare($sql);
    $params = array(
      ":uniq_id" => $result[1][3]
    );
    $res->execute($params);
    foreach( $res as $resFor ) {
      $result[1][14] = $resFor[0];
    }
  }

  // Get user state to group
  if( $result[1][3] == "self" ) {
    $result[1][15] = "self";
  } else {
    $sql = "SELECT state FROM group_relations WHERE user_id = :user_id AND group_id = :group_id";
    $res = $PDO->prepare($sql);
    $params = array(
      ":user_id" => $userUniqId,
      ":group_id" => $result[1][3]
    );
    $res->execute($params);
    foreach( $res as $resFor ) {
      $result[1][15] = $resFor[0];
    }
  }

  // Get create user name
  $sql = "SELECT name FROM users WHERE uniq_id = :uniq_id";
  $res = $PDO->prepare($sql);
  $params = array(
    ":uniq_id" => $result[1][10]
  );
  $res->execute($params);
  foreach( $res as $resFor ) {
    $result[1][16] = $resFor[0];
  }
  
  // Get update user name
  $sql = "SELECT name FROM users WHERE uniq_id = :uniq_id";
  $res = $PDO->prepare($sql);
  $params = array(
    ":uniq_id" => $result[1][11]
  );
  $res->execute($params);
  foreach( $res as $resFor ) {
    $result[1][17] = $resFor[0];
  }

  $sql = "SELECT uniq_id, id, name, icon_file_name FROM users WHERE uniq_id IN ( SELECT user_id FROM group_relations WHERE group_id = :group_id )";
  $res = $PDO->prepare($sql);
  $params = array(
    ":group_id" => $result[1][3]
  );
  $res->execute($params);
  foreach( $res as $resFor ) {
    array_push($result[2], $resFor);
  }

  $result[0] = "success";
}

echo json_encode($result);
?>