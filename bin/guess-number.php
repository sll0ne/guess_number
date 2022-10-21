<?php

    $autoloadGit = __DIR__ . '/../vendor/autoload.php';
    $autoloadPackagist = __DIR__ . '/../../../autoload.php';

if (file_exists($autoloadGit)) {
    require_once($autoloadGit);
} else {
    require_once($autoloadPackagist);
}
    use function nagaevdg\guess_number\Controller\startGame;
    startGame();
