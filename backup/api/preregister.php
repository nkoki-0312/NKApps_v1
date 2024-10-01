<?php
// INPUT: email
// OUTPUT: {state}
// state: "existEmail": すでにメールアドレスが登録されている
//        "success":    仮登録成功

include('../public/db.php');
include('../public/register_log.php');

$email = $_GET["email"];
header('Access-Control-Allow-Origin:http://localhost:3000');
$datetime = date("Y-m-d H:i:s");
$uniq_id = "";
$result = "";

// Check email Exists.
$sql = "SELECT email FROM users WHERE email = :email";
$resCheckEmails = $PDO->prepare($sql);
$params = array(
  ":email" => $email
);
$resCheckEmails->execute($params);
foreach( $resCheckEmails as $resCheckEmail ){
  $result = "existEmail";
}

if( $result == "" ){
  // Create user uniq id.
  $checkUniqId = true;
  while( $checkUniqId ) {
    $uniq_id = "";
    for( $i=0; $i<255; $i++ ){
      if(mt_rand(0,1) == 0){
        $uniq_id .= chr(mt_rand(65, 90));
      }else{
        $uniq_id .= mt_rand(0, 9);
      }
    }

    $sql = "SELECT count(*) FROM users WHERE uniq_id = :uniq_id";
    $res = $PDO->prepare($sql);
    $params = array(
      ":uniq_id" => $uniq_id
    );
    $res->execute($params);
    foreach( $res as $resFor ){
      if( $resFor[0] == 0 ) $checkUniqId = false;
    }
  }

  // Insert user datas
  $sql = "INSERT INTO users (
    uniq_id, id, name, email, password, type, state, create_at, update_at
  ) VALUES (
    :uniq_id, :id, :name, :email, :password, :type, :state, :create_at, :update_at
  )";
  $res = $PDO->prepare($sql);
  $params = array(
    ":uniq_id" => $uniq_id,
    ":id" => "",
    ":name" => "",
    ":email" => $email,
    ":password" => "",
    ":type" => "preuser",
    ":state" => 0,
    ":create_at" => $datetime,
    ":update_at" => $datetime
  );
  $res->execute($params);

  $result = "success";
}

$to = $email;
$subject = "本登録のお願い - NKApps";
$message = "NKAppsへの仮登録ありがとうございます。下記のリンクから本登録をお願いします。\n\nhttps://nk-apps.net/register?uid=".$uniq_id."\n\n引き続きNKAppsをよろしくお願いします。\n\nこのメールに心当たりがない場合は運営者に連絡の上、メールの破棄をお願いします。";
$headers = "From: info@nk-apps.net";
mb_language("Japanese");
mb_internal_encoding("UTF-8");
mb_send_mail($to, $subject, $message, $headers);

register_log("preregister", $uniq_id, $uniq_id, "", "");

echo json_encode($result);
?>