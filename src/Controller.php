<?php

namespace nagaevdg\guess_number\Controller;

use function nagaevdg\guess_number\Model\setting;
use function nagaevdg\guess_number\View\MenuGame;
use function nagaevdg\guess_number\DB\openDatabase;

function startGame()
{
    setting();
    openDatabase();
    MenuGame();
}
