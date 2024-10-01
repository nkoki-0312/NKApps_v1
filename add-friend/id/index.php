<?php
include("/var/www/html/src/php/login_ini.php");
?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <link rel="icon" href="../../src/images/favicon.ico" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>フレンドを追加 | NKApps</title>
  <link rel="stylesheet" href="../../src/styles/public.css">
  <link rel="stylesheet" href="../../src/styles/add-friend/id.css">
  <script src="https://kit.fontawesome.com/a377d52115.js" crossorigin="anonymous"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script> 
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.qrcode/1.0/jquery.qrcode.min.js"></script> 
</head>
<body>
  <?php include('../../src/includes/login-header.php') ?>

  <div class="single-block">
    <a href="https://nk-apps.net/add-friend/">
      <button class="m unstyled"><i class="fa-solid fa-angle-left"></i>&nbsp;戻る</button>
    </a>
    <div class="page-ttl-center">フレンドを追加</div>
    <div class="explanation">フレンドのユーザIDまたはメールアドレスを入力後「追加」ボタンを押してください。</div>
    <div class="form">
      <div class="row">
        <div class="ttl"><span class="must-form">必須</span>ユーザIDまたはメールアドレス</div>
        <input type="text" id="id" onchange="checkCompleteForm();">
        <div class="err" id="errId"></div>
        <div class="form-explanation">※半角で入力してください。</div>
      </div>
    </div>
    <div class="container-center">
      <button class="l primary" id="addBtn" style="margin-top: var(--margin-l);" onclick="addFriend();" disabled>追加</button>
    </div>
  </div>

  <script src="../src/scripts/public_login.js"></script>
  <script src="../../src/scripts/jsQR.js"></script>
  <script src="../../src/scripts/add-friend/id.js"></script>
</body>
</html>