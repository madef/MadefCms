MadefCms
=========

Madef Cms is a small and easy to use versioned content management system based on symfony 2 project.
Versioned means that you can plan a future version of your web site and publish it when it's ready.

Functionalities
-----------

  - Versioning
    - Preview a future version
    - Publish a version when it's perfect
    - Rollback to a previous version
  - Templating
    - Every template are versionned
    - Stored in database
    - Modification via the admin panel (widget, layout, page, media)
  - Easy to use
    - Drag & drop interface to add widgets into pages
    - Create widgets, layouts and pages directly on the admin panel
  - Extendable
    - Install/create bundles to and new features

Roadmap
-----------
  - Internationalization of the pages, layouts and widgets

Tech
-----------

* [Symfony 2.4] - PHP 5.3 full-stack web framework
* [Bootstrap] - Front-end framework for developing responsive,
* [jQuery] - Feature-rich JavaScript library
* [SQLite] - Embedded database

Installation
--------------

Composer is required to install the project. For that, download composer and copy it into your bin directory:
```sh
get composer: php -r "readfile('https://getcomposer.org/installer');" | php
mv composer.phar /usr/local/bin/composer
```

Import the project using git:
```sh
git clone https://github.com/madef/MadefCms.git cms
# OR git clone git@github.com:madef/MadefCms.git cms
```

Install and configure it using composer:
```sh
cd cms
composer install
```

Copy the public files (css, js, ...):
```sh
php app/console assetic:dump
```

Clear your cache:
```sh
php app/console cache:clear --env=dev
sudo php app/console cache:clear --env=prod
```

Allow the application to write in cache and log directories:
```sh
chmod -R a+rw app/logs/
chmod -R a+rw app/cache/
chmod -R a+rw app/data/
```

Finally, go to the admin panel to create your first page. The url is /admin (app_dev.php/admin).

Login : admin
Password : admin


License
----

The project is open source, under BSD license.

