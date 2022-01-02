#!/usr/local/bin/php -f
<?php
require_once("saml2_auth/SAML2Auth.inc");

# Display the current version of pfSense and pfSense-pkg-saml2-auth
function version() {
    # Local variables
    $pkg_info = shell_exec("pkg info pfSense-pkg-saml2-auth").PHP_EOL;
    $pkg_info = explode(PHP_EOL, $pkg_info);
    $pf_ver_line = [str_replace(PHP_EOL, "", "pfSense Version: ".SAML2Auth::get_pfsense_version(true))];
    array_splice($pkg_info, 3, 0, $pf_ver_line);

    echo implode(PHP_EOL, $pkg_info);
}

# Displays the help page for the SAML2 management tool
function help() {
    echo "pfsense-saml2 - CLI tool for pfSense-pkg-saml2-auth package management".PHP_EOL;
    echo "Copyright - ".date("Y")."© - Jared Hendrickson".PHP_EOL;
    echo "SYNTAX:".PHP_EOL;
    echo "  pfsense-saml2 <command> <args>".PHP_EOL;
    echo "COMMANDS:".PHP_EOL;
    echo "  backup      : Creates a JSON backup of the SAML2 configuration".PHP_EOL;
    echo "  restore     : Restores the SAML2 configuration from a JSON backup".PHP_EOL;
    echo "  update      : Update to the latest version of the package".PHP_EOL;
    echo "  version     : Displays the current version of pfSense-pkg-saml2-auth".PHP_EOL;
    echo "  help        : Displays the help page (this page)".PHP_EOL.PHP_EOL;
}

function runtime() {
    # Variables
    $saml2 = new SAML2Auth(true);

    # Run backup command if requested
    if ($_SERVER["argv"][1] === "backup") {
        $saml2->backup_config(true);
    }
    # Run the restore command if requested
    elseif ($_SERVER["argv"][1] === "restore") {
        $saml2->restore_config(true);
    }
    # Run the update command if requested
    elseif ($_SERVER["argv"][1] === "update") {
        # Local variables
        $version = "latest";

        # Update/revert to the requested version if present
        if ($_SERVER["argv"][2]) {
            # Add the starting 'v' if it is missing
            if (substr($_SERVER["argv"][2], 0, 1) !== "v") {
                $_SERVER["argv"][2] = "v".$_SERVER["argv"][2];
            }

            # Ensure version exists
            if (array_key_exists($_SERVER["argv"][2], $saml2->get_all_pkg_versions())) {
                $version = $_SERVER["argv"][2];
            } else {
                echo "Could not locate package version '".$_SERVER["argv"][2]."'.".PHP_EOL;
                exit(1);
            }
        }
        $saml2->update_pkg($version, true);
    }
    # Run the version command if requested
    elseif ($_SERVER["argv"][1] === "version") {
        version();
    }
    # Run the help command if requested
    elseif ($_SERVER["argv"][1] === "help") {
        help();
    }
    else {
        echo "Unrecognized command.".PHP_EOL.PHP_EOL;
        help();
    }
}

runtime();
