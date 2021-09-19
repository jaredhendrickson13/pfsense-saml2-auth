#!/usr/local/bin/php -f
<?php
require_once("saml2_auth/SAML2Auth.inc");

# Fetches the current version of pfSense
function pfsense_version($full=false) {
    # Only display the full release name if requested, Otherwise only provide the major and minor.
    return ($full) ? file_get_contents("/etc/version") : substr(file_get_contents("/etc/version"), 0, 3);
}

# Pulls our current pfSense SAML2 configuration and saves to a non-volatile location.
function backup($path="/usr/local/share/pfSense-pkg-saml2-auth/backup.json") {
    # Local variables
    $config_data = SAML2Auth::get_package_config()[1];

    # Print status message
    echo "Backing up configuration...";

    # Save a JSON file containing the data
    file_put_contents($path, json_encode($config_data));
    echo "done." . PHP_EOL;
}

# Restores the pfSense SAML2 configuration from a specified backup file
function restore($path="/usr/local/share/pfSense-pkg-saml2-auth/backup.json") {
    # Local variables
    global $config;
    $config_id = SAML2Auth::get_package_config()[0];
    $config_json = file_get_contents($path);
    $config_data = json_decode($config_json, true);

    # Print status message
    echo "Restoring configuration...";

    # Save the backup configuration to the pfSense master configuration if found
    if (file_exists($path)) {
        $config["installedpackages"]["package"][$config_id]["conf"] = $config_data;
        write_config("Restoring SAML2 configuration");
        echo "done." . PHP_EOL;
    }
    else {
        echo "no backup found.".PHP_EOL;
    }
}

# Update to the latest version of the package
function update() {
    # Local variables
    $url = "https://github.com/jaredhendrickson13/pfsense-saml2-auth/releases/latest/download/pfSense-".pfsense_version()."-pkg-saml2-auth.txz";

    # Backup the package configuration before updating
    backup();

    # Remove the existing package and add the latest
    echo shell_exec("pkg delete -y pfSense-pkg-saml2-auth");
    echo shell_exec("pkg add ".$url);
}

# Display the current version of pfSense and pfSense-pkg-saml2-auth
function version() {
    # Local variables
    $pkg_info = shell_exec("pkg info pfSense-pkg-saml2-auth").PHP_EOL;
    $pkg_info = explode(PHP_EOL, $pkg_info);
    $pf_ver_line = [str_replace(PHP_EOL, "", "pfSense Version: ".pfsense_version(true))];
    array_splice($pkg_info, 3, 0, $pf_ver_line);

    echo implode(PHP_EOL, $pkg_info);
}

# Displays the help page for the SAML2 management tool
function help() {
    echo "pfsense-saml2 - CLI tool for pfSense-pkg-saml2-auth package management".PHP_EOL;
    echo "Copyright - ".date("Y")."&copy - Jared Hendrickson".PHP_EOL;
    echo "SYNTAX:".PHP_EOL;
    echo "  pfsense-saml2 <command> <args>".PHP_EOL;
    echo "COMMANDS:".PHP_EOL;
    echo "  backup      : Creates a JSON backup of the SAML2 configuration".PHP_EOL;
    echo "  restore     : Restores the SAML2 configuration from a JSON backup".PHP_EOL;
    echo "  update      : Update to the latest version of the package".PHP_EOL;
    echo "  version     : Displays the current version of pfSense-pkg-saml2-auth".PHP_EOL;
    echo "  help        : Displays the help page (this page)".PHP_EOL.PHP_EOL;
}

function runtime()
{
    # Run backup command if requested
    if ($_SERVER["argv"][1] === "backup") {
        backup();
    }
    # Run the restore command if requested
    elseif ($_SERVER["argv"][1] === "restore") {
        restore();
    }
    # Run the update command if requested
    elseif ($_SERVER["argv"][1] === "update") {
        update();
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
