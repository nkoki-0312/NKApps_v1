<?php
include("/var/www/html/src/php/login_ini.php");

// 画像アップロード
if( isset( $_POST["upload"] ) ){
  if( $_FILES["icon"]["name"] != "" ){
    if(!file_exists('/var/www/html/src/images/user-icons/'.$userDatas[0].'/')) mkdir( '/var/www/html/src/images/user-icons/'.$userDatas[0].'/', 0755 );
    $uploaddir = '/var/www/html/src/images/user-icons/'.$userDatas[0].'/';
    $upload = $uploaddir . basename($_FILES["icon"]["name"]);
    move_uploaded_file($_FILES["icon"]["tmp_name"], $uploaddir.$_FILES['icon']['name']);

    $sql = "UPDATE users SET icon_file_name = :icon_file_name, update_at = :update_at WHERE uniq_id = :uniq_id";
    $res = $PDO->prepare($sql);
    $params = array(
      ":icon_file_name" => $_FILES['icon']['name'],
      ":update_at" => date("Y-m-d H:i:s"),
      ":uniq_id" => $userDatas[0]
    );
    $res->execute($params);
    
    echo '<script>alert("アイコンを更新しました。");</script>';
    // echo '<script>window.location.href = "../mypage/";</script>';
  }
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <link rel="icon" href="../src/images/favicon.ico" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>アイコン更新 | NKApps</title>
  <link rel="stylesheet" href="../src/styles/public.css">
  <link rel="stylesheet" href="../src/styles/edit-icon.css">
  <script src="https://kit.fontawesome.com/a377d52115.js" crossorigin="anonymous"></script>
</head>
<body>
  <?php include('../src/includes/login-header.php') ?>

  <div class="single-block">
    <button class="m unstyled" onclick="pageBack()"><i class="fa-solid fa-angle-left"></i>&nbsp;戻る</button>
    <div class="page-ttl-center">アイコン更新</div>

    <div class="explanation">画像を選択後「更新」ボタンを押してください。</div>
    
    <form action="" method="post" enctype="multipart/form-data">
      <input type="file" id="icon" name="icon" accept=".jpg, .png">
      <label for="icon">
        <div id="icon_label">ファイルを選択...</div>
      </label>
      <div class="container-center">
        <button type="submit" class="l primary" id="icon_update_btn" name="upload">更新</button>
      </div>
    </form>
  </div>

  <script src="../src/scripts/public_login.js"></script>
  <script src="../src/scripts/edit-icon.js"></script>
</body>
</html>