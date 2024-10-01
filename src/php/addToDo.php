<?php
// INPUT: [ttl, group, startAt, limitAt, beforeTodo, bgClr, fontClr, text]
// OUTPUT: state
// state: "notExitGroup": グループが存在しない。または対象のグループに参加していない。
//        "success": 成功

include("/var/www/html/src/php/db.php");
include("/var/www/html/src/php/register_log.php");

$raw = file_get_contents("php://input");
$datas = json_decode($raw);
$token = $_COOKIE["NKAppsLoginToken"];
$datetime = date("Y-m-d H:i:s");
$result = "";

if( $datas[2] == "" ) $datas[2] = null;
if( $datas[3] == "" ) $datas[3] = null;

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

// If group id is not "self", Check exist group and join group
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

// Add ToDo
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

    $sql = "SELECT count(*) FROM todos WHERE uniq_id = :uniq_id";
    $res = $PDO->prepare($sql);
    $params = array(
      ":uniq_id" => $originalId
    );
    $res->execute($params);
    foreach( $res as $resFor ){
      if( $resFor[0] == 0 ) $checkOriginalId = false;
    }
  }

  $sql = "INSERT INTO todos (
    uniq_id, group_id, before_todo_id, ttl, detail, is_checked, bg_clr, font_clr, start_at, limit_at, create_at, update_at, check_at, create_by, update_by, check_by
  ) VALUES (
    :uniq_id, :group_id, :before_todo_id, :ttl, :detail, :is_checked, :bg_clr, :font_clr, :start_at, :limit_at, :create_at, :update_at, :check_at, :create_by, :update_by, :check_by
  )";
  $res = $PDO->prepare($sql);
  $params = array(
    ":uniq_id" => $originalId,
    ":group_id" => $datas[1],
    ":before_todo_id" => $datas[4],
    ":ttl" => $datas[0],
    ":detail" => $datas[7],
    ":is_checked" => 0,
    ":bg_clr" => $datas[5],
    ":font_clr" => $datas[6],
    ":start_at" => $datas[2],
    ":limit_at" => $datas[3],
    ":create_at" => $datetime,
    ":update_at" => $datetime,
    ":check_at" => "9999-12-31 23:59",
    ":create_by" => $userUniqId,
    ":update_by" => $userUniqId,
    ":check_by" => ""
  );
  $res->execute($params);
  $result = "success";
}

echo json_encode($result);
?>