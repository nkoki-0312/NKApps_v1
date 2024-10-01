<?php
// INPUT: month
// OUTPUT: { state, eventList }
// state: "success": 成功

include("/var/www/html/src/php/db.php");
include("/var/www/html/src/php/register_log.php");

$raw = file_get_contents("php://input");
$month = json_decode($raw);
$token = $_COOKIE["NKAppsLoginToken"];
$result = ["", []];

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

// get plans
$count = 0;
if( $result[0] == "" ) {
  $startDate = $month."-01";
  $endDate = $month."-31";
  
  $sql = "SELECT original_id, uniq_id, group_id, ttl, bg_clr, font_clr, start_at, end_at, detail FROM plans WHERE ( start_at <= :end_date AND end_at >= :start_date ) AND ( ( group_id = :group_id AND create_by = :create_by ) OR ( group_id IN ( SELECT group_id FROM group_relations WHERE user_id = :user_id ) ) )";
  $res = $PDO->prepare($sql);
  $params = array(
    ":start_date" => $startDate,
    ":end_date" => $endDate,
    ":group_id" => "self",
    ":create_by" => $userUniqId,
    ":user_id" => $userUniqId
  );
  $res->execute($params);
  $result[0] = "success";
  foreach( $res as $resFor ) {
    array_push($result[1], $resFor);
    
    if( $result[1][$count][2] == "self" ) {
      $result[1][$count][9] = "自分のみ";
    } else {
      $sql = "SELECT name FROM groups WHERE uniq_id = :uniq_id";
      $resGetGroupName = $PDO->prepare($sql);
      $params = array(
        ":uniq_id" => $result[1][$count][2]
      );
      $resGetGroupName->execute($params);
      foreach( $resGetGroupName as $resGetGroupNameFor ) {
        $result[1][$count][9] = $resGetGroupNameFor[0];
      }
    }
    $count++;
  }
}
echo json_encode($result);
?>