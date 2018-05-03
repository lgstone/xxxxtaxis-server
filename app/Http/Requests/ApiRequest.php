<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class ApiRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            //
        ];
    }

    protected function failedValidation(validator $validator){
        throw new HttpResponseException(response()->json([
           'status' => config('constant.responseStatus.validateErr.code'),
           'errMsg' => config('constant.responseStatus.validateErr.errMsg'),
           'data' => [],
        ]));
        
    }

}
