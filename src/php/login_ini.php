<?php
include("/var/www/html/src/php/db.php");
$userDatas = "";
$isUserExist = false;

// Get user datas by login token
$loginToken = $_COOKIE["NKAppsLoginToken"];
$sql = "SELECT uniq_id, id, name, email, state, icon_file_name FROM users WHERE uniq_id = ( SELECT user_id FROM login_tokens WHERE token = :token )";
$resIniGetUserDatas = $PDO->prepare($sql);
$params = array(
  ":token" => $loginToken
);
$resIniGetUserDatas->execute($params);
foreach( $resIniGetUserDatas as $resIniGetUserDatasFor ) {
  $userDatas = $resIniGetUserDatasFor;
  $isUserExist = true;
}

if( !$isUserExist ) {
  echo '<script>alert("ユーザ情報の取得に失敗しました。\n再度ログインをしてください。");</script>';
  echo '<script>window.location.href = "https://nk-apps.net/login/";</script>';
}
?>