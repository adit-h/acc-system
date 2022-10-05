<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;


class MasterAccountRequest extends FormRequest
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

        $rules = [];
        switch ($method) {
            case 'post':
                $rules = [
                    'code' => 'required|unique:master_accounts|max:100',
                    'name' => 'required|max:100',
                    'status' => 'required',
                    'category_id' => 'required',
                ];
                break;
            case 'patch':
                $rules = [
                    'code' => 'required|unique:master_accounts|max:100',
                    'name' => 'required|max:100',
                    'status' => 'required',
                    'category_id' => 'required',
                ];
                break;
        }

        return $rules;
    }

    public function messages()
    {
        return [
            'category_id.*'  =>'Account Category is required.',
        ];
    }

     /**
     * @param Validator $validator
     */
    protected function failedValidation(Validator $validator){
        $data = [
            'status' => true,
            'message' => $validator->errors()->first(),
            'all_message' =>  $validator->errors()
        ];

        if ($this->ajax()) {
            throw new HttpResponseException(response()->json($data,422));
        } else {
            throw new HttpResponseException(redirect()->back()->withInput()->with('errors', $validator->errors()));
        }
    }


}
