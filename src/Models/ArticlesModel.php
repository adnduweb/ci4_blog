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
use Faker\Generator;

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
    private $b_article_table;

    use \Tatter\Relations\Traits\ModelTrait;
    use \Adnduweb\Ci4_logs\Traits\AuditsTrait;
    protected $afterInsert        = ['auditInsert'];
    protected $afterUpdate        = ['auditUpdate'];
    protected $afterDelete        = ['auditDelete'];
    protected $table              = 'b_article';
    protected $tableLang          = 'b_article_lang';
    protected $with               = ['b_article_lang'];
    protected $without            = [];
    protected $primaryKey         = 'id_article';
    protected $returnType         = Article::class;
    protected $useSoftDeletes     = false;
    protected $allowedFields      = ['id_category_default', 'author_created', 'author_created', 'active', 'important', 'picture_one', 'picture_header', 'no_follow_no_index', 'order', 'type'];
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
        $this->b_article_table          = $this->db->table('b_article');
        $this->b_article_table_lang     = $this->db->table('b_article_lang');
        $this->b_article_table_category = $this->db->table('b_article_category');
        $this->b_category_table         = $this->db->table('b_category');
        $this->b_category_table_lang    = $this->db->table('b_category_lang');
    }

    public function fake(Generator &$faker)
    {
        return [
            'id_category_default' => 1,
            'author_created' => 1,
            'author_created' => 1,
            'active' => 1,
            'important' => 1,
            'picture_one' => null,
            'picture_header' => null,
            'no_follow_no_index' => 0,
            'order' => 1,
            'type' => 1,
            'created_at' => date('Y-m-d H:i:s'),
        ];
    }

    public function fakelang(int $id_article)
    {
        $faker = \Faker\Factory::create();
        // print_r($faker);
        // exit;
        $data = [
            'id_article'      => $id_article,
            'id_lang'           => 1,
            'name'              => $faker->word(1),
            'sous_name'         => $faker->word(3),
            'description_short' => $faker->paragraph(1),
            'description'       => $faker->text,
            'meta_title'        => $faker->word(10),
            'meta_description'  => $faker->word(10),
            'tags'              => $faker->word(1),
            'slug'              => uniforme(trim($faker->word(2))),
        ];
        // Create the new participant
        $this->b_article_table_lang->insert($data);

        $dataCat = [
            'id_article'   =>  $id_article,
            'id_category' => 1,
            'created_at' => date('Y-m-d H:i:s'),
        ];
        $this->b_article_table_category->insert($dataCat);
    }

    public function getAllList(int $page, int $perpage, array $sort, array $query)
    {
        $this->b_article_table->select();
        $this->b_article_table->select('created_at as date_create_at');
        $this->b_article_table->join($this->tableLang, $this->table . '.' . $this->primaryKey . ' = ' . $this->tableLang . '.id_article');
        if (isset($query[0]) && is_array($query)) {
            $this->b_article_table->where('deleted_at IS NULL AND (name LIKE "%' . $query[0] . '%" OR description_short LIKE "%' . $query[0] . '%") AND id_lang = ' . service('settings')->setting_id_lang);
            $this->b_article_table->limit(0, $page);
        } else {
            $this->b_article_table->where('deleted_at IS NULL AND id_lang = ' . service('settings')->setting_id_lang);
            $page = ($page == '1') ? '0' : (($page - 1) * $perpage);
            $this->b_article_table->limit($perpage, $page);
        }

        $this->b_article_table->orderBy($sort['field'] . ' ' . $sort['sort']);
        $b_articleResult = $this->b_article_table->get()->getResult();

        // In va chercher les b_category_table
        if (!empty($b_articleResult)) {
            $i = 0;
            foreach ($b_articleResult as $article) {
                $b_articleResult[$i]->b_category_table = $this->getCatByArt($article->id_article);
                $i++;
            }
        }

        //echo $this->b_article_table->getCompiledSelect(); exit;
        return $b_articleResult;
    }

    public function getAllCount(array $sort, array $query)
    {
        $this->b_article_table->select($this->table . '.' . $this->primaryKey);
        $this->b_article_table->join($this->tableLang, $this->table . '.' . $this->primaryKey . ' = ' . $this->tableLang . '.id_article');
        if (isset($query[0]) && is_array($query)) {
            $this->b_article_table->where('deleted_at IS NULL AND (name LIKE "%' . $query[0] . '%" OR description_short LIKE "%' . $query[0] . '%") AND id_lang = ' . service('settings')->setting_id_lang);
        } else {
            $this->b_article_table->where('deleted_at IS NULL AND id_lang = ' . service('settings')->setting_id_lang);
        }

        $this->b_article_table->orderBy($sort['field'] . ' ' . $sort['sort']);

        $pages = $this->b_article_table->get();
        //echo $this->b_article_table->getCompiledSelect(); exit;
        return $pages->getResult();
    }

    public function getCatArt(int $id_article): array
    {
        $this->b_article_table_category->select();
        $this->b_article_table_category->where('id_article', $id_article);
        return $this->b_article_table_category->get()->getResult();
    }

    public function getCatByArt($id_article = null): array
    {
        $this->b_article_table_category->select();
        //$this->b_article_table_category->join('b_category_table_lang', 'b_article_table_category.id_category = b_category_table_lang.id_category');
        $this->b_article_table_category->where(['id_article' => $id_article]);
        $b_article_table_category =  $this->b_article_table_category->get()->getResult();
        $temp = [];
        if (!empty($b_article_table_category)) {
            $i = 0;
            foreach ($b_article_table_category as $art) {
                $temp[$art->id_category] = $art;
                $temp[$art->id_category]->name = $this->b_category_table_lang->where(['id_category' => $art->id_category, 'id_lang' => service('settings')->setting_id_lang])->get()->getRow()->name;
                $i++;
            }
        }
        return $temp;
    }

    public function getIdArticleBySlug($slug)
    {
        $this->b_article_table->select($this->table . '.' . $this->primaryKey . ', active, type');
        $this->b_article_table->join($this->tableLang, $this->table . '.' . $this->primaryKey . ' = ' . $this->tableLang . '.id_article');
        $this->b_article_table->where('deleted_at IS NULL AND ' . $this->tableLang . '.slug="' . $slug . '"');
        $b_article_table = $this->b_article_table->get()->getRow();
        // echo $this->b_article_table->getCompiledSelect();
        // exit;
        if (!empty($b_article_table)) {
            if ($b_article_table->active == '1')
                return $b_article_table;
        }
        return false;
    }

    public function getLast(int $id_lang)
    {
        $this->b_article_table->select();
        $this->b_article_table->join($this->tableLang, $this->table . '.' . $this->primaryKey . ' = ' . $this->tableLang . '.id_article');
        $this->b_article_table->where('deleted_at IS NULL AND ' . $this->tableLang . '.id_lang="' . $id_lang . '"');
        $this->b_article_table->limit(1);
        $this->b_article_table->orderBy($this->table . '.id_article ASC ');
        $b_article_table = $this->b_article_table->get()->getRow();
        return $b_article_table;
    }


    public function getLink(int $id_article, int $id_lang)
    {
        $this->b_article_table->select('slug');
        $this->b_article_table->join($this->tableLang, $this->table . '.' . $this->primaryKey . ' = ' . $this->tableLang . '.id_article');
        $this->b_article_table->where([$this->table . '.id_article' => $id_article, 'id_lang' => $id_lang]);
        $b_article_table = $this->b_article_table->get()->getRow();
        return $b_article_table;
    }

    public function getArticlesByIdCategory(int $id_category, int $id_lang)
    {
        $this->b_article_table->select();
        $this->b_article_table->join($this->tableLang, $this->table . '.' . $this->primaryKey . ' = ' . $this->tableLang . '.id_article');
        $this->b_article_table->where([$this->table . '.id_category_default' => $id_category, 'id_lang' => $id_lang, 'type' => 1]);
        $b_article_table = $this->b_article_table->get()->getResult();
        if (!empty($b_article_table)) {
            foreach ($b_article_table as &$article) {
                $article = new Article((array) $article);
            }
        }
        return $b_article_table;
    }

    public function dupliquer(int $id_article)
    {

        //Article
        $this->b_article_table->select();
        $this->b_article_table->where(['id_article' => $id_article]);
        $getArticle = $this->b_article_table->get()->getRow();

        unset($getArticle->id_article);
        $getArticle->type = 4;
        $Article = new Article((array) $getArticle);
        $this->save($Article);
        $id_articleNew = $this->insertID();

        // On enregistre les langues
        $this->b_article_table_lang->select();
        $this->b_article_table_lang->where(['id_article' => $id_article]);
        $getArticleLangs = $this->b_article_table_lang->get()->getRow();
        $getArticleLangs->id_article = $id_articleNew;
        $this->b_article_table_lang->insert((array) $getArticleLangs);

        // On enregistre les b_category_table par default
        $this->b_article_table_category->insert(['id_article' => $id_articleNew, 'id_category' => $getArticle->id_category_default]);


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
        $this->b_article_table->select("*, DATE_FORMAT(`created_at`,'Le %d-%m-%Y &agrave; %H:%i:%s') AS `created_at`, DATE_FORMAT(`updated_at`,'Le %d-%m-%Y &agrave; %H:%i:%s') AS `updated_at`");
        $this->b_article_table->where($column, $data);

        return $this->b_article_table->get()->getRow();
    }

    /**
     * @return array|mixed
     */
    public function lastFive(): array
    {
        $this->b_article_table->select("*, DATE_FORMAT(`created_at`,'<strong>%d-%m-%Y</strong> &agrave; <strong>%H:%i:%s</strong>') AS `created_at`, DATE_FORMAT(`updated_at`,'<strong>%d-%m-%Y</strong> &agrave; <strong>%H:%i:%s</strong>') AS `updated_at`");
        $this->b_article_table->limit('5');
        $this->b_article_table->orderBy('id_article', 'DESC');

        return $this->b_article_table->get()->getResult();
    }

    /**
     * @return int|void
     */
    public function count_publied(): int
    {
        $this->b_article_table->select('COUNT(id_article) as id_article');
        $this->b_article_table->where('published', 1);

        return $this->b_article_table->get()->getRow()->id_article;
    }

    /**
     * @return int|void
     */
    public function count_attCorrect(): int
    {
        $this->b_article_table->select('COUNT(id_article) as id_article');
        $this->b_article_table->where('corriged', 0);
        $this->b_article_table->where('published', 0);
        $this->b_article_table->where('brouillon', 0);

        return $this->b_article_table->get()->getRow()->id_article;
    }

    /**
     * @return int|void
     */
    public function count_attPublished(): int
    {
        $this->b_article_table->select('COUNT(id_article) as id_article');
        $this->b_article_table->where('corriged', 1);
        $this->b_article_table->where('published', 0);

        return $this->b_article_table->get()->getRow()->id_article;
    }

    /**
     * @return int|void
     */
    public function count_brouillon(): int
    {
        $this->b_article_table->select('COUNT(id_article) as id_article');
        $this->b_article_table->where('brouillon', 1);

        return $this->b_article_table->get()->getRow()->id_article;
    }

    /**
     * @param string $title
     * @param string $link
     * @param string $content
     * @param string $tags
     * @param string $b_category_table
     * @param string $pic
     * @param int $important
     *
     * @return int (Return id)
     */
    public function Add(string $title, string $link, string $content, string $tags, string $b_category_table, string $pic, int $important): int
    {
        $data = [
            'title'          => $title,
            'content'        => $content,
            'author_created' => 1,
            'important'      => $important,
            'link'           => $link,
            'picture_one'    => $pic,
            'b_category_table'     => $b_category_table,
            'tags'           => $tags
        ];
        $this->b_article_table->insert($data);

        return $this->db->insertID();
    }

    /**
     * @param int $id
     * @param string $title
     * @param string $link
     * @param string $content
     * @param string $tags
     * @param string $b_category_table
     * @param string $pic
     * @param int $important
     * @param int $type
     *
     * @return bool
     */
    public function Edit(int $id, string $title, string $link, string $content, string $tags, string $b_category_table, string $pic, int $important, int $type): bool
    {
        $data = [
            'title'          => $title,
            'content'        => $content,
            'author_created' => 1,
            'author_update'  => 1,
            'important'      => $important,
            'link'           => $link,
            'picture_one'    => $pic,
            'b_category_table'     => $b_category_table,
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

        $this->b_article_table->where('id_article', $id);
        $this->b_article_table->set('updated_at', 'NOW()', false);
        $this->b_article_table->update($data);

        return true;
    }

    /**
     * @param int $type (1 = publied, 2 = wait corrected, 3 = wait publied, 4 = brouillon)
     *
     * @return mixed
     */
    public function getArticleListAdmin(int $type)
    {
        $this->b_article_table->select("*, DATE_FORMAT(`created_at`,'<strong>%d-%m-%Y</strong> &agrave; <strong>%H:%i:%s</strong>') AS `created_at`, DATE_FORMAT(`updated_at`,'<strong>%d-%m-%Y</strong> &agrave; <strong>%H:%i:%s</strong>') AS `updated_at`");

        if ($type == 1) {
            $this->b_article_table->where('published', 1);
        } elseif ($type == 2) {
            $this->b_article_table->where('corriged', 0);
            $this->b_article_table->where('published', 0);
            $this->b_article_table->where('brouillon', 0);
        } elseif ($type == 3) {
            $this->b_article_table->where('corriged', 1);
            $this->b_article_table->where('published', 0);
        } elseif ($type == 4) {
            $this->b_article_table->where('brouillon', 1);
        }

        return $this->b_article_table->get()->getResult();
    }
}
