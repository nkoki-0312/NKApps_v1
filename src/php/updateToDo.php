<?php
// INPUT: {ttl, startAt, limitAt, bgClr, fontClr, text, todoId, groupId }
// OUTPUT: state
// state: "cannotEditPlan": 編集権限がない
//        "success": 成功

include("/var/www/html/src/php/db.php");
include("/var/www/html/src/php/register_log.php");

$raw = file_get_contents("php://input");
$datas = json_decode($raw);
$token = $_COOKIE["NKAppsLoginToken"];
$result = "";
$userState = "";
$datetime = date("Y-m-d H:i:s");
$userState = "";

if( $datas[1] == "" ) $datas[1] = null;
if( $datas[2] == "" ) $datas[2] = null;

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

// Check this user can update
$sql = "SELECT count(*) FROM todos WHERE ( group_id = :group_id AND create_by = :create_by AND uniq_id = :uniq_id_0 ) OR ( uniq_id = :uniq_id_1 AND group_id IN ( SELECT group_id FROM group_relations WHERE user_id = :user_id AND state = :state ) )";
$res = $PDO->prepare($sql);
$params = array(
  ":group_id" => "self",
  ":create_by" => $userUniqId,
  ":uniq_id_0" => $datas[6],
  ":uniq_id_1"=> $datas[6],
  ":user_id" => $userUniqId,
  ":state" => "admin"
);
$res->execute($params);
foreach( $res as $resFor ) {
  if( $resFor[0] == 0 ) {
    $result = "cannotEditPlan";
  }
}

// Update ToDo
if( $result == "" ) {
  $sql = "UPDATE todos SET ttl = :ttl, bg_clr = :bg_clr, font_clr = :font_clr, start_at = :start_at, limit_at = :limit_at, detail = :detail, update_at = :update_at, update_by = :update_by WHERE uniq_id = :uniq_id";
  $res = $PDO->prepare($sql);
  $params = array(
    ":ttl" => $datas[0],
    ":bg_clr" => $datas[3],
    ":font_clr" => $datas[4],
    ":start_at" => $datas[1],
    ":limit_at" => $datas[2],
    ":detail" => $datas[5],
    ":update_at" => $datetime,
    ":update_by" => $userUniqId,
    ":uniq_id" => $datas[6],
  );
  $res->execute($params);

  $result = "success";
}

echo json_encode($result);
?>