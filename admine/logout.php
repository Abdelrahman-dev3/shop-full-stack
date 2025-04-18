<?php
session_start();
session_unset();
session_destroy();
header("location:admine_login.php");
exit();