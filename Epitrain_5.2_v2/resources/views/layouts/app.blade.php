<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}" />

    <title>Epitrain</title>

    <!-- Fonts -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.min.css" integrity="sha384-XdYbMnZ/QjLh6iI4ogqCTaIjrFk87ip+ekIjefZch0Y+PvJ8CDYtEs1ipDmPorQ+" crossorigin="anonymous">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Lato:100,300,400,700">
		
		<link rel="stylesheet" type="text/css" href="//fonts.googleapis.com/icon?family=Material+Icons">
		
		<!-- Bootstrap Core CSS -->
    <link href="{{ asset('css/bootstrap.css') }}" rel="stylesheet">
		
		<!--Bootstrap Material Design-->
		<link type="text/css" rel="stylesheet" href="{{ asset('css/bootstrap-material-design.css')}}" media="screen,projection"/>
		<link type="text/css" rel="stylesheet" href="{{ asset('css/ripples.css')}}"/>
		

    {!! Html::style('css/style.css') !!}
    <!-- Styles -->
		
		
		<link type="text/css" rel="stylesheet" href="{{ asset('css/text.css')}}"/>
		
    <!-- <link href="https://fonts.googleapis.com/css?family=Open+Sans" rel="stylesheet"> -->
    <link href='https://fonts.googleapis.com/css?family=Open+Sans:700,400,300,300italic,400italic' rel='stylesheet' type='text/css'>
    <!-- <link rel="stylesheet" href="css/fonts.css" type="text/css">
    <link rel="stylesheet" href="css/libs.css" type="text/css">-->
    <link rel="stylesheet" href="{{ asset('css/global.css')}}" type="text/css">
    <link rel="stylesheet" href="{{ asset('css/style.css')}}" type="text/css">
    <script src="{{ asset('js/libs.js')}}"></script>
   <!--   <script src="js/script.js"></script> -->

    <!--added in feb by cathy for forum-->
    <link rel="stylesheet" href="{{ asset('css/forForum.css')}}" type="text/css">


    

    <!-- Custom CSS -->
    <link href="{{ asset('css/heroic-features.css')}}" rel="stylesheet">

    <!-- font-awesome-->
    <link rel="stylesheet" href="{{ asset('font-awesome/css/font-awesome.min.css')}}">

     <!--pdf.js for displaying preview image-->
    <script src="{{ asset('pdf/build/pdf.js')}}"></script>

     <!--back to Top arrow-->
    <script src="{{ asset('backtoTop/float-panel.js')}}"></script>

     <!-- jQuery (necessary for Bootstrap's JavaScript plugins and Typeahead) -->
     <!-- popup window-->
    <!--<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>-->
    <script src="https://cdn.rawgit.com/vast-engineering/jquery-popup-overlay/1.7.13/jquery.popupoverlay.js"></script>

    <!--ninja slider-->
    <script src="{{ asset('ninja_slider/ninja-slider.js')}}"></script>
    <link rel="stylesheet" href="{{ asset('ninja_slider/ninja-slider.css')}}" type="text/css">

    <!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.2.3/jquery.min.js" integrity="sha384-I6F5OKECLVtK/BL+8iSLDEHowSAfUo76ZL9+kGAgTRdiByINKJaqTPH/QVNS1VDb" crossorigin="anonymous"></script> -->
    
    <!--tooltip tipsy-->
    <script src="{{ asset('js/jquery.tipsy.js')}}"></script>
    <link rel="stylesheet" href="{{ asset('css/tipsy.css')}}" type="text/css">

    <!--Morris Chart-->
    <script src="http://cdnjs.cloudflare.com/ajax/libs/raphael/2.1.0/raphael-min.js"></script>
    <script src="http://cdn.oesmith.co.uk/morris-0.4.1.min.js"></script>


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


    .btn-four{font-weight: 900;background: #3C7FE7; font-color:#FDFEFE; border-color: transparent;}
    .btn-four:hover{color: #3C7FE7; background:#FDFEFE; border: 1px solid #3C7FE7;}


    </style>

    <!--Search functuion-->
    
</head>
<body id="app-layout" >
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
    $categories = \DB::table('category')
        ->where('shownInUserCategories','=',1)
        ->get();
?>
    <header>
    <nav class="navbar navbar-inverse" >
        <div class="container">
            <div class="navbar-header">

                <!-- Collapsed Hamburger -->
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#app-navbar-collapse">
                    <span class="sr-only">Toggle Navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>

                <!-- Branding Image -->
                <a class="navbar-brand" href="{{ url('/') }}">
                    Epitrain
                </a>
            </div>

            <div class="collapse navbar-collapse" id="app-navbar-collapse">
                <!-- Left Side Of Navbar -->
                <ul class="nav navbar-nav">
                    <li><a href="{{ url('/home') }}">Home</a></li>
						
						<!--Categories-->
						@if(Auth::guest())
						@elseif(Auth::user()->isAdmin)
						@else
							<li class="dropdown">
								<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">Categories<b class="caret"></b></a>
								<ul class="dropdown-menu">
                                    @for ($i = 0; $i < sizeof($categories); $i++)
                                        <?php $category = $categories[$i]; ?>
                                        <li><a id =<?php echo "link-".$i;?> href='/shop'><span class="white-text text-darken-2" id=<?php echo "cat-".$i;?>><?php echo $category->categoryname; ?></span></a></li>
                                    @endfor
									
									<li><a href='/shop?cat=viewAll'> <span class="white-text text-darken-2">View All</span></a></li>
								</ul>
							</li>
						@endif
							
						</ul>
								
								
                @if(Auth::check())
                    <form class="typeahead navbar-form navbar-left" role="search">
                      <div class="form-group">
                        <input type="search" name="q" class="search-input form-control" placeholder="Search for materials" autocomplete="off" style="width:235px">
                      </div>
                    </form>
                @endif

                <!-- Right Side Of Navbar -->
                <ul class="nav navbar-nav navbar-right">
                    <!-- Authentication Links -->
                    @if (Auth::guest())
                        <li><a href="{{ url('/login') }}">Login</a></li>
                        <li><a href="{{ url('/register') }}">Register</a></li>
                        <li><a href="{{ url('/contact') }}">Contact Admin</a></li>
                    @elseif (Auth::user()->isAdmin)
                        <li class="dropdown" style="border-left:solid 1px #85929E;">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                                {{ Auth::user()->name }} <span class="caret"></span>
                            </a>
                            <ul class="dropdown-menu" role="menu">
                                <li><a href="{{ url('/update') }}"><i class="material-icons">perm_identity</i> Update Personal Info</a></li>
                                <li><a href="{{ url('/logout') }}"><i class="material-icons">cancel</i> Logout</a></li>
                            </ul>
                        </li>
                        <li class="dropdown" style="border-left:solid 1px #85929E;">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                                Menu <span class="caret"></span>
                            </a>

                            <ul class="dropdown-menu" role="menu">
                                <li><a href="{{ url('/fileentry') }}"><i class="material-icons">library_add</i> Manage Library</a></li>
                                <li><a href="{{ url('/createUser') }}"><i class="material-icons">person_add</i> Create User</a></li>
                                <li><a href="{{ url('/forumAdmin') }}"><i class="material-icons">speaker_notes</i> Discussion Forum</a></li>
                                <li><a href="{{ url('/category') }}"><i class="material-icons">settings</i> Customize categories</a></li>
                                <li><a href="{{ url('/subscriptionplan') }}"><i class="material-icons">subscriptions</i> Customize subscription</a></li>
                                <li><a href="{{ url('/classmanagement') }}"><i class="material-icons">view_list</i> Class management</a></li>
                                <li><a href="{{ url('/viewAllUsers') }}"><i class="material-icons">group</i> User Management</a></li>
                                <li><a href="{{ url('/adminSettings') }}"><i class="material-icons">perm_data_setting</i> Admin Settings</a></li>
                                <li><a href="{{ url('/faq') }}"><i class="material-icons">question_answer</i> Manage FAQ</a></li>
                            </ul>
                        </li>
                    @else
                         <li style="border-left:solid 1px #85929E;">
                            <a href="{{ url('/mylibrary') }}" role="button" aria-expanded="false">
                               <i class="material-icons">book</i> My Library 
                            </a>
                        </li>

                        <li style="border-left:solid 1px #85929E;">
                            <a href="{{ url('/shoppingcart') }}">
                                <!--<span class="glyphicon glyphicon-shopping-cart" aria-hidden="true"></span>-->
								<i class="material-icons">shopping_cart</i>
                                <span class="badge" style="font-size:8px;"><?php echo $count;?> items</span>
                            </a>
                        </li>
                        <li class="dropdown" style="border-left:solid 1px #85929E;">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                                {{ Auth::user()->name }} <span class="caret"></span>
                            </a>

                            <ul class="dropdown-menu" role="menu">
                                <li><a href="{{ url('/update') }}"><i class="material-icons">group</i>Update Personal Info</a></li>
                                <li><a href="{{ url('/faq') }}"><i class="material-icons">question_answer</i>FAQ</a></li>
                                <li><a href="{{ url('/logout') }}"><i class="material-icons">cancel</i>Logout</a></li>
                            </ul>
                        </li>
                        <li class="dropdown" style="border-left:solid 1px #85929E;">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                                Menu <span class="caret"></span>
                            </a>

                            <ul class="dropdown-menu" role="menu">
                                <li><a href="{{ url('/mylibrary') }}"><i class="material-icons">library_books</i>My Library</a></li>
                                <li><a href="{{ url('/forum') }}"><i class="material-icons">recent_actors</i>Discussion Forum</a></li>
                                <li><a href="{{ url('/transactionHistory') }}"><i class="material-icons">monetization_on</i>Transaction History</a></li>
                                <li><a href="{{ url('/contact') }}"><i class="material-icons">record_voice_over</i>Contact Admin</a></li>
                            </ul>
                        </li>
                        
                    @endif
                </ul>
            </div>
        </div>
    </nav>

    @yield('content')
    </header>
    <!-- JavaScripts -->
     <!--typehead smart search-->
    <!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.2.3/jquery.min.js"></script>

    // <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.6/js/bootstrap.min.js" integrity="sha384-0mSbJDEHialfmuBBQP6A4Qrprq5OVfW37PRR3j5ELqxss1yVqOtnepnHVP9aJ7xS" crossorigin="anonymous"></script> -->
    {{-- <script src="{{ elixir('js/app.js') }}"></script> --}}



    <!-- Bootstrap JS -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
    <!-- Typeahead.js Bundle -->
    <script src="{{ asset('typehead/typeahead.bundle.min.js')}}"></script>
    <script src="{{ asset('typehead/bloodhound.min.js')}}"></script>
    <script src="{{ asset('typehead/typeahead.jquery.min.js')}}"></script>

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
                        '<div class="list-group search-results-dropdown"><div class="list-group-item">Nothing found.</div></div>'
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
    <script>
        for(i = 0; i < <?php echo sizeof($categories); ?>; i++) {
            document.getElementById("link-".concat(i)).href = "shop?cat=".concat(document.getElementById("cat-".concat(i)).innerHTML);
        }
    </script>
		
    <script type="text/javascript" src="{{ asset('js/material.js')}}"></script>
		
		<script type="text/javascript" rel="stylesheet" href="{{ asset('js/ripples.css')}}"/></script>
		
    <script src="https://cdn.rawgit.com/HubSpot/tether/v1.3.4/dist/js/tether.min.js"></script>
		<script>
		$(function() {
			$.material.init();
		});
      <!--$('body').bootstrapMaterialDesign();-->
      //setInterval(function(){ 
        //alert("Hello"); 
      //}, 300000);
    </script>

    <script>
    /*
     var xmlhttp = new XMLHttpRequest();
            xmlhttp.onreadystatechange = function() {
                if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {

                }
             }

       xmlhttp.open("GET", "Caller.php", true);
            xmlhttp.send();   
    
     window.onbeforeunload = function () {
        return "Are you sure?";
    }

    $(function () {
        $('input[type="checkbox"]').click(function () {
            window.onbeforeunload = function () { };
        });
    });
    */
    </script>
</body>
</html>
