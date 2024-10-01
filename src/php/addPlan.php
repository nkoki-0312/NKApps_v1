<?php
// INPUT: {ttl, group, startAt, endAt, bgClr, fontClr, text }
// OUTPUT: state
// state: "success": 成功

include("/var/www/html/src/php/db.php");
include("/var/www/html/src/php/register_log.php");

$raw = file_get_contents("php://input");
$datas = json_decode($raw);
$token = $_COOKIE["NKAppsLoginToken"];
$result = "";
$datetime = date("Y-m-d H:i:s");

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

// Check exist group
if( $datas[1] != "self" ) {
  $sql = "SELECT count(*) FROM group_relations WHERE user_id = :user_id AND group_id = :group_id";
  $res = $PDO->prepare($sql);
  $params = array(
    ":user_id" => $userUniqId,
    ":group_id" => $datas[1]
  );
  $res->execute($params);
  foreach( $res as $resFor ) {
    if( $resFor[0] == 0 ) {
      $result = "notExistGroup";
    }
  }
}

// Create event
if( $result == "" ) {
  // Create origin id.
  $checkOriginalId = true;
  while( $checkOriginalId ) {
    $originalId = "";
    for( $i=0; $i<255; $i++ ){
      if(mt_rand(0,1) == 0){
        $originalId .= chr(mt_rand(65, 90));
      }else{
        $originalId .= mt_rand(0, 9);
      }
    }

    $sql = "SELECT count(*) FROM plans WHERE original_id = :original_id";
    $res = $PDO->prepare($sql);
    $params = array(
      ":original_id" => $originalId
    );
    $res->execute($params);
    foreach( $res as $resFor ){
      if( $resFor[0] == 0 ) $checkOriginalId = false;
    }
  }

  // Create uniq id.
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

    $sql = "SELECT count(*) FROM group_relations WHERE uniq_id = :uniq_id";
    $res = $PDO->prepare($sql);
    $params = array(
      ":uniq_id" => $uniqId
    );
    $res->execute($params);
    foreach( $res as $resFor ){
      if( $resFor[0] == 0 ) $checkUniqId = false;
    }
  }

  $sql = "INSERT INTO plans (
    original_id, uniq_id, group_id, ttl, bg_clr, font_clr, start_at, end_at, detail, create_at, update_at, create_by, update_by
  ) VALUES (
    :original_id, :uniq_id, :group_id, :ttl, :bg_clr, :font_clr, :start_at, :end_at, :detail, :create_at, :update_at, :create_by, :update_by
  )";
  $res = $PDO->prepare($sql);
  $params = array(
    ":original_id" => $originalId,
    ":uniq_id" => $uniqId,
    ":group_id" => $datas[1],
    ":ttl" => $datas[0],
    ":bg_clr" => $datas[4],
    ":font_clr" => $datas[5],
    ":start_at" => $datas[2],
    ":end_at" => $datas[3],
    ":detail" => $datas[6],
    ":create_at" => $datetime,
    ":update_at" => $datetime,
    ":create_by" => $userUniqId,
    ":update_by" => $userUniqId,
  );
  $res->execute($params);

  $result = "success";
}

echo json_encode($result);
?>