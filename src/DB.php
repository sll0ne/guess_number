<?php

namespace nagaevdg\guess_number\DB;

use SQLite3;

use function nagaevdg\guess_number\View\showGamesInfo;
use function nagaevdg\guess_number\View\showTurnInfo;
use function nagaevdg\guess_number\View\showGamesTop;

function createDB()
{
    $dataBase = new \SQLite3('GN.db');

    $gamesInfoTable = "CREATE TABLE gamesInfo(
        idGame INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
        gameData DATE,
        gameTime TIME,
        playerName TEXT,
        maxNumber INTEGER,
		generatedNumber INTEGER,
		gameOutcome TEXT
	 )";
    $dataBase->exec($gamesInfoTable);


    $attemptsTable = "CREATE TABLE attempts(
		 idGame INTEGER,
		 numberAttempts INTEGER,
		 proposedNumber INTEGER,
		 computerResponds TEXT
	 )";
    $dataBase->exec($attemptsTable);
}

function openDatabase()
{
    if (!file_exists("GN.db")) {
        createDB();
    } else {
        $dataBase = new \SQLite3('GN.db');
    }
}

function insertNewGame($userName, $hidden_num, $MAX_NUM)
{
    $dataBase = new \SQLite3('GN.db');

    date_default_timezone_set("Europe/Moscow");

    $gameData = date("d") . "." . date("m") . "." . date("Y");
    $gameTime = date("H") . ":" . date("i") . ":" . date("s");

    $query = "INSERT INTO gamesInfo(
		gameData,
		gameTime,
		playerName,
		maxNumber,
		generatedNumber
   ) VALUES(
		'$gameData',
      '$gameTime',
		'$userName',
		'$MAX_NUM',
		'$hidden_num'
   )";

    $dataBase->exec($query);

    $query = "SELECT idGame FROM gamesInfo ORDER BY idGame DESC LIMIT 1";

    return $dataBase->querySingle($query);
}

function addAttemptInDB($idGame, $proposedNumber, $computerResponds, $numberAttempts)
{
    $dataBase = new \SQLite3('GN.db');

    $query = "INSERT INTO attempts(
	    idGame,
	    numberAttempts,
		proposedNumber,
		computerResponds
    ) VALUES(
        '$idGame',
        '$numberAttempts',
        '$proposedNumber',
        '$computerResponds'
    )";

    $dataBase->exec($query);
}

function updateInfoGame($idGame, $gameOutcome)
{
    $dataBase = new \SQLite3('GN.db');

    $query = "UPDATE gamesInfo SET gameOutcome = '$gameOutcome' WHERE idGame = '$idGame'";

    $dataBase->exec($query);
}

function outputListGame($gameOutcome = false)
{
    $dataBase = new \SQLite3('GN.db');

    if ($gameOutcome === "win") {
        $result = $dataBase->query("SELECT * FROM gamesInfo WHERE gameOutcome = '$gameOutcome'");
    } elseif ($gameOutcome === "loss") {
        $result = $dataBase->query("SELECT * FROM gamesInfo WHERE gameOutcome = '$gameOutcome'");
    } else {
        $result = $dataBase->query("SELECT * FROM gamesInfo");
    }

    while ($row = $result->fetchArray()) {
        showGamesInfo($row);

        $query = "SELECT
            numberAttempts,
            proposedNumber, 
            computerResponds
            FROM attempts 
            WHERE idGame='$row[0]'
            ";

        $gameTurns = $dataBase->query($query);
        while ($gameTurnsRow = $gameTurns->fetchArray()) {
            showTurnInfo($gameTurnsRow);
        }
    }
}

function showTopList()
{
    $dataBase = new \SQLite3('GN.db');

    $result = $dataBase->query("SELECT playerName, 
    (SELECT COUNT(*) FROM gamesInfo as b WHERE a.playerName = b.playerName AND gameOutcome = 'win') as countWin,
    (SELECT COUNT(*) FROM gamesInfo as c WHERE a.playerName = c.playerName AND gameOutcome = 'loss') 
    as countLoss FROM gamesInfo as a
    GROUP BY playerName ORDER BY countWin DESC, countLoss");

    while ($row = $result->fetchArray()) {
        showGamesTop($row);
    }
}

function checkGameId($id)
{
    $dataBase = new \SQLite3('GN.db');

    $query = "SELECT playerName FROM gamesInfo WHERE idGame=" . $id;

    if ($dataBase->querySingle($query)) {
        return $dataBase->querySingle($query);
    }

    return false;
}
