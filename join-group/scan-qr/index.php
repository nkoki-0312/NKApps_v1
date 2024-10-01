<?php
include("/var/www/html/src/php/login_ini.php");
?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <link rel="icon" href="../../src/images/favicon.ico" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>グループに参加 | NKApps</title>
  <link rel="stylesheet" href="../../src/styles/public.css">
  <link rel="stylesheet" href="../../src/styles/join-group/scan-qr.css">
  <script src="https://kit.fontawesome.com/a377d52115.js" crossorigin="anonymous"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script> 
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.qrcode/1.0/jquery.qrcode.min.js"></script> 
</head>
<body>
  <?php include('../../src/includes/login-header.php') ?>

  <div class="single-block">
    <a href="https://nk-apps.net/join-group/">
      <button class="m unstyled"><i class="fa-solid fa-angle-left"></i>&nbsp;戻る</button>
    </a>
    <div class="page-ttl-center">グループに参加</div>
    <div class="explanation">グループ招待用の二次元コードをスキャンしてください。</div>
    <div id="wrapper">
      <video id="video" autoplay muted playsinline></video>
      <canvas id="camera-canvas"></canvas>
      <canvas id="rect-canvas"></canvas>
    </div>
  </div>

  <script src="../src/scripts/public_login.js"></script>
  <script src="../../src/scripts/jsQR.js"></script>
  <script src="../../src/scripts/join-group/scan-qr.js"></script>
</body>
</html>