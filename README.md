skelleton
=========

A Personal Learning Environment organized with bubbles

# Setup

## Requirements
1. [Bower](http://bower.io/)
1. [Composer](https://getcomposer.org/)
2. PHP >= 5.4

## Configure
### Application
1. Copy `config/autoload/local.php.dist` to `config/autoload/local.php`
2. Edit the new file and enter your MySQL data

### Server
The Document root is `public/`. Example Apache config:
```
<VirtualHost *:80>
        DocumentRoot "/var/www/fsmpivideo/public"
        ServerName fsmpivideo.localhost
        <Directory "/var/www/fsmpivideo/public">
                DirectoryIndex index.php
                AllowOverride All
                Order allow,deny
                <IfModule mod_authz_core.c>
                        Require all granted
                </IfModule>
                Allow from all
        </Directory>
</VirtualHost>
```
Dont forget to enable `mod_rewrite`

## Setup
1. Open Commandline
2. Navigate to your cloned repository
3. run `bower install`
4. run `php composer.phar install`
5. run `php vendor/bin/doctrine-module orm:schema-tool:create`
6. run `php vendor/bin/doctrine-module data-fixture:import`
7. run `php public/index.php bower prepare-packs`

# Things to know

## User state
* The user state is a bitmask
* Activated: $user->getState & (1 << 0) !== 0
* E-Mail verified: $user->getState & (1 << 1) !== 0
