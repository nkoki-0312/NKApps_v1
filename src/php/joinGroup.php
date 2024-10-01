<?php
// INPUT: { groupId, password}
// OUTPUT: {state}
// state: "alreadyJoin": すでに参加している
//        "unMatchPassword": パスワードが一致しない
//        "success": 成功

include("/var/www/html/src/php/db.php");
include("/var/www/html/src/php/register_log.php");

$raw = file_get_contents("php://input");
$datas = json_decode($raw);
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
  $myUniqId = $resGetUniqIdFor[0];

  // Check already join group
  $sql = "SELECT count(*) FROM group_relations WHERE user_id = :user_id AND group_id = :group_id";
  $resCheckAlreadyJoin = $PDO->prepare($sql);
  $params = array(
    ":user_id" => $resGetUniqIdFor[0],
    ":group_id" => $datas[0],
  );
  $resCheckAlreadyJoin->execute($params);
  foreach( $resCheckAlreadyJoin as $resCheckAlreadyJoinFor ) {
    if( $resCheckAlreadyJoinFor[0] != 0 ) {
      $result = "alreadyJoin";
    }
  }
}

if( $result == "" ) {
  // If this group have password, Check password.
  $sql = "SELECT password FROM groups WHERE uniq_id = :uniq_id";
  $resCheckPassword = $PDO->prepare($sql);
  $params = array(
    ":uniq_id" => $datas[0]
  );
  $resCheckPassword->execute($params);
  foreach( $resCheckPassword as $resCheckPasswordFor ) {
    if( $resCheckPasswordFor[0] != "" && !password_verify( $datas[1], $resCheckPasswordFor[0] ) ) {
      $result = "unMatchPassword";
    }
  }
}

if( $result == "" ) {
  // Create uniq id.
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
  
    $sql = "SELECT count(*) FROM group_relations WHERE uniq_id = :uniq_id";
    $res = $PDO->prepare($sql);
    $params = array(
      ":uniq_id" => $relationUniqId
    );
    $res->execute($params);
    foreach( $res as $resFor ){
      if( $resFor[0] == 0 ) $checkUniqId = false;
    }
  }
  
  $sql = "INSERT INTO group_relations (
    uniq_id, user_id, group_id, state, create_at
  ) VALUES (
    :uniq_id, :user_id, :group_id, :state, :create_at
  )";
  $res = $PDO->prepare($sql);
  $params = array(
    ":uniq_id" => $relationUniqId,
    ":user_id" => $resGetUniqIdFor[0],
    ":group_id" => $datas[0],
    ":state" => "member",
    ":create_at" => $datetime
  );
  $res->execute($params);
  
  register_log("join_group", $resGetUniqIdFor[0], $resGetUniqIdFor[0], $relationUniqId, "");
  
  $result = "success";
}

echo json_encode($result);
?>