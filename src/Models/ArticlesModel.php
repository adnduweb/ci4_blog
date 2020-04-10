<?php

/*
 * BlogCI4 - Blog write with Codeigniter v4dev
 * @author Deathart <contact@deathart.fr>
 * @copyright Copyright (c) 2018 Deathart
 * @license https://opensource.org/licenses/MIT MIT License
 */

namespace Adnduweb\Ci4_blog\Models;

use CodeIgniter\Model;
use Adnduweb\Ci4_blog\Entities\Article;

/**
 * Class ArticleModel
 *
 * @package App\Models
 */
class ArticlesModel extends Model
{
    /**
     * @var \CodeIgniter\Database\BaseBuilder
     */
    private $article_table;

    use \Tatter\Relations\Traits\ModelTrait;
    use \Adnduweb\Ci4_logs\Traits\AuditsTrait;
    protected $afterInsert = ['auditInsert'];
    protected $afterUpdate = ['auditUpdate'];
    protected $afterDelete = ['auditDelete'];

    protected $table = 'articles';
    protected $tableLang = 'articles_langs';
    protected $with = ['articles_langs'];
    protected $without = [];
    protected $primaryKey = 'id_article';
    protected $returnType = Article::class;
    protected $useSoftDeletes = true;
    protected $allowedFields = ['id_categorie', 'author_created', 'author_created', 'active', 'important', 'picture_one', 'picture_header', 'no_follow_no_index', 'slug', 'order', 'type'];
    protected $useTimestamps = true;
    protected $validationRules = [
        'slug'            => 'required'
    ];
    protected $validationMessages = [];
    protected $skipValidation     = false;

    /**
     * ArticleModel constructor.
     *
     * @param array ...$params
     *
     * @throws \CodeIgniter\Database\Exceptions\DatabaseException
     */
    public function __construct(...$params)
    {
        parent::__construct();
        $this->article_table = $this->db->table('articles');
    }

    public function getAllList(int $page, int $perpage, array $sort, array $query)
    {
        $this->article_table->select();
        $this->article_table->select('created_at as date_create_at');
        $this->article_table->join($this->tableLang, $this->table . '.' . $this->primaryKey . ' = ' . $this->tableLang . '.id_article');
        if (isset($query['generalSearch']) && !empty($query['generalSearch'])) {
            $this->article_table->where('deleted_at IS NULL AND (name LIKE "%' . $query['generalSearch'] . '%" OR login_destination LIKE "%' . $query['generalSearch'] . '%") AND id_lang = ' . service('settings')->setting_id_lang);
            $this->article_table->limit(0, $page);
        } else {
            $this->article_table->where('deleted_at IS NULL AND id_lang = ' . service('settings')->setting_id_lang);
            $page = ($page == '1') ? '0' : (($page - 1) * $perpage);
            $this->article_table->limit($perpage, $page);
        }


        $this->article_table->orderBy($sort['field'] . ' ' . $sort['sort']);

        $groupsRow = $this->article_table->get()->getResult();

        //echo $this->article_table->getCompiledSelect(); exit;
        return $groupsRow;
    }

    public function getAllCount(array $sort, array $query)
    {
        $this->article_table->select($this->table . '.' . $this->primaryKey);
        $this->article_table->join($this->tableLang, $this->table . '.' . $this->primaryKey . ' = ' . $this->tableLang . '.id_article');
        if (isset($query['generalSearch']) && !empty($query['generalSearch'])) {
            $this->article_table->where('deleted_at IS NULL AND (name LIKE "%' . $query['generalSearch'] . '%" OR login_destination LIKE "%' . $query['generalSearch'] . '%") AND id_lang = ' . service('settings')->setting_id_lang);
        } else {
            $this->article_table->where('deleted_at IS NULL AND id_lang = ' . service('settings')->setting_id_lang);
        }

        $this->article_table->orderBy($sort['field'] . ' ' . $sort['sort']);

        $pages = $this->article_table->get();
        //echo $this->article_table->getCompiledSelect(); exit;
        return $pages->getResult();
    }

    /**
     * @param string $column
     * @param string $data
     *
     * @return mixed
     */
    public function GetArticle(string $column, string $data)
    {
        $this->article_table->select("*, DATE_FORMAT(`created_at`,'Le %d-%m-%Y &agrave; %H:%i:%s') AS `created_at`, DATE_FORMAT(`updated_at`,'Le %d-%m-%Y &agrave; %H:%i:%s') AS `updated_at`");
        $this->article_table->where($column, $data);

        return $this->article_table->get()->getRow();
    }

    /**
     * @return array|mixed
     */
    public function lastFive():array
    {
        $this->article_table->select("*, DATE_FORMAT(`created_at`,'<strong>%d-%m-%Y</strong> &agrave; <strong>%H:%i:%s</strong>') AS `created_at`, DATE_FORMAT(`updated_at`,'<strong>%d-%m-%Y</strong> &agrave; <strong>%H:%i:%s</strong>') AS `updated_at`");
        $this->article_table->limit('5');
        $this->article_table->orderBy('id_article', 'DESC');

        return $this->article_table->get()->getResult();
    }

    /**
     * @return int|void
     */
    public function count_publied():int
    {
        $this->article_table->select('COUNT(id_article) as id_article');
        $this->article_table->where('published', 1);

        return $this->article_table->get()->getRow()->id_article;
    }

    /**
     * @return int|void
     */
    public function count_attCorrect():int
    {
        $this->article_table->select('COUNT(id_article) as id_article');
        $this->article_table->where('corriged', 0);
        $this->article_table->where('published', 0);
        $this->article_table->where('brouillon', 0);

        return $this->article_table->get()->getRow()->id_article;
    }

    /**
     * @return int|void
     */
    public function count_attPublished():int
    {
        $this->article_table->select('COUNT(id_article) as id_article');
        $this->article_table->where('corriged', 1);
        $this->article_table->where('published', 0);

        return $this->article_table->get()->getRow()->id_article;
    }

    /**
     * @return int|void
     */
    public function count_brouillon():int
    {
        $this->article_table->select('COUNT(id_article) as id_article');
        $this->article_table->where('brouillon', 1);

        return $this->article_table->get()->getRow()->id_article;
    }

    /**
     * @param string $title
     * @param string $link
     * @param string $content
     * @param string $tags
     * @param string $categories
     * @param string $pic
     * @param int $important
     *
     * @return int (Return id)
     */
    public function Add(string $title, string $link, string $content, string $tags, string $categories, string $pic, int $important):int
    {
        $data = [
            'title'          => $title,
            'content'        => $content,
            'author_created' => 1,
            'important'      => $important,
            'link'           => $link,
            'picture_one'    => $pic,
            'categories'     => $categories,
            'tags'           => $tags
        ];
        $this->article_table->insert($data);

        return $this->db->insertID();
    }

    /**
     * @param int $id
     * @param string $title
     * @param string $link
     * @param string $content
     * @param string $tags
     * @param string $categories
     * @param string $pic
     * @param int $important
     * @param int $type
     *
     * @return bool
     */
    public function Edit(int $id, string $title, string $link, string $content, string $tags, string $categories, string $pic, int $important, int $type): bool
    {
        $data = [
            'title'          => $title,
            'content'        => $content,
            'author_created' => 1,
            'author_update'  => 1,
            'important'      => $important,
            'link'           => $link,
            'picture_one'    => $pic,
            'categories'     => $categories,
            'tags'           => $tagsy
        ];

        if ($type == 1) {
            $data['published'] = 1;
            $data['corriged'] = 1;
        } elseif ($type == 2) {
            $data['published'] = 0;
            $data['corriged'] = 0;
            $data['brouillon'] = 0;
        } elseif ($type == 3) {
            $data['published'] = 0;
            $data['corriged'] = 1;
            $data['brouillon'] = 0;
        }

        $this->article_table->where('id_article', $id);
        $this->article_table->set('updated_at', 'NOW()', false);
        $this->article_table->update($data);

        return true;
    }

    /**
     * @param int $type (1 = publied, 2 = wait corrected, 3 = wait publied, 4 = brouillon)
     *
     * @return mixed
     */
    public function getArticleListAdmin(int $type)
    {
        $this->article_table->select("*, DATE_FORMAT(`created_at`,'<strong>%d-%m-%Y</strong> &agrave; <strong>%H:%i:%s</strong>') AS `created_at`, DATE_FORMAT(`updated_at`,'<strong>%d-%m-%Y</strong> &agrave; <strong>%H:%i:%s</strong>') AS `updated_at`");

        if ($type == 1) {
            $this->article_table->where('published', 1);
        } elseif ($type == 2) {
            $this->article_table->where('corriged', 0);
            $this->article_table->where('published', 0);
            $this->article_table->where('brouillon', 0);
        } elseif ($type == 3) {
            $this->article_table->where('corriged', 1);
            $this->article_table->where('published', 0);
        } elseif ($type == 4) {
            $this->article_table->where('brouillon', 1);
        }

        return $this->article_table->get()->getResult();
    }
}
