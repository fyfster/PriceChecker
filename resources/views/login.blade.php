<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8 no-js"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9 no-js"> <![endif]-->
<html lang="en">

<head>
    <meta charset="utf-8"/>
    <title>Login</title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta content="width=device-width, initial-scale=1" name="viewport"/>
    <meta content="" name="description"/>
    <meta content="" name="author"/>
    <!-- BEGIN GLOBAL MANDATORY STYLES -->
    <link rel="icon" type="image/gif/png" href="../assets/company/title_img.jpg">
    <link href="http://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700&subset=all" rel="stylesheet" type="text/css" />
    <link href="{{ url('assets/global/plugins/font-awesome/css/font-awesome.min.css') }}" rel="stylesheet" type="text/css"/>
    <link href="{{ url('assets/global/plugins/simple-line-icons/simple-line-icons.min.css') }}" rel="stylesheet" type="text/css"/>
    <link href="{{ url('assets/global/plugins/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css"/>
    <link href="{{ url('assets/global/plugins/bootstrap-switch/css/bootstrap-switch.min.css') }}" rel="stylesheet" type="text/css"/>
    <!-- END GLOBAL MANDATORY STYLES -->
    <!-- BEGIN THEME GLOBAL STYLES -->
    <link href="{{ url('assets/global/css/components.css') }}" rel="stylesheet" type="text/css"/>
    <link href="{{ url('assets/global/css/plugins.css') }}" rel="stylesheet" type="text/css"/>
    <!-- END THEME GLOBAL STYLES -->
    <!-- BEGIN PAGE LEVEL STYLES -->
    <link href="{{ url('assets/layouts/layout/css/themes/login.css') }}" rel="stylesheet" type="text/css" />
    <!-- END PAGE LEVEL STYLES -->
    <!-- BEGIN THEME LAYOUT STYLES -->
    <!-- END THEME LAYOUT STYLES -->
    <link rel="shortcut icon" href="favicon.ico" /> </head>
<!-- END HEAD -->

<body class=" login">
<!-- BEGIN LOGO -->
<div class="logo">
    <a href="{{route('/')}}">
        <img src="{{ url('../assets/company/logo_1.jpg')}}" alt="" style="width:250px"/> </a>
</div>
<!-- END LOGO -->
<!-- BEGIN LOGIN -->
<div class="content">
    <!-- BEGIN LOGIN FORM -->
    <form class="login-form" action="{{ route("login-post")}}" method="post"  autocomplete="off">
        {{ csrf_field() }}
        <h3 class="form-title font-green">@lang('auth.title_login')</h3>
        @if(isset($message))
            <div class="alert alert-{{$message['type']}}">
                <button class="close" data-close="alert"></button>
                <span> {{$message['body']}}</span>
            </div>
        @endif
        <div class="form-group">
            <!--ie8, ie9 does not support html5 placeholder, so we just show field title for that-->
            <label class="control-label visible-ie8 visible-ie9">@lang('auth.label_username')</label>
            <input
                    class="form-control form-control-solid placeholder-no-fix"
                    type="text"
                    autocomplete="off"
                    placeholder="@lang('auth.label_username')"
                    name="username"
                    value="{{isset($username)? $username : ""}}"
            /> </div>
        <div class="form-group">
            <label class="control-label visible-ie8 visible-ie9">@lang('auth.label_password')</label>
            <input class="form-control form-control-solid placeholder-no-fix" type="password" autocomplete="off" placeholder="@lang('auth.label_password')" name="password" /> </div>
        <div class="form-actions">
            <button type="submit" class="btn green uppercase">@lang('auth.button_signin')</button>
            <a href="javascript:;" id="forget-password" class="forget-password">@lang('auth.label_forgot')</a>
        </div>
    </form>
    <!-- END LOGIN FORM -->
    <!-- BEGIN FORGOT PASSWORD FORM -->
    <form class="forget-form" action="{{ route("forgot-password") }}" method="post">
        {{ csrf_field() }}
        <h3 class="font-green">@lang('auth.label_forgot')</h3>
        <p> @lang('auth.label_forgot_pass_hint') </p>
        <div class="form-group">
            <input
                    class="form-control placeholder-no-fix"
                    type="text"
                    autocomplete="off"
                    placeholder="@lang('auth.label_username')"
                    name="username"
            /> </div>
        <div class="form-actions">
            <button type="button" id="back-btn" class="btn green btn-outline">@lang('auth.button_back')</button>
            <button type="submit" class="btn btn-success uppercase pull-right">@lang('auth.title_password_reset')</button>
        </div>
    </form>
    <!-- END FORGOT PASSWORD FORM -->
</div>
<div class="copyright"> @lang('common.label_copyright')</div>
<!--[if lt IE 9]>
<![endif]-->
<!-- BEGIN CORE PLUGINS -->
<script src="{{ url('assets/global/plugins/jquery.min.js') }}" type="text/javascript"></script>
<script src="{{ url('assets/global/plugins/bootstrap/js/bootstrap.min.js') }}" type="text/javascript"></script>
<script src="{{ url('assets/global/plugins/js.cookie.min.js') }}" type="text/javascript"></script>
<script src="{{ url('assets/global/plugins/jquery-slimscroll/jquery.slimscroll.min.js') }}" type="text/javascript"></script>
<script src="{{ url('assets/global/plugins/jquery.validate.js') }}" type="text/javascript"></script>
<script src="{{ url('assets/global/plugins/bootstrap-switch/js/bootstrap-switch.min.js') }}" type="text/javascript"></script>

<!-- END CORE PLUGINS -->
<!-- BEGIN THEME GLOBAL SCRIPTS -->
<script src="{{ url('assets/global/scripts/app.min.js') }}" type="text/javascript"></script>
<!-- END THEME GLOBAL SCRIPTS -->

<!-- BEGIN PAGE LEVEL SCRIPTS -->
<script src="{{ url('assets/global/scripts/login.js') }}" type="text/javascript"></script>
<!-- END PAGE LEVEL SCRIPTS -->
</body>

</html>
