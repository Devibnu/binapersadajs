<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateInquiryQuotationRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        if ($this->has('amount') && $this->input('amount') !== null) {
            $this->merge(['amount' => (string) $this->input('amount')]);
        }
    }

    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->canAccess('inquiry-quotation.edit');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $inquiryQuotation = $this->route('inquiryQuotation')
            ?? $this->route('inquiry')
            ?? $this->route('inquiry_quotation')
            ?? $this->route('id');

        $inquiryId = is_object($inquiryQuotation) && method_exists($inquiryQuotation, 'getKey')
            ? $inquiryQuotation->getKey()
            : $inquiryQuotation;

        return [
            // Inquiry Information
            'inquiry_date' => ['required', 'date'],
            'inquiry_by' => ['required', 'in:email,whatsapp,phone,site_instruction,meeting,referral,other'],

            // Client Information
            'client_name' => ['required', 'string', 'max:150'],
            'client_pic' => ['nullable', 'string', 'max:150'],
            'client_phone' => ['nullable', 'string', 'max:30'],
            'client_email' => ['nullable', 'email', 'max:150'],
            'visibility' => ['required', 'in:private,public'],
            'iqm_user_ids' => ['required_if:visibility,private', 'array'],
            'iqm_user_ids.*' => ['integer', 'exists:iqm_users,id'],
            'client_address' => ['nullable', 'string'],

            // Subject & Description
            'subject' => ['required', 'string', 'max:200'],
            'description' => ['nullable', 'string'],
            'pic_internal' => ['nullable', 'string', 'max:150'],

            // Site Survey
            'site_survey_status' => ['required', 'in:not_required,scheduled,done'],
            'site_survey_date' => ['nullable', 'date'],
            'site_survey_notes' => ['nullable', 'string'],

            // Quotation
            'quotation_number' => [
                'nullable',
                'string',
                'max:50',
                Rule::unique('inquiry_quotations', 'quotation_number')->ignore($inquiryId, 'id'),
            ],
            'quotation_date' => ['nullable', 'date'],
            'deadline' => ['nullable', 'date'],
            'amount' => ['nullable', 'string', 'max:30'],
            'quotation_status' => ['required', 'in:draft,process,submitted,revision,approved,rejected,closed'],

            // Additional Notes
            'notes' => ['nullable', 'string'],

            // Attachments
            'attachments' => ['nullable', 'array'],
            'attachments.*' => ['file', 'mimes:pdf,jpg,jpeg,png,webp', 'max:10240'],
        ];
    }

    public function messages(): array
    {
        return [
            'client_name.required' => 'Nama klien harus diisi',
            'client_name.max' => 'Nama klien maksimal 150 karakter',
            'subject.required' => 'Subject harus diisi',
            'subject.max' => 'Subject maksimal 200 karakter',
            'inquiry_date.required' => 'Tanggal inquiry harus diisi',
            'inquiry_by.required' => 'Inquiry by harus dipilih',
            'site_survey_status.required' => 'Status survey harus dipilih',
            'quotation_status.required' => 'Status quotation harus dipilih',
            'amount.max' => 'Jumlah maksimal 30 karakter',
            'attachments.*.max' => 'File tidak boleh lebih dari 10MB',
            'attachments.*.mimes' => 'Tipe file hanya boleh: PDF, JPG, JPEG, PNG, WebP',
        ];
    }
}
