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
    protected $afterInsert        = ['auditInsert'];
    protected $afterUpdate        = ['auditUpdate'];
    protected $afterDelete        = ['auditDelete'];
    protected $table              = 'articles';
    protected $tableLang          = 'articles_langs';
    protected $with               = ['articles_langs'];
    protected $without            = [];
    protected $primaryKey         = 'id_article';
    protected $returnType         = Article::class;
    protected $useSoftDeletes     = false;
    protected $allowedFields      = ['id_categorie_default', 'author_created', 'author_created', 'active', 'important', 'picture_one', 'picture_header', 'no_follow_no_index', 'order', 'type'];
    protected $useTimestamps      = true;
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
        $this->article_table     = $this->db->table('articles');
        $this->articles_langs    = $this->db->table('articles_langs');
        $this->article_categorie = $this->db->table('articles_categories');
        $this->categories        = $this->db->table('categories');
        $this->categories_langs  = $this->db->table('categories_langs');
    }

    public function getAllList(int $page, int $perpage, array $sort, array $query)
    {
        $this->article_table->select();
        $this->article_table->select('created_at as date_create_at');
        $this->article_table->join($this->tableLang, $this->table . '.' . $this->primaryKey . ' = ' . $this->tableLang . '.id_article');
        if (isset($query[0]) && is_array($query)) {
            $this->article_table->where('deleted_at IS NULL AND (name LIKE "%' . $query[0] . '%" OR description_short LIKE "%' . $query[0] . '%") AND id_lang = ' . service('settings')->setting_id_lang);
            $this->article_table->limit(0, $page);
        } else {
            $this->article_table->where('deleted_at IS NULL AND id_lang = ' . service('settings')->setting_id_lang);
            $page = ($page == '1') ? '0' : (($page - 1) * $perpage);
            $this->article_table->limit($perpage, $page);
        }

        $this->article_table->orderBy($sort['field'] . ' ' . $sort['sort']);
        $articlesResult = $this->article_table->get()->getResult();

        // In va chercher les categories
        if (!empty($articlesResult)) {
            $i = 0;
            foreach ($articlesResult as $article) {
                $articlesResult[$i]->categories = $this->getCatByArt($article->id_article);
                $i++;
            }
        }

        //echo $this->article_table->getCompiledSelect(); exit;
        return $articlesResult;
    }

    public function getAllCount(array $sort, array $query)
    {
        $this->article_table->select($this->table . '.' . $this->primaryKey);
        $this->article_table->join($this->tableLang, $this->table . '.' . $this->primaryKey . ' = ' . $this->tableLang . '.id_article');
        if (isset($query[0]) && is_array($query)) {
            $this->article_table->where('deleted_at IS NULL AND (name LIKE "%' . $query[0] . '%" OR description_short LIKE "%' . $query[0] . '%") AND id_lang = ' . service('settings')->setting_id_lang);
        } else {
            $this->article_table->where('deleted_at IS NULL AND id_lang = ' . service('settings')->setting_id_lang);
        }

        $this->article_table->orderBy($sort['field'] . ' ' . $sort['sort']);

        $pages = $this->article_table->get();
        //echo $this->article_table->getCompiledSelect(); exit;
        return $pages->getResult();
    }

    public function getCatArt(int $id_article): array
    {
        $this->article_categorie->select();
        $this->article_categorie->where('id_article', $id_article);
        return $this->article_categorie->get()->getResult();
    }

    public function getCatByArt($id_article = null): array
    {
        $this->article_categorie->select();
        //$this->article_categorie->join('categories_langs', 'articles_categories.id_categorie = categories_langs.id_categorie');
        $this->article_categorie->where(['id_article' => $id_article]);
        $article_categorie =  $this->article_categorie->get()->getResult();
        $temp = [];
        if (!empty($article_categorie)) {
            $i = 0;
            foreach ($article_categorie as $art) {
                $temp[$art->id_categorie] = $art;
                $temp[$art->id_categorie]->name = $this->categories_langs->where(['id_categorie' => $art->id_categorie, 'id_lang' => service('settings')->setting_id_lang])->get()->getRow()->name;
                $i++;
            }
        }
        return $temp;
    }

    public function getIdArticleBySlug($slug)
    {
        $this->article_table->select($this->table . '.' . $this->primaryKey . ', active, type');
        $this->article_table->join($this->tableLang, $this->table . '.' . $this->primaryKey . ' = ' . $this->tableLang . '.id_article');
        $this->article_table->where('deleted_at IS NULL AND ' . $this->tableLang . '.slug="' . $slug . '"');
        $article_table = $this->article_table->get()->getRow();
        // echo $this->article_table->getCompiledSelect();
        // exit;
        if (!empty($article_table)) {
            if ($article_table->active == '1')
                return $article_table;
        }
        return false;
    }

    public function getLast(int $id_lang)
    {
        $this->article_table->select();
        $this->article_table->join($this->tableLang, $this->table . '.' . $this->primaryKey . ' = ' . $this->tableLang . '.id_article');
        $this->article_table->where('deleted_at IS NULL AND ' . $this->tableLang . '.id_lang="' . $id_lang . '"');
        $this->article_table->limit(1);
        $this->article_table->orderBy($this->table . '.id_article ASC ');
        $article_table = $this->article_table->get()->getRow();
        return $article_table;
    }


    public function getLink(int $id_article, int $id_lang)
    {
        $this->article_table->select('slug');
        $this->article_table->join($this->tableLang, $this->table . '.' . $this->primaryKey . ' = ' . $this->tableLang . '.id_article');
        $this->article_table->where([$this->table . '.id_article' => $id_article, 'id_lang' => $id_lang]);
        $article_table = $this->article_table->get()->getRow();
        return $article_table;
    }

    public function dupliquer(int $id_article)
    {

        //Article
        $this->article_table->select();
        $this->article_table->where(['id_article' => $id_article]);
        $getArticle = $this->article_table->get()->getRow();

        unset($getArticle->id_article);
        $getArticle->type = 4;
        $Article = new Article((array) $getArticle);
        $this->save($Article);
        $id_articleNew = $this->insertID();

        // On enregistre les langues
        $this->articles_langs->select();
        $this->articles_langs->where(['id_article' => $id_article]);
        $getArticleLangs = $this->articles_langs->get()->getRow();
        $getArticleLangs->id_article = $id_articleNew;
        $this->articles_langs->insert((array) $getArticleLangs);

        // On enregistre les categories par default
        $this->article_categorie->insert(['id_article' => $id_articleNew, 'id_categorie' => $getArticle->id_categorie_default]);


        // exit;
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
    public function lastFive(): array
    {
        $this->article_table->select("*, DATE_FORMAT(`created_at`,'<strong>%d-%m-%Y</strong> &agrave; <strong>%H:%i:%s</strong>') AS `created_at`, DATE_FORMAT(`updated_at`,'<strong>%d-%m-%Y</strong> &agrave; <strong>%H:%i:%s</strong>') AS `updated_at`");
        $this->article_table->limit('5');
        $this->article_table->orderBy('id_article', 'DESC');

        return $this->article_table->get()->getResult();
    }

    /**
     * @return int|void
     */
    public function count_publied(): int
    {
        $this->article_table->select('COUNT(id_article) as id_article');
        $this->article_table->where('published', 1);

        return $this->article_table->get()->getRow()->id_article;
    }

    /**
     * @return int|void
     */
    public function count_attCorrect(): int
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
    public function count_attPublished(): int
    {
        $this->article_table->select('COUNT(id_article) as id_article');
        $this->article_table->where('corriged', 1);
        $this->article_table->where('published', 0);

        return $this->article_table->get()->getRow()->id_article;
    }

    /**
     * @return int|void
     */
    public function count_brouillon(): int
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
    public function Add(string $title, string $link, string $content, string $tags, string $categories, string $pic, int $important): int
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
