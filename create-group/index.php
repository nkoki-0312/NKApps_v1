<?php
include("/var/www/html/src/php/login_ini.php");

?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <link rel="icon" href="../src/images/favicon.ico" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>グループ作成 | NKApps</title>
  <link rel="stylesheet" href="../src/styles/public.css">
  <link rel="stylesheet" href="../src/styles/create-group.css">
  <script src="https://kit.fontawesome.com/a377d52115.js" crossorigin="anonymous"></script>
</head>
<body>
  <?php include('../src/includes/login-header.php') ?>

  <div class="single-block">    
    <button class="m unstyled" onclick="pageBack()"><i class="fa-solid fa-angle-left"></i>&nbsp;戻る</button>
    <div class="page-ttl-center">グループ作成</div>

    <div class="explanation">必要事項を入力して「作成」ボタンを押してください。<br>入力内容は作成後も変更できます。</div>

    <div class="form">
      <div class="row">
        <div class="ttl"><span class="must-form">必須</span>グループ名</div>
        <input type="text" id="name" onchange="checkConpleteForm();">
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
        <div class="ttl">メンバー</div>
        <div class="form-explanation">グループに追加するユーザを選択してください。<br>グループ作成後もユーザの追加・削除ができます。</div>
        <div class="members" id="members"></div>
        <div class="err" id="errMember"></div>
      </div>
      
      <div class="row">
        <div class="ttl">パスワード</div>
        <input type="password" id="password">
        <input type="password" id="confirmPassword" placeholder="確認のため、もう一度入力してください。">
        <div class="err" id="errPassword"></div>
        <div class="form-explanation">※パスワードが未入力の場合、第3者が自由に参加することができます。</div>
      </div>
      
      <div class="row">
        <div class="ttl">説明文</div>
        <textarea id="text"></textarea>
        <div class="err" id="errText"></div>
        <div class="form-explanation"></div>
      </div>
    </div>
    <div class="container-center">
      <button class="l primary" id="createBtn" style="margin-top: var(--margin-l);" onclick="createGroup();" disabled>作成</button>
    </div>
  </div>

  <script src="../src/scripts/public_login.js"></script>
  <script src="../src/scripts/create-group.js"></script>
</body>
</html>