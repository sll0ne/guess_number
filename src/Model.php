<?php

namespace nagaevdg\guess_number\Model;

use function cli\prompt;
use function nagaevdg\guess_number\View\saySalute;
use function nagaevdg\guess_number\View\endGame;
use function nagaevdg\guess_number\View\MenuGame;
use function nagaevdg\guess_number\DB\insertNewGame;
use function nagaevdg\guess_number\DB\addAttemptInDB;
use function nagaevdg\guess_number\DB\outputListGame;
use function nagaevdg\guess_number\DB\updateInfoGame;
use function nagaevdg\guess_number\DB\showTopList;
use function nagaevdg\guess_number\DB\checkGameid;

function setting()
{
    define("MAX_NUMBER", 10);
    define("NUMBER_ATTEMPT", 3);
}

function showGame($userName)
{
    $hidden_num = mt_rand(1, MAX_NUMBER);
    echo "Try to guess!" . PHP_EOL;

    $attempt = 1;

    $idNewGame = insertNewGame($userName, $hidden_num, MAX_NUMBER);

    while ($attempt <= NUMBER_ATTEMPT) {
        $get_num = readline();

        while (is_numeric($get_num) === false) {
            echo "Not a number entered! " . PHP_EOL;
            $get_num = readline();
        }

        if ($get_num == $hidden_num) {
            addAttemptInDB($idNewGame, $get_num, "guessed", $attempt);
            updateInfoGame($idNewGame, "win");
            endGame($hidden_num, $attempt);
            break;
        }

        if ($get_num < $hidden_num) {
            echo 'Your number is too small' . PHP_EOL;
            addAttemptInDB($idNewGame, $get_num, "number is small", $attempt);
        } elseif ($get_num > $hidden_num) {
            echo 'Your number is too large' . PHP_EOL;
            addAttemptInDB($idNewGame, $get_num, "number is large", $attempt);
        }

        $attempt++;
    }

    if ($attempt > NUMBER_ATTEMPT) {
        updateInfoGame($idNewGame, "loose");
        endGame($hidden_num);
    }
}

function replayGame($userName)
{
    echo $userName . ', lets try again? (y ="Yes" / n = "No")' . PHP_EOL;
    echo 'Do you want to end game? (--exit - Exit | --menu - Menu)' . PHP_EOL;
    $replay_game = readline();

    if ($replay_game === 'y' || $replay_game === 'Y') {
        showGame($userName);
    } elseif ($replay_game === 'n' || $replay_game === 'N') {
        echo 'TY ' . $userName . '. Goodbye!' . PHP_EOL;
    } elseif ($replay_game === '--exit') {
        exit();
    } elseif ($replay_game === '--menu') {
        MenuGame();
    } else {
        replayGame($userName);
    }
}

function commandHandler($getCommand)
{
    $checkCommand = false;

    while ($checkCommand === false) {
        if ($getCommand === "--new") {
            saySalute();

            $checkCommand = true;
        } elseif ($getCommand === "--list") {
            outputListGame();
        } elseif ($getCommand === "--list winners") {
            outputListGame("win");
        } elseif ($getCommand === "--list looser") {
            outputListGame("loss");
        } elseif ($getCommand === "--top") {
            showTopList();
        } elseif (preg_match('/(^--replay [0-9]+$)/', $getCommand) != 0) {
            $temp = explode(' ', $getCommand);
            $id = $temp[1];

            unset($temp);

            $checkId = checkGameid($id);

            if ($checkId) {
                showGame($checkId);
            } else {
                echo "There is no such game" . PHP_EOL;
            }
        } elseif ($getCommand === "--exit") {
            exit;
        }

        $getCommand = prompt("Enter the key value: ");
    }
}
