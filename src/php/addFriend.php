<?php
// INPUT: userId
// OUTPUT: {state}

include("/var/www/html/src/php/db.php");
include("/var/www/html/src/php/register_log.php");

$raw = file_get_contents("php://input");
$friendId = json_decode($raw);
$result = "";
$token = $_COOKIE["NKAppsLoginToken"];
$datetime = date("Y-m-d H:i:s");

$sql = "SELECT user_id FROM login_tokens WHERE token = :token";
$resGetUniqId = $PDO->prepare($sql);
$params = array(
  ":token" => $token
);
$resGetUniqId->execute($params);
foreach( $resGetUniqId as $resGetUniqIdFor ) {
  // Check already friend
  $sql = "SELECT count(*) FROM friend_relations WHERE ( user_id_1 = :user_id_1_m AND user_id_2 = :user_id_2_f ) OR ( user_id_1 = :user_id_1_f AND user_id_2 = :user_id_2_m )";
  $resCheckAlreadyFriend = $PDO->prepare($sql);
  $params = array(
    ":user_id_1_m" => $resGetUniqIdFor[0],
    ":user_id_2_f" => $friendId,
    ":user_id_1_f" => $friendId,
    ":user_id_2_m" => $resGetUniqIdFor[0],
  );
  $resCheckAlreadyFriend->execute($params);
  foreach( $resCheckAlreadyFriend as $resCheckAlreadyFriendFor ) {
    if( $resCheckAlreadyFriendFor[0] == 0 ) {
      // Create friend uniq id.
      $checkUniqId = true;
      while( $checkUniqId ) {
        $relationUniqId = "";
        for( $i=0; $i<255; $i++ ){
          if(mt_rand(0,1) == 0){
            $relationUniqId .= chr(mt_rand(65, 90));
          }else{
            $relationUniqId .= mt_rand(0, 9);
          }
        }

        $sql = "SELECT count(*) FROM friend_relations WHERE uniq_id = :uniq_id";
        $res = $PDO->prepare($sql);
        $params = array(
          ":uniq_id" => $relationUniqId
        );
        $res->execute($params);
        foreach( $res as $resFor ){
          if( $resFor[0] == 0 ) $checkUniqId = false;
        }
      }

      $sql = "INSERT INTO friend_relations (
        uniq_id, user_id_1, user_id_2, create_at
      ) VALUES (
        :uniq_id, :user_id_1, :user_id_2, :create_at
      )";
      $res = $PDO->prepare($sql);
      $params = array(
        ":uniq_id" => $relationUniqId,
        ":user_id_1" => $resGetUniqIdFor[0],
        ":user_id_2" => $friendId,
        ":create_at" => $datetime
      );
      $res->execute($params);

      register_log("add_friend", $relationUniqId, $resGetUniqIdFor[0], $friendId, "");

      $result = "success";
    } else {
      $result = "alreadyFriend";
    }
  }

}

echo json_encode($result);
?>