<?php
// INPUT: None
// OUTPUT: [state, ToDoList]
// state: "notExistGroup": 対象のグループが存在しない。または参加していない。
//        "success": 成功

include("/var/www/html/src/php/db.php");
include("/var/www/html/src/php/register_log.php");

$raw = file_get_contents("php://input");
$datas = json_decode($raw);
$token = $_COOKIE["NKAppsLoginToken"];
$datetime = date("Y-m-d H:i:s");
$result = ["", []];
$count = 0;

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

// Get ToDo datas.
if( $result[0] == "" ) {
  $sql = "SELECT uniq_id, group_id, before_todo_id, ttl, detail, is_checked, bg_clr, font_clr, start_at, limit_at FROM todos WHERE ( group_id = :group_id AND create_by = :create_by ) OR ( group_id IN ( SELECT group_id FROM group_relations WHERE user_id = :user_id ) ) ORDER BY limit_at IS NULL ASC , limit_at ASC";
  $res = $PDO->prepare($sql);
  $params = array(
    ":group_id" => "self",
    ":create_by" => $userUniqId,
    ":user_id" => $userUniqId
  );
  $res->execute($params);
  foreach( $res as $resFor ) {
    array_push( $result[1], $resFor );

    if( $resFor[2] == "none" ) {
      $result[1][$count][10] = 1;
    } else {
      $sql = "SELECT is_checked FROM todos WHERE uniq_id = :uniq_id";
      $resGetBeforeTodo = $PDO->prepare($sql);
      $params = array(
        ":uniq_id" => $resFor[2]
      );
      $resGetBeforeTodo->execute($params);
      foreach( $resGetBeforeTodo as $resGetBeforeTodoFor ) {
        // array_push($result[1][$count], $resFor[0]);
        $result[1][$count][10] = $resGetBeforeTodoFor[0];
      }

      if( count($result[1][$count]) == 10 ) {
        $result[1][$count][10] = 1;
      }
    }

    if( $result[1][$count][1] == "self" ) {
      $result[1][$count][11] = "自分のみ";
    } else {
      $sql = "SELECT name FROM groups WHERE uniq_id = :uniq_id";
      $resGetGroupName = $PDO->prepare($sql);
      $params = array(
        ":uniq_id" => $result[1][$count][1]
      );
      $resGetGroupName->execute($params);
      foreach( $resGetGroupName as $resGetGroupNameFor ) {
        $result[1][$count][11] = $resGetGroupNameFor[0];
      }
    }

    $count++;
  }

  $result[0] = "success";
}

echo json_encode($result);
?>