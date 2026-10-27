<?php

namespace App\Http\Requests;

use App\Enums\Role;
use App\Models\Ticket;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreMessageRequest extends FormRequest
{
    public function authorize(): bool
    {
        $ticket = $this->route('ticket');

        return $ticket instanceof Ticket
            && ($this->user()?->can('reply', $ticket) ?? false);
    }

    public function rules(): array
    {
        return [
            'body' => ['required', 'string', 'max:10000'],
            'is_internal' => [
                'sometimes',
                'boolean',
                Rule::prohibitedIf(fn (): bool => $this->user()?->role === Role::CLIENT),
            ],
            'attachments' => ['sometimes', 'array', 'max:5'],
            'attachments.*' => [
                'file',
                'max:10240',
                'mimes:jpg,jpeg,png,pdf,txt,zip',
            ],
        ];
    }
}
