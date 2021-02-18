<?php
require_once("saml2_auth/SAML2Auth.inc");

# Start SSO
$saml2_auth = new SAML2Auth();
$pkg_conf = SAML2Auth::get_package_config()[1];
$saml2_auth->sso($pkg_conf["sp_base_url"]."/saml2_auth/sso/redirect/");
