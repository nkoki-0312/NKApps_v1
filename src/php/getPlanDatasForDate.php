<?php
// INPUT: date
// OUTPUT: { state, eventList }
// state: "success": 成功

include("/var/www/html/src/php/db.php");
include("/var/www/html/src/php/register_log.php");

$raw = file_get_contents("php://input");
$date = json_decode($raw);
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
if( $result[0] == "" ) {
  $sql = "SELECT original_id, uniq_id, group_id, ttl, bg_clr, font_clr, start_at, end_at, detail FROM plans WHERE ( start_at <= :start_date OR end_at >= :end_date ) AND ( ( group_id = :group_id AND create_by = :create_by ) OR ( group_id IN ( SELECT group_id FROM group_relations WHERE user_id = :user_id ) ) )";
  $res = $PDO->prepare($sql);
  $params = array(
    ":start_date" => $date,
    ":end_date" => $date,
    ":group_id" => "self",
    ":create_by" => $userUniqId,
    ":user_id" => $userUniqId
  );
  $res->execute($params);
  $result[0] = "success";
  foreach( $res as $resFor ) {
    array_push($result[1], $resFor);
  }
}
echo json_encode($result);
?>