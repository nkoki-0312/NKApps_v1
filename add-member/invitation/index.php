<?php
include("/var/www/html/src/php/login_ini.php");
$gid = $_GET["gid"];
?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <link rel="icon" href="../../src/images/favicon.ico" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>メンバーを追加 | NKApps</title>
  <link rel="stylesheet" href="../../src/styles/public.css">
  <link rel="stylesheet" href="../../src/styles/add-member/invitation.css">
  <script src="https://kit.fontawesome.com/a377d52115.js" crossorigin="anonymous"></script>
</head>
<body>
  <?php include('/var/www/html/src/includes/login-header.php') ?>

  <div class="single-block">
    <a href="https://nk-apps.net/add-member/?gid=<?php echo $gid; ?>">
      <button class="m unstyled"><i class="fa-solid fa-angle-left"></i>&nbsp;戻る</button>
    </a>
    <div class="page-ttl-center">メンバーを追加</div>
    <div class="explanation">グループに追加するフレンドを選択後、「追加」ボタンを押してください。</div>
    <div class="form">
      <div class="members" id="members">
        
      </div>
    </div>
    <div class="container-center">
      <button class="l primary" id="addBtn" onclick="addMembers()" disabled>追加</button>
    </div>
  </div>

  <script src="../src/scripts/public_login.js"></script>
  <script src="../../src/scripts/add-member/invitation.js"></script>
</body>
</html>