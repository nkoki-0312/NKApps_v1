<?php
// INPUT:   userId
// OUTPUT:  {state}
// state: "notExistUser": 存在しないユーザID
//        "myself": 自分自身のユーザID
//        "alreadyFriend": すでにフレンドである
//        [friendId, friendName]: [成功時]フレンドのユーザネーム

include("/var/www/html/src/php/db.php");
include("/var/www/html/src/php/register_log.php");

$raw = file_get_contents("php://input");
$friendId = json_decode($raw);
$result = "";
$token = $_COOKIE["NKAppsLoginToken"];
$datetime = date("Y-m-d H:i:s");
$friendUniqId = "";
$friendName = "";
$userUniqId = "";

/* Check errors */
// Check user id exist.
$sql = "SELECT count(*), uniq_id, name FROM users WHERE id = :id";
$resCheckExistUser = $PDO->prepare($sql);
$params = array(
  ":id" => $friendId
);
$resCheckExistUser->execute($params);
foreach( $resCheckExistUser as $resCheckExistUserFor ) {
  $friendUniqId = $resCheckExistUserFor[1];
  $friendName = $resCheckExistUserFor[2];

  if( $resCheckExistUserFor[0] == 0 ) {
    $result = "notExistUser";
  } else {
    // Check friend id is myself.
    $sql = "SELECT user_id FROM login_tokens WHERE token = :token";
    $resGetUniqId = $PDO->prepare($sql);
    $params = array(
      ":token" => $token
    );
    $resGetUniqId->execute($params);
    foreach( $resGetUniqId as $resGetUniqIdFor ) {
      $userUniqId = $resGetUniqIdFor[0];
      if( $resCheckExistUserFor[1] == $userUniqId ) {
        $result = "myself";
      } else {
        // Check already friend
        $sql = "SELECT count(*) FROM friend_relations WHERE ( user_id_1 = :user_id_1_m AND user_id_2 = :user_id_2_f ) OR ( user_id_1 = :user_id_1_f AND user_id_2 = :user_id_2_m )";
        $resCheckAlreadyFriend = $PDO->prepare($sql);
        $params = array(
          ":user_id_1_m" => $resGetUniqIdFor[0],
          ":user_id_2_f" => $resCheckExistUserFor[1],
          ":user_id_1_f" => $resCheckExistUserFor[1],
          ":user_id_2_m" => $resGetUniqIdFor[0],
        );
        $resCheckAlreadyFriend->execute($params);
        foreach( $resCheckAlreadyFriend as $resCheckAlreadyFriendFor ) {
          if( $resCheckAlreadyFriendFor[0] != 0 ) {
            $result = "alreadyFriend";
          }
        }
      } 
    }
  }
}

// if errors is not exist, return friend datas.
if( $result == "" ) {
  $result = [$friendUniqId, $friendName];
}

echo json_encode($result);
?>