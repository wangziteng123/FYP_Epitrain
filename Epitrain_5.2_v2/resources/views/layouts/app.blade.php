<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}" />

    <title>Epitrain</title>

    <!-- Fonts -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.min.css" integrity="sha384-XdYbMnZ/QjLh6iI4ogqCTaIjrFk87ip+ekIjefZch0Y+PvJ8CDYtEs1ipDmPorQ+" crossorigin="anonymous">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Lato:100,300,400,700">

    {!! Html::style('css/style.css') !!}
    <!-- Styles -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">

    <!-- <link href="https://fonts.googleapis.com/css?family=Open+Sans" rel="stylesheet"> -->
    <link href='https://fonts.googleapis.com/css?family=Open+Sans:700,400,300,300italic,400italic' rel='stylesheet' type='text/css'>
    <!-- <link rel="stylesheet" href="css/fonts.css" type="text/css">
    <link rel="stylesheet" href="css/libs.css" type="text/css">-->
    <link rel="stylesheet" href="css/global.css" type="text/css">
    <link rel="stylesheet" href="css/style.css" type="text/css">
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
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
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
        body {
            font-family: 'Lato';
        }

        .fa-btn {
            margin-right: 6px;
        }

        .tt-query {
          -webkit-box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075);
             -moz-box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075);
                  box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075);
        }

        .tt-hint {
          color: #999
        }

        .tt-menu {    /* used to be tt-dropdown-menu in older versions */
          width: 435px;
          margin-top: 4px;
          padding: 4px;
          background-color: #fff;
          border: 1px solid #ccc;
          border: 1px solid rgba(0, 0, 0, 0.2);
          -webkit-border-radius: 4px;
             -moz-border-radius: 4px;
                  border-radius: 4px;
          -webkit-box-shadow: 0 5px 10px rgba(0,0,0,.2);
             -moz-box-shadow: 0 5px 10px rgba(0,0,0,.2);
                  box-shadow: 0 5px 10px rgba(0,0,0,.2);
        }

        .tt-suggestion {
          padding: 3px 10px;
          line-height: 3px;
        }

        .tt-suggestion.tt-cursor,.tt-suggestion:hover {
          color: #fff;
          background-color: #0097cf;

        }

        .tt-suggestion p {
          margin: 0;
        }

        .btn-three{font-weight: 700; color: #aad122; background: transparent; border: 1px solid #aad122;}
        .btn-three:hover{background: #aad122; color: #fff; border-color: transparent;}

        .btn-four{font-weight: 700;background: #aad122; color: #fff; border-color: transparent;}
        .btn-four:hover{color: #aad122; background: transparent; border: 1px solid #aad122;}

         #backtop {
            position: fixed;
            left:auto;right: 20px;top:auto;bottom: 20px;
            outline: none;
            overflow:hidden;
            color:#fff;
            text-align:center;
            background-color:rgba(49,79,96,0.84);
            height:40px;
            width:40px;
            line-height:40px;
            font-size:14px;
            border-radius:2px;
            cursor:pointer;
            transition:all 0.3s linear;
            z-index:999999;

            opacity:1;
            display:none;
        }
        #backtop:hover {
            background-color:#27CFC3;
        }
        #backtop.mcOut {
            opacity:0;
        }

    </style>

    <!--Search functuion-->
    
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
    <nav class="navbar navbar-default navbar-static-top">
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
                </ul>

                @if(Auth::check())
                    <form class="typeahead navbar-form navbar-left" role="search">
                      <div class="form-group">
                        <input type="search" name="q" class="search-input form-control" placeholder="Search Book or Spreadsheet" autocomplete="off" style="width:235px">
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
                         <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                                {{ Auth::user()->name }} <span class="caret"></span>
                            </a>

                            <ul class="dropdown-menu" role="menu">
                                <li><a href="{{ url('/fileentry') }}"><i class="fa fa-btn fa-sign-out"></i>Manage Library</a></li>
                                <li><a href="{{ url('um/tocreate') }}"><i class="fa fa-btn fa-sign-out"></i>Create User</a></li>
                                <li><a href="{{ url('/forumAdmin') }}"><i class="fa fa-btn fa-sign-out"></i>Discussion Forum</a></li>
								<li><a href="{{ url('/viewAllUsers') }}"><i class="fa fa-btn fa-sign-out"></i>View All Users</a></li>
                                <li><a href="{{ url('/update') }}"><i class="fa fa-btn fa-sign-out"></i>Update Personal Info</a></li>
                                <li><a href="{{ url('/logout') }}"><i class="fa fa-btn fa-sign-out"></i>Logout</a></li>
                            </ul>
                        </li>
                    @else
                        <li>
                            <a href="{{ url('/shoppingcart') }}">
                                <span class="glyphicon glyphicon-shopping-cart" aria-hidden="true"></span>
                                <span class="badge" style="font-size:6.5px;"><?php echo $count;?></span>
                            </a>
                        </li>
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                                {{ Auth::user()->name }} <span class="caret"></span>
                            </a>

                            <ul class="dropdown-menu" role="menu">
                                <li><a href="{{ url('/mylibrary') }}"><i class="fa fa-btn fa-sign-out"></i>My Library</a></li>
                                <li><a href="{{ url('#') }}"><i class="fa fa-btn fa-sign-out"></i>SpreadSheets</a></li>
                                <li><a href="{{ url('/forum') }}"><i class="fa fa-btn fa-sign-out"></i>Discussion Forum</a></li>
                                <li><a href="{{ url('/update') }}"><i class="fa fa-btn fa-sign-out"></i>Update Personal Info</a></li>
                                <li><a href="{{ url('/contact') }}"><i class="fa fa-btn fa-sign-out"></i>Contact Admin</a></li>
                                <li><a href="{{ url('/logout') }}"><i class="fa fa-btn fa-sign-out"></i>Logout</a></li>
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
    
</body>
</html>
