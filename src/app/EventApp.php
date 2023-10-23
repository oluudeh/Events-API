<?php

namespace App;

use App\Validators\PresentAndFutureDateValidator;
use DI\Container;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Exceptions\ValidationException;
use App\Validators\InputValidator;
use App\Validators\Rule;

class EventApp
{
    public function __construct(
        private array $routes,
        private LoggerInterface $logger,
        private Container $container
    ) {
        InputValidator::registerRuleHandler(Rule::PresentAndFutureDate, new PresentAndFutureDateValidator);
    }

    public function handle(Request $request): Response
    {
        $path = $request->getPathInfo();

        if (isset($this->routes[$path])) {

            $method = $request->getMethod();

            if (isset($this->routes[$path][$method])) {
                try {
                    $controller = $this->routes[$path][$method];

                    $response = $this->container->call($controller, [$request]);

                    $this->logger->info($request->getMethod() . "[200]: " . $request->getRequestUri());
                    return $response;
                } catch (ValidationException $e) {
                    $content = [
                        'message' => $e->getMessage(),
                        'errors' => $e->getErrors(),
                    ];
                    $this->logger->warning(
                        $request->getMethod() . "[{$e->getCode()}]: " . $request->getRequestUri(),
                        $content
                    );
                    return new Response(json_encode($content), $e->getCode(), [
                        'Content-Type' => 'application/json',
                    ]);
                } catch (\Exception $e) {
                    $content = [
                        'message' => $e->getMessage()
                    ];
                    $this->logger->info($request->getMethod() . "[500]: " . $request->getRequestUri(), $content);
                    return new Response(json_encode($content), 500, [
                        'Content-Type' => 'application/json'
                    ]);
                }
            } else {
                $content = [
                    'message' => 'Method Not Allowed'
                ];
                $this->logger->info($request->getMethod() . "[405]: " . $request->getRequestUri(), $content);
                return new Response(json_encode($content), 405, [
                    'Content-Type' => 'application/json',
                ]);
            }
        } else {
            $content = [
                'message' => 'Not Found'
            ];
            $this->logger->info($request->getMethod() . "[404]: " . $request->getRequestUri(), $content);
            return new Response(json_encode($content), 404, [
                'Content-Type' => 'application/json'
            ]);
        }
    }
}
