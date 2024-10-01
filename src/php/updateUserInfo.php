<?php
// INPUT:  [id, name, email, password, newPassword]
// OUTPUT: state
// state: "existId": すでに(自分以外で)このユーザIDが登録されている
//        "existEmail": すでに(自分以外で)このメールアドレスが登録されている
//        "incorrectPassword": 現在のパスワードが異なる
//        "success": ログイン成功

include('./db.php');
include('./register_log.php');

$raw = file_get_contents("php://input");
$userDatas = json_decode($raw);
$result = "";
$datetime = date("Y-m-d H:i:s");
$isIdCorrect = true;
$isNameCorrect = true;
$isEmailCorrect = true;
$isPasswordCorrect = true;

// 現在のユーザ情報を取得
$token = $_COOKIE["NKAppsLoginToken"];
$sql = "SELECT uniq_id, id, name, email, password FROM users WHERE uniq_id = ( SELECT user_id FROM login_tokens WHERE token = :token )";
$resGetUserDatas = $PDO->prepare($sql);
$params = array(
  ":token" => $token
);
$resGetUserDatas->execute($params);
foreach( $resGetUserDatas as $preUserDatas ) {
  // check new user id and update
  $sql = "SELECT count(*) FROM users WHERE id = :id AND uniq_id <> :uniq_id";
  $resCheckId = $PDO->prepare($sql);
  $params = array(
    ":id" => $userDatas[0],
    ":uniq_id" => $preUserDatas[0]
  );
  $resCheckId->execute($params);
  foreach( $resCheckId as $resCheckIdCount ) {
    if( $resCheckIdCount[0] >= 1 ) {
      $result = "existId";
      $isIdCorrect = false;
    }
  }

  if( $result == "" ) {
    // check new email
    $sql = "SELECT count(*) FROM users WHERE email = :email AND uniq_id <> :uniq_id";
    $resCheckEmail = $PDO->prepare($sql);
    $params = array(
      ":email" => $userDatas[2],
      ":uniq_id" => $preUserDatas[0]
    );
    $resCheckEmail->execute($params);
    foreach( $resCheckEmail as $resCheckEmailCount ) {
      if( $resCheckEmailCount[0] >= 1 ) {
        $result = "existEmail";
        $isEmailCorrect = false;
      }
    }
  }

  if( $result == "" && $userDatas[3] != "" ) {
    // check password and upate password
    $sql = "SELECT password FROM users WHERE uniq_id = :uniq_id";
    $resCheckPassword = $PDO->prepare($sql);
    $params = array(
      ":uniq_id" => $preUserDatas[0]
    );
    $resCheckPassword->execute($params);
    foreach( $resCheckPassword as $checkPassword ) {
      if( !password_verify( $userDatas[3], $checkPassword[0] ) ) {
        $result = "incorrectPassword";
        $isPasswordCorrect = false;
      }
    }
  }

  if( $isIdCorrect && $isNameCorrect && $isEmailCorrect && $isPasswordCorrect ) {
    $sql = "UPDATE users SET id = :id, name = :name, update_at = :update_at WHERE uniq_id = :uniq_id";
    $resUpdateId = $PDO->prepare($sql);
    $params = array(
      ":id" => $userDatas[0],
      ":name" => $userDatas[1],
      ":update_at" => $datetime,
      ":uniq_id" => $preUserDatas[0]
    );
    $resUpdateId->execute($params);

    // if email was updated, send email for check email available and update email
    if( $preUserDatas[3] != $userDatas[2] ) {
      $sql = "UPDATE users SET email = :email, state = :state, update_at = :update_at WHERE uniq_id = :uniq_id";
      $resUpdateEmail = $PDO->prepare($sql);
      $params = array(
        ":email" => $userDatas[2],
        ":state" => 2,
        ":update_at" => $datetime,
        ":uniq_id" => $preUserDatas[0]
      );
      $resUpdateEmail->execute($params);

      $to = $userDatas[2];
      $subject = "メールアドレス認証のお願い - NKApps";
      $message = "NKAppsをご利用いただきありがとうございます。\nユーザの操作によりメールアドレスが変更されました。新しいメールアドレスの有効性を確認するため、下記のリンクへアクセスしてメールアドレスの認証をお願いします。\n\nhttps://nk-apps.net/check-email?uid=".$preUserDatas[0]."\n\n引き続きNKAppsをよろしくお願いします。\n\nこのメールに心当たりがない場合は運営者に連絡の上、メールの破棄をお願いします。";
      $headers = "From: info@nk-apps.net";
      mb_language("Japanese");
      mb_internal_encoding("UTF-8");
      mb_send_mail($to, $subject, $message, $headers);
      
      $to = $preUserDatas[3];
      $subject = "メールアドレス変更のお知らせ - NKApps";
      $message = "NKAppsをご利用いただきありがとうございます。\nユーザの操作によりこのメールアドレスに紐づけられていたアカウントのメールアドレスが[ ".$userDatas[2]." ]に変更されましたのでお知らせいたします。\nこの操作に心当たりがない場合、第3者による不正ログインの可能性がありますので、至急NKAppsへお問い合わせください。引き続きNKAppsをよろしくお願いします。\n\nこのメールに心当たりがない場合は運営者に連絡の上、メールの破棄をお願いします。";
      $headers = "From: info@nk-apps.net";
      mb_language("Japanese");
      mb_internal_encoding("UTF-8");
      mb_send_mail($to, $subject, $message, $headers);
    }

    // if password was inputed, update password
    if( $userDatas[3] != "" ){
      $sql = "UPDATE users SET password = :password, update_at = :update_at WHERE uniq_id = :uniq_id";
      $resUpdatePassword = $PDO->prepare($sql);
      $params = array(
        ":password" => password_hash($userDatas[4], PASSWORD_DEFAULT),
        ":update_at" => $datetime,
        ":uniq_id" => $preUserDatas[0]
      );
      $resUpdatePassword->execute($params);
    }

    $result = "success";
  }
}

echo json_encode($result);
?>