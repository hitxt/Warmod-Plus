<?php	
	require_once("../configs/configs.php");
  require_once("../libs/sql.php");
  require_once("../libs/functions.php");
  
  // Debug
  $jsonString = file_get_contents("php://input");
  $myFile = date('his', time()).".json";
  file_put_contents($myFile,$jsonString);
  $pdo->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING );

  $json = json_decode(file_get_contents('php://input'), true);

  $re['success'] = false;

  if(!empty($json) && !empty($json['token'])) 
  {
    $license = checkLicense($json["token"]);

    if($license['valid']) 
    {
      if(!empty($json['p']))
      {
        $playerdata = $json['p'][0];

        if(!is_null($playerdata['rounds_played']) &&
            !is_null($playerdata['team']) &&
            !is_null($playerdata['kills']) &&
            !is_null($playerdata['deaths']) &&
            !is_null($playerdata['assists']) &&
            !is_null($playerdata['head_shots']) &&
            !is_null($playerdata['team_kills']) &&
            !is_null($playerdata['assists_team_attack']) &&
            !is_null($playerdata['damage']) &&
            !is_null($playerdata['hits']) &&
            !is_null($playerdata['shots']) &&
            !is_null($playerdata['last_alive']) &&
            !is_null($playerdata['clutch_won']) &&
            !is_null($playerdata['1k']) &&
            !is_null($playerdata['2k']) &&
            !is_null($playerdata['3k']) &&
            !is_null($playerdata['4k']) &&
            !is_null($playerdata['5k']) &&
            !is_null($playerdata['mvp']) &&
            !is_null($playerdata['rws']) &&
            !is_null($playerdata['rws2']) &&
            !is_null($playerdata['knife']) &&
            !is_null($playerdata['glock']) &&
            !is_null($playerdata['hkp2000']) &&
            !is_null($playerdata['usp_silencer']) &&
            !is_null($playerdata['p250']) &&
            !is_null($playerdata['deagle']) &&
            !is_null($playerdata['elite']) &&
            !is_null($playerdata['fiveseven']) &&
            !is_null($playerdata['tec9']) &&
            !is_null($playerdata['cz75a']) &&
            !is_null($playerdata['revolver']) &&
            !is_null($playerdata['nova']) &&
            !is_null($playerdata['xm1014']) &&
            !is_null($playerdata['mag7']) &&
            !is_null($playerdata['sawedoff']) &&
            !is_null($playerdata['bizon']) &&
            !is_null($playerdata['mac10']) &&
            !is_null($playerdata['mp9']) &&
            !is_null($playerdata['mp7']) &&
            !is_null($playerdata['mp5sd']) &&
            !is_null($playerdata['ump45']) &&
            !is_null($playerdata['p90']) &&
            !is_null($playerdata['galilar']) &&
            !is_null($playerdata['ak47']) &&
            !is_null($playerdata['scar20']) &&
            !is_null($playerdata['famas']) &&
            !is_null($playerdata['m4a1']) &&
            !is_null($playerdata['m4a1_silencer']) &&
            !is_null($playerdata['aug']) &&
            !is_null($playerdata['ssg08']) &&
            !is_null($playerdata['sg556']) &&
            !is_null($playerdata['awp']) &&
            !is_null($playerdata['g3sg1']) &&
            !is_null($playerdata['m249']) &&
            !is_null($playerdata['negev']) &&
            !is_null($playerdata['hegrenade']) &&
            !is_null($playerdata['flashbang']) &&
            !is_null($playerdata['smokegrenade']) &&
            !is_null($playerdata['inferno']) &&
            !is_null($playerdata['incgrenade']) &&
            !is_null($playerdata['molotov']) &&
            !is_null($playerdata['decoy']) &&
            !is_null($playerdata['taser']) &&
            !is_null($playerdata['generic']) &&
            !is_null($playerdata['head']) &&
            !is_null($playerdata['chest']) &&
            !is_null($playerdata['stomach']) &&
            !is_null($playerdata['left_arm']) &&
            !is_null($playerdata['right_arm']) &&
            !is_null($playerdata['left_leg']) &&
            !is_null($playerdata['right_leg']) &&
            !is_null($playerdata['c4_planted']) &&
            !is_null($playerdata['c4_exploded']) &&
            !is_null($playerdata['c4_defused']) &&
            !is_null($playerdata['hostages_rescued']) &&
            !is_null($playerdata['id'])
        ) {

          for($i=0; $i<count($json['p']); $i++)
          {
            $player = $json['p'][$i];
            // update rws
            $input = array(
              ":rws" => $player['rws'],
              ":steam" => $player['id'],
            );
            $sql = "UPDATE ".$player_table." SET rws = :rws WHERE steam_id_64 = :steam";
            $sth = $pdo->prepare($sql);
            $sth->execute($input);

            $input = array(
              ":rounds_played" => $player['rounds_played'],
              ":team" => $player['team'],
              ":kills" => $player['kills'],
              ":deaths" => $player['deaths'],
              ":assists" => $player['assists'],
              ":head_shots" => $player['head_shots'],
              ":team_kills" => $player['team_kills'],
              ":assists_team_attack" => $player['assists_team_attack'],
              ":damage" => $player['damage'],
              ":hits" => $player['hits'],
              ":shots" => $player['shots'],
              ":last_alive" => $player['last_alive'],
              ":clutch_won" => $player['clutch_won'],
              ":1k" => $player['1k'],
              ":2k" => $player['2k'],
              ":3k" => $player['3k'],
              ":4k" => $player['4k'],
              ":5k" => $player['5k'],
              ":mvp" => $player['mvp'],
              ":rws2" => $player['rws2'],
              ":knife" => $player['knife'],
              ":glock" => $player['glock'],
              ":hkp2000" => $player['hkp2000'],
              ":usp_silencer" => $player['usp_silencer'],
              ":p250" => $player['p250'],
              ":deagle" => $player['deagle'],
              ":elite" => $player['elite'],
              ":fiveseven" => $player['fiveseven'],
              ":tec9" => $player['tec9'],
              ":cz75a" => $player['cz75a'],
              ":revolver" => $player['revolver'],
              ":nova" => $player['nova'],
              ":xm1014" => $player['xm1014'],
              ":mag7" => $player['mag7'],
              ":sawedoff" => $player['sawedoff'],
              ":bizon" => $player['bizon'],
              ":mac10" => $player['mac10'],
              ":mp9" => $player['mp9'],
              ":mp7" => $player['mp7'],
              ":mp5sd" => $player['mp5sd'],
              ":ump45" => $player['ump45'],
              ":p90" => $player['p90'],
              ":galilar" => $player['galilar'],
              ":ak47" => $player['ak47'],
              ":scar20" => $player['scar20'],
              ":famas" => $player['famas'],
              ":m4a1" => $player['m4a1'],
              ":m4a1_silencer" => $player['m4a1_silencer'],
              ":aug" => $player['aug'],
              ":ssg08" => $player['ssg08'],
              ":sg556" => $player['sg556'],
              ":awp" => $player['awp'],
              ":g3sg1" => $player['g3sg1'],
              ":m249" => $player['m249'],
              ":negev" => $player['negev'],
              ":hegrenade" => $player['hegrenade'],
              ":flashbang" => $player['flashbang'],
              ":smokegrenade" => $player['smokegrenade'],
              ":inferno" => $player['inferno'],
              ":incgrenade" => $player['incgrenade'],
              ":molotov" => $player['molotov'],
              ":decoy" => $player['decoy'],
              ":taser" => $player['taser'],
              ":generic" => $player['generic'],
              ":head" => $player['head'],
              ":chest" => $player['chest'],
              ":stomach" => $player['stomach'],
              ":left_arm" => $player['left_arm'],
              ":right_arm" => $player['right_arm'],
              ":left_leg" => $player['left_leg'],
              ":right_leg" => $player['right_leg'],
              ":c4_planted" => $player['c4_planted'],
              ":c4_exploded" => $player['c4_exploded'],
              ":c4_defused" => $player['c4_defused'],
              ":hostages_rescued" => $player['hostages_rescued'],
              ":match_id" => $json['mid'],
              ":steam_id_64" => $player['id'],
            );
            $sql = "
              UPDATE ".$stats_table." SET 
              rounds_played = :rounds_played, 
              team = :team,
              kills = :kills,
              deaths = :deaths,
              assists = :assists,
              head_shots = :head_shots,
              team_kills = :team_kills,
              assists_team_attack = :assists_team_attack,
              damage = :damage,
              hits = :hits,
              shots = :shots,
              last_alive = :last_alive,
              clutch_won = :clutch_won,
              1k = :1k,
              2k = :2k,
              3k = :3k,
              4k = :4k,
              5k = :5k,
              mvp = :mvp,
              rws = :rws2,
              knife = :knife,
              glock = :glock,
              hkp2000 = :hkp2000,
              usp_silencer = :usp_silencer,
              p250 = :p250,
              deagle = :deagle,
              elite = :elite,
              fiveseven = :fiveseven,
              tec9 = :tec9,
              cz75a = :cz75a,
              revolver = :revolver,
              nova = :nova,
              xm1014 = :xm1014,
              mag7 = :mag7,
              sawedoff = :sawedoff,
              bizon = :bizon,
              mac10 = :mac10,
              mp9 = :mp9,
              mp7 = :mp7,
              mp5sd = :mp5sd,
              ump45 = :ump45,
              p90 = :p90,
              galilar = :galilar,
              ak47 = :ak47,
              scar20 = :scar20,
              famas = :famas,
              m4a1 = :m4a1,
              m4a1_silencer = :m4a1_silencer,
              aug = :aug,
              ssg08 = :ssg08,
              sg556 = :sg556,
              awp = :awp,
              g3sg1 = :g3sg1,
              m249 = :m249,
              negev = :negev,
              hegrenade = :hegrenade,
              flashbang = :flashbang,
              smokegrenade = :smokegrenade,
              inferno = :inferno,
              incgrenade = :incgrenade,
              molotov = :molotov,
              decoy = :decoy,
              taser = :taser,
              generic = :generic,
              head = :head,
              chest = :chest,
              stomach = :stomach,
              left_arm = :left_arm,
              right_arm = :right_arm,
              left_leg = :left_leg,
              right_leg = :right_leg,
              c4_planted = :c4_planted,
              c4_exploded = :c4_exploded,
              c4_defused = :c4_defused,
              hostages_rescued = :hostages_rescued 
              WHERE match_id = :match_id AND steam_id_64 = :steam_id_64";
            $sth = $pdo->prepare($sql);
            $sth->execute($input);

            // insert
            if($sth->rowCount() == 0)
            {	
              $input = array(
                ":rounds_played" => $player['rounds_played'],
                ":team" => $player['team'],
                ":kills" => $player['kills'],
                ":deaths" => $player['deaths'],
                ":assists" => $player['assists'],
                ":head_shots" => $player['head_shots'],
                ":team_kills" => $player['team_kills'],
                ":assists_team_attack" => $player['assists_team_attack'],
                ":damage" => $player['damage'],
                ":hits" => $player['hits'],
                ":shots" => $player['shots'],
                ":last_alive" => $player['last_alive'],
                ":clutch_won" => $player['clutch_won'],
                ":1k" => $player['1k'],
                ":2k" => $player['2k'],
                ":3k" => $player['3k'],
                ":4k" => $player['4k'],
                ":5k" => $player['5k'],
                ":mvp" => $player['mvp'],
                ":rws2" => $player['rws2'],
                ":knife" => $player['knife'],
                ":glock" => $player['glock'],
                ":hkp2000" => $player['hkp2000'],
                ":usp_silencer" => $player['usp_silencer'],
                ":p250" => $player['p250'],
                ":deagle" => $player['deagle'],
                ":elite" => $player['elite'],
                ":fiveseven" => $player['fiveseven'],
                ":tec9" => $player['tec9'],
                ":cz75a" => $player['cz75a'],
                ":revolver" => $player['revolver'],
                ":nova" => $player['nova'],
                ":xm1014" => $player['xm1014'],
                ":mag7" => $player['mag7'],
                ":sawedoff" => $player['sawedoff'],
                ":bizon" => $player['bizon'],
                ":mac10" => $player['mac10'],
                ":mp9" => $player['mp9'],
                ":mp7" => $player['mp7'],
                ":mp5sd" => $player['mp5sd'],
                ":ump45" => $player['ump45'],
                ":p90" => $player['p90'],
                ":galilar" => $player['galilar'],
                ":ak47" => $player['ak47'],
                ":scar20" => $player['scar20'],
                ":famas" => $player['famas'],
                ":m4a1" => $player['m4a1'],
                ":m4a1_silencer" => $player['m4a1_silencer'],
                ":aug" => $player['aug'],
                ":ssg08" => $player['ssg08'],
                ":sg556" => $player['sg556'],
                ":awp" => $player['awp'],
                ":g3sg1" => $player['g3sg1'],
                ":m249" => $player['m249'],
                ":negev" => $player['negev'],
                ":hegrenade" => $player['hegrenade'],
                ":flashbang" => $player['flashbang'],
                ":smokegrenade" => $player['smokegrenade'],
                ":inferno" => $player['inferno'],
                ":incgrenade" => $player['incgrenade'],
                ":molotov" => $player['molotov'],
                ":decoy" => $player['decoy'],
                ":taser" => $player['taser'],
                ":generic" => $player['generic'],
                ":head" => $player['head'],
                ":chest" => $player['chest'],
                ":stomach" => $player['stomach'],
                ":left_arm" => $player['left_arm'],
                ":right_arm" => $player['right_arm'],
                ":left_leg" => $player['left_leg'],
                ":right_leg" => $player['right_leg'],
                ":c4_planted" => $player['c4_planted'],
                ":c4_exploded" => $player['c4_exploded'],
                ":c4_defused" => $player['c4_defused'],
                ":hostages_rescued" => $player['hostages_rescued'],
                ":match_id" => $json['mid'],
                ":steam_id_64" => $player['id'],
              );
              $sql = "INSERT INTO ".$stats_table."
              VALUES 				
              (NULL, 
              :match_id, 
              :rounds_played,
              :steam_id_64,
              :team,
              :kills,
              :deaths,
              :assists,
              :head_shots,
              :team_kills,
              :assists_team_attack,
              :damage,
              :hits,
              :shots,
              :last_alive,
              :clutch_won,
              :1k,
              :2k,
              :3k,
              :4k,
              :5k,
              :mvp,
              :rws2,
              :knife,
              :glock,
              :hkp2000,
              :usp_silencer,
              :p250,
              :deagle,
              :elite,
              :fiveseven,
              :tec9,
              :cz75a,
              :revolver,
              :nova,
              :xm1014,
              :mag7,
              :sawedoff,
              :bizon,
              :mac10,
              :mp9,
              :mp7,
              :mp5sd,
              :ump45,
              :p90,
              :galilar,
              :ak47,
              :scar20,
              :famas,
              :m4a1,
              :m4a1_silencer,
              :aug,
              :ssg08,
              :sg556,
              :awp,
              :g3sg1,
              :m249,
              :negev,
              :hegrenade,
              :flashbang,
              :smokegrenade,
              :inferno,
              :incgrenade,
              :molotov,
              :decoy,
              :taser,
              :generic,
              :head,
              :chest,
              :stomach,
              :left_arm,
              :right_arm,
              :left_leg,
              :right_leg,
              :c4_planted,
              :c4_exploded,
              :c4_defused,
              :hostages_rescued
              )";
              $sth = $pdo->prepare($sql);
              $sth->execute($input);
            }

            $re['success'] = true;
          }
        }
      }
    }	
  }

  echo json_encode($re);
  
  $pdo = null;
?>