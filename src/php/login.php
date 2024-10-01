<?php
// INPUT:  id( or email ), password
// OUTPUT: state
// state: "incorrectData": ユーザIDまたはメールアドレスまたはパスワードが異なる
//        "success": ログイン成功

include('./db.php');
include('./register_log.php');

$raw = file_get_contents("php://input");
$userDatas = json_decode($raw);
$datetime = date("Y-m-d H:i:s");
$limitAt = date("Y-m-d H:i:s", strtotime("1 week"));
$result = "incorrectData";
$tmpUserId = "";
$token = "";

// check id (or email) and password
$sql = "SELECT uniq_id, password FROM users WHERE id = :id OR email = :email";
$resCheckPassword = $PDO->prepare($sql);
$params = array(
  ":id" => $userDatas[0],
  ":email" => $userDatas[0]
);
$resCheckPassword->execute($params);
foreach( $resCheckPassword as $resCheckPasswordFor ) {
  if( password_verify( $userDatas[1], $resCheckPasswordFor[1] ) ) {
    $result = "";
    $tmpUserId = $resCheckPasswordFor[0];
  }
}

if( $result == "" ) {
  // Delete tokens whtch are already limited.
  $sql = "DELETE FROM login_tokens WHERE limit_at <= :limit_at";
  $resDelete = $PDO->prepare($sql);
  $params = array(
    ":limit_at" => $datetime
  );
  $resDelete->execute($params);

  // Check exist token
  $sql = "SELECT token FROM login_tokens WHERE user_id = :user_id";
  $resCheckExist = $PDO->prepare($sql);
  $params = array(
    ":user_id" => $tmpUserId
  );
  $resCheckExist->execute($params);
  foreach( $resCheckExist as $resCheckExistFor ) {
    $token = $resCheckExistFor[0];
  }

  if( $token == "" ) {
    // Create token.
    $checkToken = true;
    while( $checkToken ) {
      $token = "";
      for( $i=0; $i<64; $i++ ){
        if(mt_rand(0,1) == 0){
          $token .= chr(mt_rand(65, 90));
        }else{
          $token .= mt_rand(0, 9);
        }
      }
  
      $sql = "SELECT count(*) FROM login_tokens WHERE token = :token";
      $res = $PDO->prepare($sql);
      $params = array(
        ":token" => $token
      );
      $res->execute($params);
      foreach( $res as $resFor ){
        if( $resFor[0] == 0 ) $checkToken = false;
      }
    }
  
    $sql = "INSERT INTO login_tokens (
      token, user_id, create_at, limit_at
    ) VALUES (
      :token, :user_id, :create_at, :limit_at
    )";
    $res = $PDO->prepare($sql);
    $params = array(
      ":token" => $token,
      ":user_id" => $tmpUserId,
      ":create_at" => $datetime,
      ":limit_at" => $limitAt
    );
    $res->execute($params);
  }

  $result = "success";
  setcookie("NKAppsLoginToken", $token, time()+60*60*24*7, '/');
  register_log("login", $token, $tmpUserId, "", "");
}


echo json_encode($result);
?>