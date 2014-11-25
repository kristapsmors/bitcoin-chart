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

	    <!-- CSS -->
	    <link href="{{ asset('admin/css/bootstrap.min.css') }}" rel="stylesheet" media="screen">
	    <link href="{{ asset('admin/css/font-awesome.min.css') }}" rel="stylesheet" media="screen">
	    <link href="{{ asset('admin/css/animate.min.css') }}" rel="stylesheet" media="screen">
	    <link href="{{ asset('admin/css/lightbox.css') }}" rel="stylesheet" media="screen">
	    <link href="{{ asset('admin/css/syntax/shCore.css') }}" rel="stylesheet"  media="screen">
	    <link href="{{ asset('admin/css/syntax/shThemeDefault.css') }}" rel="stylesheet"  media="screen">

	    <link href="{{ asset('admin/css/style.css') }}" rel="stylesheet" media="screen" title="default">
	    <link href="{{ asset('admin/css/color-red.css') }}" rel="stylesheet" media="screen" title="default">
	    <link href="{{ asset('admin/css/width-full.css') }}" rel="stylesheet" media="screen" title="default">

	    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->

		<!-- Javascripts
		================================================== -->
		<!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
	    <!--[if lt IE 9]>
	        <script src="{{ asset('admin/js/html5shiv.js') }}"></script>
	        <script src="{{ asset('admin/js/respond.min.js') }}"></script>
	    <![endif]-->
	</head>
    <body>
	    <div class="boxed animated fadeIn animation-delay-5">

	        <header id="header" class="hidden-xs">
	            <div class="container">
	                <div id="header-title">
	                    <h1 class="animated fadeInDown"><a href="index.html">Bitcoin <span>Charts</span></a></h1>
	                    <p class="animated fadeInLeft">Admin panel</p>
	                </div>
	            </div> <!-- container -->
	        </header> <!-- header -->

	        <nav class="navbar navbar-static-top navbar-mind" role="navigation">
	            <div class="container">
	                <!-- Brand and toggle get grouped for better mobile display -->
	                <div class="navbar-header">
	                    <a class="navbar-brand visible-xs" href="index.html">Open <span>Mind</span></a>

	                    <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-mind-collapse">
	                        <span class="sr-only">Toggle navigation</span>
	                        <i class="fa fa-bars fa-inverse"></i>
	                    </button>
	                </div>
	                
	                <!-- Collect the nav links, forms, and other content for toggling -->
	                <div class="collapse navbar-collapse navbar-mind-collapse">
	                    <ul class="nav navbar-nav">
	                    	<li {{ (Request::is( 'admin' ) ? ' class="active"' : '') }}>
	                            <a href="{{{ URL::to( 'admin') }}}">News </a>
	                        </li>
	                        <!-- dropdown
	                        <li class="dropdown">
	                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">dropdown <b class="caret"></b></a>
	                            <ul class="dropdown-menu">
	                                <li class="active"><a href="index.html">Home Option 1</a></li>
	                                <li><a href="home2.html">Home Option 2</a></li>
	                            </ul>
	                        </li>
	                        -->
	                    </ul> <!-- nav nabvar-nav -->

	                    <ul class="nav navbar-nav navbar-right">
	                        <li class="dropdown">
	                            <a href="{{{ URL::to('user/logout') }}}">Logout</a>
	                        </li> <!-- dropdown -->
	                    </ul> <!-- nav nabvar-nav -->
	                </div><!-- navbar-collapse -->
	            </div> <!-- container -->
	        </nav> <!-- navbar navbar-default -->

	        
	        @yield('content')
	       
	        <footer id="footer">
	            <p>&copy; 2013 <a href="#">Open Mind</a>, inc. All rights reserved.</p>
	        </footer>

	    </div> <!-- boxed -->
		<!-- Javascripts
		================================================== -->
	    <script src="{{ asset('admin/js/jquery-1.10.2.min.js') }}"></script>
	    <script src="{{ asset('admin/js/jquery.cookie.js') }}"></script>
	    <script src="{{ asset('admin/js/bootstrap.min.js') }}"></script>
	    <script src="{{ asset('admin/js/jquery.mixitup.min.') }}js"></script>
	    <script src="{{ asset('admin/js/lightbox-2.6.min.') }}js"></script>
	    <script src="{{ asset('admin/js/holder.js') }}"></script>
	    <script src="{{ asset('admin/js/app.js') }}"></script>
	    <script src="{{ asset('admin/js/styleswitcher.js') }}"></script>

	    <script src="{{ asset('admin/js/syntax/shCore.js') }}"></script>
	    <script src="{{ asset('admin/js/syntax/shBrushXml.js') }}"></script>
	    <script src="{{ asset('admin/js/syntax/shBrushJScript.js') }}"></script>

	    <script type="text/javascript">
	        SyntaxHighlighter.all()
	    </script>
        @yield('scripts')
    </body>
</html>