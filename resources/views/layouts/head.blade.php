@php $themeDir = config('app.ui_rtl', false) ? 'css-rtl' : 'css'; @endphp
<!-- Title -->
<title> @yield("title")</title>
<!-- Favicon -->
<link rel="icon" href="{{URL::asset('assets/img/brand/favicon.png')}}" type="image/x-icon"/>
<!-- Icons css -->
<link href="{{URL::asset('assets/css/icons.css')}}" rel="stylesheet">
<!--  Custom Scroll bar-->
<link href="{{URL::asset('assets/plugins/mscrollbar/jquery.mCustomScrollbar.css')}}" rel="stylesheet"/>
<!--  Sidebar css -->
<link href="{{URL::asset('assets/plugins/sidebar/sidebar.css')}}" rel="stylesheet">
<!-- Sidemenu css -->
<link rel="stylesheet" href="{{URL::asset('assets/'.$themeDir.'/sidemenu.css')}}">
@yield('css')
@stack('styles')
<!--- Style css -->
<link href="{{URL::asset('assets/'.$themeDir.'/style.css')}}" rel="stylesheet">
<!--- Dark-mode css -->
<link href="{{URL::asset('assets/'.$themeDir.'/style-dark.css')}}" rel="stylesheet">
<!---Skinmodes css-->
<link href="{{URL::asset('assets/'.$themeDir.'/skin-modes.css')}}" rel="stylesheet">
<!-- Dark shell (sidebar + navbar) -->
<link href="{{ URL::asset('assets/css/ofshell.css') }}" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&family=Space+Grotesk:wght@500;700&display=swap" rel="stylesheet">
