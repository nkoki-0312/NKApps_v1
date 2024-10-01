<?php
include("/var/www/html/src/php/login_ini.php");
?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <link rel="icon" href="../src/images/favicon.ico" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>グループ | NKApps</title>
  <link rel="stylesheet" href="../src/styles/public.css">
  <link rel="stylesheet" href="../src/styles/group.css">
  <script src="https://kit.fontawesome.com/a377d52115.js" crossorigin="anonymous"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script> 
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.qrcode/1.0/jquery.qrcode.min.js"></script> 
</head>
<body>
  <div class="gray-back" id="grayBack" onclick="closeModal()"></div>

  <div class="modal" id="modal">
    <div class="top-info">
      <div class="ttl" id="modalTtl">ユーザ編集</div>
      <button class="m unstyled close-btn" onclick="closeModal()"><i class="fa-solid fa-xmark"></i></button>
    </div>
    <div class="content" id="modalContent">
      <div class="user-info">
        <div class="icon" id="userIcon"></div>
        <div class="name" id="userName"></div>
        <div class="text">&nbsp;さんを編集</div>
      </div>
      <hr>
      <div class="sub-ttl" style="margin-top: 0;">ユーザ権限</div>
      <div class="form">
        <input type="radio" name="userState" id="admin" onchange="changeUserGroupState('admin')">
        <label for="admin"><span class="admin"><i class="fa-solid fa-star"></i></span>&nbsp;管理者</label>
        <input type="radio" name="userState" id="member" onchange="changeUserGroupState('member')">
        <label for="member">メンバー</label>
      </div>
      <details class="simple">
        <summary>管理者とは？</summary>
        <div class="content">
          <div class="explanation">ユーザ権限を「管理者」に変更すると、ユーザは以下の操作をできるようになります。<br><br>&nbsp;&nbsp;・グループ情報の編集<br>&nbsp;&nbsp;・他のユーザの操作(強制退会, 権限変更)</div>
        </div>
      </details>
      <hr>
      <div class="sub-ttl">強制退会</div>
      <div class="explanation">このユーザをグループから退会させます。退会後も、再度招待をすることで参加することが可能です。</div>
      <button class="m danger-secondary" onclick="compulsionDisjoinGroup()">強制退会</button>
    </div>
  </div>

  <?php include('../src/includes/login-header.php') ?>

  <div class="single-block-large">
    <a href="https://nk-apps.net/mypage/">
      <button class="m unstyled"><i class="fa-solid fa-angle-left"></i>&nbsp;マイページへ</button>
    </a>
    
    <div class="group-container">
      <div class="group-list" id="groupList">
        <div class="no-data">グループに参加していません。<br><a href="https://nk-apps.net/join-group/">こちらのページ</a>からグループに参加してください。</div>
      </div>
      <div class="group-list-sp">
        <select id="groupListSp" onchange="displayGroupDetails('sp-mode')">
          <option value="">グループを選択してください</option>
        </select>
      </div>
      <div class="detail" id="detail">
        <div class="large-tab-container">
          <div class="btns" id="btns">
            <button class="btn">グループ情報</button>
            <!-- <button class="btn">チャット</button> -->
          </div>
          <div class="contents" id="contents">
            <div class="content visiting">
              <!-- グループ情報 -->
              <div id="groupInfo">
                <div class="no-data"><span id="groupListPosition">左(←)</span>のグループリストから表示するグループを選択してください。</div>
              </div>
              <div id="memberTop">
                
              </div>
              <table class="user-table" id="members">

              </table>
            </div>
            <div class="content">
              <!-- チャット -->
              
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  
  <script src="../src/scripts/public_login.js"></script>
  <script src="../src/scripts/group.js"></script>
</body>
</html>