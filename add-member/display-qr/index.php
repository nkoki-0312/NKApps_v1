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
  <link rel="stylesheet" href="../../src/styles/add-member/display-qr.css">
  <script src="https://kit.fontawesome.com/a377d52115.js" crossorigin="anonymous"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script> 
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.qrcode/1.0/jquery.qrcode.min.js"></script> 
</head>
<body>
  <?php include('../../src/includes/login-header.php') ?>

  <div class="single-block">
    <a href="https://nk-apps.net/add-member/?gid=<?php echo $gid; ?>">
      <button class="m unstyled"><i class="fa-solid fa-angle-left"></i>&nbsp;戻る</button>
    </a>
    <div class="page-ttl-center">メンバーを追加</div>
    <div class="explanation">追加するユーザに二次元コードを提示してください。二次元コードを読み取る場合は、下部の「カメラを起動」ボタンを押してください。</div>
    <div class="qr-container container-center" style="margin-bottom: var(--margin-s);">
      <div id="myQr"></div>
    </div>
    <a href="https://nk-apps.net/join-group/scan-qr/">
      <button class="m secondary width-full">カメラを起動</button>
    </a>
  </div>

  <script src="../src/scripts/public_login.js"></script>
  <script src="../../src/scripts/add-member/display-qr.js"></script>
</body>
</html>