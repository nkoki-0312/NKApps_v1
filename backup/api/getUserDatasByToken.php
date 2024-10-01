<?php
// INPUT: token
// OUTPUT: {state}
// state: "notFoundUser": すでにメールアドレスが登録されている
//        "success":    仮登録成功

include('../public/db.php');
include('../public/register_log.php');

$token = $_GET["token"];
header('Access-Control-Allow-Origin:http://localhost:3000');
$datetime = date("Y-m-d H:i:s");
$result = "notFoundUser";

$sql = "SELECT uniq_id, id, name, email FROM users WHERE uniq_id IN ( SELECT user_id FROM login_tokens WHERE token = :token )";
$res = $PDO->prepare($sql);
$params = array(
  ":token" => $token
);
$res->execute($params);
foreach( $res as $resFor ){
  register_log("getUserDatasByToken", $resFor[0], $resFor[0], "", "");
  $result = $resFor;
}

echo json_encode($result);
?>