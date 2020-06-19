<?php

namespace Adnduweb\ci4_blog\Database\Migrations;

use CodeIgniter\Database\Migration;

class Migration_create_table_blog extends Migration
{
    public function up()
    {

        /* b_posts */
        $fields = [
            'id'                  => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'id_category_default' => ['type' => 'INT', 'constraint' => 11],
            'user_id'             => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'user_updated'       => ['type' => 'INT', 'constraint' => 11],
            'active'              => ['type' => 'INT', 'constraint' => 11],
            'important'           => ['type' => 'INT', 'constraint' => 11, 'default' => 0],
            'picture_one'         => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'picture_header'      => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'no_follow_no_index'  => ['type' => 'INT', 'constraint' => 11, 'default' => 0],
            'type'                => ['type' => 'INT', 'constraint' => 11, 'default' => 4],
            'order'               => ['type' => 'INT', 'constraint' => 11, 'default' => 0],
            'created_at'          => ['type' => 'DATETIME', 'null' => true],
            'updated_at'          => ['type' => 'DATETIME', 'null' => true],
            'deleted_at'          => ['type' => 'DATETIME', 'null' => true],
        ];

        $this->forge->addKey('id', true);
        $this->forge->addField($fields);
        $this->forge->addForeignKey('user_id', 'users', 'id', false, false);
        $this->forge->createTable('b_posts');


        $fields = [
            'id_post_lang'      => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'post_id'           => ['type' => 'int', 'constraint' => 11, 'unsigned' => true, 'default' => 0],
            'id_lang'           => ['type' => 'int', 'constraint' => 11, 'unsigned' => true, 'default' => 0],
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
        $this->forge->addKey('id_post_lang', true);
        $this->forge->addKey('id_lang');
        $this->forge->addForeignKey('post_id', 'b_posts', 'id', false, 'CASCADE');
        $this->forge->createTable('b_posts_langs', true);


        /* CATEGORIE */
        $fields = [
            'id'         => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'id_parent'  => ['type' => 'INT', 'constraint' => 11, 'default' => 0],
            'order'      => ['type' => 'INT', 'constraint' => 11, 'default' => 0],
            'active'     => ['type' => 'INT', 'constraint' => 11, 'default' => 1],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
            'deleted_at' => ['type' => 'DATETIME', 'null' => true],
        ];

        $this->forge->addField($fields);
        $this->forge->addKey('id', true);
        $this->forge->addKey('created_at');
        $this->forge->addKey('updated_at');
        $this->forge->addKey('deleted_at');
        $this->forge->createTable('b_categories');


        $fields = [
            'id_categorie_lang' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'category_id'       => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'default' => 0],
            'id_lang'           => ['type' => 'INT', 'constraint' => 11],
            'name'              => ['type' => 'VARCHAR', 'constraint' => 255],
            'description_short' => ['type' => 'TEXT'],
            'meta_title'        => ['type' => 'VARCHAR', 'constraint' => 255],
            'meta_description'  => ['type' => 'VARCHAR', 'constraint' => 255],
            'tags'              => ['type' => 'VARCHAR', 'constraint' => 255],
            'slug'              => ['type' => 'VARCHAR', 'constraint' => 255],
        ];

        $this->forge->addField($fields);
        $this->forge->addKey('id_categorie_lang', true);
        $this->forge->addKey('id_lang');
        $this->forge->addForeignKey('category_id', 'b_categories', 'id', false, 'CASCADE');
        $this->forge->createTable('b_categories_langs', true);


        $fields = [
            'post_id'      => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'category_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
        ];

        $this->forge->addField($fields);
        $this->forge->addForeignKey('post_id', 'b_posts', 'id', false, 'CASCADE');
        $this->forge->addForeignKey('category_id', 'b_categories', 'id', false, false);
        $this->forge->createTable('b_posts_categories');


        /* TAGS */
        $fields = [
            'id'     => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'slug'       => ['type' => 'VARCHAR', 'constraint' => 255],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
            'deleted_at' => ['type' => 'DATETIME', 'null' => true],
        ];

        $this->forge->addField($fields);
        $this->forge->addKey('id', true);
        $this->forge->addKey('created_at');
        $this->forge->addKey('updated_at');
        $this->forge->addKey('deleted_at');
        $this->forge->createTable('b_tags');


        $fields = [
            'id_tag_lang' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'tag_id'      => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'default' => 0],
            'id_lang'     => ['type' => 'INT', 'constraint' => 11],
            'name'        => ['type' => 'VARCHAR', 'constraint' => 255],
        ];

        $this->forge->addField($fields);
        $this->forge->addKey('id_tag_lang', true);
        $this->forge->addKey('id_lang');
        $this->forge->addForeignKey('tag_id', 'b_tags', 'id', false, 'CASCADE');
        $this->forge->createTable('b_tags_langs');

        /* COMMENTS */
        $fields = [
            'id_comment'   => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'post_id'      => ['type' => 'int', 'constraint' => 11, 'unsigned' => true, 'default' => 0],
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
        $this->forge->addForeignKey('post_id', 'b_posts', 'id', false, 'CASCADE');
        $this->forge->createTable('b_comments');
    }

    //--------------------------------------------------------------------

    public function down()
    {
        $this->forge->dropTable('b_posts');
        $this->forge->dropTable('b_posts_langs');
        $this->forge->dropTable('b_posts_categories');
        $this->forge->dropTable('b_categories');
        $this->forge->dropTable('b_categories_langs');
        $this->forge->dropTable('b_tags');
        $this->forge->dropTable('b_tags_langs');
        $this->forge->dropTable('b_comments');
    }
}
