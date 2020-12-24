#! /bin/bash
tar cpz --exclude=/var/www/website/assets -f /var/www/website.tar.gz /var/www/website

mv /var/www/html/website.tar.gz.1 /var/www/html/website.tar.gz.2
mv /var/www/html/website.tar.gz /var/www/html/website.tar.gz.1
mv website.tar.gz /var/www/html/website.tar.gz

