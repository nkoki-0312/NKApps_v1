<?php
// INPUT: todoId
// OUTPUT: [state, [uniq_id, group_id, before_todo_id, ttl, detail, is_checked, bg_clr, font_clr, start_at, limit_at, create_at, update_at, check_at, create_by, update_by, check_by, group_name, before_todo_ttl create_user_name, update_user_name, check_user_name, group_state], memberList]
// state: "cannotGetToDo": 自身が関わっている予定でないため、取得できない
//        "notExistToDo": 対象のToDoが存在しない
//        "success": 成功

include("/var/www/html/src/php/db.php");
include("/var/www/html/src/php/register_log.php");

$raw = file_get_contents("php://input");
$todoId = json_decode($raw);
$token = $_COOKIE["NKAppsLoginToken"];
$result = ["", [], []];

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

// Check ToDo exist
$sql = "SELECT count(*) FROM todos WHERE uniq_id = :uniq_id";
$res = $PDO->prepare($sql);
$params = array(
  ":uniq_id" => $todoId
);
$res->execute($params);
foreach( $res as $resFor ) {
  if( $resFor[0] == 0 ) {
    $result[0] = "notExistToDo";
  }
}

// Check this user can get the ToDo
if( $result[0] == "" ) {
  $sql = "SELECT count(*) FROM todos WHERE uniq_id = :uniq_id AND ( ( group_id = :group_id AND create_by = :create_by ) OR ( group_id IN ( SELECT group_id FROM group_relations WHERE user_id = :user_id ) ) )";
  $res = $PDO->prepare($sql);
  $params = array(
    ":uniq_id" => $todoId,
    ":group_id" => "self",
    ":create_by" => $userUniqId,
    ":user_id" => $userUniqId
  );
  $res->execute($params);
  foreach( $res as $resFor ) {
    if( $resFor[0] == 0 ) {
      $result[0] = "cannotGetToDo";
    }
  }
}

// Get ToDo datas
if( $result[0] == "" ) {
  // Get basic datas
  $sql = "SELECT * FROM todos WHERE uniq_id = :uniq_id";
  $res = $PDO->prepare($sql);
  $params = array(
    ":uniq_id" => $todoId
  );
  $res->execute($params);
  foreach( $res as $resFor ) {
    $result[1] = $resFor;
  }

  // Get group name
  if( $result[1][1] == "self" ) {
    $result[1][16] = "自分のみ";
  } else {
    $sql = "SELECT name FROM groups WHERE uniq_id = :uniq_id";
    $res = $PDO->prepare($sql);
    $params = array(
      ":uniq_id" => $result[1][1]
    );
    $res->execute($params);
    foreach( $res as $resFor ) {
      $result[1][16] = $resFor[0];
    }
  }

  // Get before ToDo name
  if( $result[1][2] == "none" ) {
    $result[1][17] = "設定されていません";
  } else {
    $sql = "SELECT ttl FROM todos WHERE uniq_id = :uniq_id";
    $res = $PDO->prepare($sql);
    $params = array(
      ":uniq_id" => $result[1][2]
    );
    $res->execute($params);
    foreach( $res as $resFor ) {
      $result[1][17] = $resFor[0];
    }
  }

  // Get create user name
  $sql = "SELECT name FROM users WHERE uniq_id = :uniq_id";
  $res = $PDO->prepare($sql);
  $params = array(
    ":uniq_id" => $result[1][13]
  );
  $res->execute($params);
  foreach( $res as $resFor ) {
    $result[1][18] = $resFor[0];
  }

  // Get update user name
  $sql = "SELECT name FROM users WHERE uniq_id = :uniq_id";
  $res = $PDO->prepare($sql);
  $params = array(
    ":uniq_id" => $result[1][14]
  );
  $res->execute($params);
  foreach( $res as $resFor ) {
    $result[1][19] = $resFor[0];
  }

  // Get check user name
  if( $result[1][15] == "" ) {
    $result[1][20] = "このToDoは完了していません。";
  } else {
    $sql = "SELECT name FROM users WHERE uniq_id = :uniq_id";
    $res = $PDO->prepare($sql);
    $params = array(
      ":uniq_id" => $result[1][15]
    );
    $res->execute($params);
    foreach( $res as $resFor ) {
      $result[1][20] = $resFor[0];
    }
  }  

  // Get user state to group
  if( $result[1][1] == "self" ) {
    $result[1][21] = "admin";
  } else {
    $sql = "SELECT state FROM group_relations WHERE user_id = :user_id AND group_id = :group_id";
    $res = $PDO->prepare($sql);
    $params = array(
      ":user_id" => $userUniqId,
      ":group_id" => $result[1][1]
    );
    $res->execute($params);
    foreach( $res as $resFor ) {
      $result[1][21] = $resFor[0];
    }
  }

  $sql = "SELECT uniq_id, id, name, icon_file_name FROM users WHERE uniq_id IN ( SELECT user_id FROM group_relations WHERE group_id = :group_id )";
  $res = $PDO->prepare($sql);
  $params = array(
    ":group_id" => $result[1][1]
  );
  $res->execute($params);
  foreach( $res as $resFor ) {
    array_push($result[2], $resFor);
  }

  $result[0] = "success";
}

echo json_encode($result);
?>