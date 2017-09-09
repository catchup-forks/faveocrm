<!DOCTYPE html>
<html ng-app="clientApp">
<head>
  <meta charset="UTF-8">
  <?php
  $title = App\Model\helpdesk\Settings\System::where('id', '=', '1')->first();
  if (isset($title->name)) {
    $title_name = $title->name;
  } else {
    $title_name = "SUPPORT CENTER";
  }
  ?>
  <title> @yield('title') {!! strip_tags($title_name) !!} </title>
  <!-- faveo favicon -->
  <link href="{{asset("lb-faveo/media/images/favicon.ico")}}" rel="shortcut icon">

  <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
  <!-- Bootstrap 3.3.2 -->
  <link href="{{asset("lb-faveo/css/bootstrap.min.css")}}" rel="stylesheet" type="text/css"/>
  <!-- Admin LTE CSS -->
  <link href="{{asset("lb-faveo/css/AdminLTEsemi.css")}}" rel="stylesheet" type="text/css"/>
  <!-- Font Awesome Icons -->
  <link href="{{asset("lb-faveo/css/font-awesome.min.css")}}" rel="stylesheet" type="text/css"/>
  <!-- Ionicons -->
  <link href="{{asset("lb-faveo/css/ionicons.min.css")}}" rel="stylesheet" type="text/css"/>
  <!-- fullCalendar 2.2.5-->
  <link href="{{asset("lb-faveo/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css")}}" rel="stylesheet"
        type="text/css"/>
  <!-- Theme style -->
  <link href="{{asset("lb-faveo/css/jquery.rating.css")}}" rel="stylesheet" type="text/css"/>

  <link href="{{asset("lb-faveo/css/app.css")}}" rel="stylesheet" type="text/css"/>

  <link href="{{asset("lb-faveo/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css")}}" rel="stylesheet"
        type="text/css"/>

  <!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
  <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
  <![endif]-->

  <script src="{{asset("lb-faveo/js/jquery2.1.1.min.js")}}" type="text/javascript"></script>
  @yield('HeadInclude')
</head>
<body>
<div id="page" class="hfeed site">
  <header id="masthead" class="site-header" role="banner">
    <div class="container" style="">
      <div id="logo" class="site-logo text-center" style="font-size: 30px;">
        <?php
        $company = App\Model\helpdesk\Settings\Company::where('id', '=', '1')->first();
        $system = App\Model\helpdesk\Settings\System::where('id', '=', '1')->first();
        ?>
        @if($system->url)
          <a href="{!! $system->url !!}" rel="home">
            @else
              <a href="{{url('/')}}" rel="home">
                @endif
                @if($company->use_logo == 1)
                  <img src="{{asset('uploads/company')}}{{'/'}}{{$company->logo}}" alt="Staff Image" width="200px"
                       height="200px"/>
                @else
                  @if($system->name)
                    {!! $system->name !!}
                  @else
                    <b>SUPPORT</b> CENTER
                  @endif
                @endif
              </a>
      </div><!-- #logo -->
      <div id="navbar" class="navbar-wrapper text-center">
        <nav class="navbar navbar-default site-navigation" role="navigation">
          <ul class="nav navbar-nav navbar-menu">
            <li @yield('home')><a href="{{url('/')}}">{!! Lang::get('lang.home') !!}</a></li>

            @if($system->first()->status == 1)
              <li @yield('submit')><a href="{{URL::route('form')}}">{!! Lang::get('lang.submit_a_ticket') !!}</a></li>
            @endif
            <li @yield('kb')><a href="{!! url('knowledgebase') !!}">{!! Lang::get('lang.knowledge_base') !!}</a>
              <ul class="dropdown-menu">
                <li><a href="{{route('category-list')}}">{!! Lang::get('lang.categories') !!}</a></li>
                <li><a href="{{route('article-list')}}">{!! Lang::get('lang.articles') !!}</a></li>
              </ul>
            </li>
            <?php $pages = App\Model\kb\Page::where('status', '1')->where('visibility', '1')->get();
            ?>
            @foreach($pages as $page)
              <li><a href="{{route('pages',$page->slug)}}">{{$page->name}}</a></li>
            @endforeach
            @if(Auth::user())
              <li @yield('myticket')><a href="{{url('mytickets')}}">{!! Lang::get('lang.my_tickets') !!}</a></li>

              {{-- <li @yield('contact')><a href="{{route('contact')}}">Contact us</a></li> --}}
              <li @yield('profile')><a href="#">{!! Lang::get('lang.my_profile') !!}</a>
                <ul class="dropdown-menu">
                  <li>
                    <div class="banner-wrapper user-menu text-center clearfix">
                      <img src="{{Auth::user()->profile_pic}}" class="img-circle" alt="Staff Image" height="80"
                           width="80"/>
                      <h3
                        class="banner-title text-info h4">{{Auth::user()->first_name." ".Auth::user()->last_name}}</h3>
                      <div class="banner-content">
                        {{-- <a href="{{url('kb/client-profile')}}" class="btn btn-custom btn-xs">{!! Lang::get('lang.edit_profile') !!}</a> --}}
                        <a href="{{url('auth/logout')}}"
                           class="btn btn-custom btn-xs">{!! Lang::get('lang.log_out') !!}</a>
                      </div>
                      @if(Auth::user())
                        @if(Auth::user()->role != 'user')
                          <div class="banner-content">
                            <a href="{{url('dashboard')}}"
                               class="btn btn-custom btn-xs">{!! Lang::get('lang.dashboard') !!}</a>
                          </div>
                        @endif
                      @endif
                      @if(Auth::user())
                        @if(Auth::user()->role == 'user')
                          <div class="banner-content">
                            <a href="{{url('client-profile')}}"
                               class="btn btn-custom btn-xs">{!! Lang::get('lang.profile') !!}</a>
                          </div>
                        @endif
                      @endif
                    </div>
                  </li>
                </ul>
              </li>
          </ul><!-- .navbar-user -->
          @else
          </ul>
          @if(isset($errors))
            <ul class="nav navbar-nav navbar-login">
              <li
                <?php
                if (is_object($errors)) {
                if ($errors->first('email') || $errors->first('password')) {
                ?> class="sfHover"
              <?php
                }
                }
                ?>
              ><a href="#" data-toggle="collapse"
                  <?php
                  if (is_object($errors)) {
                  if ($errors->first('email') || $errors->first('password')) {

                  } else {
                  ?> class="collapsed"
                  <?php
                  }
                  }
                  ?>
                  data-target="#login-form">{!! Lang::get('lang.login') !!} <i
                    class="sub-indicator fa fa-chevron-circle-down fa-fw text-muted"></i></a></li>
            </ul><!-- .navbar-login -->
          @endif
          <div id="login-form"
               @if(isset($errors))<?php if ($errors->first('email') || $errors->first('password')) { ?> class="login-form collapse fade clearfix in"
               <?php } else { ?> class="login-form collapse fade clearfix" <?php } ?>@endif >
            <div class="row">
              <div class="col-md-12">
                {!!  Form::open(['action'=>'Auth\AuthController@postLogin', 'method'=>'post']) !!}
                @if(Session::has('errors'))
                  @if(Session::has('check'))
                    <?php goto b; ?>
                  @endif
                  @if(Session::has('error'))
                    <div class="alert alert-danger alert-dismissable">

                      {!! Session::get('error') !!}

                    </div>
                  @endif
                  <?php b: ?>
                @endif
                <div
                  class="form-group has-feedback @if(isset($errors)) {!! $errors->has('email') ? 'has-error' : '' !!} @endif">
                  {!! Form::text('email',null,['placeholder'=>Lang::get('lang.e-mail'),'class' => 'form-control']) !!}
                  <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
                </div>
                <div
                  class="form-group has-feedback @if(isset($errors)) {!! $errors->has('password') ? 'has-error' : '' !!} @endif">
                  {!! Form::password('password',['placeholder'=>Lang::get('lang.password'),'class' => 'form-control']) !!}
                  <span class="glyphicon glyphicon-lock form-control-feedback"></span>
                  <?php \Event::fire('auth.login.form'); ?>
                  <a href="{{url('password/email')}}" style="font-size: .8em"
                     class="pull-left">{!! Lang::get('lang.forgot_password') !!}</a>
                </div>
                <div class="form-group pull-left">
                  <input type="checkbox" name="remember"> {!! Lang::get("lang.remember") !!}
                </div>
              </div>
              <div class="row">
                <div class="col-md-12">
                  <button type="submit" class="btn btn-custom  .btn-sm ">{!! Lang::get('lang.login') !!}</button>
                  {!! Form::close() !!}
                </div>
              </div>
              {{Lang::get('lang.or')}}
              <div class="row">
                <div class="col-md-12">
                  <ul class="list-unstyled">
                    <a href="{{url('auth/register')}}"
                       style="font-size: 1.2em">{!! Lang::get('lang.create_account') !!}</a>
                  </ul>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-12">
                @include('themes.default1.client.layout.social-login')
              </div>
            </div>
          </div><!-- #login-form -->
          @endif
          <ul class="nav navbar-nav navbar-menu">
            <?php $src = Lang::getLocale() . '.png'; ?>
            <li><a href="#"><img src="{{asset("lb-faveo/flags/$src")}}"></img></a>
              <ul class="dropdown-menu">
                @foreach($langs as $key => $value)
                  <?php $src = $key . ".png"; ?>
                  <li><a href="#" id="{{$key}}" onclick="changeLang(this.id)"><img
                        src="{{asset("lb-faveo/flags/$src")}}"></img>&nbsp;{{$value}}</a></li>
                @endforeach
              </ul>
            </li>
          </ul>
        </nav><!-- #site-navigation -->
      </div><!-- #navbar -->
      <div id="header-search" class="site-search clearfix" style="padding-bottom:5px"><!-- #header-search -->
        <div class="form-border">
          <div class="form-inline ">
            <div class="form-group">
              <input type="text" name="s" class="search-field form-control input-lg" title="Enter search term"
                     placeholder="{!! Lang::get('lang.have_a_question?_type_your_search_term_here') !!}" required/>
            </div>
            <button type="submit"
                    class="search-submit btn btn-custom btn-lg pull-right">{!! Lang::get('lang.search') !!}</button>
          </div>
        </div>
      </div>
    </div>
  </header>
  <!-- Left side column. contains the logo and sidebar -->

  <div class="site-hero clearfix">


    {!! Breadcrumbs::render() !!}
  </div>
  <!-- Main content -->
  <div id="main" class="site-main clearfix">
    <div class="container">
      <div class="content-area">
        <div class="row">
          @if(Session::has('success'))
            <div class="alert alert-success alert-dismissable">
              <i class="fa  fa-check-circle"></i>
              <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
              {{Session::get('success')}}
            </div>
          @endif
          @if(Session::has('warning'))
            <div class="alert alert-warning alert-dismissable">
              <i class="fa  fa-check-circle"></i>
              <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
              {!! Session::get('warning') !!}
            </div>
            @endif
              <!-- failure message -->
            @if(Session::has('fails'))
              @if(Session::has('check'))
                <?php goto a; ?>
              @endif
              <div class="alert alert-danger alert-dismissable">
                <i class="fa fa-ban"></i>
                <b>{!! Lang::get('lang.alert') !!} !</b>
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                {{Session::get('fails')}}
              </div>
              <?php a: ?>
            @endif
            @yield('content')
            <div id="sidebar" class="site-sidebar col-md-3">
              <div class="widget-area">
                <section id="section-banner" class="section">
                  @yield('check')
                </section><!-- #section-banner -->
                <section id="section-categories" class="section">
                  @yield('category')
                </section><!-- #section-categories -->
              </div>
            </div><!-- #sidebar -->
        </div>
      </div>
    </div>
  </div>
  <!-- /.content-wrapper -->
  <?php
  $footer1 = App\Model\helpdesk\Theme\Widgets::where('name', '=', 'footer1')->first();
  $footer2 = App\Model\helpdesk\Theme\Widgets::where('name', '=', 'footer2')->first();
  $footer3 = App\Model\helpdesk\Theme\Widgets::where('name', '=', 'footer3')->first();
  $footer4 = App\Model\helpdesk\Theme\Widgets::where('name', '=', 'footer4')->first();
  ?>
  <footer id="colophon" class="site-footer" role="contentinfo">
    <div class="container">
      <div class="row col-md-12">
        &nbsp;
      </div>
  </footer><!-- #colophon -->
  <!-- jQuery 2.1.1 -->
  <script src="{{asset("lb-faveo/js/jquery2.1.1.min.js")}}" type="text/javascript"></script>
  <!-- Bootstrap 3.3.2 JS -->
  <script src="{{asset("lb-faveo/js/bootstrap.min.js")}}" type="text/javascript"></script>
  <!-- Slimscroll -->
  <script src="{{asset("lb-faveo/js/superfish.js")}}" type="text/javascript"></script>

  <script src="{{asset("lb-faveo/js/mobilemenu.js")}}" type="text/javascript"></script>

  <script src="{{asset("lb-faveo/js/know.js")}}" type="text/javascript"></script>

  <script src="{{asset("lb-faveo/js/jquery.rating.pack.js")}}" type="text/javascript"></script>

  <script src="{{asset("lb-faveo/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js")}}"
          type="text/javascript"></script>

  <script src="{{asset("lb-faveo/plugins/iCheck/icheck.min.js")}}" type="text/javascript"></script>

  <script>
    $(function () {
//Enable check and uncheck all functionality
      $(".checkbox-toggle").click(function () {
        var clicks = $(this).data('clicks');
        if (clicks) {
          //Uncheck all checkboxes
          $("input[type='checkbox']", ".mailbox-messages").iCheck("uncheck");
        } else {
          //Check all checkboxes
          $("input[type='checkbox']", ".mailbox-messages").iCheck("check");
        }
        $(this).data("clicks", !clicks);
      });
//Handle starring for glyphicon and font awesome
      $(".mailbox-star").click(function (e) {
        e.preventDefault();
//detect type
        var $this = $(this).find("a > i");
        var glyph = $this.hasClass("glyphicon");
        var fa = $this.hasClass("fa");
//Switch states
        if (glyph) {
          $this.toggleClass("glyphicon-star");
          $this.toggleClass("glyphicon-star-empty");
        }
        if (fa) {
          $this.toggleClass("fa-star");
          $this.toggleClass("fa-star-o");
        }
      });
    });
  </script>
  <script type="text/javascript">
    function changeLang(lang) {
      location.href = "swtich-language/" + lang;
    }
  </script>
  <script src="{{asset("lb-faveo/js/angular/angular.min.js")}}" type="text/javascript"></script>
  <script src="{{asset("lb-faveo/js/angular/ng-scrollable.min.js")}}" type="text/javascript"></script>
  <script src="{{asset("lb-faveo/js/angular/angular-moment.min.js")}}" type="text/javascript"></script>
  <script src="{{asset('lb-faveo/js/angular/ng-flow-standalone.js')}}"></script>
  <script src="{{asset('lb-faveo/js/angular/fusty-flow.js')}}"></script>
  <script src="{{asset('lb-faveo/js/angular/fusty-flow-factory.js')}}"></script>
  <script src="{{asset('lb-faveo/js/angular/ng-file-upload.js')}}"></script>
  <script src="{{asset('lb-faveo/js/angular/ng-file-upload-shim.min.js')}}"></script>

  <script src="{{asset('lb-faveo/js/angular/angular-translate.js')}}" type="text/javascript"></script>

  <script>
    var app = angular.module('clientApp', ['ngFileUpload', 'pascalprecht.translate']);
    app.config(['$translateProvider', function ($translateProvider) {
      $translateProvider.translations('en', {
        "Requester": "Requester",
        "Subject": "Subject",
        "Type": "Type",
        "Status": "Status",
        "Priority": "Priority",
        "Help Topic": "Help Topic",
        "Assigned": "Assigned",
        "Description": "Description",
        "Company": "Company"
      });
      $translateProvider.translations('ar', {

        "Requester": "الطالب",
        "Subject": "موضوع",
        "Type": "اكتب",
        "Status": "الحالة",
        "Priority": "أفضلية",
        "Help Topic": "موضوع المساعدة",
        "Assigned": "تعيين",
        "Description": "وصف",
        "Company": "شركة"
      });
      if ('{{Lang::getLocale()}}' == 'ar') {
        $translateProvider.preferredLanguage('ar');
      }
      else {
        $translateProvider.preferredLanguage('en');
      }
    }]);
    $(function () {
      if ('{{Lang::getLocale()}}' == 'ar') {
        // $('.container').attr('dir','RTL');
// site-logo text-center
        //
        $('#navbar').css('margin-right', '200px');
        $('.pull-right').toggleClass("pull-left");
        $('.content-area').attr('dir', 'rtl');
        $('.hfeed').find('.text-center').addClass("pull-right");

        $('.nav-tabs li,.form-border,.navbar-nav li,.navbar-nav,.col-md-1,.col-md-2,.col-md-3.col-md-4,.col-md-5,.col-md-6,.col-md-7,.col-md-8,.col-md-9,.col-md-10,.col-md-11,.col-md-12,.col-xs-6').css('float', 'right');

        setTimeout(function () {


          $('#rtl').css('direction', 'rtl');
          $('.input-group').find('.form-control').css('float', 'inherit');
          $('.col-sm-1,.col-sm-2,.col-sm-3,.col-sm-4,.col-sm-5,.col-sm-6,.col-sm-7,.col-sm-8,.col-sm-9,.col-sm-10,.col-sm-11,.col-sm-12').css('float', 'none');
        }, 1000);
      }
    })

  </script>
@stack('scripts')

</body>
</html>