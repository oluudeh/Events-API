<?php

use App\EventApp;
use DI\Container;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class EventAppTest extends TestCase
{

    public function test404(): void
    {
        $app = $this->runAppForResponse();
        $response = $app->handle(Request::create('/not-found', 'GET'));
        $this->assertEquals(404, $response->getStatusCode());
    }

    public function test405(): void
    {
        $app = $this->runAppForResponse();
        $request = Request::create('/', 'POST');
        $response = $app->handle($request);
        $this->assertEquals(405, $response->getStatusCode());
    }


    public function test200(): void
    {
        $app = $this->runAppForResponse();
        $request = Request::create('/', 'GET');
        $response = $app->handle($request);
        $this->assertEquals(200, $response->getStatusCode());
    }

    private function runAppForResponse(): EventApp
    {
        return new EventApp(
            routes: [
                '/' => [
                    'GET' => function () {
                        return new Response('All good', 200);
                    }
                ]
            ],
            logger: $this->createMock(LoggerInterface::class),
            container: new Container([]),
        );
    }
}
