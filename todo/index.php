<?php
include("/var/www/html/src/php/login_ini.php");
?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <link rel="icon" href="../src/images/favicon.ico" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>ToDoリスト | NKApps</title>
  <link rel="stylesheet" href="../src/styles/public.css">
  <link rel="stylesheet" href="../src/styles/todo.css">
  <script src="https://kit.fontawesome.com/a377d52115.js" crossorigin="anonymous"></script>
</head>
<body>
  <?php include('../src/includes/login-header.php') ?>

  <div class="single-block-large">
    <!-- APP FORM -->
    <button class="m primary open-form-btn" onclick="displayAppForm()">ToDoを追加</button>
    <div class="app-form-container" id="appFormContainer">
      <div class="ttl-container">
        <div class="ttl">ToDoを追加</div>
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
          <div class="ttl">期間</div>
          <div class="container-flex">
            <div class="sub-text">開始</div>
            <input type="datetime-local" class="short-box" id="startAt"  onchange="checkCompleteForm()">
          </div>
          <div class="container-flex">
            <div class="sub-text">期限</div>
            <input type="datetime-local" class="short-box" id="limitAt" onchange="checkCompleteForm()">
          </div>
          <div class="err" id="errSpan"></div>
          <div class="form-explanation"></div>
        </div>
        <div class="row">
          <div class="ttl">順番ToDo</div>
          <select id="beforeToDo" onchange="checkCompleteForm()">
            <option value="none">指定しない</option>
          </select>
          <details class="simple">
            <summary>順番ToDoとは？</summary>
            <div class="content">
              <div class="explanation">指定したToDoが完了(チェックを付与)すると、表示されるようになる設定です。</div>
            </div>
          </details>
          <div class="err" id="errBeforeToDo"></div>
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
        <div class="row">
          <div class="ttl">連携<span class="developing" title="この機能は現在開発中です。">！</span></div>
          <label>
            <input type="checkbox" id="linkKakeibo" onchange="displayKakeiboForm()">
            <div class="text">家計簿と連携する</div>
          </label>
          <!-- <div class="kakeibo-form">
            <div class="explanation">取引の種類と金額を入力してください。</div>
            <div class="type">
              <label>
                <input type="radio" name="kakeibo-type">
              </label>
            </div>
            <input type="number" id="kakeiboAmount">
            <div class="text">円</div>
          </div> -->
        </div>
      </div>
      <div class="container-center">
        <button class="m primary" id="addBtn" onclick="addToDo()" disabled>追加</button>
      </div>
    </div>

    <div class="main">
      <!-- SELECT FORM -->
      <div class="select-form">
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
        <div class="row row-right" id="displayViewContainer">
          <div class="ttl">表示</div>
          <select id="displayView" onchange="changeView()">
            <option value="all">すべて</option>
            <option value="checked">完了済のみ</option>
            <option value="unchecked" selected>未完了のみ</option>
          </select>
        </div>
      </div>

      <div class="todo-list" id="todoList"></div>
    </div>
  </div>

  <div id="mouseCircle"></div>

  <script src="../src/scripts/public_login.js"></script>
  <script src="../src/scripts/todo.js"></script>
</body>
</html>