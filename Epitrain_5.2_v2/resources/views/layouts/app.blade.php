<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}" />

    <title>Epitrain</title>

		<!--Google Icon Fonts-->
		<link href="http://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
		<!--<link type="text/css" rel="stylesheet" href="css/materialize.css"  media="screen,projection"/>-->
		
    <!-- Fonts -->

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.min.css" integrity="sha384-XdYbMnZ/QjLh6iI4ogqCTaIjrFk87ip+ekIjefZch0Y+PvJ8CDYtEs1ipDmPorQ+" crossorigin="anonymous">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Lato:100,300,400,700">
      <!-- Bootstrap Material Design -->
      <link rel="stylesheet" type="text/css" href="css/bootstrap-material-design.css">
      <link rel="stylesheet" type="text/css" href="css/ripples.min.css">
		

    {!! Html::style('css/style.css') !!}
    <!-- Styles -->

		<link type="text/css" rel="stylesheet" href="css/text.css"/>
		
    <!-- <link href="https://fonts.googleapis.com/css?family=Open+Sans" rel="stylesheet"> -->
    <link href='https://fonts.googleapis.com/css?family=Open+Sans:700,400,300,300italic,400italic' rel='stylesheet' type='text/css'>
    <!-- <link rel="stylesheet" href="css/fonts.css" type="text/css">
    <link rel="stylesheet" href="css/libs.css" type="text/css">-->
    <link rel="stylesheet" href="css/global.css" type="text/css">
    <link rel="stylesheet" href="css/style.css" type="text/css">

    <!-- Bootstrap material design import-->
    <script   src="https://code.jquery.com/jquery-3.1.1.js"   integrity="sha256-16cdPddA6VdVInumRGo6IbivbERE8p7CQR3HzTBuELA="   crossorigin="anonymous"></script>
    
    <script src="js/libs.js"></script>

   <!--   <script src="js/script.js"></script> -->

    <!--added in feb by cathy for forum-->
    <link rel="stylesheet" href="css/forForum.css" type="text/css">


    <!-- Bootstrap Core CSS -->
    <link href="css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link href="css/heroic-features.css" rel="stylesheet">

    <!-- font-awesome-->
    <link rel="stylesheet" href="font-awesome/css/font-awesome.min.css">

     <!--pdf.js for displaying preview image-->
    <script src="pdf/build/pdf.js"></script>

     <!--back to Top arrow-->
    <script src="backtoTop/float-panel.js"></script>

     <!-- jQuery (necessary for Bootstrap's JavaScript plugins and Typeahead) -->
     <!-- popup window-->
    <!--<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>-->
    <script src="https://cdn.rawgit.com/vast-engineering/jquery-popup-overlay/1.7.13/jquery.popupoverlay.js"></script>

    <!--ninja slider-->
    <script src="ninja_slider/ninja-slider.js"></script>
    <link rel="stylesheet" href="ninja_slider/ninja-slider.css" type="text/css">

    <!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.2.3/jquery.min.js" integrity="sha384-I6F5OKECLVtK/BL+8iSLDEHowSAfUo76ZL9+kGAgTRdiByINKJaqTPH/QVNS1VDb" crossorigin="anonymous"></script> -->
    
    <!--tooltip tipsy-->
    <script src="js/jquery.tipsy.js"></script>
    <link rel="stylesheet" href="css/tipsy.css" type="text/css">

    <script type='text/javascript'>
     $(function() {
       $('.tooltipTipsy').tipsy({fade: true, gravity: 'n'});
     });
    </script>

    <!--typehead smart search-->
    <script>
    $(document).ready(function() {

      // Initialize the plugin
        $('#my_popup').popup();
    });
  </script>
    
    <style type="text/css">
    </style>

    <!--Search functuion-->
    
		<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
		
</head>
<body id="app-layout">
     <div id="backtop">&#9650;</div>
<?php
    $count = 0;
    if(Auth::check()) {
        $shoppingcartExist = \DB::table('shoppingcarts')
                ->where('user_id', Auth::user()->id)
                ->get();

        if(count($shoppingcartExist)) {
            $count = count($shoppingcartExist);
        } 
    }
   
?>
    <header>
		
    <div class="navbar navbar-inverse" style="background-color: #062C94">
        <div class="container-fluid">
            <div class="navbar-collapse collapse navbar-inverse-collapse text-center">
            <!--brand-->
                <div class ="nav navbar-nav">
				    <a href="{{url('/home')}}" style = "font-size:24px; color: white">EPITRAIN</a>
                </div>
				<!--For mobile Burger icon-->
                @if(Auth::check())
                    <!-- Search bar -->
                        <form class="typeahead navbar-form navbar-left" role="search">
                            <div class="form-group">
                                <input type="search" name="q" class="search-input form-control" style = "font-size:14px; color:white"; placeholder="Search Book or Spreadsheet" autocomplete="off" style="width:235px">
                            </div>
                        </form>
                @endif
                <ul id="" class="nav navbar-nav navbar-right">
                    
                    <!-- Right Side Of Navbar -->
                        <!-- Authentication Links -->
                        @if (Auth::guest())
                            <li><a style = "font-size:16px" href="{{ url('/login') }}">Login</a></li>
                            <li><a style = "font-size:16px" href="{{ url('/register') }}">Register</a></li>
                            <li><a style = "font-size:16px" href="{{ url('/contact') }}">Contact Admin</a></li>
                        @elseif (Auth::user()->isAdmin)
							<li class="dropdown"><a class="dropdown-toggle" data-target="#" data-toggle="dropdown" style = "font-size:16px">{{Auth::user()->name}}<i class="material-icons right">arrow_drop_down</i></a>
							
							<!--change admin dropdown here-->
    							<ul id="dropdown-admin-browser" class="dropdown-menu">
    								<li><a href="{{ url('/fileentry') }}"><i class="fa fa-btn fa-sign-out"></i>Manage Library</a></li>
    								<li class="divider"></li>
    								<li><a href="{{ url('createUser') }}"><i class="fa fa-btn fa-sign-out"></i>Create User</a></li>
    								<li class="divider"></li>
    								<li><a href="{{ url('/forumAdmin') }}"><i class="fa fa-btn fa-sign-out"></i>Discussion Forum</a></li>
    								<li class="divider"></li>
    								<li><a href="{{ url('/viewAllUsers') }}"><i class="fa fa-btn fa-sign-out"></i>View All Users</a></li>
    								<li class="divider"></li>
    								<li><a href="{{ url('/update') }}"><i class="fa fa-btn fa-sign-out"></i>Update Personal Info</a></li>
    								<li class="divider"></li>
    								<li><a href="{{ url('/logout') }}"><i class="fa fa-btn fa-sign-out"></i>Logout</a></li>
    							</ul>
                            </li>
                        @else
                            <li>
                                <a style = "font-size:16px" href="{{ url('/mylibrary') }}" role="button" aria-expanded="false">
                                   <i class="fa fa-book" aria-hidden="true"></i> My Library 
                                </a>
                            </li>
    												

                            <li>
                                <a href="{{ url('/shoppingcart') }}">
                                    <span style = "font-size:16px" class="glyphicon glyphicon-shopping-cart" aria-hidden="true"></span>
                                    <span class="new badge" style = "font-size:12px" data-badge-caption=" items"><?php echo $count;?></span>
                                </a>
                            </li>
    												
								<li class="dropdown"><a class="dropdown-toggle" data-target="#" data-toggle="dropdown" style = "font-size:16px">{{Auth::user()->name}}<i class="material-icons right">arrow_drop_down</i></a>
								<!--change user dropdown here-->
								<ul id="dropdown-user-browser" class="dropdown-menu" >
									<li><a href="{{ url('/mylibrary') }}"></i>My Library</a></li>
									<li class="divider"></li>
									<li><a href="{{ url('/forum') }}"></i>Discussion Forum</a></li>
									<li class="divider"></li>
									<li><a href="{{ url('/update') }}"></i>Update Personal Info</a></li>
									<li class="divider"></li>
									<li><a href="{{ url('/contact') }}"></i>Contact Admin</a></li>
									<li class="divider"></li>
									<li><a href="{{ url('/logout') }}"></i>Logout</a></li>
								</ul>
                                </li>
                        @endif
                </ul>
            </div>
        </div>
    </div>

    @yield('content')
    </header>
    <!-- JavaScripts -->
     <!--typehead smart search-->

    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.6/js/bootstrap.min.js" integrity="sha384-0mSbJDEHialfmuBBQP6A4Qrprq5OVfW37PRR3j5ELqxss1yVqOtnepnHVP9aJ7xS" crossorigin="anonymous"></script> 
    {{-- <script src="{{ elixir('js/app.js') }}"></script> --}}
    <script src="js/material.js"></script>
    <script src="js/material.min.js"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            $.material.init();
            $(".dropdown-toggle").dropdown();
        });
    </script>
    <script>
        $(document).ready(function(){
            $('[data-toggle="tooltip"]').tooltip(); 
        });
    </script>

    <!-- Bootstrap JS -->
    <!-- Typeahead.js Bundle -->
    <script src="typehead/typeahead.bundle.min.js"></script>
    <script src="typehead/bloodhound.min.js"></script>
    <script src="typehead/typeahead.jquery.min.js"></script>
    <script>
        jQuery(document).ready(function($) {
            // Set the Options for "Bloodhound" suggestion engine
            var mainUrl = window.location.hostname;
            var engine = new Bloodhound({
                remote: {
                    url: '/find?q=%QUERY%',
                    wildcard: '%QUERY%',
                },
                datumTokenizer: Bloodhound.tokenizers.whitespace('q'),
                queryTokenizer: Bloodhound.tokenizers.whitespace
            });

            engine.initialize();

            $(".search-input").typeahead({
                hint: true,
                highlight: true,
                minLength: 1
            }, {
                source: engine.ttAdapter(),
                // This will be appended to "tt-dataset-" to form the class name of the suggestion menu.
                name: 'fileentry_List',
                displayKey: 'original_filename',
                // the key from the array we want to display (name,id,email,etc...)
                templates: {
                    empty: [
                        '<div class="list-group search-results-dropdown" ><div class="list-group-item">Nothing found.</div></div>'
                    ],
                    header: [
                        '<div class="list-group search-results-dropdown">'
                    ],
                    suggestion: function (data) {                                       
                        return '<div class="user-search-result" style="color:black"><a href="http://' + mainUrl + '/searchresult?filename='+data.filename+'&original_filename='+data.original_filename+'&id='+data.id+'"><h6>' 
                        + data.original_filename +'</h6></a></div>';
              }
                }
            });

            });
    </script>
    
		<!--Materialize-css jQuery import-->
		<!--<script type="text/javascript" src="https://code.jquery.com/jquery-2.1.1.min.js"></script>
    <script type="text/javascript" src="js/materialize.js"></script>-->
</body>
</html>
