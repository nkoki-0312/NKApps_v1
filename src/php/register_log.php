<?php
// INPUT: email
// OUTPUT: None

function register_log($action, $action_to, $action_by, $data1="", $data2="") {
  include('db.php');

  $datetime = date("Y-m-d H:i:s");
  
  // Create log uniq id.
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

    $sql = "SELECT count(*) FROM action_logs WHERE uniq_id = :uniq_id";
    $res = $PDO->prepare($sql);
    $params = array(
      ":uniq_id" => $uniq_id
    );
    $res->execute($params);
    foreach( $res as $resFor ){
      if( $resFor[0] == 0 ) $checkUniqId = false;
    }
  }

  $sql = "INSERT INTO action_logs (
    uniq_id, action, action_to, action_by, action_at, data1, data2
  ) VALUES (
    :uniq_id, :action, :action_to, :action_by, :action_at, :data1, :data2
  )";
  $resLog = $PDO->prepare($sql);
  $params = array(
    ":uniq_id" => $uniq_id,
    ":action" => $action,
    ":action_to" => $action_to,
    ":action_by" => $action_by,
    ":action_at" => $datetime,
    ":data1" => $data1,
    ":data2" => $data2
  );
  $resLog->execute($params);
}

?>