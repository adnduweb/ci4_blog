<?php

namespace Adnduweb\Ci4_blog\Controllers\Front;

use CodeIgniter\API\ResponseTrait;
use Adnduweb\Ci4_blog\Entities\Article;
use Adnduweb\Ci4_blog\Models\PostModel;
use Adnduweb\Ci4_blog\Models\CategoryModel;

class FrontCategoriesController extends \App\Controllers\Front\FrontController
{
    use \App\Traits\BuilderModelTrait;
    use \App\Traits\ModuleTrait;

    public $name_module = 'blog';
    protected $idModule;

    public function __construct()
    {
        parent::__construct();
        $this->tableModel  = new CategoryModel();
        $this->tableArticleModel  = new PostModel();
        $this->idModule  = $this->getIdModule();
    }
    public function index()
    {
        //Silent
    }

    public function show($slug)
    {

        $categoryLight = $this->tableModel->getIdCategoryBySlug($slug);

        // 404 car soit elle n'existe pas ou elle n'est pas active
        if (empty($categoryLight)) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException(lang('Core.Cannot find the category item : {0}', [$slug]));
        }

        // il n'est pas encore publié
        if ($categoryLight->active != '1') {
            return redirect()->to(base_urlFront(), 302);
        }

        $this->data['page'] = $this->tableModel->where('id_category', $categoryLight->id_category)->first();

        $this->data['no_follow_no_index'] = ($this->data['page']->no_follow_no_index == 0) ?  'index follow' :  'no-index no-follow';
        $this->data['id']  = str_replace('/', '', $this->data['page']->slug);
        $this->data['class'] = $this->data['class'] . ' ' .  str_replace('/', '', $this->data['page']->slug) . ' ' .  str_replace('/', '', $this->data['page']->template) . ' category_' . $this->data['page']->id_category;
        $this->data['meta_title'] = $this->data['page']->meta_title;
        $this->data['meta_description'] = $this->data['page']->meta_description;
        $this->data['page']->list_articles = $this->tableArticleModel->getArticlesByIdCategory($categoryLight->id_category, service('switchlanguage')->getIdLocale());

        // print_r($this->data['page']);
        // exit;
        return view($this->get_current_theme_view('__template_part/category_loop', 'default'), $this->data);
    }
}
