<nav class="navbar navbar-default">
  <div class="container-fluid">
    <div class="navbar-header">
      <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
        <span class="sr-only">Toggle Navigation</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
      <a class="navbar-brand" href="#">{{ config('project.name') }}</a>
    </div>

    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
      <ul class="nav navbar-nav">
        <li><a href="{{ route('front') }}">Home</a></li>
      </ul>

      <ul class="nav navbar-nav navbar-right">
        @if (! $currentUser)
          <li><a href="{{ route('session.create', ['return' => $currentRouteName]) }}">Login</a></li>
          <li><a href="{{ route('user.create', ['return' => $currentRouteName]) }}">Register</a></li>
        @else
          <li class="dropdown">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
              {{ $currentUser->name }}
              @if ($currentUser->nickname)
                ({{ $currentUser->nickname}})
              @endif
              <span class="caret"></span>
            </a>
            <ul class="dropdown-menu" role="menu">
              <li><a href="{{ route('session.destroy') }}">Logout</a></li>
              <li><a href="{{ route('user.destroy') }}">Delete Account</a></li>
              <!--TODO implement javascript warning message before delete account-->
            </ul>
          </li>
        @endif
      </ul>
    </div>
  </div>
</nav>