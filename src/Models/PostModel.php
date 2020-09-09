<?php

/*
 * BlogCI4 - Blog write with Codeigniter v4dev
 * @author Deathart <contact@deathart.fr>
 * @copyright Copyright (c) 2018 Deathart
 * @license https://opensource.org/licenses/MIT MIT License
 */

namespace Adnduweb\Ci4_blog\Models;

use CodeIgniter\Model;
use Adnduweb\Ci4_blog\Entities\Post;
use Faker\Generator;

/**
 * Class ArticleModel
 *
 * @package App\Models
 */
class PostModel extends Model
{
    /**
     * @var \CodeIgniter\Database\BaseBuilder
     */
    private $b_posts_table;

    use \Tatter\Relations\Traits\ModelTrait, \Adnduweb\Ci4_logs\Traits\AuditsTrait, \App\Models\BaseModel;
    protected $afterInsert        = ['auditInsert'];
    protected $afterUpdate        = ['auditUpdate'];
    protected $afterDelete        = ['auditDelete'];
    protected $table              = 'b_posts';
    protected $tableLang          = 'b_posts_langs';
    protected $with               = ['b_posts_langs'];
    protected $without            = [];
    protected $primaryKey         = 'id';
    protected $primaryKeyLang     = 'post_id';
    protected $primaryKeyCatLang  = 'category_id';
    protected $returnType         = Post::class;
    protected $localizeFile       = 'Adnduweb\Ci4_blog\Models\FormModel';
    protected $useSoftDeletes     = false;
    protected $allowedFields      = ['id_category_default', 'user_id', 'user_updated', 'active', 'important', 'picture_one', 'picture_header', 'no_follow_no_index', 'order', 'type'];
    protected $useTimestamps      = true;
    protected $createdField       = 'created_at';
    protected $updatedField       = 'updated_at';
    protected $deletedField       = 'deleted_at';
    protected $validationMessages = [];
    protected $skipValidation     = false;
    protected $searchKtDatatable  = ['name', 'description_short', 'created_at'];

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
        $this->builder                 = $this->db->table('b_posts');
        $this->b_posts_table_lang      = $this->db->table('b_posts_langs');
        $this->b_posts_categories      = $this->db->table('b_posts_categories');
        $this->b_categories_table      = $this->db->table('b_categories');
        $this->b_categories_table_lang = $this->db->table('b_categories_langs');
    }

    /**
     * Genérateur de Fake
     */
    public function fake(Generator &$faker)
    {
        return [
            'id_category_default' => 1,
            'user_id'             => 1,
            'user_updated'        => 1,
            'active'              => 1,
            'important'           => 1,
            'picture_one'         => null,
            'picture_header'      => null,
            'no_follow_no_index'  => 0,
            'order'               => 1,
            'type'                => 1,
            'created_at'          => date('Y-m-d H:i:s'),
        ];
    }

    public function fakelang(int $id)
    {
        $faker = \Faker\Factory::create();
        // print_r($faker);
        // exit;
        $data = [
            'post_id'           => $id,
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
        $this->b_posts_table_lang->insert($data);

        $dataCat = [
            'post_id'      => $id,
            'category_id' => 1
        ];
        $this->b_posts_categories->insert($dataCat);
    }

    /** 
     * 
     * Affichage en listing 
     */
    public function getAllList(int $page, int $perpage, array $sort, array $query)
    {
        $b_postsResult = $this->getBaseAllList($page, $perpage, $sort, $query, $this->searchKtDatatable);
        // In va chercher les b_categories_table
        if (!empty($b_postsResult)) {
            $i = 0;
            foreach ($b_postsResult as $article) {
                $b_postsResult[$i]->b_categories_table = $this->getCatByArt($article->id);
                $LangueDisplay = [];
                foreach (service('switchlanguage')->getArrayLanguesSupported() as $k => $v) {
                    if ($article->id_lang == $v) {
                        //Existe = 
                        $LangueDisplay[$k] = true;
                    }else{
                        $LangueDisplay[$k] = false;
                    }
                }
                $b_postsResult[$i]->languages = $LangueDisplay;
                $i++;
            }
        }

        //echo $this->builder->getCompiledSelect(); exit;
        return $b_postsResult;
    }


    public function getCatArt(int $id): array
    {
        $this->b_posts_categories->select();
        $this->b_posts_categories->where('id', $id);
        return $this->b_posts_categories->get()->getResult();
    }

    public function getCatByArt($id = null): array
    {
        $this->b_posts_categories->select();
        //$this->b_posts_categories->join('b_categories_table_lang', 'b_posts_categories.id_category = b_categories_table_lang.id_category');
        $this->b_posts_categories->where(['post_id' => $id]);
        $b_posts_categories =  $this->b_posts_categories->get()->getResult();
        $temp = [];
        if (!empty($b_posts_categories)) {
            $i = 0;
            foreach ($b_posts_categories as $art) {
                $temp[$art->category_id] = $art;
                $temp[$art->category_id]->name = $this->b_categories_table_lang->where(['category_id' => $art->category_id, 'id_lang' => service('switchlanguage')->getIdLocale()])->get()->getRow()->name;
                $i++;
            }
        }
        return $temp;
    }

    public function getIdArticleBySlug($slug)
    {
        $this->builder->select($this->table . '.' . $this->primaryKey . ', active, type');
        $this->builder->join($this->tableLang, $this->table . '.' . $this->primaryKey . ' = ' . $this->tableLang . '.post_id');
        $this->builder->where('deleted_at IS NULL AND ' . $this->tableLang . '.slug="' . $slug . '"');
        $b_posts_table = $this->builder->get()->getRow();
        // echo $this->builder->getCompiledSelect();
        // exit;
        if (!empty($b_posts_table)) {
            if ($b_posts_table->active == '1')
                return $b_posts_table;
        }
        return false;
    }

    public function getLast(int $id_lang)
    {
        $this->select();
        $this->limit(1);
        $this->where($this->table . '.type = 1');
        $this->orderBy($this->table . '.id DESC ');
        $b_posts_table = $this->first();
        return $b_posts_table;
    }


    public function getLink(int $id, int $id_lang)
    {
        $this->builder->select('slug');
        $this->builder->join($this->tableLang, $this->table . '.' . $this->primaryKey . ' = ' . $this->tableLang . '.post_id');
        $this->builder->where([$this->table . '.id' => $id, 'id_lang' => $id_lang]);
        $b_posts_table = $this->builder->get()->getRow();
        return $b_posts_table;
    }

    public function getArticlesByIdCategory(int $id_category, int $id_lang)
    {
        $this->builder->select();
        $this->builder->join($this->tableLang, $this->table . '.' . $this->primaryKey . ' = ' . $this->tableLang . '.post_id');
        $this->builder->where([$this->table . '.id_category_default' => $id_category, 'id_lang' => $id_lang, 'type' => 1]);
        $b_posts_table = $this->builder->get()->getResult();
        if (!empty($b_posts_table)) {
            foreach ($b_posts_table as &$article) {
                $article = new Post((array) $article);
            }
        }
        return $b_posts_table;
    }

    public function dupliquer(int $id)
    {

        //Article
        $this->builder->select();
        $this->builder->where([$this->primaryKey => $id]);
        $getArticle = $this->builder->get()->getRow();

        unset($getArticle->id);
        $getArticle->type = 4;
        $Article = new Post((array) $getArticle);
        $this->save($Article);
        $idNew = $this->insertID();

        // On enregistre les langues
        $this->b_posts_table_lang->select();
        $this->b_posts_table_lang->where([$this->primaryKeyLang => $id]);
        $getArticleLangs = $this->b_posts_table_lang->get()->getRow();
        unset($getArticleLangs->id_post_lang);
        $getArticleLangs->{$this->primaryKeyLang} = $idNew;

        $this->b_posts_table_lang->insert((array) $getArticleLangs);
        //print_r($getArticleLangs);exit;

        // On enregistre les b_categories_table par default
        $this->b_posts_categories->insert([$this->primaryKeyLang => $idNew, $this->primaryKeyCatLang => $getArticle->id_category_default]);


        // exit;
    }

    // /**
    //  * @param string $column
    //  * @param string $data
    //  *
    //  * @return mixed
    //  */
    // public function GetArticle(string $column, string $data)
    // {
    //     $this->builder->select("*, DATE_FORMAT(`created_at`,'Le %d-%m-%Y &agrave; %H:%i:%s') AS `created_at`, DATE_FORMAT(`updated_at`,'Le %d-%m-%Y &agrave; %H:%i:%s') AS `updated_at`");
    //     $this->builder->where($column, $data);

    //     return $this->builder->get()->getRow();
    // }

    // /**
    //  * @return array|mixed
    //  */
    // public function lastFive(): array
    // {
    //     $this->builder->select("*, DATE_FORMAT(`created_at`,'<strong>%d-%m-%Y</strong> &agrave; <strong>%H:%i:%s</strong>') AS `created_at`, DATE_FORMAT(`updated_at`,'<strong>%d-%m-%Y</strong> &agrave; <strong>%H:%i:%s</strong>') AS `updated_at`");
    //     $this->builder->limit('5');
    //     $this->builder->orderBy('id', 'DESC');

    //     return $this->builder->get()->getResult();
    // }

    // /**
    //  * @return int|void
    //  */
    // public function count_publied(): int
    // {
    //     $this->builder->select('COUNT(id) as id');
    //     $this->builder->where('published', 1);

    //     return $this->builder->get()->getRow()->id;
    // }

    // /**
    //  * @return int|void
    //  */
    // public function count_attCorrect(): int
    // {
    //     $this->builder->select('COUNT(id) as id');
    //     $this->builder->where('corriged', 0);
    //     $this->builder->where('published', 0);
    //     $this->builder->where('brouillon', 0);

    //     return $this->builder->get()->getRow()->id;
    // }

    // /**
    //  * @return int|void
    //  */
    // public function count_attPublished(): int
    // {
    //     $this->builder->select('COUNT(id) as id');
    //     $this->builder->where('corriged', 1);
    //     $this->builder->where('published', 0);

    //     return $this->builder->get()->getRow()->id;
    // }

    // /**
    //  * @return int|void
    //  */
    // public function count_brouillon(): int
    // {
    //     $this->builder->select('COUNT(id) as id');
    //     $this->builder->where('brouillon', 1);

    //     return $this->builder->get()->getRow()->id;
    // }

    // /**
    //  * @param string $title
    //  * @param string $link
    //  * @param string $content
    //  * @param string $tags
    //  * @param string $b_categories_table
    //  * @param string $pic
    //  * @param int $important
    //  *
    //  * @return int (Return id)
    //  */
    // public function Add(string $title, string $link, string $content, string $tags, string $b_categories_table, string $pic, int $important): int
    // {
    //     $data = [
    //         'title'          => $title,
    //         'content'        => $content,
    //         'author_created' => 1,
    //         'important'      => $important,
    //         'link'           => $link,
    //         'picture_one'    => $pic,
    //         'b_categories_table'     => $b_categories_table,
    //         'tags'           => $tags
    //     ];
    //     $this->builder->insert($data);

    //     return $this->db->insertID();
    // }

    // /**
    //  * @param int $id
    //  * @param string $title
    //  * @param string $link
    //  * @param string $content
    //  * @param string $tags
    //  * @param string $b_categories_table
    //  * @param string $pic
    //  * @param int $important
    //  * @param int $type
    //  *
    //  * @return bool
    //  */
    // public function Edit(int $id, string $title, string $link, string $content, string $tags, string $b_categories_table, string $pic, int $important, int $type): bool
    // {
    //     $data = [
    //         'title'          => $title,
    //         'content'        => $content,
    //         'author_created' => 1,
    //         'user_updated'  => 1,
    //         'important'      => $important,
    //         'link'           => $link,
    //         'picture_one'    => $pic,
    //         'b_categories_table'     => $b_categories_table,
    //         'tags'           => $tagsy
    //     ];

    //     if ($type == 1) {
    //         $data['published'] = 1;
    //         $data['corriged'] = 1;
    //     } elseif ($type == 2) {
    //         $data['published'] = 0;
    //         $data['corriged'] = 0;
    //         $data['brouillon'] = 0;
    //     } elseif ($type == 3) {
    //         $data['published'] = 0;
    //         $data['corriged'] = 1;
    //         $data['brouillon'] = 0;
    //     }

    //     $this->builder->where('id', $id);
    //     $this->builder->set('updated_at', 'NOW()', false);
    //     $this->builder->update($data);

    //     return true;
    // }

    // /**
    //  * @param int $type (1 = publied, 2 = wait corrected, 3 = wait publied, 4 = brouillon)
    //  *
    //  * @return mixed
    //  */
    // public function getArticleListAdmin(int $type)
    // {
    //     $this->builder->select("*, DATE_FORMAT(`created_at`,'<strong>%d-%m-%Y</strong> &agrave; <strong>%H:%i:%s</strong>') AS `created_at`, DATE_FORMAT(`updated_at`,'<strong>%d-%m-%Y</strong> &agrave; <strong>%H:%i:%s</strong>') AS `updated_at`");

    //     if ($type == 1) {
    //         $this->builder->where('published', 1);
    //     } elseif ($type == 2) {
    //         $this->builder->where('corriged', 0);
    //         $this->builder->where('published', 0);
    //         $this->builder->where('brouillon', 0);
    //     } elseif ($type == 3) {
    //         $this->builder->where('corriged', 1);
    //         $this->builder->where('published', 0);
    //     } elseif ($type == 4) {
    //         $this->builder->where('brouillon', 1);
    //     }

    //     return $this->builder->get()->getResult();
    // }
}
