<?php
// INPUT: groupId
// OUTPUT: [[uniqId, id, name, password, text, createAt, memberNum],
//          [[userUniqId, userId, userName, iconFileName, state], ...],
//          {myState}]
// myState: "member": 通常ユーザ
//          "admin": 管理者

include('/var/www/html/src/php/db.php');

$raw = file_get_contents("php://input");
$groupId = json_decode($raw);
$token = $_COOKIE["NKAppsLoginToken"];
$result = [[], [], ""];
$memberCounter = 0;

// Get my uniq id.
$sql = "SELECT user_id FROM login_tokens WHERE token = :token";
$resGetMyUniqId = $PDO->prepare($sql);
$params = array(
  ":token" => $token
);
$resGetMyUniqId->execute($params);
foreach( $resGetMyUniqId as $resGetMyUniqIdFor ) {
  $myUniqId = $resGetMyUniqIdFor[0];
}

// Get group basic datas.
$sql = "SELECT uniq_id, id, name, password, text, create_at FROM groups WHERE uniq_id = :uniq_id";
$resGetGroupDatas = $PDO->prepare($sql);
$params = array(
  ":uniq_id" => $groupId
);
$resGetGroupDatas->execute($params);
foreach( $resGetGroupDatas as $resGetGroupDatasFor ) {
  $result[0] = $resGetGroupDatasFor;
}

// Get group member who is "admin"'s datas.
$sql = "SELECT uniq_id, id, name, icon_file_name FROM users WHERE uniq_id IN ( SELECT user_id FROM group_relations WHERE group_id = :group_id AND state = :state )";
$resGetMmeberDatas = $PDO->prepare($sql);
$params = array(
  ":group_id" => $groupId,
  ":state" => "admin"
);
$resGetMmeberDatas->execute($params);
foreach( $resGetMmeberDatas as $resGetMmeberData ) {
  $sql = "SELECT state FROM group_relations WHERE user_id = :user_id AND group_id = :group_id";
  $resGetUserState = $PDO->prepare($sql);
  $params = array(
    ":user_id" => $resGetMmeberData[0],
    ":group_id" => $groupId
  );
  $resGetUserState->execute($params);
  foreach( $resGetUserState as $resGetUserStateFor ) {
    $result[1][$memberCounter] = $resGetMmeberData;
    $result[1][$memberCounter][4] = $resGetUserStateFor[0];

    if( $resGetMmeberData[0] == $myUniqId ) {
      $result[2] = $resGetUserStateFor[0];
    }

    $memberCounter++;
  }
}

// Get group member who is "member"'s datas.
$sql = "SELECT uniq_id, id, name, icon_file_name FROM users WHERE uniq_id IN ( SELECT user_id FROM group_relations WHERE group_id = :group_id AND state = :state )";
$resGetMmeberDatas = $PDO->prepare($sql);
$params = array(
  ":group_id" => $groupId,
  ":state" => "member"
);
$resGetMmeberDatas->execute($params);
foreach( $resGetMmeberDatas as $resGetMmeberData ) {
  $sql = "SELECT state FROM group_relations WHERE user_id = :user_id AND group_id = :group_id";
  $resGetUserState = $PDO->prepare($sql);
  $params = array(
    ":user_id" => $resGetMmeberData[0],
    ":group_id" => $groupId
  );
  $resGetUserState->execute($params);
  foreach( $resGetUserState as $resGetUserStateFor ) {
    $result[1][$memberCounter] = $resGetMmeberData;
    $result[1][$memberCounter][4] = $resGetUserStateFor[0];

    if( $resGetMmeberData[0] == $myUniqId ) {
      $result[2] = $resGetUserStateFor[0];
    }

    $memberCounter++;
  }
}
$result[0][6] = $memberCounter;

echo json_encode($result);
?>