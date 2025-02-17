<?php

use Deljdlx\Github\Cache;
use Deljdlx\Github\GithubClient;
use Deljdlx\Github\Readme;

require_once __DIR__ . '/tools/php/vendor/autoload.php';



$options = getopt('', ['token:']);
$token = $options['token'];


$readmePath = __DIR__ . '/../../README.md';
$buffer = file_get_contents($readmePath);
$readme = new Readme($buffer);


$cachePath = __DIR__ . '/cache';
$cacheDriver = new Cache(__DIR__ . '/cache');
if(!is_dir($cachePath)) {
    mkdir($cachePath, 0774, true);
}

$client = new GithubClient($token, $cacheDriver);
$hasMore = false;


$repositoryPath = __DIR__ . '/deljdlx';
if(is_dir($repositoryPath)) {
    exec('rm -rf ' . $repositoryPath);
}

$manager = $client->clone('deljdlx/deljdlx', $repositoryPath);

$readmePath = $repositoryPath . '/README.md';
$readme = new Readme(file_get_contents($readmePath));
$readme->appendToPart('DEMOS', 'hello world');
file_put_contents($readmePath, $readme->compile());
$manager->add($readmePath);
$manager->commit('Update README.md - test');
$manager->push();

echo __FILE__.':'.__LINE__; exit();


$skips = [
    'phi-',
    'plank',
    'oneshot',
    'deljdlx',
];

$demoBuffer= '';
do {
    $ownRepositories = $client->getOwnRepositories(
        'deljdlx',
        function($repositoryData) use ($client) {
            return new Deljdlx\Repository($client, $repositoryData);
        }
    );
    $nextPage = $client->getNextPageUrl();
    foreach($ownRepositories as $repository) {
        if(
            $repository->isArchived()
            || $repository->isPrivate()
        ) {
            continue;
        }
        foreach($skips as $skip) {
            if(strpos($repository->getName(), $skip) === 0) {
                continue 2;
            }
        }
        $demoUrl = $repository->getDemoUrl();


        if($demoUrl) {

            $foreignRawReadme = $repository->getReadme();

            $description = '';
            if($foreignRawReadme) {
                $foreingReadme = new Readme($foreignRawReadme);
                $description = $foreingReadme->getPart('SHORT-PRESENTATION');
            }

            $title = ($foreingReadme->getTitle() !== false) ? $foreingReadme->getTitle() : $repository->getName();
            $demoBuffer = '### [' . $title . '](' . $repository->getUrl() . ')' . PHP_EOL;
            $demoBuffer .= $description . PHP_EOL;
            $demoBuffer .= '👓 Demo: [' . $demoUrl . '](' . $demoUrl . ')' . PHP_EOL;
            $readme->appendToPart('DEMOS', $demoBuffer);
        }


    }
} while($nextPage);


$readme->appendToPart('DEMOS', $demoBuffer);
file_put_contents($readmePath, $readme->compile());

echo $readme->compile();
