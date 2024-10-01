<?php
include("/var/www/html/src/php/login_ini.php");

$uniq_id = $_GET["uid"];
$sql = "SELECT count(*) FROM users WHERE uniq_id = :uniq_id AND state = :state";
$resCheck = $PDO->prepare($sql);
$params = array(
  ":uniq_id" => $uniq_id,
  ":state" => 2
);
$resCheck->execute($params);
foreach( $resCheck as $num ) {
  if( $num[0] == 1 ) {
    $sql = "UPDATE users SET state = :state, update_at = :update_at WHERE uniq_id = :uniq_id";
    $res = $PDO->prepare($sql);
    $params = array(
      ":state" => 1,
      ":update_at" => date("Y-m-d H:i:s"),
      ":uniq_id" => $uniq_id
    );
    $res->execute($params);

    echo '<script>alert("メールアドレスの認証が完了しました。\nマイページへ遷移します。");</script>';
    echo '<script>window.location.href = "https://nk-apps.net/mypage/";</script>';
  } else {
    echo '<script>alert("メールアドレスの有効性が確認できませんでした。\nURLがすべて入力されていることを確認して、再度お試しください。");</script>';
    echo '<script>window.location.href = "https://nk-apps.net/";</script>';
  }
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <link rel="icon" href="../src/images/favicon.ico" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>メールアドレス認証 | NKApps</title>
  <link rel="stylesheet" href="../src/styles/public.css">
  <link rel="stylesheet" href="../src/styles/check-email.css">
  <script src="https://kit.fontawesome.com/a377d52115.js" crossorigin="anonymous"></script>
</head>
<body>
  <?php include('../src/includes/unfunction-header.php') ?>

  <div class="single-block">
    <div class="explanation">メールアドレスの有効性を確認しています。しばらくお待ちください。</div>
  </div>

  <script src="../src/scripts/check-email.js"></script>
</body>
</html>