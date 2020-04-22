<?php

namespace Adnduweb\Ci4_blog\Controllers\Admin;

use App\Controllers\Admin\AdminController;
use App\Libraries\AssetsBO;
use App\Libraries\Tools;
use App\Models\LanguagesModel;
use App\Models\SettingModel;
use CodeIgniter\API\ResponseTrait;
use Adnduweb\Ci4_blog\Entities\Article;
use Adnduweb\Ci4_blog\Models\ArticlesModel;
use Adnduweb\Ci4_blog\Models\CategoriesModel;

class AdminBlogSettingsController extends AdminController
{
    use ResponseTrait;

    public $module = false;
    public $controller = 'settings';
    public $item = 'setting';
    public $pathcontroller  = '/public/blog/settings';
    public $multilangue = false;

    public function __construct()
    {
        parent::__construct();
        $this->tableModel  = new SettingModel();
    }
    public function renderForm($id = null)
    {
        $this->data['form'] =  service('settings');
        $parent = parent::renderForm($id);
        if (is_object($parent) && $parent->getStatusCode() == 307) {
            return $parent;
        }

        $this->data['countList'] = [];
        return view($this->get_current_theme_view('settings/index', 'Adnduweb/Ci4_blog'), $this->data);
    }

    public function postProcessEdit()
    {
        if ($this->request->getPost('user')) {
            $user = $this->request->getPost('user');
            foreach ($user as $k => $v) {
                $this->tableModel->getExist($k, 'user', $v);
                service('settings')->{$k} = $v;
            }
        }
        if ($this->request->getPost('global')) {

            $global = $this->request->getPost('global');

            foreach ($global as $k => $v) {
                if (is_array($v)) {
                    $v = serialize($v);
                }
                cache()->delete('settings:templates:{$k}');
                $this->tableModel->getExist($k, 'global', $v);
                service('settings')->{$k} = $v;
            }
        }

        // Success!
        Tools::set_message('success', lang('Core.save_data'), lang('Core.cool_success'));
        return redirect()->to('/' . CI_SITE_AREA . '/' . user()->id_company . $this->pathcontroller);
    }

}
