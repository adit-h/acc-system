<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;


class TransLimitRequest extends FormRequest
{
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
        $method = strtolower($this->method());
        $user_id = $this->route()->user;

        $rules = [
            'date_start' => 'required|date',
            'date_end' => 'required|date',
            'status' => 'required',
        ];

        return $rules;
    }

    public function messages()
    {
        return [
            ''  => '',
        ];
    }

    /**
     * @param Validator $validator
     */
    protected function failedValidation(Validator $validator)
    {
        $data = [
            'status' => true,
            'message' => $validator->errors()->first(),
            'all_message' =>  $validator->errors()
        ];

        if ($this->ajax()) {
            throw new HttpResponseException(response()->json($data, 422));
        } else {
            throw new HttpResponseException(redirect()->back()->withInput()->with('errors', $validator->errors()));
        }
    }
}
