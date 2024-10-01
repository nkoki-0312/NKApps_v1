<?php
// INPUT: [name, group]
// OUTPUT: state
// state: "notExistGroup": 対象のグループが存在しない
//        "success": 成功

include("/var/www/html/src/php/db.php");
include("/var/www/html/src/php/register_log.php");

$raw = file_get_contents("php://input");
$datas = json_decode($raw);
$token = $_COOKIE["NKAppsLoginToken"];
$result = "";
$datetime = date("Y-m-d H:i:s");
$userId = "";

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

// If group_id is not "self", check exist group
if( $datas[1] != "self" ) {
  $sql = "SELECT count(*) FROM groups WHERE uniq_id = :uniq_id AND uniq_id IN ( SELECT group_id FROM group_relations WHERE user_id = :user_id )";
  $res = $PDO->prepare($sql);
  $params = array(
    ":uniq_id" => $datas[1],
    ":user_id" => $userUniqId
  );
  $res->execute($params);
  foreach( $res as $resFor ) {
    if( $resFor[0] == 0 ) {
      $result = "notExistGroup";
    }
  }
}

// Add asset category
if( $result == "" ) {
  // Create friend uniq id.
  $checkUniqId = true;
  while( $checkUniqId ) {
    $uniqId = "";
    for( $i=0; $i<255; $i++ ){
      if(mt_rand(0,1) == 0){
        $uniqId .= chr(mt_rand(65, 90));
      }else{
        $uniqId .= mt_rand(0, 9);
      }
    }

    $sql = "SELECT count(*) FROM bop_cats WHERE uniq_id = :uniq_id";
    $res = $PDO->prepare($sql);
    $params = array(
      ":uniq_id" => $uniqId
    );
    $res->execute($params);
    foreach( $res as $resFor ){
      if( $resFor[0] == 0 ) $checkUniqId = false;
    }
  }

  $sql = "INSERT INTO bop_cats (
    uniq_id, group_id, user_id, name, type, clr, create_at, update_at
  ) VALUES (
    :uniq_id, :group_id, :user_id, :name, :type, :clr, :create_at, :update_at
  )";
  $res = $PDO->prepare($sql);
  $params = array(
    ":uniq_id" => $uniqId,
    ":group_id" => $datas[1],
    ":user_id" => $userUniqId,
    ":name" => $datas[0],
    ":type" => "normal",
    ":clr" => "#0066ff",
    ":create_at" => $datetime,
    ":update_at" => $datetime
  );
  $res->execute($params);

  $result = "success";
}

echo json_encode($result);
?>