<?php

$autoLoadGit = __DIR__.'/../vendor/autoload.php';
$autoLoadPackgaist = __DIR__.'/../../../autoload.php';
file_exists($autoLoadGit) ? require_once($autoLoadGit) : require_once($autoLoadPackgaist);
use function nagaevdg\guess_number\Controller\startGame;
startGame();

?>
