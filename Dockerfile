FROM alpine

WORKDIR /root/app

COPY src          src
COPY autoload.php autoload.php
COPY target       target
COPY uppm.json    uppm.json

RUN \
    apk add php81 php81-common wget bash \
    php81-bcmath php81-bz2 php81-calendar php81-cgi \
    php81-common php81-ctype php81-curl php81-dba \
    php81-doc php81-dom php81-embed \
    php81-enchant php81-exif php81-ffi php81-fileinfo \
    php81-fpm php81-ftp php81-gd php81-gettext \
    php81-gmp php81-iconv \
    php81-imap php81-intl \
    php81-ldap php81-litespeed php81-mbstring \
    php81-mysqli php81-mysqlnd php81-odbc \
    php81-opcache php81-openssl php81-pcntl php81-pdo \
    php81-pdo_dblib php81-pdo_mysql php81-pdo_odbc \
    php81-pdo_pgsql php81-pdo_sqlite php81-pear php81-pgsql \
    php81-phar php81-posix php81-pspell php81-session php81-shmop php81-simplexml \
    php81-snmp php81-soap php81-sockets \
    php81-sodium php81-sqlite3 php81-sysvmsg \
    php81-sysvsem php81-sysvshm \
    php81-tokenizer php81-xml php81-xmlreader \
    php81-xmlwriter php81-xsl php81-zip;\
    ln /usr/bin/php81 /usr/bin/php; \
    echo phar.readonly = Off >> /etc/php81/php.ini; \
    php target/uppm.phar lock; \
    php target/uppm.phar install; \
    php src/main/bootstrap.php build; \
    mv target/uppm.phar /usr/local/bin/uppm; \
    chmod +x /usr/local/bin/uppm;