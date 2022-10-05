<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;


class TransactionInRequest extends FormRequest
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
                    'trans_date' => 'required|date',
                    'receive_from' => 'required',
                    'store_to' => 'required',
                    'value' => 'required|max:1000000000',
                    'reference' => 'max:200',
                    'description' => 'max:200',
                ];
                break;
            case 'patch':
                $rules = [
                    'trans_date' => 'required|date',
                    'receive_from' => 'required',
                    'store_to' => 'required',
                    'value' => 'required|max:1000000000',
                    'reference' => 'max:200',
                    'description' => 'max:200',
                ];
                break;
        }

        return $rules;
    }

    public function messages()
    {
        return [
            'receive_from.*'  =>'Account is required.',
            'store_to.*'  =>'Account is required.',
        ];
    }

     /**
     * @param Validator $validator
     */
    protected function failedValidation(Validator $validator){
        $data = [
            'status' => true,
            'message' => $validator->errors()->first(),
            'all_message' => $validator->errors()
        ];

        if ($this->ajax()) {
            throw new HttpResponseException(response()->json($data,422));
        } else {
            throw new HttpResponseException(redirect()->back()->withInput()->with('errors', $validator->errors()));
        }
    }


}
