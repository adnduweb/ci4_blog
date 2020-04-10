# ci4_page
Gestion des menus avec Codeigniter 4

# Installation du module

<pre>
    composer require spreadaurora/ci4_page
    or 
    /opt/plesk/php/7.xx/bin/php /usr/lib/plesk-9.0/composer.phar require spreadaurora/ci4_page
    
</pre>
<pre>
    php spark migrate -all
    or
    /opt/plesk/php/7.xx/bin/php spark migrate -all

    php spark db:seed \\Spreadaurora\\ci4_page\\Database\\Seeds\\PageSeeder
    or
    /opt/plesk/php/7.xx/bin/php spark db:seed \\Spreadaurora\\ci4_page\\Database\\Seeds\\PageSeeder


    php spark ci4_page:publish
    or
    /opt/plesk/php/7.xx/bin/php spark ci4_page:publish
    </pre>