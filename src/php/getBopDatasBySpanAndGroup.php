<?php
// INPUT: [startAt, endAt, group]
// OUTPUT: [[uniq_id, asset_cat_id, bop_cat_id, group_id, type, amount, detail, happen_at, asset_cat_name, bop_cat_name, group_name], [uniq_id, asset_cat_id, bop_cat_id, group_id, type, amount, detail, happen_at, asset_cat_name, bop_cat_name, group_name], [uniq_id, asset_cat_id_from, asset_cat_id_to, amount, detail, happen_at, asset_cat_name_from, asset_cat_name_to, group_name], total]
//         ↑ [expenseList, incomeList, transferList]の順

include("/var/www/html/src/php/db.php");

$raw = file_get_contents("php://input");
$datas = json_decode($raw);
// $datas = ["2024-06-01", "2024-06-30", "self"];
$token = $_COOKIE["NKAppsLoginToken"];
$result = [[], [], [], 0];

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

// Get expense list
$sql = "SELECT uniq_id, asset_cat_id, bop_cat_id, group_id, type, amount, detail, happen_at FROM bops WHERE type = :type AND happen_at >= :start_at AND happen_at <= :end_at AND group_id = :select_group_id AND ( ( group_id = :group_id AND create_by = :create_by ) OR ( group_id IN ( SELECT group_id FROM group_relations WHERE user_id = :user_id ) ) ) ORDER BY happen_at";
$res = $PDO->prepare($sql);
$params = array(
  ":type" => "expense",
  ":start_at" => $datas[0],
  ":end_at" => $datas[1],
  ":select_group_id" => $datas[2],
  ":group_id" => "self",
  ":create_by" => $userUniqId,
  ":user_id" => $userUniqId
);
$res->execute($params);
$count = 0;
foreach( $res as $resFor ) {
  $result[0][$count] = $resFor;

  // Get asset cat name
  $sql = "SELECT name FROM asset_cats WHERE uniq_id = :uniq_id";
  $resGetName = $PDO->prepare($sql);
  $params = array(
    ":uniq_id" => $resFor[1]
  );
  $resGetName->execute($params);
  foreach( $resGetName as $resGetNameFor ) {
    $result[0][$count][8] = $resGetNameFor[0];
  }

  // Get bop cat name
  $sql = "SELECT name FROM bop_cats WHERE uniq_id = :uniq_id";
  $resGetName = $PDO->prepare($sql);
  $params = array(
    ":uniq_id" => $resFor[2]
  );
  $resGetName->execute($params);
  foreach( $resGetName as $resGetNameFor ) {
    $result[0][$count][9] = $resGetNameFor[0];
  }

  // Get group name
  if( $resFor[3] == "self" ) {
    $result[0][$count][10] = "自分のみ";
  } else {
    $sql = "SELECT name FROM groups WHERE uniq_id = :uniq_id";
    $resGetName = $PDO->prepare($sql);
    $params = array(
      ":uniq_id" => $resFor[3]
    );
    $resGetName->execute($params);
    foreach( $resGetName as $resGetNameFor ) {
      $result[0][$count][10] = $resGetNameFor[0];
    }
  }
  $count++;
}

// Get income list
$sql = "SELECT uniq_id, asset_cat_id, bop_cat_id, group_id, type, amount, detail, happen_at FROM bops WHERE type = :type AND happen_at >= :start_at AND happen_at <= :end_at AND group_id = :select_group_id AND ( ( group_id = :group_id AND create_by = :create_by ) OR ( group_id IN ( SELECT group_id FROM group_relations WHERE user_id = :user_id ) ) ) ORDER BY happen_at";
$res = $PDO->prepare($sql);
$params = array(
  ":type" => "income",
  ":start_at" => $datas[0],
  ":end_at" => $datas[1],
  ":select_group_id" => $datas[2],
  ":group_id" => "self",
  ":create_by" => $userUniqId,
  ":user_id" => $userUniqId
);
$res->execute($params);
$count = 0;
foreach( $res as $resFor ) {
  $result[1][$count] = $resFor;

  // Get asset cat name
  $sql = "SELECT name FROM asset_cats WHERE uniq_id = :uniq_id";
  $resGetName = $PDO->prepare($sql);
  $params = array(
    ":uniq_id" => $resFor[1]
  );
  $resGetName->execute($params);
  foreach( $resGetName as $resGetNameFor ) {
    $result[1][$count][8] = $resGetNameFor[0];
  }

  // Get bop cat name
  $sql = "SELECT name FROM bop_cats WHERE uniq_id = :uniq_id";
  $resGetName = $PDO->prepare($sql);
  $params = array(
    ":uniq_id" => $resFor[2]
  );
  $resGetName->execute($params);
  foreach( $resGetName as $resGetNameFor ) {
    $result[1][$count][9] = $resGetNameFor[0];
  }

  // Get group name
  if( $resFor[3] == "self" ) {
    $result[1][$count][10] = "自分のみ";
  } else {
    $sql = "SELECT name FROM groups WHERE uniq_id = :uniq_id";
    $resGetName = $PDO->prepare($sql);
    $params = array(
      ":uniq_id" => $resFor[3]
    );
    $resGetName->execute($params);
    foreach( $resGetName as $resGetNameFor ) {
      $result[1][$count][10] = $resGetNameFor[0];
    }
  }

  $count++;
}

// Get transfer list
$sql = "SELECT uniq_id, asset_cat_id_from, asset_cat_id_to, group_id, amount, detail, happen_at FROM transfers WHERE type <> :type AND happen_at >= :start_at AND happen_at <= :end_at AND group_id = :select_group_id AND ( ( group_id = :group_id AND create_by = :create_by ) OR ( group_id IN ( SELECT group_id FROM group_relations WHERE user_id = :user_id ) ) ) ORDER BY happen_at";
$res = $PDO->prepare($sql);
$params = array(
  ":type" => "deleted",
  ":start_at" => $datas[0],
  ":end_at" => $datas[1],
  ":select_group_id" => $datas[2],
  ":group_id" => "self",
  ":create_by" => $userUniqId,
  ":user_id" => $userUniqId
);
$res->execute($params);
$count = 0;
foreach( $res as $resFor ) {
  $result[2][$count] = $resFor;

  // Get asset cat name
  $sql = "SELECT name FROM asset_cats WHERE uniq_id = :uniq_id";
  $resGetName = $PDO->prepare($sql);
  $params = array(
    ":uniq_id" => $resFor[1]
  );
  $resGetName->execute($params);
  foreach( $resGetName as $resGetNameFor ) {
    $result[2][$count][7] = $resGetNameFor[0];
  }

  // Get asset cat name
  $sql = "SELECT name FROM asset_cats WHERE uniq_id = :uniq_id";
  $resGetName = $PDO->prepare($sql);
  $params = array(
    ":uniq_id" => $resFor[2]
  );
  $resGetName->execute($params);
  foreach( $resGetName as $resGetNameFor ) {
    $result[2][$count][8] = $resGetNameFor[0];
  }

  // Get group name
  if( $resFor[3] == "self" ) {
    $result[2][$count][9] = "自分のみ";
  } else {
    $sql = "SELECT name FROM groups WHERE uniq_id = :uniq_id";
    $resGetName = $PDO->prepare($sql);
    $params = array(
      ":uniq_id" => $resFor[3]
    );
    $resGetName->execute($params);
    foreach( $resGetName as $resGetNameFor ) {
      $result[2][$count][9] = $resGetNameFor[0];
    }
  }

  $count++;
}

// Get total
$sql = "SELECT sum(amount) FROM asset_cats WHERE type <> :type AND group_id = :select_group_id AND ( ( group_id = :group_id AND user_id = :user_id ) OR ( group_id IN ( SELECT group_id FROM group_relations WHERE user_id = :user_id ) ) )";
$res = $PDO->prepare($sql);
$params = array(
  ":type" => "deleted",
  ":select_group_id" => $datas[2],
  ":group_id" => "self",
  ":user_id" => $userUniqId,
  ":user_id" => $userUniqId
);
$res->execute($params);
foreach( $res as $resFor ) {
  $result[3] = $resFor[0];
}

echo json_encode($result);
?>