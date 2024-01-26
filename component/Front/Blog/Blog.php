<?php

namespace Scern\Lira\Component\Front\Blog;

use Scern\Lira\Application\Controller;
use Scern\Lira\Application\Result\{Result, Success};
use Scern\Lira\Component\Front\Models\Article;
use Scern\Lira\Extensions\Database\Adapters\Postgresql;
use Scern\Lira\View;

class Blog extends Controller
{
    public function __construct(...$args)
    {
        parent::__construct(...$args);
        //$db = new Postgresql(...$this->config->get('database'));
        //$this->model = new Article($db->db);
    }
    public function execute(string $url): Result
    {
        $this->view->seo->title = 'BLOG | SCERN';
        $view = new View($this->lexicon);
        //$view->article = $this->model->getArticleById(1,$this->lexicon->currentLang);
        return new Success($view->render(ROOT_DIR . DS . 'component' . DS . 'Front' . DS . 'Blog' . DS . 'template.inc'));
    }
}