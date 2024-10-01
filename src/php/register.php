<?php
// INPUT:  uniqId, id, name, password
// OUTPUT: state
// state: "existId": すでに同じユーザIDのユーザが存在する
//        "success": 本登録成功

include('./db.php');
include('./register_log.php');

$raw = file_get_contents("php://input");
$userDatas = json_decode($raw);
$password = password_hash($userDatas[3], PASSWORD_DEFAULT);
$datetime = date("Y-m-d H:i:s");
$result = "";

// Check id Exists.
$sql = "SELECT id FROM users WHERE id = :id";
$resCheckIds = $PDO->prepare($sql);
$params = array(
  ":id" => $userDatas[1]
);
$resCheckIds->execute($params);
foreach( $resCheckIds as $resCheckId ){
  $result = "existId";
}

if( $result == "" ){
  // register user datas
  $sql = "UPDATE users SET id = :id, name = :name, password = :password, type = :type, state = :state, update_at = :update_at WHERE uniq_id = :uniq_id";
  $res = $PDO->prepare($sql);
  $params = array(
    ":id" => $userDatas[1],
    ":name" => $userDatas[2],
    ":password" => $password,
    ":type" => "user",
    ":state" => 1,
    ":update_at" => $datetime,
    ":uniq_id" => $userDatas[0]
  );
  $res->execute($params);

  $result = "success";
}

register_log("register", $userDatas[0], $userDatas[0], "", "");

echo json_encode($result);
?>