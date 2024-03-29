<?php

namespace Scern\Lira\Component\Front;

use Scern\Lira\Application\Controller;
use Scern\Lira\Application\Results\{Error, InternalRedirect, Json, Redirect, Success,};
use Scern\Lira\Component\DefaultController;
use Scern\Lira\Component\Front\Page\PageData;
use Scern\Lira\Config\PhpFile;
use Scern\Lira\Database\Adapters\Postgresql;
use Scern\Lira\Results\Result;
use Scern\Lira\Router;
use Symfony\Component\HttpFoundation\Response;

class Front extends Controller
{
    const COMPONENT_DIR = ROOT_DIR . DS . 'component' . DS . 'Front';
    public function __construct($request,$session,$config,$view,$user,$lexicon,$database,$cache,$logger)
    {
        $view = new View($lexicon,new PageData());
        parent::__construct($request,$session,$config,$view,$user,$lexicon,$database,$cache,$logger);
        $this->database->set('database',new Postgresql(new PhpFile(CONFIG_DIR.DS.'database.php')));
        $this->view->addLinkToHeader('<link rel="stylesheet" href="/assets/css/style.min.css?'.time().'">');
        $this->view->addLinkToBodysEnd('<script defer src="https://code.jquery.com/jquery-3.7.1.min.js" 
integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>');
        $this->view->addLinkToBodysEnd('<script type="module" src="/assets/js/script.min.js?'.time().'"></script>');
        $this->lexicon->addArray([
            'ru'=>[
                'profile'=>'Страница авторизации',
            ],
            'en'=>[
                'profile'=>'Auth page',
            ],
        ]);
    }

    public function execute(string $uri): Result
    {
        try {
            $router = new Router(
                DefaultController::class,
                new PhpFile(self::COMPONENT_DIR . DS . 'routes.php')
            );

            $controllerClass = $router->execute($uri);

            $controller = new $controllerClass(
                $this->request,
                $this->session,
                $this->config,
                $this->view,
                $this->user,
                $this->lexicon,
                $this->database,
                $this->cache,
                $this->logger
            );

            $result = $controller->execute($uri);

            switch ($result::class) {
                case Success::class:
                case Error::class:
                    $this->view->content = $result->content;
                    return new Success($this->view->render(self::COMPONENT_DIR . DS . 'templates' . DS . 'template.inc'),$result->statusCode,$result->headers);
                case Json::class:
                case Redirect::class:
                case InternalRedirect::class:
                    return $result;
                default:
                    return new Error('Controller error');
            }
        } catch (\Exception $e) {
            return new Error($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

}
