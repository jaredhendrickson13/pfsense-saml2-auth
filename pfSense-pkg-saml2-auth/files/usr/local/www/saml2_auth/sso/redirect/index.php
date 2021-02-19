<?php
?>
<head>
    <link rel="stylesheet" href="/css/saml2_auth.css" type="text/css">
    <script src="/js/saml2_auth.js"></script>
</head>

<body>
    <!--Use JavaScript to redirect without a HTTP_REFERRER header. Required to bypass pfSense HTTP_REFERRER checks.-->
    <p id="saml2_redirect_notice">Redirecting...please wait</p>
    <div id="saml2_redirect_loader_container"><div class="saml2_redirect_loader"></div></div>
    <a rel="noreferrer" id="saml2_redirect_no_referrer"></a>
    <script type="application/javascript">sso_redirect();</script>
</body>