<?php
// INPUT: token
// OUTPUT: {state}
// state: "notFoundUser": 該当するユーザが存在しない または 有効期限切れである
//        "existUser":    トークンが有効である

include('../public/db.php');
include('../public/register_log.php');

$token = $_GET["token"];
header('Access-Control-Allow-Origin:http://localhost:3000');
$datetime = date("Y-m-d H:i:s");
$result = "notFoundUser";

$sql = "SELECT count(*), user_id FROM login_tokens WHERE token = :token AND limit_at >= :limit_at";
$res = $PDO->prepare($sql);
$params = array(
  ":token" => $token,
  ":limit_at" => $datetime
);
$res->execute($params);
foreach( $res as $resFor ){
  if( $resFor[0] >= 1 ) {
    $result = "existUser";
    register_log("checkLoginToken", $resFor[1], $resFor[1], "", "");
  }
}

echo json_encode($result);
?>