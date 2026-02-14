<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AnalyzeIncidentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'logs' => 'required|array',
            'logs.*' => 'string',
            'metrics' => 'required|array',
            'metrics.cpu' => 'numeric',
            'metrics.db_latency' => 'numeric',
            'metrics.requests_per_sec' => 'string',
        ];
    }
}
