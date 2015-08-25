<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
  <meta name="description" content="{{ config('project.description') }}">
  <meta name="author" content="{{ config('project.author.0.name') }}">
  <meta name="csrf-token" content="{{ csrf_token() }}" />

  <meta property="og:site_name" content="{{ config('project.name') }}" />
  <meta property="og:image" content="" />
  <meta property="og:type" content="Website" />
  <meta property="article:author" content="{{ config('project.author.0.name') }}" />

  <title>{{ config('project.name') }}</title>

  <link rel="shortcut icon" href="/favicon.ico" type="image/x-icon">
  <link rel="icon" href="/favicon.ico" type="image/x-icon">

  <link href="{{ elixir("css/all.css") }}" rel="stylesheet">

  @yield('style')

  <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
  <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
  <!--[if lt IE 9]>
    <script src="//oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    <script src="//oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->
</head>
<body>
  @if ($currentRouteName != 'front')
    @include('layouts._navigation')
  @endif

  @include('flash::message')

  <div class="container">
    @yield('content')

    @include('layouts._footer')
  </div>


  <!-- Scripts -->
  <script src="{{ elixir("js/all.js") }}"></script>

  @yield('script')
</body>
</html>
