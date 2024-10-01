<?php
include("/var/www/html/src/php/login_ini.php");
?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <link rel="icon" href="../src/images/favicon.ico" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>カレンダー | NKApps</title>
  <link rel="stylesheet" href="../src/styles/public.css">
  <link rel="stylesheet" href="../src/styles/calendar.css">
  <script src="https://kit.fontawesome.com/a377d52115.js" crossorigin="anonymous"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
  <script src="https://unpkg.com/multiple-select@1.7.0/dist/multiple-select.min.js"></script>
  <link rel="stylesheet" href="https://unpkg.com/multiple-select@1.7.0/dist/multiple-select.min.css">
  <script src="https://cdn.jsdelivr.net/npm/jquery/dist/jquery.min.js"></script>
</head>
<body>
  <?php include('../src/includes/login-header.php') ?>

  <div class="single-block-large">
    <!-- APP FORM -->
    <button class="m primary open-form-btn" onclick="displayAppForm()">予定を追加</button>
    <div class="app-form-container" id="appFormContainer">
      <div class="ttl-container">
        <div class="ttl">予定を追加</div>
        <button class="m unstyled close-btn" onclick="closeAppForm()"><i class="fa-solid fa-xmark"></i></button>
      </div>
      <div class="form">
        <div class="row">
          <div class="ttl"><span class="must-form" title="必須項目">*</span>タイトル</div>
          <input type="text" id="ttl" onchange="checkCompleteForm()">
          <div class="err" id="errTtl"></div>
          <div class="form-explanation"></div>
        </div>
        <div class="row">
          <div class="ttl"><span class="must-form" title="必須項目">*</span>グループ</div>
          <select id="group" onchange="checkCompleteForm()">
            <option value="self">自分のみ</option>
          </select>
          <div class="err" id="errGroup"></div>
          <div class="form-explanation"></div>
        </div>
        <div class="row">
          <div class="ttl"><span class="must-form" title="必須項目">*</span>期間</div>
          <div class="container-flex">
            <div class="sub-text">開始</div>
            <input type="datetime-local" class="short-box" id="startAt"  onchange="checkCompleteForm()">
          </div>
          <div class="container-flex">
            <div class="sub-text">終了</div>
            <input type="datetime-local" class="short-box" id="endAt" onchange="checkCompleteForm()">
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
      </div>
      <div class="container-center">
        <button class="m primary" id="addBtn" onclick="addPlan()" disabled>追加</button>
      </div>
    </div>

    <div class="main">
      <!-- SELECT FORM -->
      <div class="select-form">
        <div class="row-left">
          <button class="s unstyled" onclick="changeDisplayAt('before')"><i class="fa-solid fa-angle-left"></i></button>
          <div id="displayAtViewer">2024年05月</div>
          <button class="s unstyled" onclick="changeDisplayAt('after')"><i class="fa-solid fa-angle-right"></i></button>
        </div>
        <div class="row row-right">
          <div class="ttl">グループ</div>
          <button id="selectGroupBtn" onclick="displaySelectGroupForm()">
            <div class="display-select-group" id="displaySelectGroup">自分のみ</div>
            <div class="triangle"><i class="fa-solid fa-angle-down"></i></div>
          </button>
          <div class="select-group-container" id="displayGroup" value="self">
            <input type="checkbox" id="self" name="selectGroups" onchange="changeDisplayGroup()" checked>
            <label for="self">
              <div class="ttl">自分のみ</div>
            </label>
          </div>
        </div>
        <div class="row row-right" id="displaySpanContainer">
          <div class="ttl">期間</div>
          <select id="displaySpan" onchange="changeSpan()">
            <option value="month" selected>月毎</option>
            <option value="week">週毎</option>
            <option value="date">日毎</option>
            <option value="plan">予定</option>
          </select>
        </div>
      </div>

      <!-- CALENDAR -->
      <div id="calendar">
        
      </div>
    </div>
  </div>

  <div id="mouseCircle"></div>
  
  <script src="../src/scripts/public_login.js"></script>
  <script src="../src/scripts/calendar.js"></script>
</body>
</html>