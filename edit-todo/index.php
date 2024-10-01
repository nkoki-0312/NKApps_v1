<?php
include("/var/www/html/src/php/login_ini.php");
?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <link rel="icon" href="../src/images/favicon.ico" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>ToDo情報編集 | NKApps</title>
  <link rel="stylesheet" href="../src/styles/public.css">
  <link rel="stylesheet" href="../src/styles/edit-todo.css">
  <script src="https://kit.fontawesome.com/a377d52115.js" crossorigin="anonymous"></script>
</head>
<body>
  <?php include('../src/includes/unfunction-header.php') ?>

  <div class="single-block">
    <input type="text" id="groupId" readonly style="display: none;">
    <button class="m unstyled" onclick="pageBack()"><i class="fa-solid fa-angle-left"></i>&nbsp;戻る</button>
    <div class="page-ttl-center">ToDo情報編集</div>

    <div class="explanation">編集後「更新」ボタンを押してください。</div>

    <div class="form">
      <div class="row">
        <div class="ttl"><span class="must-form">必須</span>タイトル</div>
        <input type="text" id="ttl" onchange="checkCompleteForm()">
        <div class="err" id="errTtl"></div>
        <div class="form-explanation"></div>
      </div>
      
      <div class="row">
        <div class="ttl"><span class="must-form">必須</span>期間</div>
        <div class="container-flex">
          <div class="sub-text">開始</div>
          <input type="datetime-local" class="short-box" id="startAt" onchange="checkCompleteForm()">
        </div>
        <div class="container-flex">
          <div class="sub-text">終了</div>
          <input type="datetime-local" class="short-box" id="limitAt" onchange="checkCompleteForm()">
        </div>
        <div class="err" id="errSpan"></div>
        <div class="form-explanation"></div>
      </div>
      
      <div class="row">
        <div class="ttl">色</div>
          <div class="sub-text" style="width: 100%;">背景</div>
          <div class="color-pallet">
            <input type="radio" name="bgClr" id="bgBlue" value="#0066ff" checked>
            <label for="bgBlue">
              <div class="color-demo" style="background: var(--nk-primary-blue-500);"></div>
            </label>
            <input type="radio" name="bgClr" id="bgGreen" value="#00b06b">
            <label for="bgGreen">
              <div class="color-demo" style="background: var(--nk-green-500);"></div>
            </label>
            <input type="radio" name="bgClr" id="bgRed" value="#ff4b00">
            <label for="bgRed">
              <div class="color-demo" style="background: var(--nk-red-500);"></div>
            </label>
            <input type="radio" name="bgClr" id="bgYellow" value="#F2E700">
            <label for="bgYellow">
              <div class="color-demo" style="background: var(--nk-yellow-500);"></div>
            </label>
            <input type="radio" name="bgClr" id="bgBlack" value="#555555">
            <label for="bgBlack">
              <div class="color-demo" style="background: var(--nk-gray-500);"></div>
            </label>
          </div>
          <div class="sub-text" style="width: 100%;">文字</div>
          <div class="color-pallet">
            <input type="radio" name="fontClr" id="fontWhite" value="#ffffff" checked>
            <label for="fontWhite">
              <div class="color-demo" style="background: var(--nk-white); border: 1px solid var(--nk-gray-100);"></div>
            </label>
            <input type="radio" name="fontClr" id="fontBlack" value="#555555">
            <label for="fontBlack">
              <div class="color-demo" style="background: var(--nk-gray-500);"></div>
            </label>
          </div>
          <div class="err" id="errClr"></div>
          <div class="form-explanation"></div>
      </div>
      
      <div class="row">
        <div class="ttl">説明文</div>
        <textarea id="text" onchange="checkCompleteForm()"></textarea>
        <div class="err" id="errText"></div>
        <div class="form-explanation"></div>
      </div>

      <div class="container-center">
        <button class="l primary" id="updateBtn" style="margin-top: var(--margin-l);" onclick="updatePlan()" disabled>更新</button>
      </div>
    </div>
  </div>

  <script src="../src/scripts/public_login.js"></script>
  <script src="../src/scripts/edit-todo.js"></script>
</body>
</html>