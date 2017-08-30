<?php

use JPuminate\Architecture\EventBus\Events\Resolvers\GithubEventResolver;

require_once 'vendor/autoload.php';

/*
$client = new \Github\Client();
//$content = $client->api('repo')->contents()->readme('KnpLabs', 'php-github-api', 'master')['content'];
$contents = $client->api('repo')->contents()->show('JPuminate', 'contracts', "EventBus/Events", "master");
foreach ($contents as $content){
    echo $content['name'];
}
*/

$resolver = new GithubEventResolver('JPuminate', 'contracts', 'master', 'EventBus/Events');

dd($resolver->resolve('event'));

