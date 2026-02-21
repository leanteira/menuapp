<?php

require_once __DIR__ . '/mr_auth.php';

mr_logout();

header('Location: login.php');
exit;
