# Get this scripts directory so we can initiate it correctly from any directory
BASEDIR=$(dirname "$0")

# Create a backup copy of file overrides before overriding the contents
cp /etc/inc/auth.inc /etc/inc/auth.inc.original
cp /etc/inc/authgui.inc /etc/inc/authgui.inc.original
cp /etc/inc/priv.inc /etc/inc/priv.inc.original

# Copy package contents to PHP inc path
cp -r "$BASEDIR/pfSense-pkg-saml2-auth/files/etc/inc/saml2_auth" "/etc/inc/"

# Copy file overrides to their respective destintations
cp /etc/inc/saml2_auth/overrides/auth.inc /etc/inc/auth.inc
cp /etc/inc/saml2_auth/overrides/authgui.inc /etc/inc/authgui.inc
cp /etc/inc/saml2_auth/overrides/priv.inc /etc/inc/priv.inc

# Copy static content
cp -r "$BASEDIR/pfSense-pkg-saml2-auth/files/usr/local/www/saml2_auth" "/usr/local/www/saml2_auth"
cp "$BASEDIR/pfSense-pkg-saml2-auth/files/usr/local/www/js/saml2_auth.js" "/usr/local/www/js/saml2_auth.js"
cp "$BASEDIR/pfSense-pkg-saml2-auth/files/usr/local/www/css/saml2_auth.css" "/usr/local/www/css/saml2_auth.css"