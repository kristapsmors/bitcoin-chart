<!doctype html>
<html lang="en">
	<head>
		<!-- Basic Page Needs
		================================================== -->
		<meta charset="utf-8" />
		<title>
			@section('title')
			@show
		</title>
		<meta name="keywords" content="your, awesome, keywords, here" />
		<meta name="author" content="Jon Doe" />
		<meta name="description" content="Lorem ipsum dolor sit amet, nihil fabulas et sea, nam posse menandri scripserit no, mei." />

		<!-- Mobile Specific Metas
		================================================== -->
		<meta name="viewport" content="width=device-width, initial-scale=1.0">

		<!-- CSS
		================================================== -->
		<link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet" media="screen">
	    <link href="{{ asset('css/font-awesome.min.css') }}" rel="stylesheet" media="screen">
	    <link href="{{ asset('css/animate.min.css') }}" rel="stylesheet" media="screen">
	    <link href="{{ asset('css/lightbox.css') }}" rel="stylesheet" media="screen">
	    <link href="{{ asset('css/syntax/shCore.css') }}" rel="stylesheet"  media="screen">
	    <link href="{{ asset('css/syntax/shThemeDefault.css') }}" rel="stylesheet"  media="screen">
	    <link href="{{ asset('css/style.css') }}" rel="stylesheet" media="screen" title="default">
	    <link href="{{ asset('css/color-red.css') }}" rel="stylesheet" media="screen" title="default">
	    <link href="{{ asset('css/width-boxed.css') }}" rel="stylesheet" media="screen" title="default">
		<!-- Javascripts
		================================================== -->
		<!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
	    <!--[if lt IE 9]>
	        <script src="{{ asset('js/html5shiv.js') }}"></script>
	        <script src="{{ asset('js/respond.min.js') }}"></script>
	    <![endif]-->

	</head>
    <body>
        <div class="boxed animated fadeIn animation-delay-5">
	        <nav class="navbar navbar-static-top navbar-mind" role="navigation">
	            <div class="container">
	                <!-- Brand and toggle get grouped for better mobile display -->
	                <div class="navbar-header">
	                    <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-mind-collapse">
	                        <span class="sr-only">Toggle navigation</span>
	                        <i class="fa fa-bars fa-inverse"></i>
	                    </button>
	                </div>
	                
	                <!-- Collect the nav links, forms, and other content for toggling -->
	                <div class="collapse navbar-collapse navbar-mind-collapse">
	                    <ul class="nav navbar-nav">
	                        <li class="dropdown">
	                            <a href="{{ Url::to('/') }}" class="dropdown-toggle" data-toggle="dropdown">Bitcoin Prices</a>
	                        </li> <!-- dropdown -->

	                        <!--<li class="dropdown">-->
	                            <!--<a href="#" class="dropdown-toggle" data-toggle="dropdown">Bitcoin Map </a>-->
	                        <!--</li> &lt;!&ndash; dropdown &ndash;&gt;-->
	                       
	                      
	                    </ul> <!-- nav nabvar-nav -->

	                   
	                </div><!-- navbar-collapse -->
	            </div> <!-- container -->
	        </nav> <!-- navbar navbar-default -->
	        @yield('content')
	    </div>
        <div id="back-top">
		    <a href="#header"><i class="fa fa-chevron-up"></i></a>
		</div>
		@section('scripts')
            @section('scripts_libs')
			<script src="{{ asset('js/jquery-1.10.2.min.js') }}"></script>
			<script src="{{ asset('js/jquery.cookie.js') }}"></script>
			<script src="{{ asset('js/bootstrap.min.js') }}"></script>
			<script src="{{ asset('js/jquery.mixitup.min.js') }}"></script>
			<script src="{{ asset('js/lightbox-2.6.min.js') }}"></script>
			<script src="{{ asset('js/holder.js') }}"></script>
            @show
			<script src="{{ asset('js/app.js') }}"></script>
		@show
    </body>
</html>