<?php

namespace Adnduweb\Ci4_blog\Entities;

use CodeIgniter\Entity;

class Article extends Entity
{
    use \Tatter\Relations\Traits\EntityTrait;
    protected $table      = 'articles';
    protected $tableLang  = 'articles_langs';
    protected $primaryKey = 'id_article';

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
        return $this->id_article ?? null;
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
        foreach ($this->articles_langs as $lang) {
            if ($id_lang == $lang->id_lang) {
                return $lang->name ?? null;
            }
        }
    }

    public function getDescription(int $id_lang)
    {
        foreach ($this->articles_langs as $lang) {
            if ($id_lang == $lang->id_lang) {
                return $lang->description ?? null;
            }
        }
    }

    public function get_MetaDescription(int $id_lang)
    {
        foreach ($this->articles_langs as $lang) {
            if ($id_lang == $lang->id_lang) {
                return $lang->metat_description ?? null;
            }
        }
    }

    public function get_MetaTitle(int $id_lang)
    {
        foreach ($this->articles_langs as $lang) {
            if ($id_lang == $lang->id_lang) {
                return $lang->meta_title ?? null;
            }
        }
    }

    public function getPictureOneAtt()
    {
        if (!empty($this->attributes['picture_one'])) {
            return json_decode($this->attributes['picture_one']);
        }
        return null;
    }

    public function getPictureheaderAtt()
    {
        if (!empty($this->attributes['picture_header'])) {
            return json_decode($this->attributes['picture_header']);
        }
        return null;
    }

    public function getBuilder(string $id_field, int $id_lang)
    {
        foreach ($this->builders as $builder) {
            if ($id_field == $builder->id_field) {
                foreach ($builder->builders_langs as $lang) {
                    if ($id_lang == $lang->id_lang) {
                        $builder->id_lang = $lang->id_lang;
                        $builder->content = $lang->content;
                    }
                }
                unset($builder->builders_langs);
                return $builder ?? null;
            }
            return false;
        }
    }

    public function getBuilderContent(string $id_field, int $id_lang)
    {
        if (!empty($this->builders)) {
            foreach ($this->builders as $builder) {
                if ($id_field == $builder->id_field) {
                    foreach ($builder->builders_langs as $lang) {
                        if ($id_lang == $lang->id_lang) {
                            return $lang->content ?? null;
                        }
                    }
                }
                return null;
            }
            return null;
        }
    }

    public function getTextarea(string $handle, int $id_lang)
    {
        if (!empty($this->builders)) {
            $i = 0;
            foreach ($this->builders as $builder) {
                if ($handle == $builder->handle && $builder->type == "textarea") {
                    foreach ($builder->builders_langs as $lang) {
                        if ($id_lang == $lang->id_lang) {
                            return $lang->content ?? null;
                        }
                    }
                }
                $i++;
            }
            return null;
        }
    }
    public function getTitle(string $handle, int $id_lang)
    {
        if (!empty($this->builders)) {
            $i = 0;
            foreach ($this->builders as $builder) {
                if ($handle == $builder->handle && $builder->type == "textfield") {
                    foreach ($builder->builders_langs as $lang) {
                        if ($id_lang == $lang->id_lang) {
                            return $lang->content ?? null;
                        }
                    }
                }
                $i++;
            }
            return null;
        }
    }

    public function getImage(string $handle, int $id_lang)
    {
        $image = null;
        if (!empty($this->builders)) {
            $i = 0;

            foreach ($this->builders as $builder) {
                if ($handle == $builder->handle && $builder->type == "imagefield") {

                    $getAttrOptions = $getAttrOptions = $builder->getAttrOptions();
                    if (empty($getAttrOptions))
                        return $image;

                    $mediasModel = new \App\Models\mediasModel();
                    $image = $mediasModel->getMediaById($getAttrOptions->media->id_media, $id_lang);
                    if (empty($image)) {
                        $image = $mediasModel->where('id_media', $getAttrOptions->media->id_media)->get()->getRow();
                    }
                    if (is_object($image))
                        $image->options = $getAttrOptions;
                }
                $i++;
            }
        }

        return $image;
    }


    public function getNameAllLang()
    {
        $name = [];
        $i = 0;
        foreach ($this->articles_langs as $lang) {
            $name[$lang->id_lang]['name'] = $lang->name;
            $i++;
        }
        return $name ?? null;
    }


    public function _prepareLang()
    {
        $lang = [];
        if (!empty($this->id_article)) {
            foreach ($this->articles_langs as $tabs_lang) {
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
            $this->tableLang =  $builder->where(['id_lang' => $k, 'id_article' => $key])->get()->getRow();
            // print_r($this->tableLang);
            if (empty($this->tableLang)) {
                $data = [
                    'id_article'      => $key,
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
                    'id_article' => $this->tableLang->id_article,
                    'id_lang'      => $this->tableLang->id_lang,
                    'name'              => $v['name'],
                    'description_short' => $v['description_short'],
                    'description'       => $v['description'],
                    'meta_title'        => $v['meta_title'],
                    'meta_description'  => $v['meta_description'],
                ];
                print_r($data);
                $builder->set($data);
                $builder->where(['id_article' => $this->tableLang->id_article, 'id_lang' => $this->tableLang->id_lang]);
                $builder->update();
            }
        }
    }
}
