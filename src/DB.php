<?php

namespace nagaevdg\guess_number\DB;

use \RedBeanPHP\R as R;

use function nagaevdg\guess_number\View\showGamesInfo;
use function nagaevdg\guess_number\View\showTurnInfo;
use function nagaevdg\guess_number\View\showGamesTop;

function createDB()
{
    R::setup('sqlite:GN.db');

    $gamesTable = "CREATE TABLE gamesInfo(
        idGame INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
        gameData DATE,
        gameTime TIME,
        playerName TEXT,
        maxNumber INTEGER,
		generatedNumber INTEGER,
		gameOutcome TEXT
	 )";
     R::exec($gamesTable);


    $attemptsInfo = "CREATE TABLE attempts(
		 idGame INTEGER,
		 numberAttempts INTEGER,
		 proposedNumber INTEGER,
		 computerResponds TEXT
	 )";
     R::exec($attemptsInfo);
}

function openDatabase()
{
    if (!file_exists("GN.db")) {
        createDB();
    } else {
        R::setup('sqlite:GN.db');
    }
}

function insertNewGame($userName, $hidden_num, $MAX_NUM)
{
    openDatabase();

    date_default_timezone_set("Europe/Moscow");

    $gameData = date("d") . "." . date("m") . "." . date("Y");
    $gameTime = date("H") . ":" . date("i") . ":" . date("s");

    R::exec("INSERT INTO gamesInfo(
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
   )");

    return R::getRow("SELECT idGame FROM gamesInfo ORDER BY idGame DESC LIMIT 1");
}

function addAttemptInDB($idGame, $proposedNumber, $computerResponds, $numberAttempts)
{
    openDatabase();

    R::exec("INSERT INTO attempts(
	    idGame,
	    numberAttempts,
		proposedNumber,
		computerResponds
    ) VALUES(
        '$idGame',
        '$numberAttempts',
        '$proposedNumber',
        '$computerResponds'
    )");
}

function updateInfoGame($idGame, $gameOutcome)
{
    openDatabase();
    R::exec("UPDATE gamesInfo SET gameOutcome = '$gameOutcome' WHERE idGame = '$idGame'");
}

function outputListGame($gameOutcome = false)
{
    openDatabase();

    if ($gameOutcome === "win") {
        $result = R::getAll("SELECT * FROM gamesInfo WHERE gameOutcome = '$gameOutcome'");
    } elseif ($gameOutcome === "loss") {
        $result = R::getAll("SELECT * FROM gamesInfo WHERE gameOutcome = '$gameOutcome'");
    } else {
        $result = R::getAll("SELECT * FROM gamesInfo");
    }

    while ($row = $result->fetchArray()) {
        showGamesInfo($row);

        $gameTurns = R::getAll("SELECT numberAttempts,
            proposedNumber, 
            computerResponds
            FROM attempts 
            WHERE idGame='$row[0]'");
        while ($gameTurnsRow = $gameTurns->fetchArray()) {
            showTurnInfo($gameTurnsRow);
        }
    }
}

function showTopList()
{
    openDatabase();

    $result = R::getAll("SELECT playerName, 
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
    openDatabase();
    $query = R::getCell("SELECT playerName FROM gamesInfo WHERE idGame=" . [$id]);

    if ($query) {
        return $query;
    }

    return false;
}
