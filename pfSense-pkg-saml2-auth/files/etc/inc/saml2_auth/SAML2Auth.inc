<?php
require_once("saml2_auth/lib/php-saml-3.5.1/_toolkit_loader.php");
require_once("config.inc");

class SAML2Auth {
    public $auth;
    private $config;

    # Constructs the saml2_auth object by loading SAML2 settings and creating the OneLogin_Saml2_Auth object
    public function __construct() {
        session_start();
        global $config;
        $this->config = $config;
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

    public static function get_package_config() {
        global $config;
        # Only proceed if there are installed packages. Prevents PHP warnings.
        if (is_array($config["installedpackages"]["package"])) {
            # Loop through each installed package until we find the saml2-auth package
            foreach ($config["installedpackages"]["package"] as $id=>$pkg) {
                if ($pkg["internal_name"] === "saml2-auth") {
                    return [$id, $pkg["conf"]];
                }
            }
        }
    }

    private function __get_saml_settings() {
        # Local variables
        $pkg_conf = SAML2Auth::get_package_config()[1];

        return array (
            'debug' => boolval($pkg_conf["debug_mode"]),
            'sp' => array (
                'entityId' => $pkg_conf["sp_base_url"].'/saml2_auth/sso/metadata/',
                'assertionConsumerService' => array (
                    'url' => $pkg_conf["sp_base_url"].'/saml2_auth/sso/acs/',
                ),
                'NameIDFormat' => 'urn:oasis:names:tc:SAML:1.1:nameid-format:unspecified',
            ),
            'idp' => array (
                'entityId' => $pkg_conf["idp_entity_id"],
                'singleSignOnService' => array (
                    'url' => $pkg_conf["idp_sign_on_url"],
                ),
                'x509cert' => base64_decode($pkg_conf["idp_x509_cert"])
            ),
        );
    }


}