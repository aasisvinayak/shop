<!doctype html>
<html>
<head>
    @include('includes.head')
    <style>


        .panel-body .btn:not(.btn-block) {
            width: 150px;
            margin-bottom: 12px;
        }

        @media (min-width: 768px) {
            nav.sidebar  nav.sidebar .navbar-header{
                text-align: center;
                width: 100%;
                margin-left: 0px;
            }

            nav.sidebar .navbar-nav .open .dropdown-menu {
                position: static;
                float: none;
                width: auto;
                margin-top: 0;
                background-color: transparent;
            }

            nav.sidebar .navbar-collapse, nav.sidebar .container-fluid{
                padding: 0 0px 0 0px;
            }

            nav.sidebar{
                width: 200px;
                height: 100%;
                margin-left: -150px;
                float: left;
                z-index: 10000;
                margin-bottom: 0px;
            }

            nav.sidebar li {
                width: 100%;
            }

        }

        @media (min-width: 1330px) {
            nav.sidebar{
                margin-left: 0px;
                float: left;
            }
        }

        nav.sidebar .navbar-nav .open .dropdown-menu>li>a:hover, nav.sidebar .navbar-nav .open .dropdown-menu>li>a:focus {
            background-color: transparent;
        }


    </style>
</head>
<body>
<div class="container">
    <header class="row">
        @include('includes.header')
    </header>

    <div class="col-md-3">
        <div class="profile-sidebar">



            <div class="row">
                <div class="absolute-wrapper"> </div>
                <nav class="navbar navbar-inverse sidebar" role="navigation">
                    <div class="container-fluid">
                        <div class="navbar-header">
                            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-sidebar-navbar-collapse-1">
                                <span class="sr-only">Toggle navigation</span>
                                <span class="icon-bar"></span>
                                <span class="icon-bar"></span>
                                <span class="icon-bar"></span>
                            </button>
                        </div>
                        <div class="collapse navbar-collapse" id="bs-sidebar-navbar-collapse-1">
                            <ul class="nav navbar-nav">

                                <li ><a href="/account/profile"> Dashboard <span style="font-size:16px;" class="pull-right hidden-xs showopacity glyphicon glyphicon-home"></span></a></li>


                                <div class="profile-usertitle">
                                    <div style="color: white" class="profile-usertitle-name">
                                        &nbsp; &nbsp;  {{Auth::user()->email }}
                                    </div>
                                    <div style="color: white" class="profile-usertitle-job">
                                       &nbsp; &nbsp; Welcome
                                    </div>
                                </div>

                                <li >
                                    <a href="/account/payment_cards">
                                        <i class="glyphicon glyphicon-home"></i>
                                        Payment Cards </a>
                                </li>
                                <li>
                                    <a href="/account/addresses">
                                        <i class="glyphicon glyphicon-user"></i>
                                        Address Book </a>
                                </li>
                                <li>
                                    <a href="/account/order_history">
                                        <i class="glyphicon glyphicon-list"></i>
                                        Order History </a>
                                </li>

                                <li>
                                    <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                        <i class="glyphicon glyphicon-ok"></i>
                                        Logout </a>
                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">{{ csrf_field() }}</form>
                                </li>

                            </ul>
                        </div>
                    </div>
                </nav>


            </div>


        </div>
    </div>

    <div class="col-md-9">

        <div id="main" class="row">

            @yield('content')

        </div>
    </div>

    <footer class="row">
        @include('includes.footer')
    </footer>

</div>
</body>
</html>