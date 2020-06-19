<?php

namespace Adnduweb\Ci4_blog\Entities;

use CodeIgniter\Entity;
use Adnduweb\Ci4_blog\Models\CategoryModel;
use CodeIgniter\I18n\Time;

class Post extends Entity
{
    use \Tatter\Relations\Traits\EntityTrait;
    use \App\Traits\BuilderEntityTrait;
    protected $table          = 'b_posts';
    protected $tableLang      = 'b_posts_langs';
    protected $tablecArtCat   = 'b_posts_categories';
    protected $primaryKey     = 'id';
    protected $primaryKeyLang = 'post_id';

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


    // public function getSlug()
    // {
    //     return $this->attributes['slug'] ?? null;
    // }

    // public function getBundleSlug(int $id_lang)
    // {
    //     foreach ($this->b_posts_langs as $lang) {
    //         if ($id_lang == $lang->id_lang) {
    //             return $lang->slug ?? null;
    //         }
    //     }
    // }

    // public function getUpdated()
    // {
    //     $format = service('switchlanguage')->getFormat();
    //     $date = (!empty($this->attributes['updated_at'])) ? $this->attributes['updated_at'] : $this->attributes['created_at'];
    //     $date = $this->mutateDate($date);
    //     $timezone = $this->timezone ?? app_timezone();
    //     $date->setTimezone($timezone);
    //     return $date->format($format);
    // }


    public function getLinkArticle($slug = false)
    {
        foreach ($this->b_posts_langs as $lang) {
            if (service('switchlanguage')->getIdLocale() == $lang->id_lang) {
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

    public function getImageOneAtt($format = false)
    {
        $image = null;

        if (!empty($this->attributes['picture_one'])) {

            $getAttrOptions = json_decode($this->attributes['picture_one']);;
            if (empty($getAttrOptions))
                return $image;

            $mediasModel = new \App\Models\mediaModel();
            $image = $mediasModel->getMediaById($getAttrOptions->media->id_media, service('switchlanguage')->getIdLocale());
            if (empty($image)) {
                $image = $mediasModel->where('id_media', $getAttrOptions->media->id_media)->get()->getRow();
            }
            // print_r($image);
            // exit;
            if (is_object($image)) {
                if ($format == true) {
                    $getAttrOptions->media->filename =  base_url() . '/uploads/' . $format . '/' . $image->namefile;
                    list($width, $height, $type, $attr) =  getimagesize($getAttrOptions->media->filename);
                    $getAttrOptions->media->dimensions = (object) ['width' => $width, 'height' => $height];
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

    public function _prepareLang()
    {
        $lang = [];
        if (!empty($this->id)) {
            foreach ($this->b_posts_langs as $tabs_langs) {
                $lang[$tabs_langs->id_lang] = $tabs_langs;
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
            $this->tableLang =  $builder->where(['id_lang' => $k, $this->primaryKeyLang => $key])->get()->getRow();
            // print_r($this->tableLang);
            if (empty($this->tableLang)) {
                $data = [
                    $this->primaryKeyLang   => $key,
                    'id_lang'               => $k,
                    'name'                  => $v['name'],
                    'sous_name'             => $v['sous_name'],
                    'description_short'     => $v['description_short'],
                    'description'           => $v['description'],
                    'meta_title'            => $v['meta_title'],
                    'meta_description'      => $v['meta_description'],
                    'tags'                  => isset($v['tags']) ? $v['tags'] : '',
                    'slug'                  => uniforme(trim($v['slug'])),
                ];
                // Create the new participant
                $builder->insert($data);
            } else {
                $data = [
                    $this->primaryKeyLang   => $this->tableLang->{$this->primaryKeyLang},
                    'id_lang'               => $this->tableLang->id_lang,
                    'name'                  => $v['name'],
                    'sous_name'             => $v['sous_name'],
                    'description_short'     => $v['description_short'],
                    'description'           => $v['description'],
                    'meta_title'            => $v['meta_title'],
                    'meta_description'      => $v['meta_description'],
                    'tags'                  => isset($v['tags']) ? $v['tags'] : '',
                    'slug'                  => uniforme(trim($v['slug'])),
                ];
                print_r($data);
                $builder->set($data);
                $builder->where([$this->primaryKeyLang => $this->tableLang->{$this->primaryKeyLang}, 'id_lang' => $this->tableLang->id_lang]);
                $builder->update();
            }
        }
    }

    public function saveCategorie($data)
    {
        $db         = \Config\Database::connect();
        $builder    = $db->table($this->tablecArtCat);
        $post_id = $data->id;

        $builder->delete(['post_id' => $post_id]);

        foreach ($data->id_category as $k => $v) {

            $this->tablecArtCat =  $builder->where(['category_id' => $v, 'post_id' => $post_id])->get()->getRow();
            if (empty($this->tablecArtCat)) {
                $data = [
                    'post_id'   =>  $post_id,
                    'category_id' => $v
                ];

                $builder->insert($data);
            }
        }
    }
}
