<?php
// INPUT: bopId
// OUTPUT: { state }
// state: "notExistBop": 対象の取引が存在しない
//        "cannotDeleteBop": ユーザに権限がない
//        "success": 成功

include('/var/www/html/src/php/db.php');
include('/var/www/html/src/php/register_log.php');

$raw = file_get_contents("php://input");
$bopId = json_decode($raw);
$token = $_COOKIE["NKAppsLoginToken"];
$result = "";
$asset_cat = "";
$groupId = "";
$type = "";
$amount = 0;
$userState = "";

// Get user uniq id.
$sql = "SELECT user_id FROM login_tokens WHERE token = :token";
$res = $PDO->prepare($sql);
$params = array(
  ":token" => $token
);
$res->execute($params);
foreach( $res as $resFor ) {
  $userUniqId = $resFor[0];
}

// Check this bop exist and get group id.
$sql = "SELECT count(*), asset_cat_id, group_id, type, amount FROM bops WHERE uniq_id = :uniq_id";
$res = $PDO->prepare($sql);
$params = array(
  ":uniq_id" => $bopId
);
$res->execute($params);
foreach( $res as $resFor ) {
  if( $resFor[0] == 0 ) {
    $result = "notExistBop";
  } else {
    $asset_cat = $resFor[1];
    $groupId = $resFor[2];
    $type = $resFor[3];
    $amount = $resFor[4];
  }
}

// Check this user can update
$sql = "SELECT count(*) FROM bops WHERE ( group_id = :group_id AND create_by = :create_by AND uniq_id = :uniq_id_0 ) OR ( uniq_id = :uniq_id_1 AND group_id IN ( SELECT group_id FROM group_relations WHERE user_id = :user_id AND state = :state ) )";
$res = $PDO->prepare($sql);
$params = array(
  ":group_id" => "self",
  ":create_by" => $userUniqId,
  ":uniq_id_0" => $bopId,
  ":uniq_id_1"=> $bopId,
  ":user_id" => $userUniqId,
  ":state" => "admin"
);
$res->execute($params);
foreach( $res as $resFor ) {
  if( $resFor[0] == 0 ) {
    $result = "cannotDeleteBop";
  }
}

// If this user can delete bop, delete bop.
if( $result == "" ) {
  $sql = "DELETE FROM bops WHERE uniq_id = :uniq_id";
  $res = $PDO->prepare($sql);
  $params = array(
    ":uniq_id" => $bopId
  );
  $res->execute($params);

  if( $type == "expense" ) {
    $sql = "UPDATE asset_cats SET amount = amount + :amount WHERE uniq_id = :uniq_id";
    $res = $PDO->prepare($sql);
    $params = array(
      ":amount" => $amount,
      ":uniq_id" => $asset_cat
    );
    $res->execute($params);
  } else {
    $sql = "UPDATE asset_cats SET amount = amount - :amount WHERE uniq_id = :uniq_id";
    $res = $PDO->prepare($sql);
    $params = array(
      ":amount" => $amount,
      ":uniq_id" => $asset_cat
    );
    $res->execute($params);
  }
  
  $result = "success";
}

echo json_encode($result);
?>