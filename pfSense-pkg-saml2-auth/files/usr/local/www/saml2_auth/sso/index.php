<?php
require_once("saml2_auth/SAML2Auth.inc");
session_start();

# Create the saml2 authentication object
$saml2_auth = new SAML2Auth();

# Return debugging data when `debug` parameter is received
if (isset($_GET['debug'])) {
    var_dump($_SESSION);
    echo PHP_EOL."-----------------".PHP_EOL;
    var_dump($saml2_auth);
}
# Start SSO
else {
    $pkg_conf = SAML2Auth::get_package_config()[1];
    $saml2_auth->sso($pkg_conf["sp_base_url"]."/saml2_auth/sso/redirect/");
}
