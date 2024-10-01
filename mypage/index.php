<?php
include("/var/www/html/src/php/login_ini.php");

$userState = "";
switch( $userDatas[4] ) {
  case 0:
    $userState = "仮ユーザ";
    break;
  case 1:
    $userState = "一般ユーザ";
    break;
  case 2:
    $userState = "メールアドレス未認証";
    break;
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <link rel="icon" href="../src/images/favicon.ico" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>マイページ | NKApps</title>
  <link rel="stylesheet" href="../src/styles/public.css">
  <link rel="stylesheet" href="../src/styles/mypage.css">
  <script src="https://kit.fontawesome.com/a377d52115.js" crossorigin="anonymous"></script>
</head>
<body>
  <?php include('../src/includes/login-header.php') ?>

  <div class="single-block">
    <div class="page-ttl-center">マイページ</div>

    <div class="basic-info">
      <div class="icon">
        <div class="container-center">
          <img src="https://nk-apps.net/src/images/user-icons/<?php echo $userDatas[0]; ?>/<?php echo $userDatas[5]; ?>" alt="ユーザアイコン" class="user-icon"  onerror="this.src='https://nk-apps.net/src/images/no_user_images.svg'" />
        </div>
        <a href="https://nk-apps.net/edit-icon/">
          <div class="container-center">
            <button class="s secondary">アイコンを更新</button>
          </div>
        </a>
      </div>
      <div class="name"><?php echo $userDatas[2]; ?></div>
      <div class="id">ユーザID:<?php echo $userDatas[1]; ?></div>
    </div>

    <div class="normal-tab-container">
      <div class="btns" id="btns">
        <div class="btn visiting" id="userInfoBtn" onclick="changeTab(0)">ユーザ情報</div>
        <div class="btn" id="groupsBtn" onclick="changeTab(1)">グループ</div>
        <div class="btn" id="friendsBtn" onclick="changeTab(2)">フレンド</div>
      </div>
      <div class="contents" id="contents">
        <div class="content visiting">
          <!-- ユーザ情報 -->
          <div class="container-right">
            <a href="https://nk-apps.net/edit-info/">
              <button class="m secondary" style="margin-bottom: var(--margin-s);">ユーザ情報を更新</button>
            </a>
          </div>
          <table class="detail-table">
            <tr>
              <th>ユーザネーム</th>
              <td><?php echo $userDatas[2]; ?></td>
            </tr>
            <tr>
              <th>ユーザID</th>
              <td><?php echo $userDatas[1]; ?></td>
            </tr>
            <tr>
              <th>メールアドレス</th>
              <td><?php echo $userDatas[3]; ?></td>
            </tr>
            <tr>
              <th>ユーザタイプ</th>
              <td><?php echo $userState; ?></td>
            </tr>
          </table>
        </div>
        <div class="content group">
          <!-- グループ -->
          <div class="container-right" style="margin-bottom: var(--margin-s);">
            <a href="https://nk-apps.net/create-group/">
              <button class="m primary">グループを作成</button>
            </a>
            <a href="https://nk-apps.net/join-group/">
              <button class="m secondary line-up">グループに参加</button>
            </a>
          </div>
          <table class="group-table" id="groups">
            <tr>
              <td class="no-data">現在参加しているグループはありません。<br>上部の「グループに参加」ボタンから参加できます。</td>
            </tr>     
          </table>
        </div>
        <div class="content">
          <!-- フレンド -->
          <div class="container-right" style="margin-bottom: var(--margin-s);">
            <a href="https://nk-apps.net/add-friend/">
              <button class="m primary">フレンドを追加</button>
            </a>
          </div>

          <!-- フレンドリスト -->
          <table class="user-table" id="friendTable">      
            <tr>
              <td class="no-data">フレンドが登録されていません。<br>上部の「フレンドを追加」ボタンから登録できます。</td>
            </tr>     
          </table>
        </div>
      </div>
    </div>
  </div>

  <div id="mouseCircle"></div>

  <script src="../src/scripts/public_login.js"></script>
  <script src="../src/scripts/mypage.js"></script>
</body>
</html>