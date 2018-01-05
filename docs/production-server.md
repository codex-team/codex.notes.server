# Production server setup

## PHP 

Currently we support PHP v7.1
```
apt-get install software-properties-common
add-apt-repository ppa:ondrej/php
apt-get update
```

Install necessary modules for PHP
```
apt-get install php7.1
apt-get install php7.1 php7.1-cli php7.1-common php7.1-json php7.1-opcache php7.1-mysql php7.1-mbstring php7.1-mcrypt php7.1-zip php7.1-fpm
```

Build mongodb extension for PHP v7.1
```
apt-get install php-pear
apt-get install php7.1-dev
apt-get install pkg-config libssl-dev libsslcommon2-dev
pecl install mongodb
```

Enable extension
```
echo "extension=mongodb.so" > /etc/php/7.1/fpm/conf.d/20-mongodb.ini
echo "extension=mongodb.so" > /etc/php/7.1/cli/conf.d/20-mongodb.ini
```

Restart PHP FPM
```
restart FPM : systemctl restart php7.1-fpm.service
```

