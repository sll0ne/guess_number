<?php

namespace ma_karov\guess_number\Controller;
use function ma_karov\guess_number\View\showGame;

function startGame()
{
   echo "Game started" .PHP_EOL;
   showGame();
}
