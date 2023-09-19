<?php

namespace App\Http\Requests\Admin;

use App\Enums\Games\GameRoles;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;

class UserAcceptGameRequestGuest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array {
        return [
            'name' => [
                'required',
                'string',
                'min:4',
                'max:255'
            ],
            
            'gameRole' => [
                'required',
                new Enum(GameRoles::class)
            ],
        ];
    }
}