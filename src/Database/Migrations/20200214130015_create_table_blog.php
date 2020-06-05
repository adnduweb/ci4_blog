<?php

namespace Adnduweb\ci4_blog\Database\Migrations;

use CodeIgniter\Database\Migration;

class Migration_create_table_blog extends Migration
{
    public function up()
    {

        /* ARTICLES */
        $fields = [
            'id_article'           => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'id_categorie_default' => ['type' => 'INT', 'constraint' => 11],
            'author_created'       => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'author_update'        => ['type' => 'INT', 'constraint' => 11],
            'active'               => ['type' => 'INT', 'constraint' => 11],
            'important'            => ['type' => 'INT', 'constraint' => 11, 'default' => 0],
            'picture_one'          => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'picture_header'       => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'no_follow_no_index'   => ['type' => 'INT', 'constraint' => 11, 'default' => 0],
            'type'                 => ['type' => 'INT', 'constraint' => 11, 'default' => 4],
            'order'                => ['type' => 'INT', 'constraint' => 11, 'default' => 0],
            'created_at'           => ['type' => 'DATETIME', 'null' => true],
            'updated_at'           => ['type' => 'DATETIME', 'null' => true],
            'deleted_at'           => ['type' => 'DATETIME', 'null' => true],
        ];

        $this->forge->addField($fields);
        $this->forge->addKey('id_article', true);
        $this->forge->addKey('created_at');
        $this->forge->addKey('updated_at');
        $this->forge->addKey('deleted_at');
        $this->forge->addForeignKey('author_created', 'users', 'id', false, false);
        //$this->forge->addForeignKey('author_update', 'users', 'id', false, false);
        $this->forge->createTable('articles');


        $fields = [
            'id_article'        => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'default' => 0],
            'id_lang'           => ['type' => 'INT', 'constraint' => 11],
            'name'              => ['type' => 'VARCHAR', 'constraint' => 255],
            'sous_name'         => ['type' => 'VARCHAR', 'constraint' => 255],
            'description_short' => ['type' => 'TEXT'],
            'description'       => ['type' => 'TEXT'],
            'meta_title'        => ['type' => 'VARCHAR', 'constraint' => 255],
            'meta_description'  => ['type' => 'VARCHAR', 'constraint' => 255],
            'tags'              => ['type' => 'VARCHAR', 'constraint' => 255],
            'slug'              => ['type' => 'VARCHAR', 'constraint' => 255],
        ];

        $this->forge->addField($fields);
        // $this->forge->addKey(['id_item', 'id_lang'], false, true);
        $this->forge->addKey('id_article');
        $this->forge->addKey('id_lang');
        $this->forge->addForeignKey('id_article', 'articles', 'id_article', false, 'CASCADE');
        $this->forge->createTable('articles_langs', true);


        /* CATEGORIES */
        $fields = [
            'id_categorie' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'id_parent'    => ['type' => 'INT', 'constraint' => 11, 'default' => 0],
            'order'        => ['type' => 'INT', 'constraint' => 11, 'default' => 0],
            'active'       => ['type' => 'INT', 'constraint' => 11, 'default' => 1],
            'created_at'   => ['type' => 'DATETIME', 'null' => true],
            'updated_at'   => ['type' => 'DATETIME', 'null' => true],
            'deleted_at'   => ['type' => 'DATETIME', 'null' => true],
        ];

        $this->forge->addField($fields);
        $this->forge->addKey('id_categorie', true);
        $this->forge->addKey('created_at');
        $this->forge->addKey('updated_at');
        $this->forge->addKey('deleted_at');
        $this->forge->createTable('categories');


        $fields = [
            'id_categorie'      => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'default' => 0],
            'id_lang'           => ['type' => 'INT', 'constraint' => 11],
            'name'              => ['type' => 'VARCHAR', 'constraint' => 255],
            'description_short' => ['type' => 'TEXT'],
            'slug'              => ['type' => 'VARCHAR', 'constraint' => 255],
        ];

        $this->forge->addField($fields);
        // $this->forge->addKey(['id_item', 'id_lang'], false, true);
        $this->forge->addKey('id_categorie');
        $this->forge->addKey('id_lang');
        $this->forge->addForeignKey('id_categorie', 'categories', 'id_categorie', false, 'CASCADE');
        $this->forge->createTable('categories_langs', true);


        $fields = [
            'id_article'   => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'id_categorie' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'created_at'   => ['type' => 'DATETIME', 'null' => true],
            'deleted_at'   => ['type' => 'DATETIME', 'null' => true],
        ];

        $this->forge->addField($fields);
        // $this->forge->addKey(['id_item', 'id_lang'], false, true);
        $this->forge->addKey(['id_article', 'id_categorie'], FALSE, TRUE);
        $this->forge->addKey('created_at');
        $this->forge->addKey('deleted_at');
        $this->forge->addForeignKey('id_article', 'articles', 'id_article', false, 'CASCADE');
        $this->forge->addForeignKey('id_categorie', 'categories', 'id_categorie', false, false);
        $this->forge->createTable('articles_categories', true);


        /* TAGS */
        $fields = [
            'id_tag'     => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'slug'       => ['type' => 'VARCHAR', 'constraint' => 255],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
            'deleted_at' => ['type' => 'DATETIME', 'null' => true],
        ];

        $this->forge->addField($fields);
        $this->forge->addKey('id_tag', true);
        $this->forge->addKey('created_at');
        $this->forge->addKey('updated_at');
        $this->forge->addKey('deleted_at');
        $this->forge->createTable('tags');


        $fields = [
            'id_tag'  => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'default' => 0],
            'id_lang' => ['type' => 'INT', 'constraint' => 11],
            'name'    => ['type' => 'VARCHAR', 'constraint' => 255],
        ];

        $this->forge->addField($fields);
        // $this->forge->addKey(['id_item', 'id_lang'], false, true);
        $this->forge->addKey('id_tag');
        $this->forge->addKey('id_lang');
        $this->forge->addForeignKey('id_tag', 'tags', 'id_tag', false, 'CASCADE');
        $this->forge->createTable('tags_langs', true);

        /* COMMENTS */
        $fields = [
            'id_comment'   => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'id_article'   => ['type' => 'INT', 'constraint' => 11],
            'author_name'  => ['type' => 'VARCHAR', 'constraint' => 255],
            'author_email' => ['type' => 'TEXT'],
            'author_ip'    => ['type' => 'VARCHAR', 'constraint' => 255],
            'verified'     => ['type' => 'INT', 'constraint' => 11, 'default' => 0],
            'content'      => ['type' => 'TEXT'],
            'created_at'   => ['type' => 'DATETIME', 'null' => true],
            'updated_at'   => ['type' => 'DATETIME', 'null' => true],
            'deleted_at'   => ['type' => 'DATETIME', 'null' => true],
        ];

        $this->forge->addField($fields);
        $this->forge->addKey('id_comment', true);
        $this->forge->addKey('created_at');
        $this->forge->addKey('updated_at');
        $this->forge->addKey('deleted_at');
        $this->forge->createTable('comments');
    }

    //--------------------------------------------------------------------

    public function down()
    {
        $this->forge->dropTable('articles');
        $this->forge->dropTable('articles_langs');
        $this->forge->dropTable('articles_categories');
        $this->forge->dropTable('categories');
        $this->forge->dropTable('categories_langs');
        $this->forge->dropTable('tags');
        $this->forge->dropTable('tags_langs');
        $this->forge->dropTable('comments');
    }
}
