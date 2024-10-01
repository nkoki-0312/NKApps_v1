<?php
// INPUT:  uniqId, id, name, password
// OUTPUT: state
// state: "existId": すでに同じユーザIDのユーザが存在する
//        "success": 本登録成功

include('../public/db.php');
include('../public/register_log.php');

$uniq_id = $_GET["uid"];
$id = $_GET["id"];
$name = $_GET["name"];
$password = password_hash($_GET["password"], PASSWORD_DEFAULT);
header('Access-Control-Allow-Origin:http://localhost:3000');
$datetime = date("Y-m-d H:i:s");
$result = "";

// Check id Exists.
$sql = "SELECT id FROM users WHERE id = :id";
$resCheckIds = $PDO->prepare($sql);
$params = array(
  ":id" => $id
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
    ":id" => $id,
    ":name" => $name,
    ":password" => $password,
    ":type" => "user",
    ":state" => 1,
    ":update_at" => $datetime,
    ":uniq_id" => $uniq_id
  );
  $res->execute($params);

  $result = "success";
}

register_log("register", $uniq_id, $uniq_id, "", "");

echo json_encode($result);
?>