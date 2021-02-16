# Restore the file overrides to the state they were at when the package was installed
mv /etc/inc/auth.inc.original /etc/inc/auth.inc
mv /etc/inc/authgui.inc.original /etc/inc/authgui.inc
mv /etc/inc/priv.inc.original /etc/inc/priv.inc

# Copy package contents to PHP inc path
rm -rf /etc/inc/saml2_auth

# Copy static content
rm -rf /usr/local/www/saml2_auth
rm /usr/local/www/js/saml2_auth.js
rm /usr/local/www/js/saml2_auth.css