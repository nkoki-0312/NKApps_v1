<?php
include('../src/php/db.php');

$uniq_id = $_GET["uid"];
$sql = "SELECT count(*) FROM users WHERE uniq_id = :uniq_id AND type = :type";
$res = $PDO->prepare($sql);
$params = array(
  ":uniq_id" => $uniq_id,
  ":type" => "preuser"
);
$res->execute($params);
foreach($res as $resFor) {
  if( $resFor[0] != 1 ) {
    echo '<script>alert("メールアドレスの認証に失敗しました。\nURLがすべて入力されていることを確認して、再度お試しください。");</script>';
    echo '<script>window.location.href = "../";</script>';
  }
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <link rel="icon" href="../src/images/favicon.ico" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>本登録 | NKApps</title>
  <link rel="stylesheet" href="../src/styles/public.css">
  <link rel="stylesheet" href="../src/styles/register.css">
  <script src="https://kit.fontawesome.com/a377d52115.js" crossorigin="anonymous"></script>
</head>
<body>
  <?php include('../src/includes/unfunction-header.php') ?>

  <div class="single-block">
    <div class="page-ttl-center">本登録</div>
    
    <div class="progress-bar">
      <div class="bar-container">
        <div id="bar"></div>
      </div>
      <div class="text-container">
        <div class="text">入力➀</div>
        <div class="text">仮登録</div>
        <div class="text">入力⓶</div>
        <div class="text">本登録</div>
      </div>
    </div>

    <div class="form-container" id="formContainer">
      <div class="explanation">フォームに入力して「本登録」ボタンを押してください。<br>入力内容は作成後も変更できます。</div>

      <div class="form">
        <div class="row">
          <div class="ttl"><span class="must-form">必須</span>ユーザID</div>
          <input type="text" id="userId" onchange="checkCompleteForm()">
          <div class="err" id="errUserId"></div>
          <div class="form-explanation">※ユーザIDには半角英数字とハイフン(-)が使用できます。</div>
          <div class="form-explanation">※ユーザIDは32文字以内で入力してください。</div>
        </div>
        
        <div class="row">
          <div class="ttl"><span class="must-form">必須</span>ユーザネーム</div>
          <input type="text" id="userName" onchange="checkCompleteForm()">
          <div class="err" id="errUserName"></div>
          <div class="form-explanation">※ユーザIDは32文字以内で入力してください。</div>
        </div>
        
        <div class="row">
          <div class="ttl"><span class="must-form">必須</span>パスワード</div>
          <input type="password" id="password" onchange="checkCompleteForm()">
          <input type="password" id="confirmPassword" placeholder="確認のため、もう一度入力してください。" onchange="checkCompleteForm()">
          <div class="err" id="errPassword"></div>
          <div class="form-explanation"></div>
        </div>
        <div class="container-center">
          <button class="l primary" id="registerBtn" style="margin-top: var(--margin-l);" disabled onClick="register()">本登録</button>
        </div>
      </div>
    </div>
    <div class="success-container" id="successContainer" style="display: none;">
      <div class="success-icon">
        <i class="fa-solid fa-check"></i>
      </div>
      <div class="ttl">本登録完了</div>
      <div class="explanation">本登録をしていただきありがとうございます。<br /><a href="https://nk-apps.net/login/">ログインページ</a>からログインしてください。</div>
    </div>
  </div>

  <script src="../src/scripts/register.js"></script>
</body>
</html>