<?php
include("/var/www/html/src/php/login_ini.php");
?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <link rel="icon" href="../src/images/favicon.ico" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>ユーザー情報編集 | NKApps</title>
  <link rel="stylesheet" href="../src/styles/public.css">
  <link rel="stylesheet" href="../src/styles/edit-info.css">
  <script src="https://kit.fontawesome.com/a377d52115.js" crossorigin="anonymous"></script>
</head>
<body>
  <?php include('../src/includes/login-header.php') ?>

  <div class="single-block">
    <button class="m unstyled" onclick="pageBack()"><i class="fa-solid fa-angle-left"></i>&nbsp;戻る</button>
    <div class="page-ttl-center">ユーザ情報編集</div>

    <div class="explanation">編集後「更新」ボタンを押してください。</div>

    <div class="form">
      <div class="row">
        <div class="ttl"><span class="must-form">必須</span>ユーザネーム</div>
        <input type="text" id="name" value="<?php echo $userDatas[2]; ?>" onchange="checkCompleteForm()">
        <div class="err" id="errName"></div>
          <div class="form-explanation">※ユーザIDは32文字以内で入力してください。</div>
      </div>
      
      <div class="row">
        <div class="ttl"><span class="must-form">必須</span>ユーザID</div>
        <input type="text" id="id" value="<?php echo $userDatas[1]; ?>" onchange="checkCompleteForm()">
        <div class="err" id="errId"></div>
          <div class="form-explanation">※ユーザIDには半角英数字とハイフン(-)が使用できます。</div>
          <div class="form-explanation">※ユーザIDは32文字以内で入力してください。</div>
      </div>
      
      <div class="row">
        <div class="ttl"><span class="must-form">必須</span>メールアドレス</div>
        <input type="email" id="email" value="<?php echo $userDatas[3]; ?>" onchange="checkCompleteForm()">
        <div class="err" id="errEmail"></div>
        <div class="form-explanation">※半角で入力してください。</div>
        <div class="form-explanation">※メールアドレスを更新した場合、新しいメールアドレスの有効性を行います。「更新」ボタンを押した後にメールフォルダをご確認ください。</div>
      </div>
      
      <div class="row">
        <div class="ttl">パスワード</div>
        <input type="password" id="password" placeholder="現在のパスワード" onchange="checkCompleteForm()">
        <input type="password" id="newPassword" placeholder="新しいパスワード" onchange="checkCompleteForm()">
        <input type="password" id="confirmNewPassword" placeholder="新しいパスワード(再入力)" onchange="checkCompleteForm()">
        <div class="err" id="errPassword"></div>
        <div class="form-explanation">※<b>パスワードを更新しない場合は、何も入力しないでください。</b><br>※パスワードを更新する場合は、3項目とも入力してください。</div>
      </div>

      <div class="container-center">
        <button class="l primary" id="updateBtn" style="margin-top: var(--margin-l);" onclick="updateUserInfo()">更新</button>
      </div>
    </div>
  </div>

  <script src="../src/scripts/public_login.js"></script>
  <script src="../src/scripts/edit-info.js"></script>
</body>
</html>