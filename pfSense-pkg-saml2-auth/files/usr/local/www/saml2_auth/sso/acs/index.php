<?php
require_once("saml2_auth/saml2_auth.inc");
session_start();

# Create the saml2 authentication object
$saml2_auth = new saml2_auth();

# Validate SSO response when
$saml2_auth->acs();
