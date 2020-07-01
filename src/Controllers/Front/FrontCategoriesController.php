<?php

namespace Adnduweb\Ci4_blog\Controllers\Front;

use CodeIgniter\API\ResponseTrait;
use Adnduweb\Ci4_blog\Entities\Post;
use Adnduweb\Ci4_blog\Models\PostModel;
use Adnduweb\Ci4_blog\Models\CategoryModel;
use Adnduweb\Ci4_page\Libraries\PageDefault;

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

    public function all(){

        // Load Header
        $header_parameter = array(
            'title' => lang('Front_default.actu'),
            'meta_title' => lang('Front_default.actu_meta_title'),
            'meta_description' => lang('Front_default.actu_meta_description'),
            'url' => [1 => ['slug' => 'actualites'], 2 => ['slug' => 'news']],
        );

        // On définit la page
        $this->data['page'] = new PageDefault($header_parameter);
        $this->data['no_follow_no_index'] = 'index follow';
        $this->data['id']  = 'authentification';
        $this->data['class'] = $this->data['class'] . ' page_actualite page_actu_boxed page_' . $this->data['page']->getIdItem();
        $this->data['meta_title'] = '';
        $this->data['meta_description'] = '';

        // ON cherche les articles
        $list_post = $this->tableArticleModel->where('type', 1)->get()->getResult('array');

        if (!empty($list_post)) {
            $i = 0;
            foreach ($list_post as $actu) {
                $listActu[$i] = new Post($actu);
                $listActu[$i]->categorie = $this->tableModel->find($actu['id_category_default']);
                $i++;
            }
        }

        $this->data['list_post'] = $listActu;

        return view($this->get_current_theme_view('__template_part/page_actu_boxed', 'default'), $this->data);
    }

    public function show($slug)
    {

        $categoryLight = $this->tableModel->getIdCategoryBySlug($slug);

        // print_r($categoryLight); exit;

        // 404 car soit elle n'existe pas ou elle n'est pas active
        if (empty($categoryLight)) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException(lang('Core.Cannot find the category item : {0}', [$slug]));
        }

        // il n'est pas encore publié
        if ($categoryLight->active != '1') {
            return redirect()->to(base_urlFront(), 302);
        }

        $this->data['page'] = $this->tableModel->where('id', $categoryLight->id)->first();

        $this->data['no_follow_no_index'] = ($this->data['page']->no_follow_no_index == 0) ?  'index follow' :  'no-index no-follow';
        $this->data['id']  = str_replace('/', '', $this->data['page']->slug);
        $this->data['class'] = $this->data['class'] . ' ' .  str_replace('/', '', $this->data['page']->slug) . ' ' .  str_replace('/', '', $this->data['page']->template) . ' category_' . $this->data['page']->id_category;
        $this->data['meta_title'] = $this->data['page']->meta_title;
        $this->data['meta_description'] = $this->data['page']->meta_description;
        $this->data['page']->list_articles = $this->tableArticleModel->getArticlesByIdCategory($categoryLight->id, service('switchlanguage')->getIdLocale());

        // print_r($this->data['page']);
        // exit;
        return view($this->get_current_theme_view('__template_part/category_loop', 'default'), $this->data);
    }
}
