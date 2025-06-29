<?php
//    Copyright 2025 Jared Hendrickson
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

# Create the saml2 authentication object
$saml2_auth = new SAML2Auth();

# Validate SSO response when. If no request was found during validation, print simple error
try {
    $saml2_auth->acs();
} catch (OneLogin\Saml2\Error $error) {
    echo $error->getMessage().PHP_EOL;
}
