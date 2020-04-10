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
    protected $afterInsert = ['auditInsert'];
    protected $afterUpdate = ['auditUpdate'];
    protected $afterDelete = ['auditDelete'];

    protected $table = 'categories';
    protected $tableLang = 'categories_langs';
    protected $with = ['categories_langs'];
    protected $without = [];
    protected $primaryKey = 'id_categorie';
    protected $returnType = Categorie::class;
    protected $useSoftDeletes = true;
    protected $allowedFields = ['order'];
    protected $useTimestamps = true;
    protected $validationRules = [];
    protected $validationMessages = [];
    protected $skipValidation     = false;

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
        $this->categories = $this->db->table('categories');
    }

    /**
     * @return array
     */
    public function getlist(): array
    {
        $this->categories->select();
        $this->categories->where('parent', '0');
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
}
