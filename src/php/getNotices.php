<?php
// INPUT: None
// OUTPUT: [noticeDatas]

include('/var/www/html/src/php/db.php');

$datetime = date("Y-m-d H:i:s");
$token = $_COOKIE["NKAppsLoginToken"];

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

// Get notice datas.
// $sql = "SELECT uniq_id, type, state, ttl, text, start_at, end_at, update_by, data FROM notices WHERE send_to = :send_to AND ( start_at = :start_at_null OR start_at < :start_at ) AND ( end_at = :end_at_null OR end_at > :end_at)";
$sql = "SELECT uniq_id, type, state, ttl, text, start_at, end_at, update_by, data FROM notices WHERE send_to = :send_to ORDER BY update_at desc";
$res = $PDO->prepare($sql);
$params = array(
  ":send_to" => $userUniqId
  // ":send_to" => $userUniqId,
  // ":start_at_null" => null,
  // ":start_at" => $datetime,
  // ":end_at_null" => null,
  // ":end_at" => $datetime
);
$res->execute($params);
$result = [];
$count = 0;
foreach( $res as $resFor ) {
  array_push( $result, $resFor );

  if( $result[$count][7] == "NKApps簡易通知配信システム" ) {
    $result[$count][9] = "NKApps簡易通知配信システム";
  } else {
    $sql = "SELECT name FROM users WHERE uniq_id = :uniq_id";
    $updateBy = $PDO->prepare($sql);
    $params = array(
      ":uniq_id" => $result[$count][7]
    );
    $updateBy->execute($params);
    foreach( $updateBy as $updateByFor ) {
      $result[$count][9] = $updateByFor[0];
    }
  }
  $count++;
}

echo json_encode($result);
?>