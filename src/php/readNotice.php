<?php
// INPUT: uniq_id
// OUTPUT: [state, noticeDatas]
// state: notExistNotice: 対象のお知らせが存在しない
//        success: 成功

include("/var/www/html/src/php/db.php");

$raw = file_get_contents("php://input");
$uniq_id = json_decode($raw);
$token = $_COOKIE["NKAppsLoginToken"];
$result = ["", []];

$sql = "SELECT user_id FROM login_tokens WHERE token = :token";
$res = $PDO->prepare($sql);
$params = array(
  ":token" => $token
);
$res->execute($params);
foreach( $res as $resFor ) {
  $userUniqId = $resFor[0];
}

// Check this notice exist
$sql = "SELECT count(*) FROM notices WHERE uniq_id = :uniq_id AND send_to = :send_to";
$res = $PDO->prepare($sql);
$params = array(
  ":uniq_id" => $uniq_id,
  ":send_to" => $userUniqId
);
$res->execute($params);
foreach( $res as $resFor ) {
  if( $resFor[0] == 0 ) {
    $result[0] = "notExistNotice";
  }
}

if( $result[0] == "" ) {
  $sql = "UPDATE notices SET state = :state WHERE uniq_id = :uniq_id";
  $updateState = $PDO->prepare($sql);
  $params = array(
    ":state" => "read",
    ":uniq_id" => $uniq_id
  );
  $updateState->execute($params);

  $sql = "SELECT uniq_id, type, state, ttl, text, start_at, end_at, create_at, update_at, create_by, update_by, data FROM notices WHERE uniq_id = :uniq_id";
  $res = $PDO->prepare($sql);
  $params = array(
    ":uniq_id" => $uniq_id
  );
  $res->execute($params);
  foreach( $res as $resFor ) {
    $result[0] = "success";
    $result[1] = $resFor;

    if( $result[1][9] == "NKApps簡易通知配信システム" ) {
      $result[1][12] = "NKApps簡易通知配信システム";
    } else {
      $sql = "SELECT name FROM users WHERE uniq_id = :uniq_id";
      $userName = $PDO->prepare($sql);
      $params = array(
        ":uniq_id" => $result[1][9]
      );
      $userName->execute($params);
      foreach( $userName as $userNameFor ) {
        $result[1][12] = $userNameFor[0];
      }
    }

    if( $result[1][10] == "NKApps簡易通知配信システム" ) {
      $result[1][13] = "NKApps簡易通知配信システム";
    } else {
      $sql = "SELECT name FROM users WHERE uniq_id = :uniq_id";
      $userName = $PDO->prepare($sql);
      $params = array(
        ":uniq_id" => $result[1][10]
      );
      $userName->execute($params);
      foreach( $userName as $userNameFor ) {
        $result[1][13] = $userNameFor[0];
      }
    }
  }
}

echo json_encode($result);
?>