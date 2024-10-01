<?php
// INPUT: [amount, date, assetCat, bopCat, text]
// OUTPUT: state
// state: "notMatchGroup": 資産カテゴリーと収支カテゴリーのグループが一致しない
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

// Add income data
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

  // Get group_id
  $sql = "SELECT group_id FROM asset_cats WHERE uniq_id = :uniq_id";
  $res = $PDO->prepare($sql);
  $params = array(
    ":uniq_id" => $datas[2]
  );
  $res->execute($params);
  foreach( $res as $resFor ) {
    $groupId = $resFor[0];
  }

  $sql = "INSERT INTO bops (
    uniq_id, asset_cat_id, bop_cat_id, group_id, type, amount, detail, happen_at, create_at, update_at, create_by, update_by
  ) VALUES (
    :uniq_id, :asset_cat_id, :bop_cat_id, :group_id, :type, :amount, :detail, :happen_at, :create_at, :update_at, :create_by, :update_by
  )";
  $res = $PDO->prepare($sql);
  $params = array(
    ":uniq_id" => $uniqId,
    ":asset_cat_id" => $datas[2],
    ":bop_cat_id" => $datas[3],
    ":group_id" => $groupId,
    ":type" => "income",
    ":amount" => $datas[0],
    ":detail" => $datas[4],
    ":happen_at" => $datas[1],
    ":create_at" => $datetime,
    ":update_at" => $datetime,
    ":create_by" => $userUniqId,
    ":update_by" => $userUniqId
  );
  $res->execute($params);

  $sql = "UPDATE asset_cats SET amount = amount + :amount, update_at = :update WHERE uniq_id = :uniq_id";
  $res = $PDO->prepare($sql);
  $params = array(
    ":amount" => $datas[0],
    ":update" => $datetime,
    ":uniq_id" => $datas[2]
  );
  $res->execute($params);
  
  $result = "success";
}

echo json_encode($result);
?>