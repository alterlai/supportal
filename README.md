
# IQ support 
---

## Installatiehandleiding ## 

Supportal webapplicatie

Jeroen van der Laan
7-6-2020

 ## Inleiding
 
Dit document beschrijft de stappen om de webapplicatie te installeren op een nieuwe omgeving. De webapplicatie betreft de IQ support webapplicatie, gemaakt door Jeroen van der Laan, als afstudeerproject. Deze handleiding gaat er van uit dat er een bestaande VM of server beschikbaar is die over voldoende rekenkracht beschikt.

## Voorwaarden
Om deze installatiehandleiding uit te voeren, dienen de volgende voorwaarden in orde te zijn.
1.1.    Server
De server waarop de webapplicatie gehost wordt, moet aan de minimale systeemeisen voldoen.
| Naam | Omschrijving |
| ---- | ---- |
| Application server | Dual core > 2GHz CPU |
| | 2GB+ RAM |
||500GB disk space|
||Ubuntu server 18.10

PHP extensies:
- Ctype
- Mbstring
- Xml
- Curl
- Cli
- Mysql
- Fpm
- Zip

Overige voorwaarden:
- SSH toegang.
- Een kopie van de git repository.
- Een SMTP mail-account voor het versturen van e-mail.

## Web services
### PHP-FPM
Na het installeren van een kaal Ubuntu 18.10 image is het tijd om de web services te installeren en configureren.
Om php7.4 fpm te installeren, voeg eerst de sury.org repository toe. Installeer daarna php7.4-fpm met de bijbehorende packages.
```
sudo apt-get install apt-transport-https -y
sudo wget -O /etc/apt/trusted.gpg.d/php.gpg https://packages.sury.org/php/apt.gpg
echo "deb https://packages.sury.org/php/ stretch main" | sudo tee /etc/apt/sources.list.d/php.list
sudo apt update
sudo apt-get install libcurl3 php7.4-cli php7.4-curl php7.4-mysql php7.4-fpm php7.4-xml php7.4-mbstring php7.4-zip php7.4-dev -y
```

Bij het uitvoeren van php –version zou nu php7.4 vermeld moeten worden.
``` 
iqsupport@iqsupport:~$ php --version
PHP 7.4.7 (cli) (built: Jun 12 2020 07:48:26) ( NTS )
Copyright (c) The PHP Group
Zend Engine v3.4.0, Copyright (c) Zend Technologies
    with Zend OPcache v7.4.7, Copyright (c), by Zend Technologies
```
### Nginx
Installeer de Nginx service met het volgende commando:
sudo apt install nginx
Om PHP-FPM te gebruiken, open `/etc/nginx/sites-available/default` en gebruik de volgende configuratie. Pas de `server_name` variabele aan naar het domein waar de server op komt te draaien. Uncomment ook de listen 443 lijn als er SSL gebruikt wordt.
```
server {
    server_name domain.tld www.domain.tld;
    root /var/www/html/supportal/public/;
    client_max_body_size 50M;

    location / {
        # try to serve file directly, fallback to index.php
        try_files $uri /index.php$is_args$args;
    }

    # optionally disable falling back to PHP script for the asset directories;
    # nginx will return a 404 error when files are not found instead of passing the
    # request to Symfony (improves performance but Symfony's 404 page is not displayed)
    # location /bundles {
    #     try_files $uri =404;
    # }

    location ~ ^/index\.php(/|$) {
        fastcgi_pass unix:/var/run/php/php7.4-fpm.sock;
        fastcgi_split_path_info ^(.+\.php)(/.*)$;
        include fastcgi_params;

        # optionally set the value of the environment variables used in the application
        # fastcgi_param APP_ENV prod;
        # fastcgi_param APP_SECRET <app-secret-id>;
        # fastcgi_param DATABASE_URL "mysql://db_user:db_pass@host:3306/db_name";

        # When you are using symlinks to link the document root to the
        # current version of your application, you should pass the real
        # application path instead of the path to the symlink to PHP
        # FPM.
        # Otherwise, PHP's OPcache may not properly detect changes to
        # your PHP files (see https://github.com/zendtech/ZendOptimizerPlus/issues/126
        # for more information).
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        fastcgi_param DOCUMENT_ROOT $realpath_root;
        # Prevents URIs that include the front controller. This will 404:
        # http://domain.tld/index.php/some-path
        # Remove the internal directive to allow URIs like this
        internal;
    }

    # return 404 for all other php files not matching the front controller
    # this prevents access to other php files you don't want to be accessible.
    location ~ \.php$ {
        return 404;
    }

    error_log /var/log/nginx/project_error.log;
    access_log /var/log/nginx/project_access.log;
}

```
Voer vervolgens sudo service nginx reload uit om de configuratie te laden.


### Composer
Installeer composer met 
```
sudo apt install composer
```

### MySQL
De webapplicatie maakt gebruik van een MySQL database voor het opslaan van user- en documentdata.
Installeer MySQL server:
```
sudo apt install mysql_server
```
Configureer de MySQL server met
```
sudo mysql

Welcome to the MySQL monitor.  Commands end with ; or \g.
Your MySQL connection id is 9
Server version: 5.7.30-0ubuntu0.18.04.1 (Ubuntu)

Copyright (c) 2000, 2020, Oracle and/or its affiliates. All rights reserved.

Oracle is a registered trademark of Oracle Corporation and/or its
affiliates. Other names may be trademarks of their respective
owners.

Type 'help;' or '\h' for help. Type '\c' to clear the current input statement.

mysql> CREATE USER 'iqadmin'@'localhost' IDENTIFIED BY 'password';
Query OK, 0 rows affected (0.00 sec)

mysql> GRANT ALL PRIVILEGES ON *.* TO 'iqadmin'@'localhost';
Query OK, 0 rows affected (0.00 sec)

mysql> create database supportal;
Query OK, 1 row affected (0.00 sec)

exit;

```

### Testen
Controleer of de huidige configuratie goed is toegepast door de public map aan te maken in de `/var/www/html/supportal/` folder en hier een phpinfo() bestand in te zetten. Als alles juist is geconfigureerd wordt de phpinfo weergegeven als het IP-adres wordt bezocht in de browser.

## Applicatie deployment
Nu dat de server gereed is om de applicatie te hosten, moet de applicatie gedeployed worden.

Clone de repository vanaf github ``https://github.com/alterlai/supportal.git``, of unzip de code in de `/var/www/html/supportal/` folder.

Kopieer en pas de environment configureer de variabelen gemarkeerd met ....
```
cp .env.example .env
```
Voorbeeld:
```
# In all environments, the following files are loaded if they exist,
# the latter taking precedence over the former:
#
#  * .env                contains default values for the environment variables needed by the app
#  * .env.local          uncommitted file with local overrides
#  * .env.$APP_ENV       committed environment-specific defaults
#  * .env.$APP_ENV.local uncommitted environment-specific overrides
#
# Real environment variables win over .env files.
#
# DO NOT DEFINE PRODUCTION SECRETS IN THIS FILE NOR IN ANY OTHER COMMITTED FILES.
#
# Run "composer dump-env prod" to compile .env files for production use (requires symfony/flex >=1.2).
# https://symfony.com/doc/current/best_practices.html#use-environment-variables-for-infrastructure-configuration

###> symfony/framework-bundle ###
APP_ENV=prod
APP_SECRET=1234567
#TRUSTED_PROXIES=127.0.0.0/8,10.0.0.0/8,172.16.0.0/12,192.168.0.0/16
#TRUSTED_HOSTS='^(localhost|example\.com)$'
###< symfony/framework-bundle ###

###> symfony/mailer ###
#MAILER_DSN=smtp://$NOREPLY_USER:$NOREPLY_PASSWORD@ses

###< symfony/mailer ###

###> doctrine/doctrine-bundle ###
# Format described at https://www.doctrine-project.org/projects/doctrine-dbal/en/latest/reference/configuration.html#connecting-using-a-url
# For an SQLite database, use: "sqlite:///%kernel.project_dir%/var/data.db"
# For a PostgreSQL database, use: "postgresql://db_user:db_password@127.0.0.1:5432/db_name?serverVersion=11&charset=utf8"
# IMPORTANT: You MUST configure your server version, either here or in config/packages/doctrine.yaml
DATABASE_URL=mysql://username:password@127.0.0.1:3306/database
###< doctrine/doctrine-bundle ###

###> symfony/swiftmailer-bundle ###
# For Gmail as a transport, use: "gmail://username:password@localhost"
# For a generic SMTP server, use: "smtp://localhost:25?encryption=&auth_mode="
# Delivery is disabled by default via "null://localhost"
NOREPLY_USER=iqsupport
NOREPLY_PASSWORD=password
MAILER_URL=smtp://smtp.office365.com:587?encryption=tls&auth_mode=login&username=$NOREPLY_USER&password=$NOREPLY_PASSWORD
###< symfony/swiftmailer-bundle ###

```
Installeer benodigde packages met 
```
composer install
```

### File permissies
Omdat het Nginx accout (www-data) de bestanden moet kunnen lezen worden de file permissies ingesteld op de folderstructuur.
```
cd /var/www/html/
mkdir supportal
sudo chmod 775 supportal
sudo chown www-data:www-data supportal
```
Om met een user account de bestanden te kunnen bewerken, kan je het account toevoegen aan de www-data groep.
    ```
    sudo usermod -a -G www-data [user account]
    ```
    
## Production environment
Wanneer de installatie klaar is, open .env, en verander de `APP_ENV` variabele van `dev` naar `prod`


