<?php
include("/var/www/html/src/php/login_ini.php");
?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <link rel="icon" href="../src/images/favicon.ico" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>取引情報編集 | NKApps</title>
  <link rel="stylesheet" href="../src/styles/public.css">
  <link rel="stylesheet" href="../src/styles/edit-bop.css">
  <script src="https://kit.fontawesome.com/a377d52115.js" crossorigin="anonymous"></script>
</head>
<body>
  <?php include('../src/includes/unfunction-header.php') ?>

  <div class="single-block">
    <input type="text" id="groupId" readonly style="display: none;">
    <button class="m unstyled" onclick="pageBack()"><i class="fa-solid fa-angle-left"></i>&nbsp;戻る</button>
    <div class="page-ttl-center">取引情報編集</div>

    <div class="explanation">編集後「更新」ボタンを押してください。</div>

    <div class="form">
      <div class="row">
        <div class="ttl">種類</div>
        <input type="text" class="short-form" readonly id="type">
      </div>

      <div class="row">
        <div class="ttl"><span class="must-form">必須</span>金額</div>
        <div class="container-left">
          <input type="number" class="short-form" id="amount" onchange="checkCompleteForm()">
          <div class="sub-text">円</div>
        </div>
        <div class="err" id="errAmount"></div>
        <div class="form-explanation"></div>
      </div>
      
      <div class="row">
        <div class="ttl"><span class="must-form">必須</span>日付</div>
        <input type="date" class="short-form" id="date" onchange="checkCompleteForm()">
        <div class="err" id="errDate"></div>
        <div class="form-explanation"></div>
      </div>
      
      <div class="row">
        <div class="ttl"><span class="must-form">必須</span>資産カテゴリー</div>
        <select id="assetCat" onchange="checkCompleteForm()"></select>
        <div class="err" id="errAssetCat"></div>
        <div class="form-explanation"></div>
      </div>
      
      <div class="row">
        <div class="ttl"><span class="must-form">必須</span>収支カテゴリー</div>
        <select id="bopCat" onchange="checkCompleteForm()"></select>
        <div class="err" id="errBopCat"></div>
        <div class="form-explanation"></div>
      </div>
      
      <div class="row">
        <div class="ttl">詳細</div>
        <textarea id="text" onchange="checkCompleteForm()"></textarea>
        <div class="err" id="errText"></div>
        <div class="form-explanation"></div>
      </div>

      <div class="container-center">
        <button class="l primary" id="updateBtn" style="margin-top: var(--margin-l);" onclick="updateBop()">更新</button>
      </div>
    </div>
  </div>

  <script src="../src/scripts/public_login.js"></script>
  <script src="../src/scripts/edit-bop.js"></script>
</body>
</html>