<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Railway Cloud MySQL Database Credentials
define('DB_HOST', 'ballast.proxy.rlwy.net');
define('DB_PORT', '27321');
define('DB_NAME', 'railway');
define('DB_USER', 'root');
define('DB_PASS', 'YbLDNCiwCsLAdIKSHNtshDsoXsCFVoZQ');

// PayMongo keys (https://dashboard.paymongo.com -> Developers)
define('PAYMONGO_SECRET', 'sk_test_xxxxxxxxxxxxxxxx');

define('CURRENCY', 'PHP');
define('BASE_URL', 'https://novelverse-qsavbd29z-ihwa.vercel.app');