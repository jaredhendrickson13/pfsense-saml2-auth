function sso_redirect() {
    // Find our anchor tag and simulate a click using JavaScript
    var redirect = document.getElementById("saml2_redirect_no_referrer");
    redirect.href = "/";
    redirect.click();
}