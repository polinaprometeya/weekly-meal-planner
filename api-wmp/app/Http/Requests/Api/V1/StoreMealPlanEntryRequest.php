<?php

namespace App\Http\Requests\Api\V1;

use App\Enums\MealSlot;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreMealPlanEntryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'recipe_id' => [
                'required',
                'integer',
                Rule::exists('recipes', 'id')->where('is_reviewed', true),
            ],
            'day_of_week' => ['required', 'integer', 'min:0', 'max:6'],
            'meal_slot' => ['required', Rule::enum(MealSlot::class)],
            'servings_override' => ['nullable', 'integer', 'min:1'],
        ];
    }
}
