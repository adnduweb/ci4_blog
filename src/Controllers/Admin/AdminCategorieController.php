<?php

namespace Adnduweb\Ci4_blog\Controllers\Admin;

use App\Controllers\Admin\AdminController;
use App\Libraries\AssetsBO;
use App\Libraries\Tools;
use Adnduweb\Ci4_blog\Entities\Categorie;
use Adnduweb\Ci4_blog\Models\ArticlesModel;
use Adnduweb\Ci4_blog\Models\CategoriesModel;

/**
 * Class Article
 *
 * @package App\Controllers\Admin
 */
class AdminCategorieController extends AdminController
{

    use \App\Traits\BuilderTrait;
    use \App\Traits\ModuleTrait;
    use \Adnduweb\Ci4_blog\Traits\ArticleTrait;

    /**
     * @var \Adnduweb\Ci4_blog\Models\CategoriesModel
     */
    public $tableModel;

    /**
     * @var \Adnduweb\Ci4_blog\Models\ArticlesModel
     */
    private $articles_model;

    public $module = true;
    public $name_module = 'blog';
    protected $idModule;
    public $controller = 'blog';
    public $item = 'blog';
    public $type = 'Adnduweb/Ci4_blog';
    public $pathcontroller  = '/public/blog/categories';
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
        $this->tableModel     = new CategoriesModel();
        $this->articles_model = new ArticlesModel();
        $this->module         = "blog";
        $this->idModule       = $this->getIdModule();
    }


    public function renderViewList()
    {
        AssetsBO::add_js([$this->get_current_theme_view('controllers/' . $this->controller . '/js/listCat.js', 'default')]);
        helper('form');

        if (!has_permission(ucfirst($this->controller) . '::views', user()->id)) {
            Tools::set_message('danger', lang('Core.not_acces_permission'), lang('Core.warning_error'));
            return redirect()->to('/' . CI_SITE_AREA . '/' . user()->id_company . '/dashboard');
        }
        $this->data['nameController'] = lang('Core.' . $this->controller);
        $this->data['addPathController'] = $this->pathcontroller . '/add';
        if (isset($this->add) && $this->add == true)
            $this->data['add'] = lang('Core.add_' . $this->item);
        $this->data['countList'] = $this->tableModel->getAllCount(['field' => $this->fieldList, 'sort' => 'ASC'], []);

        return view($this->get_current_theme_view('categorie/index', $this->type), $this->data);
    }


    public function ajaxProcessList()
    {
        $parent = parent::ajaxProcessList();
        return $this->respond($parent, 200, lang('Core.liste des categories'));
    }

    public function renderForm($id = null)
    {
        if (is_null($id)) {
            $this->data['form'] = new Categorie($this->request->getPost());
        } else {
            $this->data['form'] = $this->tableModel->where('id_categorie', $id)->first();
            if (empty($this->data['form'])) {
                Tools::set_message('danger', lang('Core.not_{0}_exist', [$this->item]), lang('Core.warning_error'));
                return redirect()->to('/' . env('CI_SITE_AREA') . '/' . user()->id_company . '/public/blog/categories');
            }
        }

        $this->data['form']->allCategories = $this->tableModel->getAllCategoriesOptionParent();
        $this->data['form']->id_module = $this->idModule;
        $this->data['form']->id_item = $id;
        $this->data['form']->categories = $this->tableModel->join('categories_langs', 'categories.id_categorie = categories_langs.id_categorie')->where('id_lang', 1)->orderBy('name', 'ACS')->get()->getResult();
        //print_r($this->data['form']); exit;

        parent::renderForm($id);
        $this->data['edit_title'] = lang('Core.edit_categorie');
        return view($this->get_current_theme_view('categorie/form', 'Adnduweb/Ci4_blog'), $this->data);
    }

    public function postProcessEdit($param)
    {

        // Try to create the user
        $categorieBase = new Categorie($this->request->getPost());
        $this->lang = $this->request->getPost('lang');
        $categorieBase->active = isset($categorieBase->active) ? 1 : 0;

        if (!$this->tableModel->save($categorieBase)) {
            Tools::set_message('danger', $this->tableModel->errors(), lang('Core.warning_error'));
            return redirect()->back()->withInput();
        }
        $categorieBase->saveLang($this->lang, $categorieBase->id_categorie);

        // Success!
        Tools::set_message('success', lang('Core.save_data'), lang('Core.cool_success'));
        $redirectAfterForm = [
            'url'                   => '/' . env('CI_SITE_AREA') . '/' . user()->id_company . '/public/blog/categories',
            'action'                => 'edit',
            'submithandler'         => $this->request->getPost('submithandler'),
            'id'                    => $categorieBase->id_categorie,
        ];
        $this->redirectAfterForm($redirectAfterForm);
    }

    public function postProcessAdd($param)
    {
        // Try to create the user
        $categorieBase = new Categorie($this->request->getPost());
        $this->lang = $this->request->getPost('lang');

        if (!$this->tableModel->save($categorieBase)) {
            Tools::set_message('danger', $this->tableModel->errors(), lang('Core.warning_error'));
            return redirect()->back()->withInput();
        }
        $id_categorie = $this->tableModel->insertID();
        $categorieBase->saveLang($this->lang, $id_categorie);

        // Success!
        Tools::set_message('success', lang('Core.save_data'), lang('Core.cool_success'));
        $redirectAfterForm = [
            'url'                   => '/' . env('CI_SITE_AREA') . '/' . user()->id_company . '/public/blog/categories',
            'action'                => 'add',
            'submithandler'         => $this->request->getPost('submithandler'),
            'id'                    => $id_categorie,
        ];
        $this->redirectAfterForm($redirectAfterForm);
    }

    public function ajaxProcessUpdate()
    {
        if ($value = $this->request->getPost('value')) {
            $data = [];
            if (isset($value['selected']) && !empty($value['selected'])) {
                foreach ($value['selected'] as $selected) {

                    $data[] = [
                        'id_categorie' => $selected,
                        'active'       => $value['active'],
                    ];
                }
            }
            if ($this->tableModel->updateBatch($data, 'id_categorie')) {
                return $this->respond(['status' => true, 'type' => 'success', 'message' => lang('Js.your_seleted_records_statuses_have_been_updated')], 200);
            } else {
                return $this->respond(['status' => false, 'database' => true, 'display' => 'modal', 'message' => lang('Js.aucun_enregistrement_effectue')], 200);
            }
        }
    }

    public function ajaxProcessDelete()
    {
        if ($value = $this->request->getPost('value')) {
            if (!empty($value['selected'])) {
                $default = false;

                foreach ($value['selected'] as $id) {
                    if ($id == '1') {
                        $default = true;
                        break;
                    } else {
                        // On regarde si le groupe est déja affecté
                        if ($this->tableModel->changeArticlesIncat($id) == false) {
                            $this->tableModel->delete($id);
                        } else {
                            return $this->respond(['status' => false, 'type' => 'warning', 'message' => lang('Js.not_action_default')], 200);
                        }
                    }
                }
                if ($default == true) {
                    return $this->respond(['status' => false, 'type' => 'warning', 'message' => lang('Js.not_delete_group')], 200);
                } else {
                    return $this->respond(['status' => true, 'type' => 'success', 'message' => lang('Js.your_selected_records_have_been_deleted')], 200);
                }
            }
        }
        return $this->failUnauthorized(lang('Js.not_autorized'), 400);
    }
}
