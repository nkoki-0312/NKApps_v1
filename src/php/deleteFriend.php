<?php
// INPUT: friendId
// OUTPUT: friendName

include('/var/www/html/src/php/db.php');
include('/var/www/html/src/php/register_log.php');

$raw = file_get_contents("php://input");
$friendId = json_decode($raw);
$token = $_COOKIE["NKAppsLoginToken"];
$result = "";
$sql = "SELECT user_id FROM login_tokens WHERE token = :token";
$resGetUniqId = $PDO->prepare($sql);
$params = array(
  ":token" => $token
);
$resGetUniqId->execute($params);
foreach( $resGetUniqId as $resGetUniqIdFor ) {
  $sql = "DELETE FROM friend_relations WHERE ( user_id_1 = :user_id_1_m AND user_id_2 = :user_id_2_f ) OR ( user_id_1 = :user_id_1_f AND user_id_2 = :user_id_2_m )";
  $resDelete = $PDO->prepare($sql);
  $params = array(
    ":user_id_1_m" => $resGetUniqIdFor[0],
    ":user_id_2_f" => $friendId,
    ":user_id_1_f" => $friendId,
    ":user_id_2_m" => $resGetUniqIdFor[0],
  );
  $resDelete->execute($params);

  $sql = "SELECT name FROM users WHERE uniq_id = :uniq_id";
  $resGetUserName = $PDO->prepare($sql);
  $params = array(
    ":uniq_id" => $friendId
  );
  $resGetUserName->execute($params);
  foreach( $resGetUserName as $resGetUserNameFor ){
    $result = $resGetUserNameFor[0];
  }
}

echo json_encode($result);

?>