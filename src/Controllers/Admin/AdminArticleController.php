<?php

namespace Adnduweb\Ci4_blog\Controllers\Admin;

use App\Controllers\Admin\AdminController;
use App\Libraries\AssetsBO;
use App\Libraries\Tools;
use Adnduweb\Ci4_blog\Entities\Article;
use Adnduweb\Ci4_blog\Models\ArticlesModel;
use Adnduweb\Ci4_blog\Models\CategoriesModel;

/**
 * Class Article
 *
 * @package App\Controllers\Admin
 */
class AdminArticleController extends AdminController
{

    use \App\Traits\BuilderTrait;
    use \App\Traits\ModuleTrait;
    use \Adnduweb\Ci4_blog\Traits\ArticleTrait;

    /**
     * @var \Adnduweb\Ci4_blog\Models\ArticlesModel
     */
    public $tableModel;

    /**
     * @var \Adnduweb\Ci4_blog\Models\CategoriesModel
     */
    private $categories_model;

    public $module = true;
    public $name_module = 'blog';
    protected $idModule;
    public $controller = 'blog';
    public $item = 'blog';
    public $type = 'Adnduweb/Ci4_blog';
    public $pathcontroller  = '/public/blog/articles';
    public $fieldList = 'name';
    public $add = true;
    public $multilangue = true;

    /**
     * Article constructor.
     *
     * @throws \CodeIgniter\Database\Exceptions\DatabaseException
     */
    public function __construct()
    {
        parent::__construct();
        $this->tableModel       = new ArticlesModel();
        $this->categories_model = new CategoriesModel();
        $this->module           = "blog";
        $this->idModule         = $this->getIdModule();
    }


    public function renderViewList()
    {
        AssetsBO::add_js([$this->get_current_theme_view('controllers/' . $this->controller . '/js/listBlog.js', 'default')]);
        $parent =  parent::renderViewList();
        if (is_object($parent) && $parent->getStatusCode() == 307) {
            return $parent;
        }
        return $parent;
    }


    public function ajaxProcessList()
    {
        $parent = parent::ajaxProcessList();
        return $this->respond($parent, 200, lang('Core.liste des articles'));
    }

    public function renderForm($id = null)
    {
        AssetsBO::add_js([$this->get_current_theme_view('plugins/custom/ckeditor/ckeditor-classic.bundle.js', 'default')]);
        AssetsBO::add_js([$this->get_current_theme_view('controllers/medias/js/manager.js', 'default')]);
        AssetsBO::add_js([$this->get_current_theme_view('js/builder.js', 'default')]);
        if (is_null($id)) {
            $this->data['form'] = new Article($this->request->getPost());
        } else {
            $this->data['form'] = $this->tableModel->where('id_article', $id)->first();
            if (empty($this->data['form'])) {
                Tools::set_message('danger', lang('Core.not_{0}_exist', [$this->item]), lang('Core.warning_error'));
                return redirect()->to('/' . env('CI_SITE_AREA') . '/' . user()->id_company . '/public/pages');
            }
        }

        $this->data['form']->builders = [];
        $this->data['form']->id_module = $this->idModule;
        $this->data['form']->id_item = $id;
        if (!empty($this->getBuilderIdItem($id, $this->idModule))) {
            $this->data['form']->builders = $this->getBuilderIdItem($id, $this->idModule);
            $temp = [];
            foreach ($this->data['form']->builders as $builder) {
                $temp[$builder->order] = $builder;
            }
            ksort($temp);
            $this->data['form']->builders = $temp;
        }

        $this->data['form']->gettype = $this->getType();
        $this->data['form']->categories =  $this->categories_model->getlist();
        //print_r($this->data['form']->categories); exit;

        parent::renderForm($id);
        $this->data['edit_title'] =lang('Core.edit_article');
        return view($this->get_current_theme_view('form', 'Adnduweb/Ci4_blog'), $this->data);
    }

    public function postProcessEdit($param)
    {
        // validate
        $rules = [
            'slug' => 'required',
        ];
        if (!$this->validate($rules)) {
            Tools::set_message('danger', $this->validator->getErrors(), lang('Core.warning_error'));
            return redirect()->back()->withInput();
        }

        // Try to create the user
        $articleBase = new Article($this->request->getPost());
        $this->lang = $this->request->getPost('lang');
        $articleBase->slug = "/" . strtolower(preg_replace('/[^a-zA-Z0-9\-]/', '', preg_replace('/\s+/', '-', $articleBase->slug)));

        // Les images
        $articleBase->picture_one = $this->getImagesPrep($articleBase->getPictureOneAtt());
        $articleBase->picture_header = $this->getImagesPrep($articleBase->getPictureheaderAtt());

        if (!$this->tableModel->save($articleBase)) {
            Tools::set_message('danger', $this->tableModel->errors(), lang('Core.warning_error'));
            return redirect()->back()->withInput();
        }
        $articleBase->saveLang($this->lang, $articleBase->id_article);

        // On enregistre le Builder si existe
        $this->saveBuilder($this->request->getPost('builder'));

        // Success!
        Tools::set_message('success', lang('Core.save_data'), lang('Core.cool_success'));
        $redirectAfterForm = [
            'url'                   => '/' . env('CI_SITE_AREA') . '/' . user()->id_company . '/public/blog/articles',
            'action'                => 'edit',
            'submithandler'         => $this->request->getPost('submithandler'),
            'id'                    => $articleBase->id_article,
        ];
        $this->redirectAfterForm($redirectAfterForm);
    }
    public function getImagesPrep($imageJson){
        $options  = [];
        if (!empty($imageJson) || !is_null($imageJson)) {

            if (!in_array($imageJson->media->format, ['thumbnail', 'small', 'medium', 'large'])) {
                if (strpos($imageJson->media->filename, 'custom') === false) {
                    $oldName = pathinfo($imageJson->media->filename);
                    $imageJson->media->filename = base_url() . '/uploads/custom/' . $imageJson->media->format;
                    $imageJson->media->class = $oldName['filename'];
                    $imageJson->media->format = 'custom';
                }
            } else {
                $imageJson->media->class = $imageJson->media->format;
            }

            try {
                $client = \Config\Services::curlrequest();
                $response = $client->request('GET', $imageJson->media->filename);
                list($width, $height, $type, $attr) =  getimagesize($imageJson->media->filename);
                $imageJson->media->dimensions = ['width' => $width, 'height' => $height];
                $options = json_encode($imageJson);
            } catch (\Exception $e) {
                $options = '';
            }
        } else {
            $options = '';
        }
        return $options;
    }

}
