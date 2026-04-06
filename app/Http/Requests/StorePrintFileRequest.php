<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePrintFileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'file' => [
                'required',
                'file',
                'max:51200',
                'extensions:stl,obj,3mf,gcode',
            ],
            'notes' => ['nullable', 'string', 'max:2000'],
        ];
    }
}
