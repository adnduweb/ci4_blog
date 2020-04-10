<?php

namespace Adnduweb\Ci4_blog\Database\Seeds;

use joshtronic\LoremIpsum;

class BlogSeeder extends \CodeIgniter\Database\Seeder
{
    //\\Adnduweb\\Ci4_blog\\Database\\Seeds\\BlogSeeder
    /**
     * @return mixed|void
     */
    function run()
    {
        $lipsum = new LoremIpsum();
        // Define default project setting templates
        $rows = [
            [
                'id_article'         => 1,
                'id_categorie'       => 1,
                'author_created'     => 1,
                'author_update'      => 1,
                'active'             => 1,
                'important'          => 1,
                'slug'               => '/jkljk-dsfgsdfg-fgsdfgsdfg',
                'picture_one'        => 4,
                'picture_header'     => 5,
                'no_follow_no_index' => 0,
                'type'               => 1,
                'order'              => 0,
                'created_at'         => date('Y-m-d H:i:s'),
            ]

        ];
        $rowsLang = [
            [
                'id_article'        => 1,
                'id_lang'           => 1,
                'name'              => 'Bonjour',
                'description_short' => $lipsum->sentence(),
                'description'       => $lipsum->paragraphs(5),
                'meta_title'        => $lipsum->sentence(),
                'meta_description'  => $lipsum->sentence(),
                'tags'              => 'test,gsdfgsdf,fgfsdgdsfg,fgsdfg'
            ]

        ];

        // Check for and create project setting templates
        //$pages = new PagesModel();
        $db = \Config\Database::connect();
        foreach ($rows as $row) {
            $article = $db->table('articles')->where('id_article', $row['id_article'])->get()->getRow();
            //print_r($article); exit;
            if (empty($article)) {
                // No setting - add the row
                $db->table('articles')->insert($row);
            }
        }

        foreach ($rowsLang as $rowLang) {
            $articlelang = $db->table('articles_langs')->where('id_article', $rowLang['id_article'])->get()->getRow();

            if (empty($articlelang)) {
                // No setting - add the row
                $db->table('articles_langs')->insert($rowLang);
            }
        }

        $lipsum = new LoremIpsum();
        // Define default project setting templates
        $rowsCat = [
            [
                'id_categorie' => 1,
                'parent'       => 0,
                'order'        => 1,
                'created_at'   => date('Y-m-d H:i:s'),
            ]

        ];
        $rowsCatLang = [
            [
                'id_categorie'      => 1,
                'id_lang'           => 1,
                'name'              => 'Défaut',
                'description_short' => $lipsum->sentence()
            ]

        ];

        // Check for and create project setting templates
        //$pages = new PagesModel();
        $db = \Config\Database::connect();
        foreach ($rowsCat as $row) {
            $article = $db->table('categories')->where('id_categorie', $row['id_categorie'])->get()->getRow();
            //print_r($article); exit;
            if (empty($article)) {
                // No setting - add the row
                $db->table('categories')->insert($row);
            }
        }

        foreach ($rowsCatLang as $rowLang) {
            $articlelang = $db->table('categories_langs')->where('id_categorie', $rowLang['id_categorie'])->get()->getRow();

            if (empty($articlelang)) {
                // No setting - add the row
                $db->table('categories_langs')->insert($rowLang);
            }
        }

        $rowsBlogTabs = [
            'id_parent'         => 17,
            'depth'             => 2,
            'left'              => 11,
            'right'             => 18,
            'position'          => 1,
            'section'           => 0,
            'module'            => 'Adnduweb\Ci4_blog',
            'class_name'        => 'AdminBlog',
            'active'            =>  1,
            'icon'              => '',
            'slug'             => 'blog',
            'name_controller'       => ''
        ];

        $rowsBlogTabsLangs = [
            [
                'id_lang'         => 1,
                'name'             => 'actualités',
            ],
            [
                'id_lang'         => 2,
                'name'             => 'actualités',
            ],
        ];


        $rowsArticlesTabs = [
            'depth'             => 3,
            'left'              => 12,
            'right'             => 13,
            'position'          => 1,
            'section'           => 0,
            'module'            => 'Adnduweb\Ci4_blog',
            'class_name'        => 'AdminArticles',
            'active'            =>  1,
            'icon'              => '',
            'slug'             => 'blog/articles',
            'name_controller'       => ''
        ];

        $rowsArticlesTabsLangs = [
            [
                'id_lang'         => 1,
                'name'             => 'articles',
            ],
            [
                'id_lang'         => 2,
                'name'             => 'articles',
            ],
        ];

        $rowsCatTabs = [
            'depth'             => 3,
            'left'              => 14,
            'right'             => 15,
            'position'          => 1,
            'section'           => 0,
            'module'            => 'Adnduweb\Ci4_blog',
            'class_name'        => 'AdminCategorie',
            'active'            =>  1,
            'icon'              => '',
            'slug'             => 'blog/categories',
            'name_controller'       => ''
        ];

        $rowsCatTabsLangs = [
            [
                'id_lang'         => 1,
                'name'             => 'catégories',
            ],
            [
                'id_lang'         => 2,
                'name'             => 'catégories',
            ],
        ];

        $rowsTagsTabs = [
            'depth'             => 3,
            'left'              => 16,
            'right'             => 17,
            'position'          => 1,
            'section'           => 0,
            'module'            => 'Adnduweb\Ci4_blog',
            'class_name'        => 'AdminTags',
            'active'            =>  1,
            'icon'              => '',
            'slug'             => 'blog/tags',
            'name_controller'       => ''
        ];

        $rowsTagsTabsLangs = [
            [
                'id_lang'         => 1,
                'name'             => 'tags',
            ],
            [
                'id_lang'         => 2,
                'name'             => 'tags',
            ],
        ];


        $tabBlog = $db->table('tabs')->where('class_name', $rowsBlogTabs['class_name'])->get()->getRow();
        //print_r($tab); exit;
        if (empty($tabBlog)) {
            // No setting - add the row
            $db->table('tabs')->insert($rowsBlogTabs);
            $newInsert = $db->insertID();
            $i = 0;
            foreach ($rowsBlogTabsLangs as $rowLang) {
                $rowLang['tab_id']   = $newInsert;
                // No setting - add the row
                $db->table('tabs_langs')->insert($rowLang);
                $i++;
            }

            // on insere les articles
            $tabArticles = $db->table('tabs')->where('class_name', $rowsArticlesTabs['class_name'])->get()->getRow();
            //print_r($tab); exit;
            if (empty($tabArticles)) {
                // No setting - add the row
                $rowsArticlesTabs['id_parent']  = $newInsert;
                $db->table('tabs')->insert($rowsArticlesTabs);
                $newInsertArt = $db->insertID();
                $i = 0;
                foreach ($rowsArticlesTabsLangs as $rowLang) {
                    $rowLang['tab_id']   = $newInsertArt;
                    // No setting - add the row
                    $db->table('tabs_langs')->insert($rowLang);
                    $i++;
                }
            }

            // On Insére les categories
            $tabCategorie = $db->table('tabs')->where('class_name', $rowsCatTabs['class_name'])->get()->getRow();
            //print_r($tab); exit;
            if (empty($tabCategorie)) {
                // No setting - add the row
                $rowsCatTabs['id_parent']  = $newInsert;
                $db->table('tabs')->insert($rowsCatTabs);
                $newInsertCat = $db->insertID();
                $i = 0;
                foreach ($rowsCatTabsLangs as $rowLang) {
                    $rowLang['tab_id']   = $newInsertCat;
                    // No setting - add the row
                    $db->table('tabs_langs')->insert($rowLang);
                    $i++;
                }
            }

            // On Insére les Tags
            $tabTag = $db->table('tabs')->where('class_name', $rowsTagsTabs['class_name'])->get()->getRow();
            //print_r($tab); exit;
            if (empty($tabTag)) {
                // No setting - add the row
                $rowsTagsTabs['id_parent']  = $newInsert;
                $db->table('tabs')->insert($rowsTagsTabs);
                $newInsertTags = $db->insertID();
                $i = 0;
                foreach ($rowsTagsTabsLangs as $rowLang) {
                    $rowLang['tab_id']   = $newInsertTags;
                    // No setting - add the row
                    $db->table('tabs_langs')->insert($rowLang);
                    $i++;
                }
            }
        }


        /**
         *
         * Gestion des permissions
         */
        $rowsPermissionsArticles = [
            [
                'name'              => 'Articles::views',
                'description'       => 'Voir les articles',
                'is_natif'          => '0',
            ],
            [
                'name'              => 'Articles::create',
                'description'       => 'Créer des articles',
                'is_natif'          => '0',
            ],
            [
                'name'              => 'Articles::edit',
                'description'       => 'Modifier les articles',
                'is_natif'          => '0',
            ],
            [
                'name'              => 'Articles::delete',
                'description'       => 'Supprimer des articles',
                'is_natif'          => '0',
            ]
        ];

        // On insére le role par default au user
        foreach ($rowsPermissionsArticles as $row) {
            $tabRow =  $db->table('auth_permissions')->where(['name' => $row['name']])->get()->getRow();
            if (empty($tabRow)) {
                // No langue - add the row
                $db->table('auth_permissions')->insert($row);
            }
        }

        //Gestion des module
        $rowsModulePages = [
            'name'       => 'blog',
            'namespace'  => 'Adnduweb\Ci4_blog',
            'active'     => 1,
            'version'    => '1.0.2',
            'created_at' =>  date('Y-m-d H:i:s')
        ];

        $tabRow =  $db->table('modules')->where(['name' => $rowsModulePages['name']])->get()->getRow();
        if (empty($tabRow)) {
            // No langue - add the row
            $db->table('modules')->insert($rowsModulePages);
        }
    }
}
