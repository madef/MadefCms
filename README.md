MadefCms
=========

Madef Cms is a small and easy to use versioned content management system based on symfony 2 project.

Functionalities
-----------

  - Versioning
    - Preview of any version
    - Publish any version
    - Restore any version
    - Forbid modification of version already published
  - Templating
    - Every template are versionned
    - Stored in database
    - Modification via the admin panel (widget, layout, page)
  - Easy to use
    - Drag & drop interface to add widgets into pages
    - Create widgets, layouts and pages directly on the admin panel

Roadmap
-----------
  - Internationalization of the pages, layouts and widgets
  - Manage accounts (ACL)

Tech
-----------

* [Symfony 2.4] - PHP 5.3 full-stack web framework
* [Bootstrap] - Front-end framework for developing responsive,
* [jQuery] - Feature-rich JavaScript library

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

Create a database:
```sql
CREATE DATABASE cms;
```

Install and configure it using composer:
```sh
cd cms
composer install
```

Create the tables:
```sh
php app/console doctrine:schema:update --force
```
An other way to create the database is to execute the dump /dump/demo.sql. It include some example.

Allow the application to write in cache and log directories:
```sh
chmod a+rw app/logs/
chmod a+rw app/cache/
```

Finally, go to the admin panel to create your first page. The url is /admin (app_dev.php/admin).


License
----

The project is open source, under BSD license.

