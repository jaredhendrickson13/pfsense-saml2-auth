DEFAULT FILE OVERRIDES
======================
pfSense-pkg-saml2-auth works by overriding the original pfSense authentication functions with versions that include 
SAML2 authentication. This essential replaces the existing PHP files with the same file that contains the extra code.
Because of this, the file overrides are dependent on the platform they were originally created for. Upon installation, 
the package attempts to determine the version pfSense running on the local system by reading the `/etc/version` file
before overriding files. In the case that no file overrides exist for your version of pfSense, the default file overrides
will be used. This may or may not work with your version of pfSense and you will essentially run the package in an 
unsupported manner. A warning message will be displayed during package installation if the package is installed using
the default overrides. In the case that the default overrides break your pfSense instance, simply uninstall the package
to restore the overridden files to their original state.

## Default Override Version
2.5.2-RELEASE
