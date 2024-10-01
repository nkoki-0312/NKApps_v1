<?php
// INPUT:  id, password
// OUTPUT: state
// state: "incorrectData": ユーザIDまたはメールアドレスまたはパスワードが異なる
//        : 

include('./db.php');
include('./register_log.php');

$id = $_GET["id"];
$password = $_GET["password"];
header('Access-Control-Allow-Origin:http://localhost:3000');
$datetime = date("Y-m-d H:i:s");
$limitAt = date("Y-m-d H:i:s", strtotime("1 week"));
$result = "incorrectData";
$tmpUserId = "";

// check id (or email) and password
$sql = "SELECT uniq_id, password FROM users WHERE id = :id OR email = :email";
$resCheckPassword = $PDO->prepare($sql);
$params = array(
  ":id" => $id,
  ":email" => $id
);
$resCheckPassword->execute($params);
foreach( $resCheckPassword as $resCheckPasswordFor ) {
  if( password_verify( $password, $resCheckPasswordFor[1] ) ) {
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
    if( $resCheckExistFor[0] == "" ) {
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
    } else {
      $token = $resCheckExistFor[0];
    }
  }

  $result = $token;
  register_log("login", $token, $tmpUserId, "", "");
}


echo json_encode($result);
?>