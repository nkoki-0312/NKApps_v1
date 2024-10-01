<?php
// INPUT: transferId
// OUTPUT: { state }
// state: "notExistTransfer": 対象の取引が存在しない
//        "cannotDeleteTransfer": ユーザに権限がない
//        "notEnoughAmount": 金額が足りない
//        "success": 成功

include('/var/www/html/src/php/db.php');
include('/var/www/html/src/php/register_log.php');

$raw = file_get_contents("php://input");
$transferId = json_decode($raw);
$token = $_COOKIE["NKAppsLoginToken"];
$result = "";
$assetCatIdFrom = "";
$assetCatIdTo = "";
$groupId = "";
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

// Check this transfer exist and get group id.
$sql = "SELECT count(*), asset_cat_id_from, asset_cat_id_to, group_id, amount FROM transfers WHERE uniq_id = :uniq_id";
$res = $PDO->prepare($sql);
$params = array(
  ":uniq_id" => $transferId
);
$res->execute($params);
foreach( $res as $resFor ) {
  if( $resFor[0] == 0 ) {
    $result = "notExistTransfer";
  } else {
    $assetCatIdFrom = $resFor[1];
    $assetCatIdTo = $resFor[2];
    $groupId = $resFor[3];
    $amount = $resFor[4];
  }
}

// Check this user can update
if( $result == "" ) {
  $sql = "SELECT count(*) FROM transfers WHERE ( group_id = :group_id AND create_by = :create_by AND uniq_id = :uniq_id_0 ) OR ( uniq_id = :uniq_id_1 AND group_id IN ( SELECT group_id FROM group_relations WHERE user_id = :user_id AND state = :state ) )";
  $res = $PDO->prepare($sql);
  $params = array(
    ":group_id" => "self",
    ":create_by" => $userUniqId,
    ":uniq_id_0" => $transferId,
    ":uniq_id_1"=> $transferId,
    ":user_id" => $userUniqId,
    ":state" => "admin"
  );
  $res->execute($params);
  foreach( $res as $resFor ) {
    if( $resFor[0] == 0 ) {
      $result = "cannotDeleteTransfer";
    }
  }
}

// Check enough amount
if( $result == "" ) {
  $sql = "SELECT amount FROM asset_cats WHERE uniq_id = :uniq_id";
  $res = $PDO->prepare($sql);
  $params = array(
    ":uniq_id" => $assetCatIdTo
  );
  $res->execute($params);
  foreach( $res as $resFor ) {
    if( $resFor[0] < $amount ) {
      $result = "notEnoughAmount";
    }
  }
}

// If this user can delete transfer, delete transfer.
if( $result == "" ) {
  $sql = "DELETE FROM transfers WHERE uniq_id = :uniq_id";
  $res = $PDO->prepare($sql);
  $params = array(
    ":uniq_id" => $transferId
  );
  $res->execute($params);

  $sql = "UPDATE asset_cats SET amount = amount + :amount WHERE uniq_id = :uniq_id";
  $res = $PDO->prepare($sql);
  $params = array(
    ":amount" => $amount,
    ":uniq_id" => $assetCatIdFrom
  );
  $res->execute($params);

  $sql = "UPDATE asset_cats SET amount = amount - :amount WHERE uniq_id = :uniq_id";
  $res = $PDO->prepare($sql);
  $params = array(
    ":amount" => $amount,
    ":uniq_id" => $assetCatIdTo
  );
  $res->execute($params);
  
  $result = "success";
}

echo json_encode($result);
?>