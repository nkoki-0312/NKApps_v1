<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <link rel="icon" href="../src/images/favicon.ico" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>会員登録 | NKApps</title>
  <link rel="stylesheet" href="../src/styles/public.css">
  <link rel="stylesheet" href="../src/styles/signup.css">
  <script src="https://kit.fontawesome.com/a377d52115.js" crossorigin="anonymous"></script>
</head>
<body>
  <?php include('../src/includes/unlogin-header.php') ?>

  <div class="single-block">
    <div class="page-ttl-center">会員登録</div>
    
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
      <div class="explanation">メールアドレスを入力して「仮登録」ボタンを押してください。</div>

      <div class="form">
        <div class="row">
          <div class="ttl"><span class="must-form">必須</span>メールアドレス</div>
          <input type="email" id="email" placeholder="例: example@nk-apps.net" onchange="checkCompleteForm()">
          <div class="err" id="errEmail"></div>
          <div class="form-explanation">※半角で入力してください。</div>
        </div>
        <div class="container-center">
          <button class="l primary" id="preregisterBtn" style="margin-top: var(--margin-l);" disabled onClick="preregister()">仮登録</button>
        </div>
      </div>
    </div>
    <div class="success-container" id="successContainer" style="display: none;">
      <div class="success-icon">
        <i class="fa-solid fa-check"></i>
      </div>
      <div class="explanation">仮登録をしていただき、ありがとうございます。<br />入力されたメールアドレスに、本登録ページへのリンクを添付したメールを送信しましたのでご確認ください。</div>
      <div class="no-mail">
        <div class="ttl">メールが届かない場合は...</div>
        <ul>
          <li>入力されたメールアドレス( <span id="checkEmail" style="fon-weight: bold;"></span> )が正しいかを確認してください。</li>
          <li>「@nk-apps.net」からの受信を許可してください。</li>
          <li>迷惑メールフォルダを確認してください。</li>
          <li>ご利用のメールサーバーの容量が十分にあるかを確認してください。</li>
        </ul>
      </div>
    </div>
  </div>

  <script src="https://nk-apps.net/src/scripts/public.js"></script>
  <script src="../src/scripts/signup.js"></script>
</body>
</html>