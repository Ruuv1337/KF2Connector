<?php
    //error_reporting(1);
    //ini_set('display_errors', 1);
    //ini_set("error_reporting", E_ALL);

    $mysql_host = "localhost";
    $mysql_port = 3306;
    $mysql_user = "";
    $mysql_pass = "";
    $mysql_db   = "";

    $conn = getConnection();


//Get Maps
    $maplist = [];

    $sql ="SELECT mapname FROM kf2_trackstats";
    $data = $conn->query($sql);

    while ($row = $data->fetch_assoc()) {
        if (!in_array($row['mapname'], $maplist)) {
            array_push($maplist, $row['mapname']);
        }
    }

    $data->free();
//

    function getStatsData(string $map) {

        global $conn;
        $result = [];

        $sql = "SELECT * FROM kf2_trackstats WHERE mapname = '".$map."' ORDER BY dmgdealt DESC;";

        $data = $conn->query($sql);

        while ($row = $data->fetch_array(MYSQLI_NUM)) {
            array_push($result, $row);
        }

        return $result;
    }


    function getUserData($ID) {

        global $conn;

        $sql = "SELECT name, steamid FROM kf2_players WHERE id = '".$ID."';";
        $data = $conn->query($sql);
        $data = $data->fetch_assoc();

        return [$data['name'], $data['steamid']];
    }

    function getConnection()
    {
        global $mysql_host, $mysql_user, $mysql_pass, $mysql_db, $mysql_port;

        $conn = new mysqli($mysql_host, $mysql_user, $mysql_pass, $mysql_db, $mysql_port);
        // Check connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
        return $conn;
    }

?>

<html lang="en">

<head>
    <title>Killing Floor 2 - Records Table</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.0/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.0/js/bootstrap.min.js"></script>

<style>
.sublinks{
    max-height: 300px;
    overflow-y: scroll;
}
</style>
</head>

<body>
    <div class="container-fluid shadow-lg p-3 mb-5 bg-white rounded">
        <center>
            <h2>KF2 Records Table</h2>
        </center>
        <hr />
        <div class="table-responsive">
            <div id="accordion">
                <?for($m=0; $m < count($maplist); $m++) :?>

                <a class="card-link" data-toggle="collapse" href="#collapse<?=$m?>">
                    <table class="table table-dark table-bordered">
                        <tr>
                            <th>Map: <?=mb_strtoupper($maplist[$m])?></th>
                        </tr>
                    </table>
                </a>
                <div id="collapse<?=$m?>" class="collapse hide" data-parent="#accordion">
                    <table class="table table-sm table-hover">
                        <thead class="thead-light">
                            <tr>
                                <th>Player</th>
                                <th>Headshoots</th>
                                <th>HS-Accuracy</th>
                                <th>Accuracy</th>
                                <th>DmgDealt</th>
                                <th>DmgTaken</th>
                                <th>Kills</th>
                                <th>Assists</th>
                                <th>Deaths</th>
                                <th>DoshEarn</th>
                                <th>Platform</th>
                                <th>StatsTime</th>
                            </tr>
                        </thead>
                        <?$statData = getStatsData($maplist[$m])?>
                        <?for($w=0; $w < count($statData); $w++) :?>
                        <?$pData = getUserData($statData[$w][1])?>
                        <tbody>
                            <tr>
                                <th><?=$pData[0]?></th>
                                <th><?=$statData[$w][2]?></th>
                                <th><?=round($statData[$w][9], 2)?></th>
                                <th><?=round($statData[$w][10], 2)?></th>
                                <th><?=$statData[$w][7]?></th>
                                <th><?=$statData[$w][8]?></th>
                                <th><?=$statData[$w][3]?></th>
                                <th><?=$statData[$w][4]?></th>
                                <th><?=$statData[$w][5]?></th>
                                <th><?=$statData[$w][6]?></th>
                                <th><?=(mb_strlen($pData[1],'UTF-8') == 17) ? "<a href='https://steamcommunity.com/profiles/".$pData[1]."' target='_blank'>STEAM</a>" : "EPIC GAMES"?></th>
                                <th><?=$statData[$w][12]?></th>
                            </tr>
                        </tbody>
                        <?endfor;?>
                    </table>
                </div>
                <?endfor;?>
            </div>
        </div>

    </div>
    </div>
</body>

</html>

<?if ($conn) $conn->close()?>