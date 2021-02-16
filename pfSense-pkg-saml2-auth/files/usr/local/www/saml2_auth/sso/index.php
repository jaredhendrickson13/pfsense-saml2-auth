<?php
require_once("saml2_auth/saml2_auth.inc");
session_start();

# Create the saml2 authentication object
$saml2_auth = new saml2_auth();

# Return debugging data when `debug` parameter is received
if (isset($_GET['debug'])) {
    var_dump($_SESSION);
    echo PHP_EOL."-----------------".PHP_EOL;
    var_dump($saml2_auth);
}
# Redirect to login page after completing SSO when `redirect` parameter is received.
# This is needed to bypass pfSense's HTTP_REFERRER checks
elseif(isset($_GET["redirect"])) {
    header("Location: /");
    exit();
}
# Start SSO
else {
    $saml2_auth->sso("https://172.16.209.9/saml2_auth/sso/redirect/");
}
