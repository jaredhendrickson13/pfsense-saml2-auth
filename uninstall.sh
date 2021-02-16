# Restore the file overrides to the state they were at when the package was installed
cp /etc/inc/auth.inc.original /etc/inc/auth.inc
cp /etc/inc/authgui.inc.original /etc/inc/authgui.inc
cp /etc/inc/priv.inc.original /etc/inc/priv.inc

# Copy package contents to PHP inc path
rm -rf /etc/inc/saml2_auth

# Copy static content
rm -rf /usr/local/www/saml2_auth
rm /usr/local/www/js/saml2_auth.js
rm /usr/local/www/css/saml2_auth.css

# Restart PHP-FPM to remove package code in-memory
/etc/rc.php-fpm_restart