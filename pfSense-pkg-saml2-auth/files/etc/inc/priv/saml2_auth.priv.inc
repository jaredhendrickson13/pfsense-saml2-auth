<?php
//   Copyright 2025 Jared Hendrickson
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

global $priv_list;

$priv_list['page-system-saml2-auth'] = array();
$priv_list['page-system-saml2-auth']['name'] = "WebCfg - System: SAML2 authentication package";
$priv_list['page-system-saml2-auth']['descr'] = "Allow access to SAML2 authentication package UI";
$priv_list['page-system-saml2-auth']['match'] = array();
$priv_list['page-system-saml2-auth']['match'][] = "saml2_auth*";
