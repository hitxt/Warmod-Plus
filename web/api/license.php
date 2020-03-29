<?php	
  require_once("../configs/configs.php");
  require_once("../libs/sql.php");
  require_once("../libs/functions.php");
  
  $re = array();

  if(!empty($_GET["token"])) {
    $license = checkLicense($_GET["token"]);

    if($license['valid']) {
      $date = date("Y-m-d");

      $re['time_exp'] = $license['result'][0]['time_exp'];
      $re['time_now'] = $date;
      $re['ftp_a'] = $license['result'][0]['ftp_a'];
      $re['ftp_p'] = $license['result'][0]['ftp_p'];
      $re['version'] = $plugin_version;
    }
  }
  
  echo json_encode($re);
  
  $pdo = null;
?>