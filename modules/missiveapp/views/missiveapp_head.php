<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php $isRTL = (is_rtl() ? 'true' : 'false'); ?>

<!DOCTYPE html>
<html lang="<?php echo $locale; ?>" dir="<?php echo ($isRTL == 'true') ? 'rtl' : 'ltr' ?>">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <title><?php echo isset($title) ? $title : get_option('companyname'); ?></title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <?php echo compile_theme_css(); ?>

    <style type="text/css">
        .tag [data-role="remove"]:after {
            content: "x";
            padding: 0px 2px;
            color: red;
        }
        #wrapper {
          margin: 0 0 0 0;
        }
        .alert.alert-vk {
            position: relative;
        }
        .contact-nav-tabs {
            margin-bottom: 0px !important;
        }
        .box-sales {
            background-color: #fff;
        }
        .table-responsive.table-responsive-sales {
            padding: 0px;
            margin-bottom: 0px;
        }
        .new-proposal-card{
            padding: 10px;
        }
        .input-transparent{
            border: none !important;
            background-color: transparent !important;
            box-shadow: none !important;
            width: 50px !important;
            padding: 0px !important;
            line-height: 15px !important;
            height: 15px !important;
        }
        .unit-label{
            display: flex;
            align-items: center;
        }
        .new-proposal-addRow{
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .gap-row{
            display: flex;
            align-items: center;
            gap: 4px;
        }
        .form-group{
            width: 100%;
        }
        .Client-badge{
            padding: 0px 18px;
            background: #c7c7c7;
            border-radius: 5px;
        }
        .Client-badge{
            padding: 0px 18px;
            background: #c7c7c7;
            border-radius: 5px;
        }
        .lead-badge{
            padding: 0px 18px;
            background: #f3afaf;
            border-radius: 5px;
        }
        .tagsinput,
        input#tags {
        width: 100%;
        opacity: 0;
        height: 31.56px;
        }
        .select2-container{
            width: 100% !important;
        }
        .new-proposal-card input{
            padding: 0px 8px;
            line-height: 30px;
            height: 30px;
        }
        ul.contact-nav-tabs{
            display: flex;
            justify-content: center;
            align-items: center;
        }
        ul.contact-nav-tabs li{
            border: 3px solid #787a7d;
            margin: 5px 10px;
            border-radius: 13px;
            appearance: none;
            backface-visibility: hidden;
            background-color: #787a7d;
            border-radius: 10px;
            border-style: none;
            box-shadow: none;
            box-sizing: border-box;
            color: #fff;
            cursor: pointer;
            display: inline-block;
            font-family: Inter,-apple-system,system-ui,"Segoe UI",Helvetica,Arial,sans-serif;
            font-size: 15px;
            font-weight: 500;
            letter-spacing: normal;
            line-height: 1.5;
            outline: none;
            overflow: hidden;
            position: relative;
            text-align: center;
            text-decoration: none;
            transform: translate3d(0, 0, 0);
            transition: all .3s;
            user-select: none;
            -webkit-user-select: none;
            touch-action: manipulation;
            vertical-align: top;
            white-space: nowrap;
        }
        ul.contact-nav-tabs li a{
            color: white;
            padding: 6px 10px;
        }
        ul.contact-nav-tabs li.active{
            border: 3px solid #c3b9c3;
        }
        ul.contact-nav-tabs li.active a{
            color: white !important;
        }
        .align-start{
            align-items: flex-start !important;
        }
        .missive-img{
            width: 15px;
            height: 15px;
            margin-right: 10px;
        }
        .sales-card{
            padding: 3px 6px;
            font-weight: 500;
            font-size: 12px;
        }
        .seles-card-row{
            font-size: 11px;
            font-weight: 500;
            line-height: 20px;
            display: flex;
            justify-content: start;
            align-items: center;
        }
        .lastNote-row{
            width: 100%;
        }
        .lastNote-description{
            width: 100%;
            padding: 0 0 10px 0;
            color: #858585;
        }
        .lastNote-date{
            width: 100%;
            border-bottom: 1px solid;
            font-style: italic;
            font-size: 11px;
            margin-bottom: 10px;
            color: #858585;
        }
        
        select.form-control{
            padding: 0 3px !important;
            margin: 0px !important;
            line-height: 1px !important;
            height: 25px !important;
        }
        input[type=text]{
            padding: 0px 3px !important;
        }
        input[type=email]{
            padding: 0px 3px !important;
        }
        input[type=checkbox]{
            margin: 0 !important;
            font-size: 11px;
            appearance: none;
            -webkit-appearance: none;
            align-content: center;
            justify-content: center;
            padding: 0.1rem;
            margin: 0;
        }
        input[type=checkbox]::before{
            font-family: "Font Awesome 4 Free";
            content: "\f095";
            padding: 0 1px;
            display: inline-block;
            border-radius: 50px;
        }
        .seles-default-checked::before {
            background-color: white;
            color: white;
            border: 1px solid #787a7d;
        }
        .seles-default-checked {
            margin-right: 5px;
            border-radius: 50px;
        }
        .seles-invoiced-checked:checked:before{
            background-color: #04af54;
            color: #04af54;
            border: 1px solid #787a7d;
        }
        .seles-invoiced-checked::before {
            background-color: white;
            color: white;
            border: 1px solid #787a7d;
        }
        .seles-invoiced-checked {
            margin-right: 5px;
            border-radius: 50px;
        }
        .seles-passed-checked::before {
            background-color: yellow;
            color: yellow;
            border: 1px solid #787a7d;
        }
        .seles-passed-checked {
            margin-right: 5px;
            border-radius: 50px;
        }
        .contact-card{
            padding: 3px 6px;
            font-weight: 500;
        }
        .contact-card-head{
            width: 100%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 6px;
        }
        .button-small{
            padding: 3px 12px !important;
        }
        .customerGroup-badge{
            padding: 3px 14px;
            border-radius: 8px;
            font-weight: 500;
        }
        .contact-data-row{
            display: flex;
            justify-content: start;
            align-items: center;
            padding: 1px;
        }
        .contact-data-row .data-Name{
            width: 30%;
        }
        .contact-data-row .data-value{
            display: flex;
            width: 70%;
        }
        .lead-input{
            padding: 0px 3px !important;
            border: none !important;
            background: white !important;
        }
        .lead-input.active{
            border: 1px solid !important;
            background: #d1cfcf !important;
        }
        .contact-input{
            padding: 0px 3px !important;
            border: none !important;
            background: white !important;
        }
        input.contact-input[name='firstName']{
            width: 50%;
        }
        .contact-input.active{
            border: 1px solid !important;
            background: #d1cfcf !important;
        }
    </style>
    <link href="https://integrations.missiveapp.com/missive.css" rel="stylesheet">

    <?php render_admin_js_variables(); ?>
    
</head>

<body>
