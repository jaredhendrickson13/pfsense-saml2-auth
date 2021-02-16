<?php
session_start();

# Strip the email portion of the userNameId if present. This is necessary since pfSense doesn't allow email usernames.
if (isset($_SESSION["samlNameId"] )) {
    $_SESSION["samlNameId"] = explode("@", $_SESSION["samlNameId"])[0];
}

?>
<!--Use JavaScript to redirect without a HTTP_REFERRER header. Required to bypass pfSense HTTP_REFERRER checks.-->
<a rel="noreferrer" id="no-referrer-anchor"></a>
<script type="application/javascript" src="/js/saml2_auth.js"></script>
<script>sso_redirect()</script>

