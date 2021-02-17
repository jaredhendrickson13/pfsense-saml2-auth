<?php
require_once("saml2_auth/SAML2Auth.inc");
session_start();

# Create the saml2 authentication object
$saml2_auth = new SAML2Auth();
$pkg_conf = SAML2Auth::get_package_config()[1];

# Functions
function get_debug_data() {
    echo "<p>--------- GENERAL --------</p>".PHP_EOL;
    echo "<pre>SAML2 Name ID: ".$_SESSION['samlNameId']."</pre>".PHP_EOL;
    echo "<p>--------- ATTRIBUTES --------</p>".PHP_EOL;
    # Loop through and print each of our received SAML2 attributes
    if (is_array($_SESSION["samlUserdata"])) {
        foreach ($_SESSION["samlUserdata"] as $attr_name=>$attr_val) {
            echo "<pre>";
            echo $attr_name.": ";
            print_r($attr_val);
            echo "</pre>";
        }
    }
}

# Return debugging data when `debug_mode` parameter is received
if (isset($_GET['debug_mode']) and boolval($pkg_conf["debug_mode"])) {
    get_debug_data();
}
# Start SSO
else {
    $pkg_conf = SAML2Auth::get_package_config()[1];
    $saml2_auth->sso($pkg_conf["sp_base_url"]."/saml2_auth/sso/redirect/");
}
