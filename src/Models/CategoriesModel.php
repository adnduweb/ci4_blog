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
    private $categories;

    use \Tatter\Relations\Traits\ModelTrait;
    use \Adnduweb\Ci4_logs\Traits\AuditsTrait;
    protected $afterInsert          = ['auditInsert'];
    protected $afterUpdate          = ['auditUpdate'];
    protected $afterDelete          = ['auditDelete'];
    protected $table                = 'categories';
    protected $tableLang            = 'categories_langs';
    protected $with                 = ['categories_langs'];
    protected $without              = [];
    protected $primaryKey           = 'id_categorie';
    protected $returnType           = Categorie::class;
    protected $useSoftDeletes       = true;
    protected $allowedFields        = ['id_parent', 'active', 'order'];
    protected $useTimestamps        = true;
    protected $validationRules      = [];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $id_categorie_default = 1;

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
        $this->articles          = $this->db->table('articles');
        $this->categories        = $this->db->table('categories');
        $this->categories_langs  = $this->db->table('categories_langs');
        $this->article_categorie = $this->db->table('articles_categories');
    }

    public function getAllList(int $page, int $perpage, array $sort, array $query)
    {
        $this->categories->select();
        $this->categories->select('created_at as date_create_at');
        $this->categories->join($this->tableLang, $this->table . '.' . $this->primaryKey . ' = ' . $this->tableLang . '.id_categorie');
        if (isset($query['generalSearch']) && !empty($query['generalSearch'])) {
            $this->categories->where('deleted_at IS NULL AND (name LIKE "%' . $query['generalSearch'] . '%" OR login_destination LIKE "%' . $query['generalSearch'] . '%") AND id_lang = ' . service('settings')->setting_id_lang);
            $this->categories->limit(0, $page);
        } else {
            $this->categories->where('deleted_at IS NULL AND id_lang = ' . service('settings')->setting_id_lang);
            $page = ($page == '1') ? '0' : (($page - 1) * $perpage);
            $this->categories->limit($perpage, $page);
        }


        $this->categories->orderBy($sort['field'] . ' ' . $sort['sort']);

        $groupsRow = $this->categories->get()->getResult();

        //echo $this->categories->getCompiledSelect(); exit;
        return $groupsRow;
    }

    public function getAllCount(array $sort, array $query)
    {
        $this->categories->select($this->table . '.' . $this->primaryKey);
        $this->categories->join($this->tableLang, $this->table . '.' . $this->primaryKey . ' = ' . $this->tableLang . '.id_categorie');
        if (isset($query['generalSearch']) && !empty($query['generalSearch'])) {
            $this->categories->where('deleted_at IS NULL AND (name LIKE "%' . $query['generalSearch'] . '%" OR login_destination LIKE "%' . $query['generalSearch'] . '%") AND id_lang = ' . service('settings')->setting_id_lang);
        } else {
            $this->categories->where('deleted_at IS NULL AND id_lang = ' . service('settings')->setting_id_lang);
        }

        $this->categories->orderBy($sort['field'] . ' ' . $sort['sort']);

        $pages = $this->categories->get();
        //echo $this->categories->getCompiledSelect(); exit;
        return $pages->getResult();
    }



    /**
     * @return array
     */
    public function getlist(): array
    {
        $this->categories->select();
        $this->categories->where('id_parent', '0');
        $this->categories->orderBy('id_categorie', 'ASC');

        $categories =  $this->categories->get()->getResult('array');
        $instance  = [];
        if (!empty($categories)) {
            foreach ($categories as $categorie) {
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
    public function UpdateCategories(int $id, string $column, string $data): bool
    {
        $this->categories->set($column, $data);
        $this->categories->where('id_categorie', $id);
        $this->categories->update();

        return true;
    }

    /**
     * @param string $title
     * @param string $content
     * @param string $slug
     * @param string $icon
     */
    public function AddCategories(string $title, string $content, string $slug, string $icon)
    {
        $data = [
            'title'       => $title,
            'description' => $content,
            'slug'        => $slug,
            'icon'        => $icon
        ];
        $this->categories->insert($data);
    }


    public function getNameCat(int $id_categorie, int $id_lang): string
    {
        $this->categories_langs->select('name');
        $this->categories_langs->where(['id_categorie' => $id_categorie, 'id_lang' => $id_lang]);
        return $this->categories_langs->get()->getRow()->name;
    }

    public function changeArticlesIncat(int $id_categorie)
    {

        $this->article_categorie->select();
        $this->article_categorie->where(['id_categorie' => $id_categorie]);
        $article_categorie = $this->article_categorie->get()->getResult();

        // On met par default les relatiosn categories
        if (!empty($article_categorie)) {
            foreach ($article_categorie as $article) {
                // ON supprime cette categories des articles
                $this->article_categorie->delete(['id_article' => $article->id_article, 'id_categorie' => $id_categorie]);
                $this->article_categorie->delete(['id_article' => $article->id_article, 'id_categorie' => $this->id_categorie_default]);


                $this->article_categorie->set(['id_categorie' => $this->id_categorie_default]);
                $this->article_categorie->where('id_categorie', $id_categorie);

                $data = [
                    'id_article'   =>  $article->id_article,
                    'id_categorie' => $this->id_categorie_default,
                    'created_at' => date('Y-m-d H:i:s'),
                ];
                try {
                    $this->article_categorie->insert($data);
                } catch (\Exception $e) {
                    return $this->db->error()['code'];
                }
            }
        }

        $this->articles->select('id_categorie_default');
        $this->articles->where(['id_categorie_default' => $id_categorie]);
        $articles = $this->categories_langs->get()->getResult();

        // On met par default les relatiosn categories
        if (!empty($articles)) {
            foreach ($articles as $article) {
                $this->articles->set(['id_categorie_default' => $this->id_categorie_default]);
                $this->articles->where('id_categorie_default', $id_categorie);
                $this->articles->update();
            }
        }
    }

    public function getAllCat(){
        $this->categories_langs->select('id_categorie, name');
        return $this->categories_langs->get()->getResult();
    }
}
