<?php
# Imports and inits
$pgtitle = array(gettext("System"), gettext("SAML2"));
require_once("guiconfig.inc");
include('head.inc');

# Variables
$form = new Form(false);
$general_section = new Form_Section('General');
$idp_section = new Form_Section('Identity Provider Settings (IdP)');
$sp_section = new Form_Section('Service Provider Settings (SP)');

# POPULATE THE GENERAL SECTION OF THE UI
$general_section->addInput(new Form_Checkbox(
    'enable',
    'Enable',
    '',
    true
))->setHelp("Enable SAML2 authentication for the pfSense webConfigurator.");

$general_section->addInput(new Form_Checkbox(
    'strip_username',
    'Filter Email Usernames',
    '',
    true
))->setHelp(
    "Enable removal any characters after the @ character on email usernames. This is required if you intend to use SAML
    authentication that maps to an existing local user and your IdP returns email addresses as the username by default."
);

# POPULATE THE IDP SECTION OF THE UI
$idp_section->addInput(new Form_Input(
    'idp_entity_id',
    'Identity Provider Entity ID',
    'text',
    null,
    ['placeholder' => 'URL or alternate ID']
))->setHelp('Set the entity ID of the upstream identity provider. This will be provided by your IdP.');

$idp_section->addInput(new Form_Input(
    'idp_sign_on_url',
    'Identity Provider Sign-on URL',
    'text',
    null,
    ['placeholder' => 'URL']
))->setHelp('Set the sign-on URL of the upstream identity provider. This will be provided by your IdP.');

$idp_section->addInput(new Form_Textarea(
    'idp_x509_cert',
    'Identity Provider x509 Certificate',
    ''
))->setHelp(
    'Paste the x509 certificate data from the upstream identity provider. In most cases, this will be provided
    by your IdP.'
);

# POPULATE THE IDP SECTION OF THE UI
$sp_section->addInput(new Form_Input(
    'sp_base_url',
    'Service Provider Base URL',
    'text',
    null,
    ['placeholder' => 'URL']
))->setHelp(
    "Set the base URL of the service provider (pfSense). This must be the URL that is used to access pfSense's
    webConfigurator"
);

$form->add($general_section);
$form->add($idp_section);
$form->add($sp_section);


$form->addGlobal(new Form_Button(
    'Submit',
    'Save',
    null,
    'fa-save'
))->addClass('btn-primary');

print $form;
include('foot.inc');
