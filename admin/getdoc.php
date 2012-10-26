<?php
$admin='yup';
require_once('../db.inc.php');
$alias=array_pop(array_keys($_GET));
$db->getLicenseDoc($alias);