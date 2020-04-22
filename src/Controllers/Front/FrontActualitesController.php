<?php

namespace Adnduweb\Ci4_blog\Controllers\Front;

use CodeIgniter\API\ResponseTrait;
use Adnduweb\Ci4_blog\Entities\Article;
use Adnduweb\Ci4_blog\Models\ArticlesModel;
use Adnduweb\Ci4_blog\Models\CategoriesModel;

class FrontActualitesController extends \App\Controllers\Front\FrontController
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

    public function show()
    {
        $loccale = 1;
        $setting_supportedLocales = unserialize(service('Settings')->setting_supportedLocales);
        foreach ($setting_supportedLocales as $setting_supportedLocale) {
            $v = explode('|', $setting_supportedLocale);
            if ($this->request->getLocale() == $v[1]) {
                $loccale = $v[0];
            }
        }
        $this->data['actu'] = $this->tableModel->get()->getResult();
        if (empty($this->data['actu'])) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException(lang('Core.Cannot find the actu item : {0}', []));
        }

        $this->data['no_follow_no_index'] = ($this->data['actu']->no_follow_no_index == 0) ?  'index follow' :  'no-index no-follow';
        $this->data['id']  = str_replace('/', '', $this->data['actu']->slug);
        $this->data['class'] = $this->data['class'] . ' ' .  str_replace('/', '', $this->data['actu']->slug) . ' ' .  str_replace('/', '', $this->data['actu']->template);
        $this->data['meta_title'] = $this->data['actu']->meta_title;
        $this->data['meta_description'] = $this->data['actu']->meta_description;
        $this->data['actuContent'] = $this->data['actu'];
        $this->data['actuContent']->builders = [];
        if (!empty($this->getBuilderIdItem($this->data['actu']->id_actu, $this->idModule))) {
            $this->data['form']->builders = $this->getBuilderIdItem($id, $this->idModule);
            $temp = [];
            foreach ($this->data['actuContent']->builders as $builder) {
                $temp[$builder->order] = $builder;
            }
            ksort($temp);
            $this->data['actuContent']->builders = $temp;
        }

        if ($this->data['actu']->template == 'code') {
            return view($this->get_current_theme_view($this->data['actu']->slug, 'default'), $this->data);
        } else {
            return view($this->get_current_theme_view('actu', 'Adnduweb/Ci4_blog'), $this->data);
        }
    }
}
