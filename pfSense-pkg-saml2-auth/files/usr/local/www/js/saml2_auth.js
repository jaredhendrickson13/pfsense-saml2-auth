function start_sso() {
    // Set the saml_login trigger and submit the login form to begin SSO authentication
    document.getElementById("saml2_login_input").value = "1";
    document.getElementById("saml2_login_button").parentNode.submit();
}