<?php
use Aura\Http\Message\Request;
$username = 'username';
$password = 'password';
$http = require dirname(__DIR__) . '/scripts/instance.php';
$request = $http->newRequest();
$request->setAuth(Request::AUTH_BASIC);
$request->setUsername($username);
$request->setPassword($password);
$request->setUrl('https://api.github.com/orgs/auraphp/repos?per_page=100');
$request->setMethod(Request::METHOD_GET);
$request->headers->set('Accept', 'application/vnd.github.beta+json');
$request->headers->set('User-Agent', 'Mozilla');
$stack = $http->send($request);
$repos = json_decode($stack[0]->content);
$contributors = array();
foreach ($repos as $repo) {
    $repo_url = "https://api.github.com/repos/auraphp/{$repo->name}/contributors";
    // $http = require __DIR__ . '/vendor/aura/http/scripts/instance.php';
    // $request = $http->newRequest();
    // $request->setAuth(Request::AUTH_BASIC);
    // $request->setUsername($username);
    // $request->setPassword($password);
    // $request->headers->set('Accept', 'application/vnd.github.beta+json');
    // $request->headers->set('User-Agent', 'Mozilla');
    $request->setUrl($repo_url);
    $request->setMethod(Request::METHOD_GET);
    $stack = $http->send($request);
    try {
        $repo_contributors = json_decode($stack[0]->content);
        foreach ($repo_contributors as $contributor) {
            $contributors[$contributor->login] = $contributor;
        }
    } catch (Exception $e) {
        echo $e->getMessage();
    }
}
file_put_contents('contribute.json', json_encode($contributors));
