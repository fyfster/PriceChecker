<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9"> <![endif]-->
<!--[if !IE]><!--> <html lang="en"> <!--<![endif]-->
    <!-- BEGIN HEAD -->

    <head>
        <meta charset="utf-8"/>
        <title>Wide Front Pack}</title>
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta content="width=device-width, initial-scale=1" name="viewport"/>
        <meta content="" name="description"/>
        <meta content="" name="author"/>
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <!-- BEGIN GLOBAL MANDATORY STYLES -->
        <link rel="icon" type="image/gif/png" href="{{$base_url}}/assets/img/title_img.jpg">
        <link href="http://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700&subset=all" rel="stylesheet" type="text/css"/>
        <link href="{{ url('assets/global/plugins/font-awesome/css/font-awesome.min.css') }}" rel="stylesheet" type="text/css"/>
        <link href="{{ url('assets/global/plugins/simple-line-icons/simple-line-icons.min.css') }}" rel="stylesheet" type="text/css"/>
        <link href="{{ url('assets/global/plugins/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css"/>
        <link href="{{ url('assets/global/plugins/bootstrap-switch/css/bootstrap-switch.min.css') }}" rel="stylesheet" type="text/css"/>
        <!-- END GLOBAL MANDATORY STYLES -->


        <!-- BEGIN THEME GLOBAL STYLES -->
        <link href="{{ url('assets/global/css/components.css') }}" rel="stylesheet" type="text/css"/>
        <link href="{{ url('assets/global/css/plugins.css') }}" rel="stylesheet" type="text/css"/>
        <link href="{{ url('assets/global/plugins/jquery-ui/jquery-ui.min.css') }}" rel="stylesheet" type="text/css"/>
        <!-- END THEME GLOBAL STYLES -->
        <!-- BEGIN THEME LAYOUT STYLES -->
        <link href="{{ url('assets/layouts/layout/css/layout.css') }}" rel="stylesheet" type="text/css"/>
        <link href="{{ url('assets/layouts/layout/css/themes/light2.css') }}" rel="stylesheet" type="text/css" id="style_color"/>
        <link href="{{ url('assets/layouts/layout/css/custom.css') }}" rel="stylesheet" type="text/css"/>
        <!-- END THEME LAYOUT STYLES -->

        @if(isset($useSelect2))
            <link href="{{ url('assets/global/plugins/select2/css/select2.css') }}" rel="stylesheet" type="text/css"/>
            <link href="{{ url('assets/global/plugins/select2/css/select2-bootstrap.min.css') }}" rel="stylesheet" type="text/css"/>
        @endif
        @if(isset($useDatatables))
            <link href="{{ url('assets/global/plugins/datatables/datatables.min.css') }}" rel="stylesheet" type="text/css"/>
            <link href="{{ url('assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.css') }}" rel="stylesheet" type="text/css"/>
        @endif
        @if(isset($useDatePicker))
            <link href="{{ url('assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker.css') }}" rel="stylesheet" type="text/css"/>
            <link href="{{ url('assets/global/plugins/bootstrap-datetimepicker/bootstrap-datetimepicker.css') }}" rel="stylesheet" type="text/css"/>
        @endif
        @if(isset($useCalendar))
            <link href="{{ url('assets/global/plugins/calendar/fullcalendar.min.css') }}" rel="stylesheet" type="text/css"/>
        @endif
        @if(isset($useProfile))
            <link href="{{ url('assets/global/css/profile.css') }}" rel="stylesheet" type="text/css"/>
        @endif

        <link href="{{ url('css/custom.css') }}" rel="stylesheet" type="text/css"/>

        <link rel="shortcut icon" href="{{ url('favicon.ico') }}"/></head>

        <script src="{{ url('assets/global/plugins/jquery.min.js') }}" type="text/javascript"></script>

        <body class="page-header-fixed page-sidebar-closed-hide-logo page-content-white">
            <div class="page-wrapper">
            @include('common.header-navbar')
            <!-- BEGIN HEADER & CONTENT DIVIDER -->
            <div class="clearfix"> </div>
            <!-- END HEADER & CONTENT DIVIDER -->
            @include('common.menu-sidebar')
