<?php
include("/var/www/html/src/php/login_ini.php");
?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <link rel="icon" href="../src/images/favicon.ico" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>ポータル | NKApps</title>
  <link rel="stylesheet" href="../src/styles/public.css">
  <link rel="stylesheet" href="../src/styles/portal.css">
  <script src="https://kit.fontawesome.com/a377d52115.js" crossorigin="anonymous"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script> 
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.qrcode/1.0/jquery.qrcode.min.js"></script> 
</head>
<body>
  <?php include('../src/includes/login-header.php') ?>

  <div class="single-block-large">
    <a href="https://nk-apps.net/calendar/">
      <button class="l secondary">カレンダー</button>
    </a>
    <a href="https://nk-apps.net/todo/">
      <button class="l secondary">ToDoリスト</button>
    </a>
    <a href="https://nk-apps.net/kakeibo/">
      <button class="l secondary">家計簿</button>
    </a>
  </div>

  <div id="mouseCircle"></div>
  
  <script src="../src/scripts/public_login.js"></script>
  <script src="../src/scripts/portal.js"></script>
</body>
</html>