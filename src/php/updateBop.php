<?php
// INPUT: {amount, date, assetCatId, bopCatId, text, bopId, groupId, beforeAmount }
// OUTPUT: state
// state: "notEnoughAmount": 金額が足りない
//        "notMatchGroup": 資産カテゴリーと収支カテゴリーのグループが一致しない
//        "cannotEditBop": 編集権限がない
//        "success": 成功

include("/var/www/html/src/php/db.php");
include("/var/www/html/src/php/register_log.php");

$raw = file_get_contents("php://input");
$datas = json_decode($raw);
$token = $_COOKIE["NKAppsLoginToken"];
$result = "";
$userState = "";
$datetime = date("Y-m-d H:i:s");
$userState = "";

// Get user uniq id
$sql = "SELECT user_id FROM login_tokens WHERE token = :token";
$res = $PDO->prepare($sql);
$params = array(
  ":token" => $token
);
$res->execute($params);
foreach( $res as $resFor ) {
  $userUniqId = $resFor[0];
}

// Check this user can update
$sql = "SELECT count(*), asset_cat_id, type FROM bops WHERE ( group_id = :group_id AND create_by = :create_by AND uniq_id = :uniq_id_0 ) OR ( uniq_id = :uniq_id_1 AND group_id IN ( SELECT group_id FROM group_relations WHERE user_id = :user_id AND state = :state ) )";
$res = $PDO->prepare($sql);
$params = array(
  ":group_id" => "self",
  ":create_by" => $userUniqId,
  ":uniq_id_0" => $datas[5],
  ":uniq_id_1"=> $datas[5],
  ":user_id" => $userUniqId,
  ":state" => "admin"
);
$res->execute($params);
foreach( $res as $resFor ) {
  if( $resFor[0] == 0 ) {
    $result = "cannotEditBop";
  } else {
    $beforeAssetCatId = $resFor[1];
    $beforeType = $resFor[2];
  }
}

// Check match group
$sql = "SELECT count(*) FROM asset_cats WHERE uniq_id = :uniq_id_asset AND group_id = ( SELECT group_id FROM bop_cats WHERE uniq_id = :uniq_id )";
$res = $PDO->prepare($sql);
$params = array(
  ":uniq_id_asset" => $datas[2],
  ":uniq_id" => $datas[3]
);
$res->execute($params);
foreach( $res as $resFor ) {
  if( $resFor[0] == 0 ) {
    $result = "notMatchGroup";
  }
}

// Check enough amount
if( $result == "" ) {
  $sql = "SELECT amount FROM asset_cats WHERE uniq_id = :uniq_id";
  $res = $PDO->prepare($sql);
  $params = array(
    ":uniq_id" => $datas[2]
  );
  $res->execute($params);
  foreach( $res as $resFor ) {
    if( $resFor[0] < $datas[0] ) {
      $result = "notEnoughAmount";
    }
  }
}

// Update bop
if( $result == "" ) {
  $sql = "UPDATE bops SET amount = :amount, asset_cat_id = :asset_cat_id, bop_cat_id = :bop_cat_id, detail = :detail, happen_at = :happen_at, update_at = :update_at, update_by = :update_by WHERE uniq_id = :uniq_id";
  $res = $PDO->prepare($sql);
  $params = array(
    ":amount" => $datas[0],
    ":asset_cat_id" => $datas[2],
    ":bop_cat_id" => $datas[3],
    ":detail" => $datas[4],
    ":happen_at" => $datas[1],
    ":update_at" => $datetime,
    ":update_by" => $userUniqId,
    ":uniq_id" => $datas[5]
  );
  $res->execute($params);

  if( $beforeType == "expense" ) {
    $sql = "UPDATE asset_cats SET amount = amount + :amount, update_at = :update_at WHERE uniq_id = :uniq_id";
    $res = $PDO->prepare($sql);
    $params = array(
      ":amount" => $datas[7],
      ":update_at" => $datetime,
      ":uniq_id" => $beforeAssetCatId
    );
    $res->execute($params);
    
    $sql = "UPDATE asset_cats SET amount = amount - :amount, update_at = :update_at WHERE uniq_id = :uniq_id";
    $res = $PDO->prepare($sql);
    $params = array(
      ":amount" => $datas[0],
      ":update_at" => $datetime,
      ":uniq_id" => $datas[2]
    );
    $res->execute($params);
  } else {
    $sql = "UPDATE asset_cats SET amount = amount - :amount, update_at = :update_at WHERE uniq_id = :uniq_id";
    $res = $PDO->prepare($sql);
    $params = array(
      ":amount" => $datas[7],
      ":update_at" => $datetime,
      ":uniq_id" => $beforeAssetCatId
    );
    $res->execute($params);

    $sql = "UPDATE asset_cats SET amount = amount + :amount, update_at = :update_at WHERE uniq_id = :uniq_id";
    $res = $PDO->prepare($sql);
    $params = array(
      ":amount" => $datas[0],
      ":update_at" => $datetime,
      ":uniq_id" => $datas[2]
    );
    $res->execute($params);
  }

  $result = "success";
}

echo json_encode($result);
?>