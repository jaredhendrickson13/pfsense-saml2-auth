<?php
require_once("saml2_auth/SAML2Auth.inc");
session_start();

# Create the saml2 authentication object
$saml2_auth = new SAML2Auth();

# Validate SSO response when. If no request was found during validation, print simple error
try {
    $saml2_auth->acs();
} catch (OneLogin\Saml2\Error $error) {
    echo $error->getMessage().PHP_EOL;
}
