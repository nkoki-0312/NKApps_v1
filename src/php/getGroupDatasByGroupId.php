<?php
// INPUT: groupId
// OUTPUT: state or {state, [1, uniqId, id, name, password, text]}
// state: "notExistGroup": このグループIDを持つグループが存在しない
//        "success": 成功

include("/var/www/html/src/php/db.php");

$raw = file_get_contents("php://input");
$groupId = json_decode($raw);
$result = [""];

$sql = "SELECT count(*), uniq_id, id, name, password, text FROM groups WHERE uniq_id = :uniq_id";
$res = $PDO->prepare($sql);
$params = array(
  ":uniq_id" => $groupId
);
$res->execute($params);
foreach( $res as $resFor ) {
  if( $resFor[0] == 0 ) {
    $result[0] = "notExistGroup";
  } else {
    $result[0] = "success";
    $result[1] = $resFor;
  }
}
echo json_encode($result);
?>