<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
    <base data="<?php echo base_url(); ?>" />    
	<link rel="icon" href="<?php echo base_url('favicon.png'); ?>" type="image/gif">   
    <meta name="application-name" content="Debbys Kitchen">
    <link rel="manifest" href="<?php echo base_url("manifest.json").$time; ?>">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<title><?php echo isset($title) ? $title : common::config("app_name"); ?></title>	       
    <link rel="stylesheet" type="text/css" href="<?php echo base_url('bootstrap/materialize/css/materialize.css'.$time.''); ?>">     
    <link type="text/css" rel="stylesheet" href="<?php echo base_url('bootstrap/css/main.css'.$time.''); ?>"\>
    <script type="text/javascript" src="<?php echo base_url('bootstrap/js/jquery-3.1.1.min.js'); ?>"></script>
    <script type="text/javascript" src="<?php echo base_url('bootstrap/js/jquery.min.js'); ?>"></script>    
    <script type="text/javascript" src="<?php echo base_url('bootstrap/materialize/js/materialize.min.js'); ?>"></script>
    <!-- <script type="text/javascript" src="<?php echo  base_url('bootstrap/js/google_charts.js'); ?>"></script>-->
    <script type="text/javascript" src="<?php echo  base_url('bootstrap/js/main.js'.$time.''); ?>"></script>
   
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <!-- <link href="https://fonts.googleapis.com/icon?family=Material+Icons"
      rel="stylesheet"> -->
    <meta name="theme-color" content="#17394B">    
</head>
<body>