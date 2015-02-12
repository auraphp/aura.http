<?php
namespace Aura\Http;

use Aura\Http\Message\Request;

class Issue28Test extends \PHPUnit_Framework_TestCase
{
    protected $http;

    public function setUp()
    {
        error_reporting(E_ALL);
        ini_set("display_errors", 1);
        $this->http = require dirname(dirname(dirname(__DIR__))) . '/scripts/instance.php';
    }

    public function testFails()
    {
        $username = getenv('github_username');
        $password = getenv('github_password');
        $request = $this->http->newRequest();
        $request->setAuth(Request::AUTH_BASIC);
        $request->setUsername($username);
        $request->setPassword($password);
        $request->setUrl('https://api.github.com/orgs/auraphp/repos?per_page=100');
        $request->setMethod(Request::METHOD_GET);
        $request->headers->set('Accept', 'application/vnd.github.beta+json');
        $request->headers->set('User-Agent', 'Mozilla');
        $stack = $this->http->send($request);
        $repos = json_decode($stack[0]->content);
        $contributors = array();
        $i = 0;
        foreach ($repos as $repo) {
            $i++;
            if ($i > 2) {
                // iterate only a few
                break;
            }
            $repo_url = "https://api.github.com/repos/auraphp/{$repo->name}/contributors";
            // creating new requests each time did solved the warning though
            // $http = require __DIR__ . '/vendor/aura/http/scripts/instance.php';
            // $request = $http->newRequest();
            // $request->setAuth(Request::AUTH_BASIC);
            // $request->setUsername($username);
            // $request->setPassword($password);
            // $request->headers->set('Accept', 'application/vnd.github.beta+json');
            // $request->headers->set('User-Agent', 'Mozilla');
            $request->setUrl($repo_url);
            $request->setMethod(Request::METHOD_GET);
            $stack = $this->http->send($request);
            try {
                $repo_contributors = json_decode($stack[0]->content);
                foreach ($repo_contributors as $contributor) {
                    $contributors[$contributor->login] = $contributor;
                }
            } catch (Exception $e) {
                echo $e->getMessage();
            }
        }

    }
}
