<?php
// INPUT: user id
// OUTPUT: {state}
// state: "matchTheId":   IDが一致した
//        "unmatchTheId": IDが一致しない

include('../public/db.php');
include('../public/register_log.php');

$uniq_id = $_GET["uid"];
header('Access-Control-Allow-Origin:http://localhost:3000');
$datetime = date("Y-m-d H:i:s");
$result = "unmatchTheId";

$sql = "SELECT count(*) FROM users WHERE uniq_id = :uniq_id";
$res = $PDO->prepare($sql);
$params = array(
  ":uniq_id" => $uniq_id
);
$res->execute($params);
foreach( $res as $resFor ){
  if( $resFor[0] != 0 ){
    $result = "matchTheId";
  }
}
register_log("checkRegisterId", $uniq_id, $uniq_id, $result, "");

echo json_encode($result);
?>