<?php
// INPUT:   None
// OUTPUT:  userUniqId

include("/var/www/html/src/php/db.php");

$loginToken = $_COOKIE["NKAppsLoginToken"];
$sql = "SELECT id FROM users WHERE uniq_id = ( SELECT user_id FROM login_tokens WHERE token = :token )";
$resGetUseUniqId = $PDO->prepare($sql);
$params = array(
  ":token" => $loginToken
);
$resGetUseUniqId->execute($params);
foreach( $resGetUseUniqId as $resGetUseUniqIdFor ) {
  echo json_encode($resGetUseUniqIdFor[0]);
}
?>