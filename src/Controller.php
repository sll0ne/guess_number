<?php

namespace nagaevdg\guess_number\Controller;

use function nagaevdg\guess_number\View\startGame;
use function nagaevdg\guess_number\View\showList;
use function nagaevdg\guess_number\View\showReplays;
use function nagaevdg\guess_number\View\showTopRating;

function menu()
{
    $menu = readline("Enter the key value: ");
    switch ($menu) {
        case "--new" || "-n":
            startGame();
            break;
        case "--list" || "-l":
            showList();
            break;
        case "--top" || "-t":
            showTopRating();
            break;
        case "--replay" || "-r":
            showReplays();
            break;
        default:
            echo "Invalid value! Try again!";
            menu();
    }
}

function setting()
{
    define("MAX_NUMBER", 10);
    define("NUMBER_ATTEMPT", 3);
}

function endGame($hidden_num, $attempt = false)
{
    if ($attempt) {
        echo "Congratulations! You won the game with " . $attempt . "attempts!" . PHP_EOL;
        replay();
    } else {
        echo "You lose :-( I thought of a number: " . $hidden_num . PHP_EOL;
        replay();
    }
}

function replay()
{
    global $userName;
    echo $userName . ", you want try again? (Y = 'Yeah' / n = 'No')" . PHP_EOL;
    $replay = readline();
    if ($replay == "Y" || $replay == "y") {
        showGame();
    } elseif ($replay == "N" || $replay == "n") {
        echo "Okay, I'll wait for you, " . $userName . PHP_EOL;
    } else {
        replay();
    }
}

function showGame()
{
    $hidden_num = rand(1, MAX_NUMBER);
    echo "Try again!" . PHP_EOL;

    $attempt = 1;
    while ($attempt <= NUMBER_ATTEMPT) {
        $get_num = readline();

        if ($get_num == $hidden_num) {
            endGame($hidden_num, $attempt);
            break;
        } elseif ($get_num < $hidden_num) {
            echo "The target number is greater" . PHP_EOL;
        } elseif ($get_num > $hidden_num) {
            echo "The target number is less" . PHP_EOL;
        }

        $attempt++;
    }

    if ($attempt > NUMBER_ATTEMPT) {
        endGame($hidden_num);
    }
}

function saySalute()
{
    global $userName;
    echo "Enter your name, please: " . PHP_EOL;
    $userName = readline();
    echo "Hello, " . $userName . "!" . PHP_EOL . "Lets get started play the game"
    . "I guess a number from 1 to " . MAX_NUMBER . "and you have to guess the number for " . NUMBER_ATTEMPT .
    " attempts" . PHP_EOL;
}
