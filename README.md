Cats and Brock
=================================================


Первая установка, разворачивание проекта
----------------------------------------

Установка композера:

    php -r "readfile('https://getcomposer.org/installer');" | php

Установка вендоров:

    php composer.phar install

Установка базы:

    php app/console doctrine:database:create

Установка необходимых для записи прав для папок кэша и логов:

    sudo chmod -R 0777 app/cache/

    sudo chmod -R 0777 app/logs/

Необходимые команды для запуска после пула
------------------------------------------

Обновление вендоров:

    php composer.phar install

Обновление базы:

    php app/console doctrine:schema:update --force

Установка асетика:

    php app/console assets:install --symlink

Обновление ассетсов:

    php app/console assetic:watch

Другие команды
--------------

Все доступные команды и их описание можно посмотреть запустив:

    php app/console

Генерация setters и getters:

    php app/console doctrine:generate:entities Ewave/CoreBundle/Entity/ModelName

Ссылка на русскую документацию
------------------------------

http://symfony-gu.ru/documentation/ru/html/

Ссылки на офф сайт
------------------
[1]:  http://symfony.com/doc/2.4/book/installation.html
[2]:  http://getcomposer.org/
[3]:  http://symfony.com/download
[4]:  http://symfony.com/doc/2.4/quick_tour/the_big_picture.html
[5]:  http://symfony.com/doc/2.4/index.html
[6]:  http://symfony.com/doc/2.4/bundles/SensioFrameworkExtraBundle/index.html
[7]:  http://symfony.com/doc/2.4/book/doctrine.html
[8]:  http://symfony.com/doc/2.4/book/templating.html
[9]:  http://symfony.com/doc/2.4/book/security.html
[10]: http://symfony.com/doc/2.4/cookbook/email.html
[11]: http://symfony.com/doc/2.4/cookbook/logging/monolog.html
[12]: http://symfony.com/doc/2.4/cookbook/assetic/asset_management.html
[13]: http://symfony.com/doc/2.4/bundles/SensioGeneratorBundle/index.html
