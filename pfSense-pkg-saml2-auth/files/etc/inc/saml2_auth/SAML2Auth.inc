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

        # Try to start SAML2 authentication, handle errors accordingly
        try {
            $this->auth = new OneLogin\Saml2\Auth($this::get_saml_settings());
        } catch (OneLogin\Saml2\Error $error) {
            echo $error->getMessage().PHP_EOL;
            exit();
        }
    }

    # Initiates the SSO login. Requires a URL to redirect to
    public function sso($redirect) {
        # Mark the SAML2 login as in process and start login
        $_SESSION["saml2_started"] = true;
        $this->auth->login($redirect);
    }

    public function acs() {
        # Check the state of SAML2 authentication. Only proceeds if in expected state.
        $this->__check_saml2_state();

        # Remove the saml2_started handler and process our request response
        unset($_SESSION['saml2_started']);
        $this->auth->processResponse($_SESSION['AuthNRequestID']);

        # If the sign on attempt is valid, map attributes to our session array.
        if ($this->auth->isAuthenticated()) {
            # Set session data
            $_SESSION["saml2_auth"] = true;
            $_SESSION['saml2_user_data'] = $this->auth->getAttributes();
            $_SESSION['saml2_name_id'] = $this->auth->getNameId();
            unset($_SESSION['AuthNRequestID']);

            # Support RelayState settings
            if (isset($_POST['RelayState']) && OneLogin\Saml2\Utils::getSelfURL() != $_POST['RelayState']) {
                $this->auth->redirectTo($_POST['RelayState']);
            }
        }

        # Handle SAML errors
        $this->get_saml2_errors();
    }

    public function metadata() {
        try {
            # Validate the SP metadata and print the XML metadata if valid
            $settings = new OneLogin\Saml2\Settings($this->get_saml_settings(), true);
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

    private function __check_saml2_state() {
        # Redirect to home page if user is already logged in
        if ($_SESSION['Logged_In']) {
            header("Location: /saml2_auth/sso/redirect/");
            exit();
        }
        # Redirect to SSO login if the login process has not been started
        elseif (!$_SESSION['saml2_started']) {
            header("Location: /saml2_auth/sso/");
            exit();
        }
    }

    public function get_saml2_errors() {
        # Print SAML errors if they exist
        if (!empty($this->auth->getErrors())) {
            session_destroy();
            echo '<pre>',implode(', ', $this->auth->getErrors()),'</pre>';
            if ($this->auth->getSettings()->isDebugActive()) {
                echo '<pre>'.$this->auth->getLastErrorReason().'</pre>';
                exit();
            }
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

    public static function get_saml_settings() {
        # Local variables
        $pkg_conf = SAML2Auth::get_package_config()[1];
        $php_saml_config = array (
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

        # When custom parameters are configured, update the config to include them
        if (!empty($pkg_conf["custom_conf"])) {
            # Decode the custom configuration values. This should be a Base64 encoded JSON string
            $custom_conf = json_decode(base64_decode($pkg_conf["custom_conf"]), true);
            # Only merge custom configuration in if it decodes to an array
            if (is_array($custom_conf)) {
                $php_saml_config = array_merge_recursive($php_saml_config, $custom_conf);
            }
        }
        return $php_saml_config;
    }


}