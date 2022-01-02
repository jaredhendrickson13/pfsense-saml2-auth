<?php
//    Copyright 2022 Jared Hendrickson
//
//   Licensed under the Apache License, Version 2.0 (the "License");
//   you may not use this file except in compliance with the License.
//   You may obtain a copy of the License at
//
//       http://www.apache.org/licenses/LICENSE-2.0
//
//   Unless required by applicable law or agreed to in writing, software
//   distributed under the License is distributed on an "AS IS" BASIS,
//   WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
//   See the License for the specific language governing permissions and
//   limitations under the License.

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
