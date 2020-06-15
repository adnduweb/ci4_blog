<?php

/*
 * BlogCI4 - Blog write with Codeigniter v4dev
 * @author Deathart <contact@deathart.fr>
 * @copyright Copyright (c) 2018 Deathart
 * @license https://opensource.org/licenses/MIT MIT License
 */

namespace Adnduweb\Ci4_blog\Models;

use CodeIgniter\Model;
use Adnduweb\Ci4_blog\Entities\Categorie;

/**
 * Class CatModel
 *
 * @package App\Models
 */
class CategoriesModel extends Model
{
    /**
     * @var \CodeIgniter\Database\BaseBuilder
     */
    private $b_category_table;

    use \Tatter\Relations\Traits\ModelTrait;
    use \Adnduweb\Ci4_logs\Traits\AuditsTrait;
    protected $afterInsert          = ['auditInsert'];
    protected $afterUpdate          = ['auditUpdate'];
    protected $afterDelete          = ['auditDelete'];
    protected $table                = 'b_category';
    protected $tableLang            = 'b_category_lang';
    protected $with                 = ['b_category_lang'];
    protected $without              = [];
    protected $primaryKey           = 'id_category';
    protected $returnType           = Categorie::class;
    protected $useSoftDeletes       = true;
    protected $allowedFields        = ['id_parent', 'active', 'order'];
    protected $useTimestamps        = true;
    protected $validationRules      = [];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $id_category_default = 1;

    /**
     * Site constructor.
     *
     * @param array ...$params
     *
     * @throws \CodeIgniter\Database\Exceptions\DatabaseException
     */
    public function __construct(...$params)
    {
        parent::__construct(...$params);
        $this->b_article_table       = $this->db->table('b_article');
        $this->b_category_table      = $this->db->table('b_category');
        $this->b_category_table_lang = $this->db->table('b_category_lang');
        $this->b_article_categorie   = $this->db->table('b_article_category');
    }

    public function getAllCategoriesOptionParent()
    {
        $instance = [];
        $this->b_category_table->select($this->table . '.id_category, slug, name, id_parent, created_at');
        $this->b_category_table->join($this->tableLang, $this->table . '.' . $this->primaryKey . ' = ' . $this->tableLang . '.id_category');
        $this->b_category_table->where('deleted_at IS NULL AND id_lang = ' . service('settings')->setting_bo_id_lang);
        $this->b_category_table->orderBy($this->table . '.id_category DESC');
        $b_category_tables = $this->b_category_table->get()->getResult();
        //echo $this->b_category_table->getCompiledSelect(); exit;
        if (!empty($b_category_tables)) {
            foreach ($b_category_tables as $b_category_table) {
                $instance[] = new Categorie((array) $b_category_table);
            }
        }
        return $instance;
    }

    public function getAllList(int $page, int $perpage, array $sort, array $query)
    {
        $this->b_category_table->select();
        $this->b_category_table->select('created_at as date_create_at');
        $this->b_category_table->join($this->tableLang, $this->table . '.' . $this->primaryKey . ' = ' . $this->tableLang . '.id_category');
        if (isset($query[0]) && is_array($query)) {
            $this->b_category_table->where('deleted_at IS NULL AND (name LIKE "%' . $query[0] . '%" OR description_short LIKE "%' . $query[0] . '%") AND id_lang = ' . service('settings')->setting_id_lang);
            $this->b_category_table->limit(0, $page);
        } else {
            $this->b_category_table->where('deleted_at IS NULL AND id_lang = ' . service('settings')->setting_id_lang);
            $page = ($page == '1') ? '0' : (($page - 1) * $perpage);
            $this->b_category_table->limit($perpage, $page);
        }


        $this->b_category_table->orderBy($sort['field'] . ' ' . $sort['sort']);

        $groupsRow = $this->b_category_table->get()->getResult();

        //echo $this->b_category_table->getCompiledSelect(); exit;
        return $groupsRow;
    }

    public function getAllCount(array $sort, array $query)
    {
        $this->b_category_table->select($this->table . '.' . $this->primaryKey);
        $this->b_category_table->join($this->tableLang, $this->table . '.' . $this->primaryKey . ' = ' . $this->tableLang . '.id_category');
        if (isset($query[0]) && is_array($query)) {
            $this->b_category_table->where('deleted_at IS NULL AND (name LIKE "%' . $query[0] . '%" OR description_short LIKE "%' . $query[0] . '%") AND id_lang = ' . service('settings')->setting_id_lang);
        } else {
            $this->b_category_table->where('deleted_at IS NULL AND id_lang = ' . service('settings')->setting_id_lang);
        }

        $this->b_category_table->orderBy($sort['field'] . ' ' . $sort['sort']);

        $pages = $this->b_category_table->get();
        //echo $this->b_category_table->getCompiledSelect(); exit;
        return $pages->getResult();
    }

    public function getIdCategoryBySlug($slug)
    {
        $this->b_category_table->select($this->table . '.' . $this->primaryKey . ', active');
        $this->b_category_table->join($this->tableLang, $this->table . '.' . $this->primaryKey . ' = ' . $this->tableLang . '.id_category');
        $this->b_category_table->where('deleted_at IS NULL AND ' . $this->tableLang . '.slug="' . $slug . '"');
        $b_category_table  = $this->b_category_table->get()->getRow();
        // echo $this->b_category_table ->getCompiledSelect();
        // exit;
        if (!empty($b_category_table)) {
            if ($b_category_table->active == '1')
                return $b_category_table;
        }
        return false;
    }


    public function getLink(int $id_category, int $id_lang)
    {
        $this->b_category_table->select('slug');
        $this->b_category_table->join($this->tableLang, $this->table . '.' . $this->primaryKey . ' = ' . $this->tableLang . '.id_category');
        $this->b_category_table->where([$this->table . '.id_category' => $id_category, 'id_lang' => $id_lang]);
        $b_category_table = $this->b_category_table->get()->getRow();
        return $b_category_table;
    }



    /**
     * @return array
     */
    public function getlist(): array
    {
        $this->b_category_table->select();
        $this->b_category_table->where('id_parent', '0');
        $this->b_category_table->orderBy('id_category', 'ASC');

        $b_category_table =  $this->b_category_table->get()->getResult('array');
        $instance  = [];
        if (!empty($b_category_table)) {
            foreach ($b_category_table as $categorie) {
                $instance[] = new Categorie($categorie);
            }
        }
        return $instance;
    }

    /**
     * @param int $id
     * @param string $column
     * @param string $data
     *
     * @return bool
     */
    public function Updateb_Category_table(int $id, string $column, string $data): bool
    {
        $this->b_category_table->set($column, $data);
        $this->b_category_table->where('id_category', $id);
        $this->b_category_table->update();

        return true;
    }

    /**
     * @param string $title
     * @param string $content
     * @param string $slug
     * @param string $icon
     */
    public function Addb_Category_table(string $title, string $content, string $slug, string $icon)
    {
        $data = [
            'title'       => $title,
            'description' => $content,
            'slug'        => $slug,
            'icon'        => $icon
        ];
        $this->b_category_table->insert($data);
    }


    public function getNameCat(int $id_category, int $id_lang): string
    {
        $this->b_category_table_lang->select('name');
        $this->b_category_table_lang->where(['id_category' => $id_category, 'id_lang' => $id_lang]);
        return $this->b_category_table_lang->get()->getRow()->name;
    }

    public function changeb_Article_tableIncat(int $id_category)
    {

        $this->b_article_categorie->select();
        $this->b_article_categorie->where(['id_category' => $id_category]);
        $b_article_categorie = $this->b_article_categorie->get()->getResult();

        // On met par default les relatiosn b_category_table
        if (!empty($b_article_categorie)) {
            foreach ($b_article_categorie as $article) {
                // ON supprime cette b_category_table des b_article_table
                $this->b_article_categorie->delete(['id_article' => $article->id_article, 'id_category' => $id_category]);
                $this->b_article_categorie->delete(['id_article' => $article->id_article, 'id_category' => $this->id_category_default]);


                $this->b_article_categorie->set(['id_category' => $this->id_category_default]);
                $this->b_article_categorie->where('id_category', $id_category);

                $data = [
                    'id_article'   =>  $article->id_article,
                    'id_category' => $this->id_category_default,
                    'created_at' => date('Y-m-d H:i:s'),
                ];
                try {
                    $this->b_article_categorie->insert($data);
                } catch (\Exception $e) {
                    return $this->db->error()['code'];
                }
            }
        }

        $this->b_article_table->select('id_category_default');
        $this->b_article_table->where(['id_category_default' => $id_category]);
        $b_article_table = $this->b_category_table_lang->get()->getResult();

        // On met par default les relatiosn b_category_table
        if (!empty($b_article_table)) {
            foreach ($b_article_table as $article) {
                $this->b_article_table->set(['id_category_default' => $this->id_category_default]);
                $this->b_article_table->where('id_category_default', $id_category);
                $this->b_article_table->update();
            }
        }
    }


    public function getAllCat()
    {
        $this->b_category_table->select($this->table . '.' . $this->primaryKey . ', name');
        $this->b_category_table->join($this->tableLang, $this->table . '.' . $this->primaryKey . ' = ' . $this->tableLang . '.id_category');
        $this->b_category_table->where('deleted_at IS NULL AND id_lang = ' . service('settings')->setting_id_lang);
        return $this->b_category_table->get()->getResult();
    }
}
