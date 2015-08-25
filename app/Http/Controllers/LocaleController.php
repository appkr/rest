<?php

namespace App\Http\Controllers;

use App\Http\Requests\LocaleRequest;

class LocaleController extends Controller
{

    /**
     * Process locale setting request
     *
     * @param LocaleRequest $request
     *
     * @return $this
     */
    public function index(LocaleRequest $request){
        $cookie = cookie()->forever('locale', $request->input('locale'));

        cookie()->queue($cookie);

        return ($return = $request->input('return'))
            ? redirect(urldecode($return))->withCookie($cookie)
            : redirect()->route('home')->withCookie($cookie);
    }

}
