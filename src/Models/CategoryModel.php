<?php

/*
 * BlogCI4 - Blog write with Codeigniter v4dev
 * @author Deathart <contact@deathart.fr>
 * @copyright Copyright (c) 2018 Deathart
 * @license https://opensource.org/licenses/MIT MIT License
 */

namespace Adnduweb\Ci4_blog\Models;

use CodeIgniter\Model;
use Adnduweb\Ci4_blog\Entities\Category;

/**
 * Class CatModel
 *
 * @package App\Models
 */
class CategoryModel extends Model
{
    /**
     * @var \CodeIgniter\Database\BaseBuilder
     */
    private $b_categories_table;

    use \Tatter\Relations\Traits\ModelTrait;
    use \Adnduweb\Ci4_logs\Traits\AuditsTrait;
    protected $afterInsert        = ['auditInsert'];
    protected $afterUpdate        = ['auditUpdate'];
    protected $afterDelete        = ['auditDelete'];
    protected $table              = 'b_categories';
    protected $tableLang          = 'b_categories_langs';
    protected $with               = ['b_categories_langs'];
    protected $without            = [];
    protected $primaryKey         = 'id';
    protected $primaryKeyLang     = 'category_id';
    protected $tableP             = 'b_posts';
    protected $tablePLang         = 'b_posts_langs';
    protected $primaryKeyP        = 'id';
    protected $primaryKeyPLang    = 'post_id';
    protected $returnType         = Category::class;
    protected $useSoftDeletes     = true;
    protected $allowedFields      = ['id_parent', 'active', 'order'];
    protected $useTimestamps      = true;
    protected $validationRules    = [];
    protected $validationMessages = [];
    protected $skipValidation     = false;
    protected $id_default         = 1;

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
        $this->b_posts           = $this->db->table('b_posts');
        $this->b_categories_table      = $this->db->table('b_categories');
        $this->b_categories_table_lang = $this->db->table('b_categories_langs');
        $this->b_posts_categories      = $this->db->table('b_posts_categories');
    }

    public function getAllCategoriesOptionParent()
    {
        $instance = [];
        $this->b_categories_table->select($this->table . '.id, slug, name, id_parent, created_at');
        $this->b_categories_table->join($this->tableLang, $this->table . '.' . $this->primaryKey . ' = ' . $this->tableLang . '.category_id');
        $this->b_categories_table->where('deleted_at IS NULL AND id_lang = ' . service('switchlanguage')->getIdLocale());
        $this->b_categories_table->orderBy($this->table . '.id DESC');
        $b_categories_tables = $this->b_categories_table->get()->getResult();
        //echo $this->b_categories_table->getCompiledSelect(); exit;
        if (!empty($b_categories_tables)) {
            foreach ($b_categories_tables as $b_categories_table) {
                $instance[] = new Category((array) $b_categories_table);
            }
        }
        return $instance;
    }

    public function getAllList(int $page, int $perpage, array $sort, array $query)
    {
        $this->b_categories_table->select();
        $this->b_categories_table->select('created_at as date_create_at');
        $this->b_categories_table->join($this->tableLang, $this->table . '.' . $this->primaryKey . ' = ' . $this->tableLang . '.category_id');
        if (isset($query[0]) && is_array($query)) {
            $this->b_categories_table->where('deleted_at IS NULL AND (name LIKE "%' . $query[0] . '%" OR description_short LIKE "%' . $query[0] . '%") AND id_lang = ' . service('switchlanguage')->getIdLocale());
            $this->b_categories_table->limit(0, $page);
        } else {
            $this->b_categories_table->where('deleted_at IS NULL AND id_lang = ' . service('switchlanguage')->getIdLocale());
            $page = ($page == '1') ? '0' : (($page - 1) * $perpage);
            $this->b_categories_table->limit($perpage, $page);
        }


        $this->b_categories_table->orderBy($sort['field'] . ' ' . $sort['sort']);

        $categoriesRow = $this->b_categories_table->get()->getResult();

        // In va chercher les products
        if (!empty($categoriesRow)) {
            $i = 0;
            foreach ($categoriesRow as $category) {
                $categoriesRow[$i]->count_product = $this->changeItemIncat($category->{$this->primaryKey})->id;
                $LangueDisplay = [];
                foreach (service('switchlanguage')->getArrayLanguesSupported() as $k => $v) {
                    if ($category->id_lang == $v) {
                        //Existe = 
                        $LangueDisplay[$k] = true;
                    }else{
                        $LangueDisplay[$k] = false;
                    }
                }
                $categoriesRow[$i]->languages = $LangueDisplay;
                $i++;
            }
        }

        //echo $this->b_categories_table->getCompiledSelect(); exit;
        return $categoriesRow;
    }

    public function getAllCount(array $sort, array $query)
    {
        $this->b_categories_table->select($this->table . '.' . $this->primaryKey);
        $this->b_categories_table->join($this->tableLang, $this->table . '.' . $this->primaryKey . ' = ' . $this->tableLang . '.category_id');
        if (isset($query[0]) && is_array($query)) {
            $this->b_categories_table->where('deleted_at IS NULL AND (name LIKE "%' . $query[0] . '%" OR description_short LIKE "%' . $query[0] . '%") AND id_lang = ' . service('switchlanguage')->getIdLocale());
        } else {
            $this->b_categories_table->where('deleted_at IS NULL AND id_lang = ' . service('switchlanguage')->getIdLocale());
        }

        $this->b_categories_table->orderBy($sort['field'] . ' ' . $sort['sort']);

        $pages = $this->b_categories_table->get();
        //echo $this->b_categories_table->getCompiledSelect(); exit;
        return $pages->getResult();
    }

    public function getIdCategoryBySlug($slug)
    {
        $this->b_categories_table->select($this->table . '.' . $this->primaryKey . ', active');
        $this->b_categories_table->join($this->tableLang, $this->table . '.' . $this->primaryKey . ' = ' . $this->tableLang . '.category_id');
        $this->b_categories_table->where('deleted_at IS NULL AND ' . $this->tableLang . '.slug="' . $slug . '"');
        $b_categories_table  = $this->b_categories_table->get()->getRow();
        // echo $this->b_categories_table ->getCompiledSelect();
        // exit;
        if (!empty($b_categories_table)) {
            if ($b_categories_table->active == '1')
                return $b_categories_table;
        }
        return false;
    }


    public function getLink(int $id, int $id_lang)
    {
        $this->b_categories_table->select('slug');
        $this->b_categories_table->join($this->tableLang, $this->table . '.' . $this->primaryKey . ' = ' . $this->tableLang . '.category_id');
        $this->b_categories_table->where([$this->table . '.id' => $id, 'id_lang' => $id_lang]);
        $b_categories_table = $this->b_categories_table->get()->getRow();
        return $b_categories_table;
    }



    /**
     * @return array
     */
    public function getlist(): array
    {
        $this->b_categories_table->select();
        $this->b_categories_table->where('id_parent', '0');
        $this->b_categories_table->orderBy('id', 'ASC');

        $b_categories_table =  $this->b_categories_table->get()->getResult('array');
        $instance  = [];
        if (!empty($b_categories_table)) {
            foreach ($b_categories_table as $categorie) {
                $instance[] = new Category($categorie);
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
    public function Updateb_Categories_table(int $id, string $column, string $data): bool
    {
        $this->b_categories_table->set($column, $data);
        $this->b_categories_table->where('id', $id);
        $this->b_categories_table->update();

        return true;
    }

    /**
     * @param string $title
     * @param string $content
     * @param string $slug
     * @param string $icon
     */
    public function Addb_Categories_table(string $title, string $content, string $slug, string $icon)
    {
        $data = [
            'title'       => $title,
            'description' => $content,
            'slug'        => $slug,
            'icon'        => $icon
        ];
        $this->b_categories_table->insert($data);
    }


    public function getNameCat(int $id, int $id_lang): string
    {
        $this->b_categories_table_lang->select('name');
        $this->b_categories_table_lang->where(['id' => $id, 'id_lang' => $id_lang]);
        return $this->b_categories_table_lang->get()->getRow()->name;
    }

    // public function changeb_postsIncat(int $id)
    // {

    //     $this->b_article_categorie->select();
    //     $this->b_article_categorie->where(['id' => $id]);
    //     $b_article_categorie = $this->b_article_categorie->get()->getResult();

    //     // On met par default les relatiosn b_categories_table
    //     if (!empty($b_article_categorie)) {
    //         foreach ($b_article_categorie as $article) {
    //             // ON supprime cette b_categories_table des b_posts
    //             $this->b_article_categorie->delete(['post_id' => $article->post_id, 'id' => $id]);
    //             $this->b_article_categorie->delete(['post_id' => $article->post_id, 'id' => $this->id_default]);


    //             $this->b_article_categorie->set(['id' => $this->id_default]);
    //             $this->b_article_categorie->where('id', $id);

    //             $data = [
    //                 'post_id'   =>  $article->post_id,
    //                 'id' => $this->id_default,
    //                 'created_at' => date('Y-m-d H:i:s'),
    //             ];
    //             try {
    //                 $this->b_article_categorie->insert($data);
    //             } catch (\Exception $e) {
    //                 return $this->db->error()['code'];
    //             }
    //         }
    //     }

    //     $this->b_posts->select('id_default');
    //     $this->b_posts->where(['id_default' => $id]);
    //     $b_posts = $this->b_categories_table_lang->get()->getResult();

    //     // On met par default les relatiosn b_categories_table
    //     if (!empty($b_posts)) {
    //         foreach ($b_posts as $article) {
    //             $this->b_posts->set(['id_default' => $this->id_default]);
    //             $this->b_posts->where('id_default', $id);
    //             $this->b_posts->update();
    //         }
    //     }
    // }


    public function getAllCat()
    {
        $this->b_categories_table->select($this->table . '.' . $this->primaryKey . ', name');
        $this->b_categories_table->join($this->tableLang, $this->table . '.' . $this->primaryKey . ' = ' . $this->tableLang . '.category_id');
        $this->b_categories_table->where('deleted_at IS NULL AND id_lang = ' . service('switchlanguage')->getIdLocale());
        return $this->b_categories_table->get()->getResult();
    }


    /****
     *
     * Il ya des produits dans cette categories ?
     */
    public function changeItemIncat(int $id)
    {
        $this->b_posts->selectCount($this->tableP . '.' . $this->primaryKeyP);
        $this->b_posts->where('deleted_at IS NULL AND  id_category_default = ' . $id);
        return $this->b_posts->get()->getRow();
    }

    /**
     *
     * On change la categorie
     */
    public function updatePostCategorie(array $data)
    {

        //print_r($data);
        foreach ($data as $subdata) {

            $this->b_posts->select($this->tableP . '.' . $this->primaryKeyP);
            $this->b_posts->where('deleted_at IS NULL AND  id_category_default = ' . $subdata['old_categorie']);
            $listProducts = $this->b_posts->get()->getResult();
            if (!empty($listProducts)) {
                foreach ($listProducts as $post) {
                    //print_r([$this->primaryKeyPLang => $post->{$this->primaryKeyPLang}]);
                    // print_r($post);
                    // exit;
                    $this->b_posts_categories->delete([$this->primaryKeyPLang => $post->{$this->primaryKey}]);
                    // print_r([$this->primaryKeyPLang => $post->{$this->primaryKey}]);
                    // exit;

                    $tab = ['id_category_default' => $subdata['new_categorie_id']];
                    $this->b_posts->set($tab);
                    $this->b_posts->where([$this->primaryKeyP => $post->{$this->primaryKeyP}]);
                    //echo $this->b_posts->getCompiledUpdate();
                    $this->b_posts->update();

                    $this->b_posts_categories->insert([$this->primaryKeyPLang => $post->{$this->primaryKey}, $this->primaryKeyLang => $subdata['new_categorie_id']]);
                }
            }
        }
    }
}
