function sso_redirect() {
    // Find our anchor tag and simulate a click using JavaScript
    var redirect = document.getElementById("no-referrer-anchor");
    redirect.href = "/";
    redirect.click();
}