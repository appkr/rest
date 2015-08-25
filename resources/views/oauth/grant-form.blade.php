@extends('layouts.master')

@section('content')
{{--
array:5 [▼
  "client" => ClientEntity {#357 ▼
    #id: "client1id"
    #secret: "client1secret"
    #name: "client1"
    #redirectUri: "http://localhost:8000/social/captive"
    #server: ResourceServer {#355 ▶}
  }

Available methods for client
array:6 [▼
  0 => "__construct"
  1 => "getId"
  2 => "getSecret"
  3 => "getName"
  4 => "getRedirectUri"
  5 => "hydrate"
]

  "redirect_uri" => "http://localhost:8000/social/captive"
  "state" => "cfe5fff62ae1a3d04620652f942bc4dcb2016a8f"
  "response_type" => "code"
  "scopes" => array:1 [▼
    "scope1" => ScopeEntity {#349 ▼
      #id: "scope1"
      #description: "Scope 1 Description"
      #server: ResourceServer {#355 ▶}
    }
  ]

Available methods for scopes
array:5 [▼
  0 => "__construct"
  1 => "getId"
  2 => "getDescription"
  3 => "jsonSerialize"
  4 => "hydrate"
]

]
--}}
<div class="container-fluid">
  <div class="row">
    <div class="col-md-8 col-md-offset-2">
      <div class="panel panel-default">
        <div class="panel-heading">Grant Access</div>
        <div class="panel-body">

          <h1>Grant access to {{ $client->getName() }}?</h1>

          <ul>
            <li>client_id: {{ $client->getId() }}</li>
            <li>client_secret: {{ $client->getSecret() }}</li>
          </ul>

          <ul>
            @foreach($scopes as $scope)
              <li>{{ $scope->getId() }} {{ $scope->getDescription() }}</li>
            @endforeach
          </ul>

          <form class="form-horizontal" role="form" method="POST" action="{{ route('oauth.authorize', Request::getQueryString()) }}">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">

            <div class="form-group">
              <div class="col-md-6 col-md-offset-4">
                <button type="submit" class="btn btn-primary" name="authorization" value="approve">Approve</button>
                <button type="submit" class="btn btn-warning" name="authorization" value="deny">Deny</button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
