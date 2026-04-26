<?php

session_start();

require_once '../config/config.php';
require_once '../config/database.php';
require_once '../config/google.php';
require_once '../core/App.php';
require_once '../core/Controller.php';
require_once '../core/Model.php';

$app = new App();