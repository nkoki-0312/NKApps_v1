<?php
include("/var/www/html/src/php/login_ini.php");
$gid = $_GET["gid"];
?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <link rel="icon" href="../src/images/favicon.ico" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>メンバーを追加 | NKApps</title>
  <link rel="stylesheet" href="../src/styles/public.css">
  <link rel="stylesheet" href="../src/styles/add-member.css">
  <script src="https://kit.fontawesome.com/a377d52115.js" crossorigin="anonymous"></script>
</head>
<body>
  <?php include('../src/includes/login-header.php') ?>

  <div class="single-block">
    <a href="https://nk-apps.net/group/?gid=<?php echo $gid; ?>">
      <button class="m unstyled"><i class="fa-solid fa-angle-left"></i>&nbsp;戻る</button>
    </a>
    <div class="page-ttl-center">メンバーを追加</div>
    <div class="btn-container">
      <a href="https://nk-apps.net/add-member/display-qr/?gid=<?php echo $gid; ?>">
        <button class="select-btn" style="margin-right: var(--margin-l);">
          <div class="icon"><i class="fa-solid fa-qrcode"></i></div>
          <div class="ttl" style="line-height: calc( ( var(--font-size-xl) * var(--line-height-l) ) * 2 );">二次元コードから追加</div>
        </button>
      </a>
      <a href="https://nk-apps.net/add-member/invitation/?gid=<?php echo $gid; ?>">
        <button class="select-btn">
          <div class="icon"><i class="fa-solid fa-pen-to-square"></i></div>
          <div class="ttl" style="line-height: calc( ( var(--font-size-xl) * var(--line-height-l) ) * 2 );">フレンドをメンバーに追加</div>
        </button>
      </a>
    </div>
  </div>

  <script src="../src/scripts/public_login.js"></script>
  <script src="../src/scripts/add-member.js"></script>
</body>
</html>