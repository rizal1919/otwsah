<?php require_once __DIR__ . '/../config/db.php'; // Panggil config ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= isset($pageTitle) ? $pageTitle : 'Dashboard' ?> || OtwSah Admin</title>
    
    <link rel="shortcut icon" type="image/x-icon" href="<?= url('assets/images/favicon.ico') ?>">
    <link rel="stylesheet" type="text/css" href="<?= url('assets/css/bootstrap.min.css') ?>">
    <link rel="stylesheet" type="text/css" href="<?= url('assets/vendors/css/vendors.min.css') ?>">
    <link rel="stylesheet" type="text/css" href="<?= url('assets/vendors/css/dataTables.bs5.min.css') ?>">
    <link rel="stylesheet" type="text/css" href="<?= url('assets/vendors/css/select2.min.css') ?>">
    <link rel="stylesheet" type="text/css" href="<?= url('assets/vendors/css/select2-theme.min.css') ?>">
    <link rel="stylesheet" type="text/css" href="<?= url('assets/css/theme.min.css') ?>">
</head>
<body>