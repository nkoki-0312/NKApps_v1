<?php
include("/var/www/html/src/php/login_ini.php");
?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <link rel="icon" href="../src/images/favicon.ico" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>フレンドを追加 | NKApps</title>
  <link rel="stylesheet" href="../src/styles/public.css">
  <link rel="stylesheet" href="../src/styles/add-friend.css">
  <script src="https://kit.fontawesome.com/a377d52115.js" crossorigin="anonymous"></script>
</head>
<body>
  <?php include('../src/includes/login-header.php') ?>

  <div class="single-block">
    <a href="https://nk-apps.net/mypage/">
      <button class="m unstyled"><i class="fa-solid fa-angle-left"></i>&nbsp;戻る</button>
    </a>
    <div class="page-ttl-center">フレンドを追加</div>
    <div class="btn-container">
      <a href="https://nk-apps.net/add-friend/display-qr/">
        <button class="select-btn" style="margin-right: var(--margin-l);">
          <div class="icon"><i class="fa-solid fa-qrcode"></i></div>
          <div class="ttl one-line">二次元コードから追加</div>
        </button>
      </a>
      <a href="https://nk-apps.net/add-friend/id/">
        <button class="select-btn">
          <div class="icon"><i class="fa-solid fa-pen-to-square"></i></div>
          <div class="ttl">ユーザIDまたは<br>メールアドレスから追加</div>
        </button>
      </a>
    </div>
  </div>

  <script src="../src/scripts/public_login.js"></script>
  <script src="../src/scripts/add-friend.js"></script>
</body>
</html>