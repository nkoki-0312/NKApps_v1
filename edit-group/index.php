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
  <title>グループ情報を編集 | NKApps</title>
  <link rel="stylesheet" href="../src/styles/public.css">
  <link rel="stylesheet" href="../src/styles/edit-group.css">
  <script src="https://kit.fontawesome.com/a377d52115.js" crossorigin="anonymous"></script>
</head>
<body>
  <?php include('../src/includes/login-header.php') ?>

  <div class="single-block">  
    <a href="https://nk-apps.net/group/?gid=<?php echo $gid; ?>">
      <button class="m unstyled"><i class="fa-solid fa-angle-left"></i>&nbsp;戻る</button>
    </a>  
    <div class="page-ttl-center">グループ情報を編集</div>

    <div class="explanation">編集後「更新」ボタンを押してください。</div>
    <div class="form">
      <div class="row">
        <div class="ttl"><span class="must-form">必須</span>グループ名</div>
        <input type="text" id="name" onchange="checkCompleteForm()">
        <div class="err" id="errName"></div>
        <div class="form-explanation">※32文字以内で入力してください。</div>
      </div>
      <div class="row">
        <div class="ttl">グループID</div>
        <input type="text" id="id">
        <div class="err" id="errId"></div>
        <div class="form-explanation">※グループIDを設定すると、検索しやすくなります。</div>
        <div class="form-explanation">※32文字以内で入力してください。</div>
        <div class="form-explanation">※半角英数字とハイフン(-)が使用できます。</div>
      </div>
      <div class="row">
        <div class="ttl">パスワード</div>
        <label for="usePassword" class="checkbox-wrap">
          <input type="checkbox" id="usePassword" onchange="changeUsePassword()">
          <span class="checkmark"></span>パスワードを設定する
        </label>
        <input type="password" id="password" onchange="checkCompleteForm()" placeholder="新しいパスワード">
        <input type="password" id="confirmPassword" placeholder="新しいパスワード(再入力)"  onchange="checkCompleteForm()">
        <div class="err" id="errPassword"></div>
        <div class="form-explanation">※パスワードを変更しない場合は何も入力しないでください。</div>
      </div>
      <div class="row">
        <div class="ttl">説明文</div>
        <textarea id="text"></textarea>
        <div class="err" id="errText"></div>
        <div class="form-explanation"></div>
      </div>
    </div>
    <div class="container-center">
      <button class="l primary" id="updateBtn" style="margin-top: var(--margin-l);" onclick="update()" disabled>更新</button>
    </div>
  </div>

  <script src="../src/scripts/public_login.js"></script>
  <script src="../src/scripts/edit-group.js"></script>
</body>
</html>