<?php

namespace nagaevdg\guess_number\Controller;
use function nagaevdg\guess_number\View\showGame;

function startGame()
{
   echo "Game started" .PHP_EOL;
   showGame();
}
