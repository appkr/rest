<?php

namespace App\Http\Requests;

use App\Todo;

class TodoRequest extends Request
{

    /**
     * @var array
     */
    protected $rules = [
        'title'       => 'required|min:2',
        'description' => 'min:2'
    ];

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        if ($this->isUpdateRequest() or $this->isDeleteRequest()) {
            $id = $this->route('todo');

            return Todo::where('id', $id)
                ->where('user_id', $this->user()->id)->exists();
        }

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

        if ($this->isUpdateRequest()) {
            $rules['done'] = 'boolean';
        }

        if ($this->isDeleteRequest()) {
        }

        return $rules;
    }

}