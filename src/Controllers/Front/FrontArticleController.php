<?php

namespace Adnduweb\Ci4_blog\Controllers\Front;

use CodeIgniter\API\ResponseTrait;
use Adnduweb\Ci4_blog\Entities\Article;
use Adnduweb\Ci4_blog\Models\ArticlesModel;
use Adnduweb\Ci4_blog\Models\CategoriesModel;

class FrontArticleController extends \App\Controllers\Front\FrontController
{
    use \App\Traits\BuilderTrait;
    use \App\Traits\ModuleTrait;

    public $name_module = 'blog';
    protected $idModule;

    public function __construct()
    {
        parent::__construct();
        $this->tableModel  = new ArticlesModel();
        $this->idModule  = $this->getIdModule();
    }
    public function index()
    {
        //Silent
    }

    public function show($slug)
    {

        $articleLight = $this->tableModel->getIdArticleBySlug($slug);

        // 404 car soit elle n'existe pas ou elle n'est pas active
        if (empty($articleLight)) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException(lang('Core.Cannot find the page item : {0}', [$slug]));
        }

        // il n'est pas encore publié
        if ($articleLight->type != '1') {
            return redirect()->to(base_urlFront(), 302);
        }

        $this->data['page'] = $this->tableModel->where('id_article', $articleLight->id_article)->first();

        $this->data['no_follow_no_index'] = ($this->data['page']->no_follow_no_index == 0) ?  'index follow' :  'no-index no-follow';
        $this->data['id']  = str_replace('/', '', $this->data['page']->slug);
        $this->data['class'] = $this->data['class'] . ' ' .  str_replace('/', '', $this->data['page']->slug) . ' ' .  str_replace('/', '', $this->data['page']->template) . ' article_' . $this->data['page']->id_article;
        $this->data['meta_title'] = $this->data['page']->meta_title;
        $this->data['meta_description'] = $this->data['page']->meta_description;
        $builders = $this->getBuilderIdItem($this->data['page']->id_article, $this->idModule);
        if (!empty($builders)) {
            $this->data['page']->builders = $builders;
            $temp = [];
            foreach ($this->data['page']->builders as $builder) {
                $temp[$builder->order] = $builder;
            }
            ksort($temp);
            $this->data['page']->builders = $temp;
        }
        // print_r($this->data['page']->getLangsLink());
        // exit;
        return view($this->get_current_theme_view('__template_part/article_single', 'default'), $this->data);
    }
}
