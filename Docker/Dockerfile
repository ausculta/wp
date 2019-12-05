FROM php:7.3-apache-buster

# install the PHP extensions we need (https://make.wordpress.org/hosting/handbook/handbook/server-environment/#php-extensions)
RUN apt list

RUN set -ex; \
	\
	savedAptMark="$(apt-mark showmanual)"; \
	\
	apt-get update; \
	apt-get install -y --no-install-recommends \
		libjpeg-dev \
		libmagickwand-dev \
		libpng-dev \
		libzip-dev \
	; \
	\
	docker-php-ext-configure gd --with-png-dir=/usr --with-jpeg-dir=/usr; \
	docker-php-ext-install -j "$(nproc)" \
		bcmath \
		exif \
		gd \
		mysqli \
		opcache \
		zip \
	; \
	pecl install imagick-3.4.4; \
	docker-php-ext-enable imagick; \
	\
# reset apt-mark's "manual" list so that "purge --auto-remove" will remove all build dependencies
	apt-mark auto '.*' > /dev/null; \
	apt-mark manual $savedAptMark; \
	ldd "$(php -r 'echo ini_get("extension_dir");')"/*.so \
		| awk '/=>/ { print $3 }' \
		| sort -u \
		| xargs -r dpkg-query -S \
		| cut -d: -f1 \
		| sort -u \
		| xargs -rt apt-mark manual; \
	\
	apt-get purge -y --auto-remove -o APT::AutoRemove::RecommendsImportant=false; \
	rm -rf /var/lib/apt/lists/*

# set recommended PHP.ini settings
# see https://secure.php.net/manual/en/opcache.installation.php
RUN { \
		echo 'opcache.memory_consumption=128'; \
		echo 'opcache.interned_strings_buffer=8'; \
		echo 'opcache.max_accelerated_files=4000'; \
		echo 'opcache.revalidate_freq=2'; \
		echo 'opcache.fast_shutdown=1'; \
	} > /usr/local/etc/php/conf.d/opcache-recommended.ini
# https://wordpress.org/support/article/editing-wp-config-php/#configure-error-logging
RUN { \
# https://www.php.net/manual/en/errorfunc.constants.php
# https://github.com/docker-library/wordpress/issues/420#issuecomment-517839670
		echo 'error_reporting = E_ERROR | E_WARNING | E_PARSE | E_CORE_ERROR | E_CORE_WARNING | E_COMPILE_ERROR | E_COMPILE_WARNING | E_RECOVERABLE_ERROR'; \
		echo 'display_errors = Off'; \
		echo 'display_startup_errors = Off'; \
		echo 'log_errors = On'; \
		echo 'error_log = /dev/stderr'; \
		echo 'log_errors_max_len = 1024'; \
		echo 'ignore_repeated_errors = On'; \
		echo 'ignore_repeated_source = Off'; \
		echo 'html_errors = Off'; \
	} > /usr/local/etc/php/conf.d/error-logging.ini

# VOLUME /var/www/html /home /var/log

ENV WORDPRESS_VERSION 5.3	
ENV WORDPRESS_SHA1 e3edcb1131e539c2b2e10fed37f8b6683c824a98

RUN a2enmod rewrite expires; \
	mkdir -p /tmp; \
	set -ex; \
	curl -o wordpress.tar.gz -fSL "https://wordpress.org/wordpress-${WORDPRESS_VERSION}.tar.gz"; \
	echo "$WORDPRESS_SHA1 *wordpress.tar.gz" | sha1sum -c -; \
	# upstream tarballs include ./wordpress/ so this gives us /usr/src/wordpress
	tar -xzf wordpress.tar.gz -C /usr/src/; \
	rm wordpress.tar.gz; \
	chown -R www-data:www-data /usr/src/wordpress; \
	apt-get update; \
	apt-get install -yq nano vim tar wget grep sed; \
	apt-get install -yq certbot python-certbot-apache; \
	# apt list | grep ssh; \
	apt-get install -yq --no-install-recommends  openssh-server; \
	echo "root:Docker!" | chpasswd
	
COPY bashrc /root/.bashrc
COPY sshd_config /etc/ssh/
COPY ssh_setup.sh /tmp
COPY letsencrypt.endeavouresu.tar.gz /tmp
# COPY etc.apache2.endeavouresu.tar.gz /tmp
COPY website.tar.gz /tmp

RUN chmod 644 /root/.bashrc; \
	chmod -R +x /tmp/ssh_setup.sh; \
	(sleep 1;/tmp/ssh_setup.sh 2>&1 > /dev/null); \
	# tar xvfC /tmp/etc.apache2.tar /etc; \
	tar xvzfC /tmp/letsencrypt.endeavouresu.tar.gz /etc; \
	mkdir /var/www/website; \
	mkdir /var/www/website/assets; \
	chmod 755 /var/www/website; \
	chmod 755 /var/www/website/assets; \
	tar xvfC /tmp/website.tar.gz /; \
	chown -R www-data.www-data /var/www/website; \
	rm -rf /tmp/*

# Setup PHP and Apache, place latest config files
COPY BaltimoreCyberTrustRoot.crt.pem /usr/src/wordpress/BaltimoreCyberTrustRoot.crt.pem
COPY BaltimoreCyberTrustRoot.crt.pem /var/www/website/BaltimoreCyberTrustRoot.crt.pem
# COPY BaltimoreCyberTrustRoot.crt.pem /website/BaltimoreCyberTrustRoot.crt.pem
COPY htaccess /var/www/website/.htaccess
COPY htaccess /usr/src/wordpress/htaccess
COPY php.ini /usr/local/etc/php/php.ini
COPY wp-config.php /usr/src/wordpress/wp-config.php
COPY wp-config.php /var/www/website/wp-config.php
COPY apache2.conf /etc/apache2/apache2.conf
COPY default-le-ssl.conf /etc/apache2/sites-available/000-default-le-ssl.conf
COPY default.conf /etc/apache2/sites-available/000-default.conf
COPY certbot /etc/cron.d/certbot
COPY docker-entrypoint.sh /usr/local/bin/
COPY backup.sh /var/www/backup.sh

RUN chmod 644 /usr/local/etc/php/php.ini; \
	chmod 644 /etc/apache2/apache2.conf; \
	chmod 644 /etc/apache2/sites-available/000-default-le-ssl.conf; \
	chmod 644 /etc/apache2/sites-available/000-default.conf; \
	chmod 700 /var/www/backup.sh; \
	a2ensite 000-default.conf; \
	a2ensite 000-default-le-ssl.conf; \
	chmod 644 /etc/cron.d/certbot; \
	chmod 0755 /usr/local/bin/docker-entrypoint.sh;

#VOLUME /var/www/html /home /var/log

EXPOSE 80 2222

RUN apt list

ENTRYPOINT ["/usr/local/bin/docker-entrypoint.sh"]
CMD ["apache2-foreground"]
