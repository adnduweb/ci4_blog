<?php

namespace Adnduweb\Ci4_blog\Controllers\Admin;

use App\Controllers\Admin\AdminController;
use App\Libraries\AssetsBO;
use App\Libraries\Tools;
use Adnduweb\Ci4_blog\Entities\Post;
use Adnduweb\Ci4_blog\Models\PostModel;
use Adnduweb\Ci4_blog\Models\CategoryModel;
use \CodeIgniter\Test\Fabricator;


/**
 * Class Article
 *
 * @package App\Controllers\Admin
 */
class AdminPostController extends AdminController
{

    use \App\Traits\BuilderModelTrait, \App\Traits\ModuleTrait, \Adnduweb\Ci4_blog\Traits\PostTrait;


    /**
     *  Module Object
     */
    public $module = true;

    /**
     * name controller
     */
    public $controller = 'post';

    /**
     * Localize slug
     */
    public $pathcontroller  = '/posts';

    /**
     * Localize namespace
     */
    public $namespace = 'Adnduweb/Ci4_blog';

    /**
     * Id Module
     */
    protected $idModule;

    /**
     * Localize slug
     */
    public $dirList  = 'blog';

    /**
     * Display default list column
     */
    public $fieldList = 'b_posts.id';

    /**
     * Bouton add
     */
    public $add = true;

    /**
     * Display Multilangue
     */
    public $multilangue = true;

    /**
     * Event fake data
     */
    public $fake = true;

    /**
     * Update item List
     */
    public $toolbarUpdate = true;

    /**
     * @var \Adnduweb\Ci4_blog\Models\PostModel
     */
    public $tableModel;

    /**
     * @var \Adnduweb\Ci4_blog\Models\CategoryModel
     */
    private $categories_model;

    /**
     * Article constructor.
     *
     * @throws \CodeIgniter\Database\Exceptions\DatabaseException
     */
    public function __construct()
    {
        parent::__construct();
        $this->tableModel       = new PostModel();
        $this->categories_model = new CategoryModel();
        $this->idModule         = $this->getIdModule();

        $this->data['paramJs']['baseSegmentAdmin'] = config('Blog')->urlMenuAdmin;
        $this->pathcontroller  = '/' . config('Blog')->urlMenuAdmin . '/' .  $this->dirList . $this->pathcontroller;
    }


    public function renderViewList()
    {
        AssetsBO::add_js([$this->get_current_theme_view('controllers/' . $this->dirList . '/js/listBlog.js', 'default')]);
        $this->data['gettype'] = $this->getType();
        $parent =  parent::renderViewList();
        if (is_object($parent) && $parent->getStatusCode() == 307) {
            return $parent;
        }

        return $parent;
    }


    public function ajaxProcessList()
    {
        $parent = parent::ajaxProcessList();
        return $this->respond($parent, 200, lang('Core.liste des posts'));
    }

    public function renderForm($id = null)
    {
        AssetsBO::add_js([$this->get_current_theme_view('plugins/custom/ckeditor/ckeditor-classic.bundle.js', 'default')]);
        AssetsBO::add_js([$this->get_current_theme_view('controllers/medias/js/manager.js', 'default')]);
        AssetsBO::add_js([$this->get_current_theme_view('js/builder.js', 'default')]);

        if (class_exists('\Adnduweb\Ci4_blog\Controllers\Admin\AdminArticleController'))
            AssetsBO::add_js([$this->get_current_theme_view('controllers/blog/js/builder.js', 'default')]);

        if (class_exists('\Adnduweb\Ci4_diaporama\Controllers\Admin\AdminDiaporamasController'))
            AssetsBO::add_js([$this->get_current_theme_view('controllers/diaporamas/js/builder.js', 'default')]);

        if (is_null($id)) {
            $this->data['form'] = new Post($this->request->getPost());
        } else {
            $this->data['form'] = $this->tableModel->where('id', $id)->first();
            if (empty($this->data['form'])) {
                Tools::set_message('danger', lang('Core.not_{0}_exist', [$this->controller]), lang('Core.warning_error'));
                return redirect()->to('/' . env('CI_SITE_AREA') . '/' . config('Blog')->urlMenuAdmin . '/' . $this->dirList . '/posts');
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

        $this->data['form']->allCategories = $this->categories_model->getAllCategoriesOptionParent();
        $this->data['form']->gettype = $this->getType();
        $this->data['form']->categories =  $this->categories_model->getlist();
        $this->data['form']->getCatByArt = $this->tableModel->getCatByArt($id);
        //print_r($this->data['form']->getposts_categories(service('switchlanguage')->getIdLocale())); exit;
        // print_r($this->data['form']);
        // exit;

        parent::renderForm($id);
        $this->data['edit_title'] = lang('Core.edit_article');
        return view($this->get_current_theme_view('form', $this->namespace), $this->data);
    }

    public function postProcessEdit($param)
    {
        $this->validation->setRules(['lang.1.slug' => 'required']);
        if (!$this->validation->run($this->request->getPost())) {
            Tools::set_message('danger', $this->validation->getErrors(), lang('Core.warning_error'));
            return redirect()->back()->withInput();
        }

        // Try to create the user
        $articleBase = new Post($this->request->getPost());
        $this->lang = $this->request->getPost('lang');

        $articleBase->id_category = $articleBase->id_category_default;
        $articleBase->id_category_default = $articleBase->id_category_default[0];
        $articleBase->user_updated = user()->id;
        $articleBase->active =  1;

        // Les images
        $articleBase->picture_one = $this->getImagesPrep($articleBase->getPictureOneAtt());
        $articleBase->picture_header = $this->getImagesPrep($articleBase->getPictureheaderAtt());

        if (!$this->tableModel->save($articleBase)) {
            Tools::set_message('danger', $this->tableModel->errors(), lang('Core.warning_error'));
            return redirect()->back()->withInput();
        }

        // On enregistre les categories
        $articleBase->saveCategorie($articleBase);

        // On enregistre les langues
        $articleBase->saveLang($this->lang, $articleBase->id);

        // On enregistre le Builder si existe
        $this->saveBuilder($this->request->getPost('builder'));

        // Success!
        Tools::set_message('success', lang('Core.save_data'), lang('Core.cool_success'));
        $redirectAfterForm = [
            'url'                   => '/' . env('CI_SITE_AREA') . '/' . config('Blog')->urlMenuAdmin . '/' . $this->dirList . '/posts',
            'action'                => 'edit',
            'submithandler'         => $this->request->getPost('submithandler'),
            'id'                    => $articleBase->id,
        ];
        $this->redirectAfterForm($redirectAfterForm);
    }

    public function postProcessAdd()
    {

        $this->validation->setRules(['lang.1.slug' => 'required']);
        if (!$this->validation->run($this->request->getPost())) {
            Tools::set_message('danger', $this->validation->getErrors(), lang('Core.warning_error'));
            return redirect()->back()->withInput();
        }

        // Try to create the user
        $articleBase = new Post($this->request->getPost());
        $this->lang = $this->request->getPost('lang');

        $articleBase->id_category = $articleBase->id_category_default;
        $articleBase->id_category_default = $articleBase->id_category_default[0];
        $articleBase->user_id = user()->id;
        $articleBase->user_updated = user()->id;
        $articleBase->active = 1;

        // Les images
        $articleBase->picture_one = $this->getImagesPrep($articleBase->getPictureOneAtt());
        $articleBase->picture_header = $this->getImagesPrep($articleBase->getPictureheaderAtt());

        if (!$this->tableModel->save($articleBase)) {
            Tools::set_message('danger', $this->tableModel->errors(), lang('Core.warning_error'));
            return redirect()->back()->withInput();
        }
        $id = $this->tableModel->insertID();
        $articleBase->id = $id;

        // On enregistre les categories
        $articleBase->saveCategorie($articleBase);

        // On enregistre les langues
        $this->lang = $this->request->getPost('lang');
        $articleBase->saveLang($this->lang, $id);

        // Success!
        Tools::set_message('success', lang('Core.save_data'), lang('Core.cool_success'));
        $redirectAfterForm = [
            'url'                   => '/' . env('CI_SITE_AREA') . '/' . config('Blog')->urlMenuAdmin . '/' . $this->dirList . '/posts',
            'action'                => 'add',
            'submithandler'         => $this->request->getPost('submithandler'),
            'id'                    => $id,
        ];
        $this->redirectAfterForm($redirectAfterForm);
    }

    public function getImagesPrep($imageJson)
    {
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

    public function dupliquer(int $id)
    {
        try {
            $this->tableModel->dupliquer($id);
            Tools::set_message('success', lang('Core.save_data_dupliquer'), lang('Core.cool_success'));
            return redirect()->to('/' . CI_SITE_AREA . $this->pathcontroller);
        } catch (\Exception $e) {
            Tools::set_message('danger', str_replace('::', '->', $e->getMessage()), lang('Core.warning_error'));
            return redirect()->to('/' . CI_SITE_AREA . $this->pathcontroller);
        }
    }

    public function ajaxProcessUpdate()
    {
        if ($value = $this->request->getPost('value')) {
            $data = [];
            if (isset($value['selected']) && !empty($value['selected'])) {
                foreach ($value['selected'] as $selected) {

                    $data[] = [
                        'id' => $selected,
                        'active'     => $value['active'],
                    ];
                }
            }

            if ($this->tableModel->updateBatch($data, 'id')) {
                return $this->respond(['status' => true, 'message' => lang('Js.your_seleted_records_statuses_have_been_updated')], 200);
            } else {
                return $this->respond(['status' => false, 'database' => true, 'display' => 'modal', 'message' => lang('Js.aucun_enregistrement_effectue')], 200);
            }
        }
    }

    public function ajaxProcessUpdateType()
    {
        if ($value = $this->request->getPost('value')) {
            $data = [];
            if (isset($value['selected']) && !empty($value['selected'])) {
                foreach ($value['selected'] as $selected) {

                    $data[] = [
                        'id' => $selected,
                        'type'       => $value['type'],
                    ];
                }
            }

            if ($this->tableModel->updateBatch($data, 'id')) {
                return $this->respond(['status' => true, 'message' => lang('Js.your_seleted_records_statuses_have_been_updated')], 200);
            } else {
                return $this->respond(['status' => false, 'database' => true, 'display' => 'modal', 'message' => lang('Js.aucun_enregistrement_effectue')], 200);
            }
        }
    }


    public function ajaxProcessDelete()
    {
        if ($value = $this->request->getPost('value')) {
            $data = [];
            if (isset($value['selected']) && !empty($value['selected'])) {
                foreach ($value['selected'] as $selected) {
                    $this->tableModel->delete(['id' => $selected]);
                }
                return $this->respond(['status' => true, 'type' => 'success', 'message' => lang('Js.your_selected_records_have_been_deleted')], 200);
            }
        }
        return $this->failUnauthorized(lang('Js.not_autorized'), 400);
    }


    /**
     *
     * Fake Product
     */
    public function fake(int $num = 10)
    {

        $fabricator = new Fabricator($this->tableModel);
        $makeArticle   = $fabricator->make($num);
        $posts = $fabricator->create($num);
        if (!empty($posts)) {
            foreach ($posts as $article) {
                $this->tableModel->fakelang($article->id);
            }
        }
        Tools::set_message('success', lang('Core.fakedata'), lang('Core.cool_success'));
        return redirect()->to('/' . CI_SITE_AREA . $this->pathcontroller);
    }
}
