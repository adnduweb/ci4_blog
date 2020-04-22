<?php

namespace Adnduweb\Ci4_blog\Entities;

use CodeIgniter\Entity;
use Adnduweb\Ci4_blog\Models\CategoriesModel;

class Article extends Entity
{
    use \Tatter\Relations\Traits\EntityTrait;
    protected $table        = 'articles';
    protected $tableLang    = 'articles_langs';
    protected $tablecArtCat = 'articles_categories';
    protected $primaryKey   = 'id_article';

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

    public function getDescriptionShort(int $id_lang)
    {
        foreach ($this->articles_langs as $lang) {
            if ($id_lang == $lang->id_lang) {
                return $lang->description_short ?? null;
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

    public function getLink($slug = false, int $id_lang)
    {
        foreach ($this->articles_langs as $lang) {
            if ($id_lang == $lang->id_lang) {
                return base_urlFront($slug . '/' . $lang->slug);
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

    public function getImageOneAtt($id_lang, $format = false)
    {
        $image = null;

        if (!empty($this->attributes['picture_one'])) {

            $getAttrOptions = json_decode($this->attributes['picture_one']);;
            if (empty($getAttrOptions))
                return $image;

            $mediasModel = new \App\Models\mediasModel();
            $image = $mediasModel->getMediaById($getAttrOptions->media->id_media, $id_lang);
            if (empty($image)) {
                $image = $mediasModel->where('id_media', $getAttrOptions->media->id_media)->get()->getRow();
            }
            if (is_object($image)) {
                if($format == true){
                    $getAttrOptions->media->filename =  base_url() . '/uploads/'.$format.'/' . $image->namefile;
                    list($width, $height, $type, $attr) =  getimagesize($getAttrOptions->media->filename);
                    $getAttrOptions->media->dimensions = (object)['width' => $width, 'height' => $height];
                    $getAttrOptions->media->format = $format;  
                }
                $image->class = 'adw_lazyload ';
                $image->options = $getAttrOptions;
            }
        }

        //var_dump($image);exit;

        return $image;
    }

    public function getPictureheaderAtt()
    {
        if (!empty($this->attributes['picture_header'])) {
            return json_decode($this->attributes['picture_header']);
        }
        return null;
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
                    'tags'              => isset($v['tags']) ? $v['tags'] : '',
                    'slug'              => uniforme(trim($v['slug'])),
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
                    'tags'              => isset($v['tags']) ? $v['tags'] : '',
                    'slug'              => uniforme(trim($v['slug'])),
                ];
                print_r($data);
                $builder->set($data);
                $builder->where(['id_article' => $this->tableLang->id_article, 'id_lang' => $this->tableLang->id_lang]);
                $builder->update();
            }
        }
    }

    public function saveCategorie($data)
    {
        $db         = \Config\Database::connect();
        $builder    = $db->table($this->tablecArtCat);
        $id_article = $data->id_article;

        $builder->delete(['id_article' => $id_article]);

        foreach ($data->id_categorie as $k => $v) {

            $this->tablecArtCat =  $builder->where(['id_categorie' => $v, 'id_article' => $id_article])->get()->getRow();
            if (empty($this->tablecArtCat)) {
                $data = [
                    'id_article'   =>  $id_article,
                    'id_categorie' => $v,
                    'created_at' => date('Y-m-d H:i:s'),
                ];
                $builder->insert($data);
            }
        }
    }
}
