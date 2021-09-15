<?php
//    Copyright 2021 Jared Hendrickson
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

# Imports and inits
$pgtitle = array(gettext("System"), gettext("SAML2"));
require_once("guiconfig.inc");
require_once("saml2_auth/SAML2Auth.inc");
include('head.inc');
global $config;

# Variables
$form = new Form(false);
$general_section = new Form_Section('General');
$idp_section = new Form_Section('Identity Provider Settings (IdP)');
$sp_section = new Form_Section('Service Provider Settings (SP)');
$advanced_section = new Form_Section('Advanced Settings');

$pkg_id = SAML2Auth::get_package_config()[0];
$pkg_conf = SAML2Auth::get_package_config()[1];

if ($_POST["save"]) {
    # Validate the enable value
    if (isset($_POST["enable"])) {
        if (boolval($_POST["enable"])) {
            $pkg_conf["enable"] = "yes";
        } else {
            unset($pkg_conf["enable"]);
        }
    }

    # Validate the enable value
    if (isset($_POST["enable"])) {
        $pkg_conf["enable"] = "yes";
    } else {
        unset($pkg_conf["enable"]);
    }

    # Validate the strip_username value
    if (isset($_POST["strip_username"])) {
        $pkg_conf["strip_username"] = "yes";
    } else {
        unset($pkg_conf["strip_username"]);
    }

    # Validate the debug_mode value
    if (isset($_POST["debug_mode"])) {
        $pkg_conf["debug_mode"] = "yes";
    } else {
        unset($pkg_conf["debug_mode"]);
    }

    # Validate the idp_entity_id value
    if (isset($_POST["idp_entity_id"])) {
        $pkg_conf["idp_entity_id"] = $_POST["idp_entity_id"];
    }

    # Validate the idp_sign_on_url value
    if (isset($_POST["idp_sign_on_url"])) {
        $pkg_conf["idp_sign_on_url"] = $_POST["idp_sign_on_url"];
    }

    # Validate the idp_groups_attribute value
    if (isset($_POST["idp_groups_attribute"])) {
        $pkg_conf["idp_groups_attribute"] = $_POST["idp_groups_attribute"];
    }

    # Validate the idp_x509_cert value
    if (isset($_POST["idp_x509_cert"])) {
        $pkg_conf["idp_x509_cert"] = base64_encode($_POST["idp_x509_cert"]);
    }

    # Validate the sp_base_url value
    if (isset($_POST["sp_base_url"])) {
        $pkg_conf["sp_base_url"] = $_POST["sp_base_url"];
    }

    # Validate the custom_conf value
    if (isset($_POST["custom_conf"])) {
        $pkg_conf["custom_conf"] = base64_encode($_POST["custom_conf"]);
    }

    # Write the configuration changes
    $config["installedpackages"]["package"][$pkg_id]["conf"] = $pkg_conf;
    write_config(sprintf(gettext(" Modified SAML2 settings")));
    shell_exec("pfsense-saml2 backup");
    print_apply_result_box(0);
}

# When the SP base URL is blank, default the values to the webConfigurators URL
if (empty($pkg_conf["sp_base_url"])) {
    $protocol = $config["system"]["webgui"]["protocol"];
    $hostname = $config["system"]["hostname"].".".$config["system"]["domain"];
    $port = (!empty($config["system"]["webgui"]["port"])) ? ":".$config["system"]["webgui"]["port"] : "";
    $pkg_conf["sp_base_url"] = $protocol."://".$hostname.$port;

    # Write the configuration changes
    $config["installedpackages"]["package"][$pkg_id]["conf"] = $pkg_conf;
    write_config(sprintf(gettext(" Reverted SAML2 base URL")));
    shell_exec("pfsense-saml2 backup");
}

# POPULATE THE GENERAL SECTION OF THE UI
$general_section->addInput(new Form_Checkbox(
    'enable',
    'Enable',
    '',
    $pkg_conf["enable"]
))->setHelp("Enable SAML2 authentication for the pfSense webConfigurator.");

$general_section->addInput(new Form_Checkbox(
    'strip_username',
    'Filter Email Usernames',
    '',
    $pkg_conf["strip_username"]
))->setHelp(
    "Enable removal any characters after the @ character on email usernames. This is required if you intend to use SAML
    authentication that maps to an existing local user and your IdP returns email addresses as the username by default."
);

$general_section->addInput(new Form_Checkbox(
    'debug_mode',
    'Debug',
    '',
    $pkg_conf["debug_mode"]
))->setHelp(
    "Enable debug mode for SAML2 logins. This will provide verbose errors when encountering SAML2 authentication errors.
    Do not leave debug mode enabled in a production environment!"
);

# POPULATE THE IDP SECTION OF THE UI
$idp_section->addInput(new Form_Input(
    'idp_entity_id',
    'Identity Provider Entity ID',
    'text',
    $pkg_conf["idp_entity_id"],
    ['placeholder' => 'URL or alternate ID']
))->setHelp('Set the entity ID of the upstream identity provider. This will be provided by your IdP.');

$idp_section->addInput(new Form_Input(
    'idp_sign_on_url',
    'Identity Provider Sign-on URL',
    'text',
    $pkg_conf["idp_sign_on_url"],
    ['placeholder' => 'URL']
))->setHelp('Set the sign-on URL of the upstream identity provider. This will be provided by your IdP.');

$idp_section->addInput(new Form_Input(
    'idp_groups_attribute',
    'Identity Provider Groups Attribute',
    'text',
    $pkg_conf["idp_groups_attribute"],
    ['placeholder' => 'Group attribute name']
))->setHelp('Set the groups attribute returned in the SAML assertion. This will be provided by your IdP if supported.');

$idp_section->addInput(new Form_Textarea(
    'idp_x509_cert',
    'Identity Provider x509 Certificate',
    base64_decode($pkg_conf["idp_x509_cert"])
))->setHelp(
    'Paste the x509 certificate data from the upstream identity provider. In most cases, this will be provided
    by your IdP.'
);

# POPULATE THE SP SECTION OF THE UI
$sp_section->addInput(new Form_Input(
    'sp_base_url',
    'Service Provider Base URL',
    'text',
    $pkg_conf["sp_base_url"],
    ['placeholder' => 'URL']
))->setHelp(
    "Set the base URL of the service provider (pfSense). This must be the URL that is used to access pfSense's
    webConfigurator."
);

$sp_section->addInput(new Form_StaticText(
    'Service Provider Entity ID',
    $pkg_conf["sp_base_url"].'/saml2_auth/sso/metadata/'
))->setHelp("Displays the service provider's entity ID. This is the entity ID you will need to provide to your IdP.");

$sp_section->addInput(new Form_StaticText(
    'Service Provider Sign-on URL',
    $pkg_conf["sp_base_url"].'/saml2_auth/sso/acs/'
))->setHelp(
    "Displays the service provider's sign-on URL. This is the URL you will need to provide to your IdP. They may refer
    to this URL as the assertion consumer service (ACS)."
);

# POPULATE THE ADVANCED SECTION OF THE UI
$advanced_section->addInput(new Form_Textarea(
    'custom_conf',
    'Custom SAML2 configuration',
    base64_decode($pkg_conf["custom_conf"])
))->setHelp(
    'Adds custom configuration for SAML2 logins. This allows you to add custom settings in JSON format for the
    <a href="https://github.com/onelogin/php-saml" target="_blank">OneLogin PHP-SAML</a> library to use. This option is
    unsupported. Use at your own risk.'
);

# POPULATE OUR COMPLETE FORM
$form->add($general_section);
$form->add($idp_section);
$form->add($sp_section);
$form->add($advanced_section);
$form->addGlobal(new Form_Button('save', 'Save', null, 'fa-save'))->addClass('btn-primary');

# PRINT OUR FORM AND PFSENSE FOOTER
print $form;
include('foot.inc');
