<?php

// ENCERRA A SESSÃO DO USUÁRIO

session_start();
session_destroy();
header("Location: ../login.php");
exit;
