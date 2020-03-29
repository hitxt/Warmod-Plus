<?php
	require_once("../configs/configs.php");
  require_once("../libs/sql.php");
  require_once("../libs/functions.php");
  
  //Debug
  $jsonString = file_get_contents("php://input");
  $myFile = "player-".date('his', time()).".json";
  file_put_contents($myFile,$jsonString);
  $pdo->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING );

  $json = json_decode(file_get_contents('php://input'), true);

  $re = array();

  if(!empty($json) && !empty($json['token']) && !empty($json['id']) && !empty($json['ip'])) 
  {
    $license = checkLicense($json["token"]);
    
    if($license['valid']) 
    {
      $rws = 0.00;
      $round = 0;
      $team_name = "";
      $team_logo = "";
      $leader = false;
      $team_id = "";

      $input = array(
        ":steam" => $json['id'],
      );
      $sql = "SELECT * FROM ".$player_table." WHERE steam_id_64 = :steam";
      $sth = $pdo->prepare($sql);
      $sth->execute($input);
      $result = $sth->fetchAll();
  
      if($sth->rowCount() > 0) {
        $player = $result[0];
  
        $rws = $player['rws'];
        $team_id = strval($player['team']);
  
        // update ip
        if($player['last_ip'] != $json['ip']){
          $input = array(
            ":ip" => $json['ip'],
            ":steam" => $json['id'],
          );
          $sql = "UPDATE ".$player_table." SET last_ip = :ip WHERE steam_id_64 = :steam";
          $sth = $pdo->prepare($sql);
          $sth->execute($input);
        }
  
        if($team_id > 0) {
          // check leader
          $input = array(
            ":team_id" => $team_id,
          );
          $sql = "SELECT * FROM ".$team_table." WHERE id = :team_id";
          $sth = $pdo->prepare($sql);
          $sth->execute($input);
          
          if($sth->rowCount() > 0) {
            $result = $sth->fetchAll();
            $team_name = $result[0]['name'];
            $team_leader = $result[0]['leader'];
  
            if($team_leader == $json['id'])	$leader = true;
          }
  
          // check logo
          $input = array(
            ":team_id" => $team_id,
          );
          $sql = "SELECT * FROM ".$game_logo_table." WHERE team_id = :team_id";
          $sth = $pdo->prepare($sql);
          $sth->execute($input);
          
          if($sth->rowCount() > 0) {
            $result = $sth->fetchAll();
            $team_logo = strval($result[0]['id']);
          }
        }
  
        $input = array(
          ":steam" => $json['id'],
        );
        $sql = "SELECT SUM(rounds_played) AS 'rounds' FROM ".$stats_table." WHERE steam_id_64 = :steam";
        $sth = $pdo->prepare($sql);
        $sth->execute($input);
        $result = $sth->fetchAll();

        if($sth->rowCount() > 0 && $result[0]['rounds'] != null) {
          $round = $result[0]['rounds'];
        }
      }
      else {
        $input = array(
          ":ip" => $json['ip'],
          ":steam" => $json['id'],
        );
        $sql = "INSERT INTO ".$player_table." VALUES (NULL, :steam, :ip, 0.00, '', '', '', '', '')";
        $sth = $pdo->prepare($sql);
        $sth->execute($input);
      }
  
      $re['rws'] = $rws;
      $re['team_name'] = $team_name;
      $re['team_logo'] = $team_logo;
      $re['round'] = $round;
      $re['team_leader'] = $leader;
      $re['team_id'] = $team_id;
    }
  }

  echo json_encode($re);

  $pdo = null;
?>