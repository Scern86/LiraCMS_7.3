<?php

namespace Scern\Lira\Application;

use Scern\Lira\Application\Result\{Error, InternalRedirect, Json, Redirect, Result, Success};
use Scern\Lira\Config\Config;
use Scern\Lira\Lexicon\Lexicon;
use Scern\Lira\Router;
use Scern\Lira\{State\StateStrategy, View, User};
use Symfony\Component\HttpFoundation\Request;

class Application
{
    const VERSION = '7.3.0';

    public function __construct(
        protected StateStrategy $stateManager,
        protected Config        $config,
        protected Request       $request,
        protected Router        $router,
        protected View          $view,
        protected Lexicon       $lexicon,
        protected User          $user,
        protected Extensions    $extensions
    )
    {
    }

    public function execute(string $requestUri): Result
    {
        $this->view->addLinkToBodysEnd('<script defer src="https://code.jquery.com/jquery-3.7.1.min.js" 
integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>');

        $controllerClass = $this->router->execute($requestUri);

        $controller = new $controllerClass(
            $this->stateManager,
            $this->config,
            $this->request,
            $this->view,
            $this->lexicon,
            $this->user,
            $this->extensions
        );

        $result = $controller->execute($requestUri);

        return match ($result::class) {
            Success::class, Error::class, Json::class, Redirect::class => $result,
            InternalRedirect::class => $this->execute($result->url),
            default => new Error('Application error')
        };
    }
}