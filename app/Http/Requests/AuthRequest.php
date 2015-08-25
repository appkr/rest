<?php

namespace App\Http\Requests;

class AuthRequest extends Request
{
    /**
     * @var array
     */
    protected $rules = [
        'email'    => 'required|email',
        'password' => 'required'
    ];

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = $this->rules;

        if ($this->isRegisterRequest()) {
            $rules = [
                'name'     => 'required|between:2,255',
                'email'    => 'required|email|max:255|unique:users',
                'password' => 'required|confirmed|min:6',
            ];
        }

        return $rules;
    }

    /**
     * Determine if the request is for new user registration
     *
     * @return bool
     */
    protected function isRegisterRequest()
    {
        return $this->is(get_path_from_route('user.store'))
        or $this->is(get_path_from_route('api.user.store'));
    }

    /**
     * Determine if the request is for user login
     *
     * @return bool
     */
    protected function isLoginRequest()
    {
        return $this->is(get_path_from_route('session.store'))
        or $this->is(get_path_from_route('api.session.store'));
    }
}
