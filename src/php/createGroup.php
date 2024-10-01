<?php
// INPUT: [name, id, [memberId, ...], password, text]
// OUTPUT: {state}
// state: "existGroupId": すでに同じグループIDが登録されている
//        "success": "成功"

include("/var/www/html/src/php/db.php");
include("/var/www/html/src/php/register_log.php");

$raw = file_get_contents("php://input");
$groupDatas = json_decode($raw);
$datetime = date("Y-m-d H:i:s");
$token = $_COOKIE["NKAppsLoginToken"];
$result = "";
$userUniqId = "";

$sql = "SELECT user_id FROM login_tokens WHERE token = :token";
$resGetUniqId = $PDO->prepare($sql);
$params = array(
  ":token" => $token
);
$resGetUniqId->execute($params);
foreach( $resGetUniqId as $resGetUniqIdFor ) {
  $userUniqId = $resGetUniqIdFor[0];

  // Check group id
  if( $groupDatas[1] != "" ) {
    $sql = "SELECT count(*) FROM groups WHERE id = :id";
    $resCheckGroupId = $PDO->prepare($sql);
    $params = array(
      ":id" => $groupDatas[1]
    );
    $resCheckGroupId->execute($params);
    foreach( $resCheckGroupId as $resCheckGroupIdFor ) {
      if( $resCheckGroupIdFor[0] >= 1 ) {
        $result = "existGroupId";
      }
    }
  }
}

if( $result == "" ) {
  // Create group uniq id.
  $checkUniqId = true;
  while( $checkUniqId ) {
    $uniq_id = "";
    for( $i=0; $i<255; $i++ ){
      if(mt_rand(0,1) == 0){
        $uniq_id .= chr(mt_rand(65, 90));
      }else{
        $uniq_id .= mt_rand(0, 9);
      }
    }

    $sql = "SELECT count(*) FROM groups WHERE uniq_id = :uniq_id";
    $res = $PDO->prepare($sql);
    $params = array(
      ":uniq_id" => $uniq_id
    );
    $res->execute($params);
    foreach( $res as $resFor ){
      if( $resFor[0] == 0 ) $checkUniqId = false;
    }
  }

  if( $groupDatas[3] == "" ) {
    $sql = "INSERT INTO groups (
      uniq_id, id, name, password, text, create_at, update_at
    ) VALUES (
      :uniq_id, :id, :name, :password, :text, :create_at, :update_at
    )";
    $res = $PDO->prepare($sql);
    $params = array(
      ":uniq_id" => $uniq_id,
      ":id" => $groupDatas[1],
      ":name" => $groupDatas[0],
      ":password" => "",
      ":text" => $groupDatas[4],
      ":create_at" => $datetime,
      ":update_at" => $datetime
    );
    $res->execute($params);
  } else {
    $sql = "INSERT INTO groups (
      uniq_id, id, name, password, text, create_at, update_at
    ) VALUES (
      :uniq_id, :id, :name, :password, :text, :create_at, :update_at
    )";
    $res = $PDO->prepare($sql);
    $params = array(
      ":uniq_id" => $uniq_id,
      ":id" => $groupDatas[1],
      ":name" => $groupDatas[0],
      ":password" => password_hash($groupDatas[3], PASSWORD_DEFAULT),
      ":text" => $groupDatas[4],
      ":create_at" => $datetime,
      ":update_at" => $datetime
    );
    $res->execute($params);
  }
  
  register_log("create_group", $uniq_id, $userUniqId, "", "");

  // Add myself
  // Create group relation uniq id.
  $checkUniqId = true;
  while( $checkUniqId ) {
    $relation_uniq_id = "";
    for( $i=0; $i<255; $i++ ){
      if(mt_rand(0,1) == 0){
        $relation_uniq_id .= chr(mt_rand(65, 90));
      }else{
        $relation_uniq_id .= mt_rand(0, 9);
      }
    }

    $sql = "SELECT count(*) FROM group_relations WHERE uniq_id = :uniq_id";
    $res = $PDO->prepare($sql);
    $params = array(
      ":uniq_id" => $relation_uniq_id
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
    ":uniq_id" => $relation_uniq_id,
    ":user_id" => $userUniqId,
    ":group_id" => $uniq_id,
    ":state" => "admin",
    ":create_at" => $datetime
  );
  $res->execute($params);

  register_log("join_group", $userUniqId, $userUniqId, $uniq_id, "");

  // Add users
  for( $userNum=0; $userNum<count($groupDatas[2]); $userNum++ ) {
    // Create group relation uniq id.
    $checkUniqId = true;
    while( $checkUniqId ) {
      $relation_uniq_id = "";
      for( $i=0; $i<255; $i++ ){
        if(mt_rand(0,1) == 0){
          $relation_uniq_id .= chr(mt_rand(65, 90));
        }else{
          $relation_uniq_id .= mt_rand(0, 9);
        }
      }
  
      $sql = "SELECT count(*) FROM group_relations WHERE uniq_id = :uniq_id";
      $res = $PDO->prepare($sql);
      $params = array(
        ":uniq_id" => $relation_uniq_id
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
      ":uniq_id" => $relation_uniq_id,
      ":user_id" => $groupDatas[2][$userNum],
      ":group_id" => $uniq_id,
      ":state" => "member",
      ":create_at" => $datetime
    );
    $res->execute($params);

    register_log("join_group", $groupDatas[2][$userNum], $userUniqId, $uniq_id, "");
  }
  
  $result = "success";
}

echo json_encode($result);
?>