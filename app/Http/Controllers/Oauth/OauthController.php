<?php

namespace App\Http\Controllers\Oauth;

use App\Http\Controllers\Controller;
use App\Http\Requests;
use Illuminate\Http\Request;
use LucaDegasperi\OAuth2Server\Authorizer;

class OauthController extends Controller
{
    protected $authorizer;

    public function __construct(Authorizer $authorizer)
    {
        $this->authorizer = $authorizer;

        $this->middleware('auth', ['only' => ['getAuthorize', 'postAuthorize']]);
        $this->middleware('csrf', ['only' => 'postAuthorize']);
        $this->middleware('check-authorization-params', ['only' => ['getAuthorize', 'postAuthorize']]);

        parent::__construct();
    }

    /**
     * Respond to the incoming requests for "code"
     *
     * @return \Illuminate\View\View
     */
    public function getAuthorize()
    {
        // display a form where the user can authorize the client to access it's data
        return view('oauth.grant-form', $this->authorizer->getAuthCodeRequestParams());
    }

    /**
     * Respond to the form being posted
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function postAuthorize(Request $request)
    {
        $params['user_id'] = $request->user()->id;

        $redirectUri = '';

        // if the user has allowed the client to access its data,
        // redirect back to the client with an auth code
        if ($request->input('approve') !== null) {
            $redirectUri = $this->authorizer->issueAuthCode('user', $params['user_id'], $params);
        }

        // if the user has denied the client to access its data,
        // redirect back to the client with an error message
        if ($request->input('deny') !== null) {
            $redirectUri = $this->authorizer->authCodeRequestDeniedRedirectUri();
        }

        return redirect($redirectUri);
    }

    /**
     * Respond to the incoming access token requests
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function token()
    {
        return response()->json($this->authorizer->issueAccessToken());
    }
}
