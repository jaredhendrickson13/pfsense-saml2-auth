<?php
//    Copyright 2022 Jared Hendrickson
//
//   Licensed under the Apache License, Version 2.0 (the "License");
//   you may not use this file except in compliance with the License.
//   You may obtain a copy of the License at
//
//       http://www.apache.org/licenses/LICENSE-2.0
//
//   Unless required by applicable law or agreed to in writing, software
//   distributed under the License is distributed on an "AS IS" BASIS,
//   WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
//   See the License for the specific language governing permissions and
//   limitations under the License.
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
