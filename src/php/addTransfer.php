<?php
// INPUT: [amount, date, assetCatFrom, assetCatTo, text]
// OUTPUT: state
// state: "notEnoughAmount": 金額が足りない
//        "success": 成功

include("/var/www/html/src/php/db.php");
include("/var/www/html/src/php/register_log.php");

$raw = file_get_contents("php://input");
$datas = json_decode($raw);
$datetime = date("Y-m-d H:i:s");
$token = $_COOKIE["NKAppsLoginToken"];
$result = "";

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

// Check match group
if( $result == "" ) {
  $sql = "SELECT count(*), group_id FROM asset_cats WHERE uniq_id = :uniq_id_asset AND group_id = ( SELECT group_id FROM asset_cats WHERE uniq_id = :uniq_id )";
  $res = $PDO->prepare($sql);
  $params = array(
    ":uniq_id_asset" => $datas[2],
    ":uniq_id" => $datas[3]
  );
  $res->execute($params);
  foreach( $res as $resFor ) {
    if( $resFor[0] == 0 ) {
      $result = "notMatchGroup";
    } else {
      $group_id = $resFor[1];
    }
  }
}

// Add transfer data
if( $result == "" ) {
  // Create friend uniq id.
  $checkUniqId = true;
  while( $checkUniqId ) {
    $uniqId = "";
    for( $i=0; $i<255; $i++ ){
      if(mt_rand(0,1) == 0){
        $uniqId .= chr(mt_rand(65, 90));
      }else{
        $uniqId .= mt_rand(0, 9);
      }
    }

    $sql = "SELECT count(*) FROM bops WHERE uniq_id = :uniq_id";
    $res = $PDO->prepare($sql);
    $params = array(
      ":uniq_id" => $uniqId
    );
    $res->execute($params);
    foreach( $res as $resFor ){
      if( $resFor[0] == 0 ) $checkUniqId = false;
    }
  }

  $sql = "INSERT INTO transfers (
    uniq_id, asset_cat_id_from, asset_cat_id_to, group_id, type, amount, detail, happen_at, create_at, update_at, create_by, update_by
  ) VALUES (
    :uniq_id, :asset_cat_id_from, :asset_cat_id_to, :group_id, :type, :amount, :detail, :happen_at, :create_at, :update_at, :create_by, :update_by
  )";
  $res = $PDO->prepare($sql);
  $params = array(
    ":uniq_id" => $uniqId,
    ":asset_cat_id_from" => $datas[2],
    ":asset_cat_id_to" => $datas[3],
    ":group_id" => $group_id,
    ":type" => "normal",
    ":amount" => $datas[0],
    ":detail" => $datas[4],
    ":happen_at" => $datas[1],
    ":create_at" => $datetime,
    ":update_at" => $datetime,
    ":create_by" => $userUniqId,
    ":update_by" => $userUniqId
  );
  $res->execute($params);

  $sql = "UPDATE asset_cats SET amount = amount - :amount, update_at = :update WHERE uniq_id = :uniq_id";
  $res = $PDO->prepare($sql);
  $params = array(
    ":amount" => $datas[0],
    ":update" => $datetime,
    ":uniq_id" => $datas[2]
  );
  $res->execute($params);
  
  $sql = "UPDATE asset_cats SET amount = amount + :amount, update_at = :update WHERE uniq_id = :uniq_id";
  $res = $PDO->prepare($sql);
  $params = array(
    ":amount" => $datas[0],
    ":update" => $datetime,
    ":uniq_id" => $datas[3]
  );
  $res->execute($params);
  
  $result = "success";
}

echo json_encode($result);
?>