<?php
// INPUT: None
// OUTPUT: friend list

include("/var/www/html/src/php/db.php");

$token = $_COOKIE["NKAppsLoginToken"];
$result = [];

$sql = "SELECT user_id FROM login_tokens WHERE token = :token";
$resGetUniqId = $PDO->prepare($sql);
$params = array(
  ":token" => $token
);
$resGetUniqId->execute($params);
foreach( $resGetUniqId as $resGetUniqIdFor ) {
  $sql = "SELECT user_id_1, user_id_2 FROM friend_relations WHERE user_id_1 = :user_id_1 OR user_id_2 = :user_id_2";
  $resUserIds = $PDO->prepare($sql);
  $params = array(
    ":user_id_1" => $resGetUniqIdFor[0],
    ":user_id_2" => $resGetUniqIdFor[0]
  );
  $resUserIds->execute($params);
  foreach( $resUserIds as $resUserId ) {
    $sql = "SELECT uniq_id, id, name, icon_file_name FROM users WHERE uniq_id = :uniq_id";
    $resFriendList = $PDO->prepare($sql);
    if( $resUserId[0] != $resGetUniqIdFor[0] ){
      $params = array(
        ":uniq_id" => $resUserId[0]
      );
    } else {
      $params = array(
        ":uniq_id" => $resUserId[1]
      );
    }
    $resFriendList->execute($params);
    foreach( $resFriendList as $resFriendListFor ) {
      array_push($result, $resFriendListFor);
    }
  }
}

echo json_encode($result);
?>