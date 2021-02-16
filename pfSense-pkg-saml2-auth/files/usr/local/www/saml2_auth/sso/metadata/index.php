<?php
require_once("saml2_auth/saml2_auth.inc");
session_start();

# Create the saml2 authentication object
$saml2_auth = new saml2_auth();

# Return SP metadata
$saml2_auth->metadata();
