<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <link rel="icon" href="../src/images/favicon.ico" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>ログイン | NKApps</title>
  <link rel="stylesheet" href="../src/styles/public.css">
  <link rel="stylesheet" href="../src/styles/login.css">
  <script src="https://kit.fontawesome.com/a377d52115.js" crossorigin="anonymous"></script>
</head>
<body>
  <?php include('../src/includes/unfunction-header.php') ?>

  <div class="single-block">
    <div class="page-ttl-center">ログイン</div>
    
    <div class="form-container" id="formContainer">
      <div class="explanation">フォームを入力して「ログイン」ボタンを押してください。</div>

      <div class="form">
        <div class="row">
          <div class="ttl"><span class="must-form">必須</span>ユーザIDまたはメールアドレス</div>
          <input type="text" id="userId" onchange="checkCompleteForm()">
          <div class="err" id="errUserId"></div>
          <div class="form-explanation"></div>
        </div>
        
        <div class="row">
          <div class="ttl"><span class="must-form">必須</span>パスワード</div>
          <input type="password" id="password" onchange="checkCompleteForm()">
          <div class="err" id="errPassword"></div>
          <div class="form-explanation"></div>
        </div>
        <div class="container-center">
          <button class="l primary" id="loginBtn" style="margin-top: var(--margin-l);" disabled onClick="login()">ログイン</button>
        </div>
      </div>
    </div>
  </div>

  <script src="../src/scripts/login.js"></script>
</body>
</html>