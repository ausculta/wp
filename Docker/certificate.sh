#! /bin/bash

certbot renew

mv /var/www/html/endeavouresu.cert.pfx.1 /var/www/html/endeavouresu.cert.pfx.2
mv /var/www/html/endeavouresu.cert.pfx /var/www/html/endeavouresu.cert.pfx.1

openssl pkcs12 -inkey /etc/letsencrypt/live/endeavouresu.uk/privkey.pem -in /etc/letsencrypt/live/endeavouresu.uk/cert.pem -export -out /etc/letsencrypt/live/endeavouresu.uk/endeavouresu.pfx

cp /etc/letsencrypt/live/endeavouresu.uk/cert.pfx /var/www/html/endeavouresu.cert.pfx

mv /var/www/html/letsencrypt.endeavouresu.tar.gz.1 /var/www/html/letsencrypt.endeavouresu.tar.gz.2
mv /var/www/html/letsencrypt.endeavouresu.tar.gz /var/www/html/letsencrypt.endeavouresu.tar.gz.1
tar cpzf /var/www/html/letsencrypt.endeavouresu.tar.gz /etc/letsencrypt

