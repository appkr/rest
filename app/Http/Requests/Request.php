<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

abstract class Request extends FormRequest
{
    /**
     * {@inheritDoc}
     */
    public function response(array $errors)
    {
        if (is_api_request()) {
            return app('api.response')->unprocessableError($errors);
        }

        return parent::response($errors);
    }

    /**
     * {@inheritDoc}
     */
    protected function failedAuthorization()
    {
        if (is_api_request()) {
            return app('api.response')->unauthorizedError();
        }

        return parent::failedAuthorization();
    }

    /**
     * Determine if the request is update
     *
     * @return bool
     */
    protected function isUpdateRequest()
    {
        $needle = ['put', 'PUT', 'patch', 'PATCH'];

        return in_array($this->input('_method'), $needle)
        or in_array($this->header('x-http-method-override'), $needle);
    }

    /**
     * Determine if the request is delete
     *
     * @return bool
     */
    protected function isDeleteRequest()
    {
        $needle = ['delete', 'DELETE'];

        return in_array($this->input('_method'), $needle)
        or in_array($this->header('x-http-method-override'), $needle);
    }
}
