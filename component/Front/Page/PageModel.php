<?php

namespace Scern\Lira\Component\Front\Page;

use Scern\Lira\Database\Database;
use Scern\Lira\Lexicon\Lang;
use Scern\Lira\Model;

class PageModel extends Model
{
    protected string $table = 'web_pages';
    protected string $tableContent = 'web_pages_content';

    public function __construct(Database $database)
    {
        parent::__construct($database);
    }
    public function getPageById(int $id,Lang $lang): PageData
    {
        try {
            $query = $this->db->prepare("SELECT p.*,pc.language,pc.meta_title,pc.meta_description,pc.robots_index,pc.robots_follow 
FROM {$this->table} AS p,{$this->tableContent} AS pc 
         WHERE p.id=pc.id_page AND p.id = :id AND pc.language = :language");
            $query->execute(
                [
                    'id' => $id,
                    'language' => $lang->code
                ]
            );
            $result = $query->fetch(\PDO::FETCH_ASSOC);
            if(empty($result)) throw new \Exception('Page not found');
            return new PageData(...$result);
        } catch (\Throwable $e) {
            return new PageData();
        }
    }
}