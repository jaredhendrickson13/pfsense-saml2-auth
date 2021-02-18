<?php
require_once("saml2_auth/SAML2Auth.inc");
session_start();

# Print debug information if debug mode is enabled
if (boolval(SAML2Auth::get_package_config()[1]["debug_mode"])) {
    echo "<p>--------- GENERAL --------</p>".PHP_EOL;
    echo "<pre>SAML2 Name ID: ".$_SESSION['saml2_name_id']."</pre>".PHP_EOL;
    echo "<p>--------- ATTRIBUTES --------</p>".PHP_EOL;
    # Loop through and print each of our received SAML2 attributes
    if (is_array($_SESSION["saml2_user_data"])) {
        foreach ($_SESSION["saml2_user_data"] as $attr_name=>$attr_val) {
            echo "<pre>";
            echo $attr_name.": ";
            print_r($attr_val);
            echo "</pre>";
        }
    }
    echo "<p>--------- PHP-SAML2 SETTINGS --------</p>".PHP_EOL;
    echo "<pre>";
    print_r(SAML2Auth::get_saml_settings());
    echo "</pre>";
}
# Otherwise redirect to home page
else {
    header("Location: /");
    exit();
}