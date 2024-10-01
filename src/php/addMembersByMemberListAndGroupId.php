<?php
// INPUT: {memberList, groupId}
// OUTPUT: {state}
// state: "success": 成功

include("/var/www/html/src/php/db.php");
include("/var/www/html/src/php/register_log.php");

$raw = file_get_contents("php://input");
$datas = json_decode($raw);
$token = $_COOKIE["NKAppsLoginToken"];
$userUniqId = "";

$sql = "SELECT user_id FROM login_tokens WHERE token = :token";
$res = $PDO->prepare($sql);
$params = array(
  ":token" => $token
);
$res->execute($params);
foreach( $res as $resFor ) {
  $userUniqId = $resFor[0];
}

for( $i=0; $i<count($datas[0]); $i++ ) {
  $checkUniqId = true;
  while( $checkUniqId ) {
    $uniq_id = "";
    for( $j=0; $j<255; $j++ ){
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

  $sql = "INSERT INTO group_relations (
    uniq_id, user_id, group_id, state, create_at
  ) VALUES (
    :uniq_id, :user_id, :group_id, :state, :create_at
  )";
  $res = $PDO->prepare($sql);
  $params = array(
    ":uniq_id" => $uniq_id,
    ":user_id" => $datas[0][$i],
    ":group_id" => $datas[1],
    ":state" => "member",
    ":create_at" => date("Y-m-d H:i:s")
  );
  $res->execute($params);
  
  register_log("join_group", $datas[0][$i], $userUniqId, $datas[1], "");
}

echo json_encode("success");
?>