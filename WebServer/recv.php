<?php

$mysql_host = "127.0.0.1";
$mysql_port = 3306;
$mysql_user = "";
$mysql_pass = "";
$mysql_db = "";

$webhookurl = "DISCORD WEB HOOK URL";
$BotName = "Killing Floor 2 Server";
$apiKey = "STEAM API KEY";


$json_data = null;
$timestamp = date("c", strtotime("now"));

//If SaveToFile=False
$postData = file_get_contents('php://input');
//If SaveToFile=True
//$postData = file_get_contents( 'C:/KF2Server/KFGame/Stats' . '/StatsRealTime.ctr' );

$data = json_decode($postData, true);


function getConnection()
{
    global $mysql_host, $mysql_user, $mysql_pass, $mysql_db, $mysql_port;

    $conn = new mysqli($mysql_host, $mysql_user, $mysql_pass, $mysql_db, $mysql_port);
    // Check connection
    if ($conn->connect_error)
    {
        die("Connection failed: " . $conn->connect_error);
    }
    return $conn;
}

function StartNewGame($NewSession, $MapName, $gameID)
{
    $conn = getConnection();

    $oldSession = $conn->query("SELECT session, mapname FROM `kf2_session` WHERE active='$gameID' LIMIT 1;");
    $oldSession = $oldSession->fetch_row();
    $oldMapName = $oldSession[1];
    $oldSession = $oldSession[0];

    $playersRows = $conn->query("SELECT id, headshots, kills, assists, deaths, dosh, damagedealt, damagetaken, accuracy, hsaccuracy FROM `kf2_players` WHERE last_session='$oldSession';");

    while($playersData = $playersRows->fetch_array()) {
        $playerID = $playersData['id'];
        $playerHS = $playersData['headshots'];
        $playerKills = $playersData['kills'];
        $playerAssists = $playersData['assists'];
        $playerDeaths = $playersData['deaths'];
        $playerDosh = $playersData['dosh'];
        $playerDD = $playersData['damagedealt'];
        $playerDT = $playersData['damagetaken'];
        $playerA = $playersData['accuracy'];
        $playerHSA = $playersData['hsaccuracy'];

        $chkStats = "SELECT id FROM `kf2_trackstats` WHERE pid='$playerID' AND mapname='$oldMapName';";
        $chkStats = $conn->query($chkStats);
        $chkStats = $chkStats->num_rows;
    
        if ($chkStats) 
        {
            $PrevDamage = $conn->query("SELECT dmgdealt FROM `kf2_trackstats` WHERE pid='$playerID' AND mapname='$oldMapName';");
            $PrevDamage = $PrevDamage->fetch_row();
            $PrevDamage = intval($PrevDamage[0]);

            if ($PrevDamage < $playerDD) {
                $updStats = "UPDATE `kf2_trackstats` SET headshots='$playerHS', kills='$playerKills', assists='$playerAssists', deaths='$playerDeaths', doshearn='$playerDosh', dmgdealt='$playerDD', dmgtaken='$playerDT', accuracy='$playerA', hsaccuracy='$playerHSA'  WHERE pid='$playerID' AND mapname='$oldMapName';";
                $conn->query($updStats);
            }
        } else {
            $addStats = "INSERT INTO `kf2_trackstats` (pid, headshots, kills, assists, deaths, doshearn, dmgdealt, dmgtaken, accuracy, hsaccuracy, mapname) VALUES ('$playerID', '$playerHS', '$playerKills', '$playerAssists', '$playerDeaths', '$playerDosh', '$playerDD', '$playerDT', '$playerA', '$playerHSA', '$oldMapName');";
            $conn->query($addStats);
        }
    }

    //Clean Stats Link
    $conn->query("UPDATE `kf2_players` SET last_session='None' WHERE last_session='$oldSession';");

    //Update Active Session
    $updSession = "UPDATE `kf2_session` SET active='0' WHERE active='$gameID';";
    $conn->query($updSession);

    $addSession = "INSERT INTO `kf2_session` (session, mapname, active) VALUES ('$NewSession', '$MapName', '$gameID');";
    $conn->query($addSession);

    $conn->close();
}

function UpdateSession($sessionData, $conn, $gameID)
{
    $session = $sessionData['session'];
    $sessionMapName = $sessionData['mapname'];
    $sessionGD = $sessionData['gamedifficulty'];
    $sessionTotalWave = $sessionData['totalwave'];
    $sessionCurrentWave = $sessionData['currentwave'];
    $sessionWaveStarted = $sessionData['wavestarted'];
    $sessionWaveTrader = $sessionData['trader'];
    $sessionTotalZedKilled = $sessionData['totalZedKilled'];

    $chkSession = "SELECT id FROM `kf2_session` WHERE session='$session' AND mapname='$sessionMapName';";
    $chkSession = $conn->query($chkSession);
    $chkSession = $chkSession->num_rows;

    if ($chkSession) 
    {
        $updSession = "UPDATE `kf2_session` SET difficulty='$sessionGD', totalwave='$sessionTotalWave', currentwave='$sessionCurrentWave', wavestarted='$sessionWaveStarted', trader='$sessionWaveTrader', totalZedKilled='$sessionTotalZedKilled' WHERE session='$session' AND mapname='$sessionMapName';";
        $conn->query($updSession);
    } else {
        $addSession = "INSERT INTO `kf2_session` (session, mapname, difficulty, totalwave, currentwave, wavestarted, trader, totalZedKilled, active) VALUES ('$session', '$sessionMapName', '$sessionGD', '$sessionTotalWave', '$sessionCurrentWave', '$sessionWaveStarted', '$sessionWaveTrader', '$sessionTotalZedKilled', '$gameID');";
        $conn->query($addSession);
    }

    UpdatePlayer($sessionData['playerlist'], $conn, $session);

    //Write Stats
    $playersRows = $conn->query("SELECT id, headshots, kills, assists, deaths, dosh, damagedealt, damagetaken, accuracy, hsaccuracy FROM `kf2_players` WHERE last_session='$session';");

    while($playersData = $playersRows->fetch_array()) {
        $playerID = $playersData['id'];
        $playerHS = $playersData['headshots'];
        $playerKills = $playersData['kills'];
        $playerAssists = $playersData['assists'];
        $playerDeaths = $playersData['deaths'];
        $playerDosh = $playersData['dosh'];
        $playerDD = $playersData['damagedealt'];
        $playerDT = $playersData['damagetaken'];
        $playerA = $playersData['accuracy'];
        $playerHSA = $playersData['hsaccuracy'];

        $chkStats = "SELECT id FROM `kf2_trackstats` WHERE pid='$playerID' AND mapname='$sessionMapName';";
        $chkStats = $conn->query($chkStats);
        $chkStats = $chkStats->num_rows;
    
        if ($chkStats) 
        {
            $PrevDamage = $conn->query("SELECT dmgdealt FROM `kf2_trackstats` WHERE pid='$playerID' AND mapname='$sessionMapName';");
            $PrevDamage = $PrevDamage->fetch_row();
            $PrevDamage = intval($PrevDamage[0]);

            if ($PrevDamage < $playerDD) {
                $updStats = "UPDATE `kf2_trackstats` SET headshots='$playerHS', kills='$playerKills', assists='$playerAssists', deaths='$playerDeaths', doshearn='$playerDosh', dmgdealt='$playerDD', dmgtaken='$playerDT', accuracy='$playerA', hsaccuracy='$playerHSA'  WHERE pid='$playerID' AND mapname='$sessionMapName';";
                $conn->query($updStats);
            }
        } else {
            $addStats = "INSERT INTO `kf2_trackstats` (pid, headshots, kills, assists, deaths, doshearn, dmgdealt, dmgtaken, accuracy, hsaccuracy, mapname) VALUES ('$playerID', '$playerHS', '$playerKills', '$playerAssists', '$playerDeaths', '$playerDosh', '$playerDD', '$playerDT', '$playerA', '$playerHSA', '$sessionMapName');";
            $conn->query($addStats);
        }
    }
}

function UpdatePlayer($playerList, $conn, $session)
{
    for ($i = 0;$i < count($playerList);$i++)
    {
        $dataName = $playerList[$i]['playername'];
        $dataUid = $playerList[$i]['uid'];
        $dataSteamId = $playerList[$i]['steamid'];
        $dataIP = $playerList[$i]['IP'];
        $dataGameId = $playerList[$i]['id'];
        $dataReady = $playerList[$i]['ready'];
        $dataPerkName = $playerList[$i]['perkname'];
        $dataPerkLevel = $playerList[$i]['perklevel'];
        $dataPrestige = $playerList[$i]['prestigeLevel'];
        $dataDamageDealt = $playerList[$i]['damagedealt'];

        $dataDamageTaken = $playerList[$i]['damagetaken'];
        $dataHeadshots = $playerList[$i]['headshots'];
        $dataAccuracy = $playerList[$i]['accuracy'];
        $dataHSAccuracy = $playerList[$i]['hsaccuracy'];

        $dataKills = $playerList[$i]['kills'];
        $dataAssists = $playerList[$i]['assists'];
        $dataDeaths = $playerList[$i]['deaths'];

        $dataDosh = $playerList[$i]['dosh'];

        $dataPing = $playerList[$i]['ping'];

        $chkPlayer = "SELECT unique_net_id FROM `kf2_players` WHERE unique_net_id='$dataUid';";
        $chkPlayer = $conn->query($chkPlayer);
        $chkPlayer = $chkPlayer->num_rows;

        if ($chkPlayer)
        {
            $updPlayer = "UPDATE `kf2_players` SET name='$dataName', ip_address='$dataIP', ready='$dataReady', perk_name='$dataPerkName', perk_level='$dataPerkLevel', prestige='$dataPrestige', damagedealt='$dataDamageDealt', damagetaken='$dataDamageTaken', headshots='$dataHeadshots', accuracy='$dataAccuracy', hsaccuracy='$dataHSAccuracy', deaths='$dataDeaths', kills='$dataKills', assists='$dataAssists', dosh='$dataDosh', ping='$dataPing', last_session='$session' WHERE unique_net_id='$dataUid';";
            $conn->query($updPlayer);
        }
        else
        {
            $addPlayer = "INSERT INTO `kf2_players` (name, unique_net_id, steamid, ip_address, gameid, ready, perk_name, perk_level, prestige, damagedealt, damagetaken, headshots, accuracy, hsaccuracy, kills, assists, deaths, dosh, ping, last_session) VALUES ('$dataName', '$dataUid', '$dataSteamId', '$dataIP', '$dataGameId', '$dataReady', '$dataPerkName', '$dataPerkLevel', '$dataPrestige', '$dataDamageDealt', '$dataDamageTaken', '$dataHeadshots', '$dataAccuracy', '$dataHSAccuracy', '$dataKills', '$dataAssists', '$dataDeaths', '$dataDosh', '$dataPing', '$session');";
            $conn->query($addPlayer);
        }
    }
}

//=======================================================================================================
// Config for NGinx
//=======================================================================================================
//server {
//    listen 7070 default_server;
//
//    root '%sprogdir%/domains/KF2_ToDiscord';
//    index index.php index.html index.htm;
//        %allow%allow all;
//        allow 127.0.0.0/8;
//        allow ::1/128;
//        allow %ips%;
//        deny all;
//    server_name 127.0.0.1 192.168.1.128;
//    location / {
//        try_files $uri $uri/ /index.php;
//    }
//
//    location ~ \.php$ {
//        fastcgi_pass   backend;
//        include        '%sprogdir%/userdata/config/nginx_fastcgi_params.txt';
//    }
//}

//=======For Debug=======
//$logFile = __DIR__ . '/data.log';
//$logWrite = '';
//
//if (!file_exists($logFile)) {
//    $fp = fopen($logFile, "w");
//    fclose($fp);
//}
//
//$logWrite .= print_r($data, true);
//$logWrite .= file_get_contents($logFile);
//file_put_contents($logFile, $logWrite);
//========================

if ($data['code'] === "KF2_MSG") 
{
    $GID = $data['content']['gameid'];
    $UID = $data['content']['uid'];
    $PlayerName = $data['content']['name'];
    $msg = $data['content']['message'];
    $steamID = $data['content']['steamID'];

    $steamContent = file_get_contents("https://api.steampowered.com/ISteamUser/GetPlayerSummaries/v0002/?key=$apiKey&steamids=$steamID");
    $json = json_decode($steamContent);

    $avatar = $json->response->players[0]->avatarfull;
    $country = $json->response->players[0]->loccountrycode;
    
    if ($avatar == '') {
        $json_data = json_encode([
            "username" => "$PlayerName [Server: $GID]",
            "avatar_url" => "https://cdn.akamai.steamstatic.com/steamcommunity/public/images/avatars/d9/d94f23591f0c3509f139a1737b26cbbc4fee258b_full.jpg",
            "tts" => false, //Voice
    
            "content" => "$msg"
        
        ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );
    } else {
        $json_data = json_encode([
            "username" => "$PlayerName ($country) [Server: $GID]",
            "avatar_url" => "$avatar",
            "tts" => false, //Voice
    
            "content" => "$msg"
        
        ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );
    }
}

if ($data['code'] === "KF2_LOBBY_UPDATE") 
{
    $conn = getConnection();
    $thisSess = $data['content']['session'];
    $totalZedKilled = $data['content']['totalZedKilled'];
    $currentwave= $data['content']['currentwave'];
    $gamedifficulty = $data['content']['gamedifficulty'];
    $map = strtoupper($data['content']['mapname']);
    $playersNum = count($data['content']['playerlist']);
    $wavetrader = ($data['content']['trader'] == 1 ? "Yes" : "No");
    $gameID = $data['content']['gameid'];

    UpdateSession($data['content'], $conn, $gameID);
    
    $LastWave = $conn->query("SELECT lastwave FROM `kf2_session` WHERE active='$gameID' AND session='$thisSess' AND mapname='$map' LIMIT 1;");
    $LastWave = $LastWave->fetch_row();
    $LastWave = $LastWave[0];

    if ($wavetrader == "Yes" && $playersNum > 0 && $LastWave < $currentwave) {

        $TopRows = $conn->query("SELECT pid FROM `kf2_trackstats` WHERE mapname = '$map' ORDER BY dmgdealt DESC LIMIT 3;");
        $TopPlayers = [];

        while($row = $TopRows->fetch_array()) {
            $playerID = $row['pid'];
    
            $PlayerUID = $conn->query("SELECT name FROM `kf2_players` WHERE id='$playerID';");
            $PlayerUID = $PlayerUID->fetch_row();
            $PlayerName = $PlayerUID[0];
    
            $PlayerName = str_replace("@", "", $PlayerName);
            $PlayerName = str_replace("*", "", $PlayerName);
            
            array_push($TopPlayers, $PlayerName);
        }

        $newData = [
            "username" => $BotName,
            "avatar_url" => "https://cdn.akamai.steamstatic.com/steamcommunity/public/images/avatars/d9/d94f23591f0c3509f139a1737b26cbbc4fee258b_full.jpg",
            "tts" => false, //Voice
        
            "embeds" => [
                [
                    "type" => "rich",
                    "url" => "https://kf2stats.tix.su",
                    "timestamp" => $timestamp,
                    "color" => hexdec( "ffff33" ),
        
                    "title" => "Завершена волна - $currentwave",
                    "description" => "$map | $gamedifficulty",
        
                    "footer" => [
                        "text" => "Нихерасе",
                        "icon_url" => "https://xxx365.info/wp-content/uploads/2018/05/101998_17342864-1756198841360792-3405319389011615295-n-1024x1010.jpg"
                    ],
            
                    "fields" => [
                        [
                            "name" => "Total Zed Killed",
                            "value" => $totalZedKilled,
                            "inline" => true
                        ],
                        [
                            "name" => "Current Players",
                            "value" => "$playersNum/12",
                            "inline" => true
                        ],
                        [
                            "name" => "Top Players on This Map",
                            "value" => $TopPlayers[0]." | ".$TopPlayers[1]." | ".$TopPlayers[2],
                            "inline" => false
                        ],
                    ]
                ]
            ]
        ];
        
        for ($i=0; $i < $playersNum; $i++) 
        {
            $playerName = $data['content']['playerlist'][$i]['playername'];
        
            $playerDD = $data['content']['playerlist'][$i]['damagedealt'];
            $playerDeaths = $data['content']['playerlist'][$i]['deaths'];
        
            $test = ["name" => $playerName, "value" => "$playerDD | $playerDeaths", "inline" => true];
            array_push($newData['embeds'][0]['fields'], $test);
        };
        
        $json_data = json_encode($newData, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );

        $updStatsWave = "UPDATE `kf2_session` SET lastwave='$currentwave' WHERE active='$gameID' AND session='$thisSess' AND mapname='$map';";
        $conn->query($updStatsWave);
    }

    if ($conn) $conn->close();
}

if ($data['code'] === "KF2_MATCHCREATED") 
{
    $GID = $data['content']['gameid'];
    StartNewGame($data['content']['session'], $data['content']['map'], $GID);

    $map = strtoupper($data['content']['map']);
    $json_data = json_encode([

        "username" => "Killing Floor 2 (REDEYE) Server [№ $GID]",
        "avatar_url" => "https://cdn.akamai.steamstatic.com/steamcommunity/public/images/avatars/d9/d94f23591f0c3509f139a1737b26cbbc4fee258b_full.jpg",
        "tts" => false, //Voice

        "content" => "Запущен новый матч на карте $map",
    ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );
}

if ($json_data === null)
{
    return;
}

$ch = curl_init($webhookurl);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'Content-type: application/json'
));
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
curl_setopt($ch, CURLOPT_HEADER, 0);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

$response = curl_exec($ch);
curl_close($ch);

