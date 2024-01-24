<?php

namespace Scern\Lira\Component\Front\About;

use Scern\Lira\Application\Controller;
use Scern\Lira\Application\Result\{Result, Success};
use Scern\Lira\Component\Front\Models\Article;
use Scern\Lira\Extensions\Database\Adapters\Postgresql;
use Scern\Lira\View;

class About extends Controller
{
    public function __construct(...$args)
    {
        parent::__construct(...$args);
        $db = new Postgresql(...$this->config->get('database'));
        $this->model = new Article($db->db);
    }
    public function execute(string $url): Result
    {
        $view = new View($this->lexicon);
        $view->article = $article = $this->model->getArticleById(3,$this->lexicon->currentLang);
        $this->view->meta_title = $article['title'].' | Lira';
        return new Success($view->render(ROOT_DIR . DS . 'component' . DS . 'Front' . DS . 'About' . DS . 'template.inc'));
    }
}