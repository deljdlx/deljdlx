<?php

use Deljdlx\Cache;
use Deljdlx\GithubClient;
use Deljdlx\Readme;

require_once __DIR__ . '/vendor/autoload.php';



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

$skips = [
    'phi-',
    'plank',
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
            // echo $repository->getFullName() . PHP_EOL;
            // echo 'Demo: ' . $demoUrl . PHP_EOL;
            // echo "---------------------------------------------" . PHP_EOL;

            $demoBuffer = '### [' . $repository->getName() . '](' . $repository->getUrl() . ')' . PHP_EOL;
            $demoBuffer .= 'Demo: [' . $demoUrl . '](' . $demoUrl . ')' . PHP_EOL;
            $readme->appendToPart('DEMOS', $demoBuffer);


        }


    }
} while($nextPage);


$readme->appendToPart('DEMOS', $demoBuffer);
file_put_contents($readmePath, $readme->compile());

echo $readme->compile();





// if($client->hasNextPage()) {
//     $nextPage = $client->getNextPage();
//     $ownRepositories = array_merge($ownRepositories, $nextPage);
// }

// echo json_encode($ownRepositories, JSON_PRETTY_PRINT, JSON_UNESCAPED_UNICODE);


