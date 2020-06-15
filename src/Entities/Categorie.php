<?php

namespace Adnduweb\Ci4_blog\Entities;

use CodeIgniter\Entity;

class Categorie extends Entity
{
    use \Tatter\Relations\Traits\EntityTrait;
    use \App\Traits\BuilderEntityTrait;
    protected $table      = 'b_category';
    protected $tableLang  = 'b_category_lang';
    protected $primaryKey = 'id_category';

    protected $datamap = [];
    /**
     * Define properties that are automatically converted to Time instances.
     */
    protected $dates = ['created_at', 'updated_at', 'deleted_at'];
    /**
     * Array of field names and the type of value to cast them as
     * when they are accessed.
     */
    protected $casts = [];

    public function getId()
    {
        return $this->id_category ?? null;
    }

    public function getClassEntities()
    {
        return $this->table;
    }

    public function getName()
    {
        return $this->attributes['name'] ?? null;
    }
    public function getSlug()
    {
        return $this->attributes['slug'] ?? null;
    }

    public function getNameLang(int $id_lang)
    {
        foreach ($this->b_category_lang as $lang) {
            if ($id_lang == $lang->id_lang) {
                return $lang->name ?? null;
            }
        }
    }

    public function getDescription()
    {
        foreach ($this->b_category_lang as $lang) {
            if (service('switchlanguage')->getIdLocale() == $lang->id_lang) {
                return $lang->description ?? null;
            }
        }
    }

    public function get_MetaDescription()
    {
        foreach ($this->b_category_lang as $lang) {
            if (service('switchlanguage')->getIdLocale() == $lang->id_lang) {
                return $lang->metat_description ?? null;
            }
        }
    }

    public function get_MetaTitle()
    {
        foreach ($this->b_category_lang as $lang) {
            if (service('switchlanguage')->getIdLocale() == $lang->id_lang) {
                return $lang->meta_title ?? null;
            }
        }
    }


    public function _prepareLang()
    {
        $lang = [];
        if (!empty($this->id_category)) {
            foreach ($this->b_category_lang as $tabs_lang) {
                $lang[$tabs_lang->id_lang] = $tabs_lang;
            }
        }
        return $lang;
    }
    public function saveLang(array $data, int $key)
    {
        //print_r($data);
        $db      = \Config\Database::connect();
        $builder = $db->table($this->tableLang);
        foreach ($data as $k => $v) {
            $this->tableLang =  $builder->where(['id_lang' => $k, 'id_category' => $key])->get()->getRow();
            if (empty($this->tableLang)) {
                $data = [
                    'id_category'      => $key,
                    'id_lang'           => $k,
                    'name'              => $v['name'],
                    'description_short' => $v['description_short'],
                    'meta_title' => $v['meta_title'],
                    'meta_description' => $v['meta_description'],
                    'slug' => strtolower(preg_replace('/[^a-zA-Z0-9\-]/', '', preg_replace('/\s+/', '-', trim($v['slug']))))
                ];
                $builder->insert($data);
            } else {
                $data = [
                    'id_category' => $this->tableLang->id_category,
                    'id_lang'      => $this->tableLang->id_lang,
                    'name'              => $v['name'],
                    'description_short' => $v['description_short'],
                    'meta_title' => $v['meta_title'],
                    'meta_description' => $v['meta_description'],
                    'slug' => strtolower(preg_replace('/[^a-zA-Z0-9\-]/', '', preg_replace('/\s+/', '-', trim($v['slug']))))
                ];
                $builder->set($data);
                $builder->where(['id_category' => $this->tableLang->id_category, 'id_lang' => $this->tableLang->id_lang]);
                $builder->update();
            }
        }
    }
}
