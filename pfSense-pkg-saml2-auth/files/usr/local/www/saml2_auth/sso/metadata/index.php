<?php
require_once("saml2_auth/SAML2Auth.inc");
session_start();

# Create the saml2 authentication object
$saml2_auth = new SAML2Auth();

# Return SP metadata
$saml2_auth->metadata();
