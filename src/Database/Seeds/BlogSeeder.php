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
                'id'                  => 1,
                'id_category_default' => 1,
                'user_id'             => 1,
                'user_updated'        => 1,
                'active'              => 1,
                'important'           => 1,
                'picture_one'         => null,
                'picture_header'      => null,
                'no_follow_no_index'  => 0,
                'type'                => 1,
                'order'               => 0,
                'created_at'          => date('Y-m-d H:i:s'),
            ]

        ];
        $rowsLang = [
            [
                'post_id'        => 1,
                'id_lang'           => 1,
                'name'              => 'Bonjour',
                'sous_name'         => 'Bonjour',
                'description_short' => $lipsum->sentence(),
                'description'       => $lipsum->paragraphs(5),
                'meta_title'        => $lipsum->sentence(),
                'meta_description'  => $lipsum->sentence(),
                'tags'              => 'test,gsdfgsdf,fgfsdgdsfg,fgsdfg',
                'slug'              => 'jkljk-dsfgsdfg-fgsdfgsdfg',
            ]

        ];

        // Check for and create project setting templates
        //$pages = new PageModel();
        $db = \Config\Database::connect();
        foreach ($rows as $row) {
            $article = $db->table('b_posts')->where('id', $row['id'])->get()->getRow();
            //print_r($article); exit;
            if (empty($article)) {
                // No setting - add the row
                $db->table('b_posts')->insert($row);
            }
        }

        foreach ($rowsLang as $rowLang) {
            $articlelang = $db->table('b_posts_langs')->where('post_id', $rowLang['post_id'])->get()->getRow();

            if (empty($articlelang)) {
                // No setting - add the row
                $db->table('b_posts_langs')->insert($rowLang);
            }
        }

        $lipsum = new LoremIpsum();
        // Define default project setting templates
        $rowsCat = [
            [
                'id' => 1,
                'id_parent'    => 0,
                'order'        => 1,
                'active'       => 1,
                'created_at'   => date('Y-m-d H:i:s'),
            ]

        ];
        $rowsCatLang = [
            [
                'category_id'      => 1,
                'id_lang'           => 1,
                'name'              => 'Défaut',
                'description_short' => $lipsum->sentence(),
                'meta_title'        => $lipsum->sentence(),
                'meta_description'  => $lipsum->sentence(),
                'tags'              => 'test,gsdfgsdf,fgfsdgdsfg,fgsdfg',
                'slug'              => 'default'
            ]

        ];

        // Check for and create project setting templates
        //$pages = new PageModel();
        $db = \Config\Database::connect();
        foreach ($rowsCat as $row) {
            $article = $db->table('b_categories')->where('id', $row['id'])->get()->getRow();
            //print_r($article); exit;
            if (empty($article)) {
                // No setting - add the row
                $db->table('b_categories')->insert($row);
            }
        }

        foreach ($rowsCatLang as $rowLang) {
            $articlelang = $db->table('b_categories_langs')->where('category_id', $rowLang['category_id'])->get()->getRow();

            if (empty($articlelang)) {
                // No setting - add the row
                $db->table('b_categories_langs')->insert($rowLang);
            }
        }

        //Association d'article et de categorie
        $rowsCatArt = [
            'post_id'   => 1,
            'category_id' => 1

        ];

        $rowsCatArtItem = $db->table('b_posts_categories')->where('post_id', $rowsCatArt['post_id'])->get()->getRow();
        //print_r($article); exit;
        if (empty($rowsCatArtItem)) {
            // No setting - add the row
            $db->table('b_posts_categories')->insert($rowsCatArt);
        }


        // gestionde l'application
        $rowsBlogTabs = [
            'id_parent'       => 17,
            'depth'           => 2,
            'left'            => 11,
            'right'           => 19,
            'position'        => 1,
            'section'         => 0,
            'namespace'       => 'Adnduweb\Ci4_blog',
            'class_name'      => '',
            'active'          => 1,
            'icon'            => '',
            'slug'            => 'blog',
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
            'depth'           => 3,
            'left'            => 12,
            'right'           => 13,
            'position'        => 1,
            'section'         => 0,
            'namespace'       => 'Adnduweb\Ci4_blog',
            'class_name'      => 'post',
            'active'          => 1,
            'icon'            => '',
            'slug'            => 'blog/posts',
        ];

        $rowsArticlesTabsLangs = [
            [
                'id_lang'         => 1,
                'name'             => 'articles',
            ],
            [
                'id_lang'         => 2,
                'name'             => 'posts',
            ],
        ];

        $rowsCatTabs = [
            'depth'           => 3,
            'left'            => 14,
            'right'           => 15,
            'position'        => 1,
            'section'         => 0,
            'namespace'       => 'Adnduweb\Ci4_blog',
            'class_name'      => 'category',
            'active'          => 1,
            'icon'            => '',
            'slug'            => 'blog/categories',
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


        $tabBlog = $db->table('tabs')->where('class_name', $rowsBlogTabs['class_name'])->where('namespace', $rowsBlogTabs['namespace'])->get()->getRow();
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
            $tabArticles = $db->table('tabs')->where('class_name', $rowsArticlesTabs['class_name'])->where('namespace', $rowsArticlesTabs['namespace'])->get()->getRow();
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
            $tabCategorie = $db->table('tabs')->where('class_name', $rowsCatTabs['class_name'])->where('namespace', $rowsCatTabs['namespace'])->get()->getRow();
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
        }


        /**
         *
         * Gestion des permissions
         */
        $rowsPermissionsBlog = [
            [
                'name'              => 'Post::view',
                'description'       => 'Voir les articles',
                'is_natif'          => '0',
            ],
            [
                'name'              => 'Post::create',
                'description'       => 'Créer des articles',
                'is_natif'          => '0',
            ],
            [
                'name'              => 'Post::edit',
                'description'       => 'Modifier les articles',
                'is_natif'          => '0',
            ],
            [
                'name'              => 'Post::delete',
                'description'       => 'Supprimer des articles',
                'is_natif'          => '0',
            ],
            [
                'name'              => 'PostCategorie::view',
                'description'       => 'Voir les categories',
                'is_natif'          => '0',
            ],
            [
                'name'              => 'PostCategorie::create',
                'description'       => 'Créer des categories',
                'is_natif'          => '0',
            ],
            [
                'name'              => 'PostCategorie::edit',
                'description'       => 'Modifier les categories',
                'is_natif'          => '0',
            ],
            [
                'name'              => 'PostCategorie::delete',
                'description'       => 'Supprimer des categories',
                'is_natif'          => '0',
            ],
        ];

        // On insére le role par default au user
        foreach ($rowsPermissionsBlog as $row) {
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
