<?php
require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/services/SessionService.php';

use services\SessionService;

SessionService::getInstance()->logout();
header('Location: /index.php');
