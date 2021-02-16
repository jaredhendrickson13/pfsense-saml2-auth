<?php
require_once("saml2_auth/lib/php-saml-3.5.1/_toolkit_loader.php");

class saml2_auth {
    public $auth;

    # Constructs the saml2_auth object by loading SAML2 settings and creating the OneLogin_Saml2_Auth object
    public function __construct() {
        session_start();
        $this->auth = new OneLogin\Saml2\Auth($this->__get_saml_settings());
    }

    # Initiates the SSO login. Requires a URL to redirect to
    public function sso($redirect) {
        # If AuthNRequest ID need to be saved in order to later validate it, do instead
        $this->auth->login($redirect);
    }

    public function acs() {
        # Process our request response
        $this->auth->processResponse($_SESSION['AuthNRequestID']);

        # Handle errors
        if (!empty($this->auth->getErrors())) {
            echo '<p>',implode(', ', $this->auth->getErrors()),'</p>';
            if ($this->auth->getSettings()->isDebugActive()) {
                echo '<p>'.$this->auth->getLastErrorReason().'</p>';
                exit();
            }
        }

        # If the sign on attempt is valid, map attributes to our session array.
        if ($this->auth->isAuthenticated()) {
            # Set session data
            $_SESSION["saml2_auth"] = true;
            $_SESSION['samlUserdata'] = $this->auth->getAttributes();
            $_SESSION['samlNameId'] = $this->auth->getNameId();
            $_SESSION['samlNameIdFormat'] = $this->auth->getNameIdFormat();
            $_SESSION['samlNameIdNameQualifier'] = $this->auth->getNameIdNameQualifier();
            $_SESSION['samlNameIdSPNameQualifier'] = $this->auth->getNameIdSPNameQualifier();
            $_SESSION['samlSessionIndex'] = $this->auth->getSessionIndex();
            unset($_SESSION['AuthNRequestID']);

            # Support RelayState settings
            if (isset($_POST['RelayState']) && OneLogin\Saml2\Utils::getSelfURL() != $_POST['RelayState']) {
                $this->auth->redirectTo($_POST['RelayState']);
            }
        }
    }

    public function metadata() {
        try {
            #$auth = new OneLogin_Saml2_Auth($settingsInfo);
            #$settings = $auth->getSettings();
            // Now we only validate SP settings
            $settings = new OneLogin\Saml2\Settings($this->__get_saml_settings(), true);
            $metadata = $settings->getSPMetadata();
            $errors = $settings->validateMetadata($metadata);
            if (empty($errors)) {
                header('Content-Type: text/xml');
                echo $metadata;
            } else {
                throw new OneLogin\Saml2\Error (
                    'Invalid SP metadata: '.implode(', ', $errors),
                    OneLogin\Saml2\Error::METADATA_SP_INVALID
                );
            }
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }

    private function __get_saml_settings() {
        return array (
            'debug' => true,
            'sp' => array (
                'entityId' => 'https://172.16.77.2/saml2_auth/sso/metadata/',
                'assertionConsumerService' => array (
                    'url' => 'https://172.16.77.2/saml2_auth/sso/acs/',
                ),
                'NameIDFormat' => 'urn:oasis:names:tc:SAML:1.1:nameid-format:unspecified',
            ),
            'idp' => array (
                'entityId' => 'http://www.okta.com/exk5ufltyrn6qaY3a357',
                'singleSignOnService' => array (
                    'url' => 'https://securitymetrics.okta.com/app/securitymetrics_lecert_3/exk5ufltyrn6qaY3a357/sso/saml',
                ),
                'x509cert' => '-----BEGIN CERTIFICATE-----
MIIDrjCCApagAwIBAgIGAW7IHfELMA0GCSqGSIb3DQEBCwUAMIGXMQswCQYDVQQGEwJVUzETMBEG
A1UECAwKQ2FsaWZvcm5pYTEWMBQGA1UEBwwNU2FuIEZyYW5jaXNjbzENMAsGA1UECgwET2t0YTEU
MBIGA1UECwwLU1NPUHJvdmlkZXIxGDAWBgNVBAMMD3NlY3VyaXR5bWV0cmljczEcMBoGCSqGSIb3
DQEJARYNaW5mb0Bva3RhLmNvbTAeFw0xOTEyMDIxOTM2MTVaFw0yOTEyMDIxOTM3MTVaMIGXMQsw
CQYDVQQGEwJVUzETMBEGA1UECAwKQ2FsaWZvcm5pYTEWMBQGA1UEBwwNU2FuIEZyYW5jaXNjbzEN
MAsGA1UECgwET2t0YTEUMBIGA1UECwwLU1NPUHJvdmlkZXIxGDAWBgNVBAMMD3NlY3VyaXR5bWV0
cmljczEcMBoGCSqGSIb3DQEJARYNaW5mb0Bva3RhLmNvbTCCASIwDQYJKoZIhvcNAQEBBQADggEP
ADCCAQoCggEBAOEd/cSHdKF9EunVwdk+WoxbSRHDBo/AFW91XQP3WKftAQ0hBvUlIxJ5CznZQmsU
QcGVNE2oEkOkKuP+GOil2vKa5k3sBvBDcFB5LdqQCS1mfL2o3ETTzixZZHZlCjpcL+4oRVnkSDoz
i8Sn4AQ+X3h0xOdkEruXds0F/+KjWzEVUIbhZhTuLkBe3D0igqg+ZxEHucL1gJK+PCzd2jDFCTw/
a5qQlpExEIB9gKUeTzuh0EuCQTn881y3mq+8mq6uGQeenHanA4oPtLnuTY0WlZtIMaKRimWrvEfj
zgS8ilJ7/PzQ9ZUHFlOTXueJ/F6u7w4Gjc0FkBk4Fsj3uKlliv0CAwEAATANBgkqhkiG9w0BAQsF
AAOCAQEAKhu5k/iJWgapcJXZfDs87WMyAGfncKJ/PnzBDtEn3Y/OaTAnBf0nzIOlHDv+evKu8NwE
9U4nLYnCtfwyEXnr+/YW+v3l7Q98SMSeM/boivjXeSRcv4u2+JLc6e4eJEYQmMDYoV44jrZMdWCc
425+w2LQYOZj+ECHW6rYzh1e8CjMOhNgg8bXOo/+TUqzJqj7BRuvdM/XQXZohiJBfnWrq38a4ami
2XuWdG+4eanRO0v55H6PqN+CK1no4uRDk6lhnm36GWTwPJu8EPa7DbVu0F6RH971eE7jLrtVa0Rg
WuzotgSL1xE9+m2L5LVX4FYdYvwAZ+NkCJQkpwRCvDUqvw==
-----END CERTIFICATE-----
',
            ),
        );
    }


}