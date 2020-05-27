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
        $locale = 1;
        $setting_supportedLocales = unserialize(service('Settings')->setting_supportedLocales);
        foreach ($setting_supportedLocales as $setting_supportedLocale) {
            $v = explode('|', $setting_supportedLocale);
            if ($this->request->getLocale() == $v[1]) {
                $locale = $v[0];
            }
        }

        $articleLight = $this->tableModel->getIdArticleBySlug($slug);
        if (empty($articleLight)) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException(lang('Core.Cannot find the page item : {0}', [$slug]));
        }

        // check cache
        if (env('cache.active') == true) {
            if ($this->page = cache("pages:{$articleLight->id_article}")) {
                $this->data['page'] = $this->page;
            } else {
                $this->data['page'] = $this->tableModel->where('id_article', $articleLight->id_article)->first();
                $this->cache("pages:{$articleLight->id_article}", $this->data['page']);
            }
        }

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
        // print_r($this->data['page']->builders);
        // exit;
        return view($this->get_current_theme_view('__template_part/article_single', 'default'), $this->data);
    }
}
