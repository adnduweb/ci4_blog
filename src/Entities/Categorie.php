<?php

namespace Spreadaurora\Ci4_blog\Entities;

use CodeIgniter\Entity;

class Categorie extends Entity
{
    use \Tatter\Relations\Traits\EntityTrait;
    protected $table      = 'categories';
    protected $tableLang  = 'categories_langs';
    protected $primaryKey = 'id_categorie';

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
        return $this->id_categorie ?? null;
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
        foreach ($this->categories_langs as $lang) {
            if ($id_lang == $lang->id_lang) {
                return $lang->name ?? null;
            }
        }
    }

    public function getDescription(int $id_lang)
    {
        foreach ($this->categories_langs as $lang) {
            if ($id_lang == $lang->id_lang) {
                return $lang->description ?? null;
            }
        }
    }

    public function get_MetaDescription(int $id_lang)
    {
        foreach ($this->categories_langs as $lang) {
            if ($id_lang == $lang->id_lang) {
                return $lang->metat_description ?? null;
            }
        }
    }

    public function get_MetaTitle(int $id_lang)
    {
        foreach ($this->categories_langs as $lang) {
            if ($id_lang == $lang->id_lang) {
                return $lang->meta_title ?? null;
            }
        }
    } 


    public function _prepareLang()
    {
        $lang = [];
        if (!empty($this->id_categorie)) {
            foreach ($this->categories_langs as $tabs_lang) {
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
            $this->tableLang =  $builder->where(['id_lang' => $k, 'id_categorie' => $key])->get()->getRow();
            // print_r($this->tableLang);
            if (empty($this->tableLang)) {
                $data = [
                    'id_categorie'      => $key,
                    'id_lang'           => $k,
                    'name'              => $v['name'],
                    'description_short' => $v['description_short'],
                    'description'       => $v['description'],
                    'meta_title'        => $v['meta_title'],
                    'meta_description'  => $v['meta_description'],
                ];
                // Create the new participant
                $builder->insert($data);
            } else {
                $data = [
                    'id_categorie' => $this->tableLang->id_categorie,
                    'id_lang'      => $this->tableLang->id_lang,
                    'name'              => $v['name'],
                    'description_short' => $v['description_short'],
                    'description'       => $v['description'],
                    'meta_title'        => $v['meta_title'],
                    'meta_description'  => $v['meta_description'],
                ];
                print_r($data);
                $builder->set($data);
                $builder->where(['id_categorie' => $this->tableLang->id_categorie, 'id_lang' => $this->tableLang->id_lang]);
                $builder->update();
            }
        }
    }
}
