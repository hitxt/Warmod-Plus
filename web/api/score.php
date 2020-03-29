<?php
  require_once "../configs/configs.php";
  require_once "../libs/sql.php";
  require_once "../libs/functions.php";

  // Debug
  // $jsonString = file_get_contents("php://input");
  // $myFile = date('his', time()).".json";
  // file_put_contents($myFile,$jsonString);
  // $pdo->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING );

  $json = json_decode(file_get_contents('php://input'), true);
  $re['success'] = false;
  $re['id'] = -1;

  if(!empty($json) && !empty($json['token'])) 
  {
    $license = checkLicense($json["token"]);

    if($license['valid']) 
    {
      if(!is_null($json['demo']) &&
        !is_null($json['comp']) &&
        !is_null($json['ev']) &&
        !is_null($json['map']) &&
        !is_null($json['mr']) &&
        !is_null($json['otr']) &&
        !is_null($json['otc']) &&
        !is_null($json['po']) &&
        !is_null($json['type']) &&
        !is_null($json['ctid']) &&
        !is_null($json['cts']) &&
        !is_null($json['ctfh']) &&
        !is_null($json['ctsh']) &&
        !is_null($json['ctots']) &&
        !is_null($json['tid'])&&
        !is_null($json['ts']) &&
        !is_null($json['tfh']) &&
        !is_null($json['tsh']) &&
        !is_null($json['tots']))
      {
        $input = array(
          ":map" => $json['map'],
          ":max_rounds" => $json['mr'],
          ":overtime_max_rounds" => $json['otr'],
          ":overtime_count" => $json['otc'],
          ":played_out" => $json['po'],
          ":t_id" => $json['tid'], 
          ":t_overall_score" => $json['ts'], 
          ":t_first_half_score" => $json['tfh'], 
          ":t_second_half_score" => $json['tsh'], 
          ":t_overtime_score" => $json['tots'], 
          ":ct_id" => $json['ctid'], 
          ":ct_overall_score" => $json['cts'], 
          ":ct_first_half_score" => $json['ctfh'], 
          ":ct_second_half_score" => $json['ctsh'], 
          ":ct_overtime_score" => $json['ctots'], 
          ":demo" => $json['demo'], 
          ":competition" => $json['comp'],
          ":event" => $json['ev'],
        );

        switch($json['type']) {
          // update round
          case "update_round":
            if(empty($json['id']))  return;
            $id = $json['id'];
            $input[':id'] = $json['id'];
            $sql = "UPDATE ".$result_table." SET match_end = UTC_TIMESTAMP(), 
              map = :map, 
              max_rounds = :max_rounds, 
              overtime_max_rounds = :overtime_max_rounds, 
              overtime_count = :overtime_count, 
              played_out = :played_out, 
              t_id = :t_id, 
              t_overall_score = :t_overall_score, 
              t_first_half_score = :t_first_half_score, 
              t_second_half_score = :t_second_half_score, 
              t_overtime_score = :t_overtime_score, 
              ct_id = :ct_id, 
              ct_overall_score = :ct_overall_score, 
              ct_first_half_score = :ct_first_half_score, 
              ct_second_half_score = :ct_second_half_score, 
              ct_overtime_score = :ct_overtime_score, 
              demo = :demo, 
              competition = :competition, 
              event = :event WHERE id = :id";
              $sth = $pdo->prepare($sql);
              $sth->execute($input);
          break;
          
          // insert round
          case "insert_round":
            $sql = "INSERT INTO ".$result_table." VALUES
              (NULL, 
              UTC_TIMESTAMP(), 
              UTC_TIMESTAMP(), 
              :map,
              :max_rounds,
              :overtime_max_rounds,
              :overtime_count,
              :played_out,
              :t_id,
              :t_overall_score,
              :t_first_half_score,
              :t_second_half_score,
              :t_overtime_score,
              :ct_id,
              :ct_overall_score,
              :ct_first_half_score,
              :ct_second_half_score,
              :ct_overtime_score,
              :demo,
              :competition,
              :event)";
            $sth = $pdo->prepare($sql);
            $sth->execute($input);

            $id = $pdo->lastInsertId();
          break;
  
          // CreateResultKey
          case "create":
            $sql = "INSERT INTO ".$result_table." VALUES 
              (NULL, 
              UTC_TIMESTAMP(), 
              UTC_TIMESTAMP(), 
              :map,
              :max_rounds,
              :overtime_max_rounds,
              :overtime_count,
              :played_out,
              :t_id,
              :t_overall_score,
              :t_first_half_score,
              :t_second_half_score,
              :t_overtime_score,
              :ct_id,
              :ct_overall_score,
              :ct_first_half_score,
              :ct_second_half_score,
              :ct_overtime_score,
              :demo,
              :competition,
              :event)";
            $sth = $pdo->prepare($sql);
            $sth->execute($input);

            $id = $pdo->lastInsertId();
          break;
  
          // update
          case "update":
            if(empty($json['id']))  return;
            if(empty($json['ml']))  return;

            $input[':id'] = $json['id'];
            $input[':ml'] = $json['ml'];

            $sql = "UPDATE ".$result_table." SET 
              match_start = DATE_SUB(UTC_TIMESTAMP(), INTERVAL :ml SECOND),
              match_end = UTC_TIMESTAMP(),
              map = :map, 
              max_rounds = :max_rounds, 
              overtime_max_rounds = :overtime_max_rounds, 
              overtime_count = :overtime_count, 
              played_out = :played_out, 
              t_id = :t_id, 
              t_overall_score = :t_overall_score, 
              t_first_half_score = :t_first_half_score, 
              t_second_half_score = :t_second_half_score, 
              t_overtime_score = :t_overtime_score, 
              ct_id = :ct_id, 
              ct_overall_score = :ct_overall_score, 
              ct_first_half_score = :ct_first_half_score, 
              ct_second_half_score = :ct_second_half_score, 
              ct_overtime_score = :ct_overtime_score, 
              demo = :demo, 
              competition = :competition, 
              event = :event
              WHERE id = :id";
            
            $sth = $pdo->prepare($sql);
            $sth->execute($input);

            $id = $json['id'];
          break;
          
          // insert
          case "insert":
            if(empty($json['id']))  return;
            if(empty($json['ml']))  return;

            $input[':id'] = $json['id'];
            $input[':ml'] = $json['ml'];

            $sql = "INSERT INTO " .$result_table. " VALUES
              (NULL, 
              DATE_SUB(UTC_TIMESTAMP(), INTERVAL :ml SECOND), 
              UTC_TIMESTAMP(),
              :map,
              :max_rounds,
              :overtime_max_rounds,
              :overtime_count,
              :played_out,
              :t_id,
              :t_overall_score,
              :t_first_half_score,
              :t_second_half_score,
              :t_overtime_score,
              :ct_id,
              :ct_overall_score,
              :ct_first_half_score,
              :ct_second_half_score,
              :ct_overtime_score,
              :demo,
              :competition,
              :event)";

              $sth = $pdo->prepare($sql);
              $sth->execute($input);
  
              $id = $pdo->lastInsertId();
          break;
        }

        $re['success'] = true;
        $re['id'] = intval($id);
      }
    }
  }

  echo json_encode($re);

  $pdo = null;
?>