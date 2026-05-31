<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class InquiryQuotationRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        if ($this->has('amount') && $this->input('amount') !== null) {
            $this->merge(['amount' => (string) $this->input('amount')]);
        }
    }

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $inquiryQuotation = $this->route('inquiryQuotation')
            ?? $this->route('inquiry')
            ?? $this->route('inquiry_quotation')
            ?? $this->route('id');

        $id = is_object($inquiryQuotation) && method_exists($inquiryQuotation, 'getKey')
            ? $inquiryQuotation->getKey()
            : $inquiryQuotation;

        return [
            'inquiry_date' => ['required','date'],
            'inquiry_by' => ['required', Rule::in(['email','whatsapp','phone','site_instruction','meeting','referral','other'])],

            'client_name' => ['required','string','max:150'],
            'client_pic' => ['nullable','string','max:150'],
            'client_phone' => ['nullable','string','max:30'],
            'client_email' => ['nullable','email','max:150'],
            'client_logo' => ['nullable','image','mimes:jpg,jpeg,png,webp','max:2048'],
            'visibility' => ['required', Rule::in(['private', 'public'])],
            'iqm_user_ids' => ['required_if:visibility,private', 'array'],
            'iqm_user_ids.*' => ['integer', 'exists:iqm_users,id'],
            'client_address' => ['nullable','string'],

            'subject' => ['required','string','max:200'],
            'description' => ['nullable','string'],
            'pic_internal' => ['nullable','string','max:150'],

            'site_survey_status' => ['nullable', Rule::in(['not_required','scheduled','done'])],
            'site_survey_date' => ['nullable','date'],
            'site_survey_notes' => ['nullable','string'],

            'quotation_number' => [$this->maybeUniqueQuotationNumber($id), 'nullable', 'string', 'max:50'],
            'quotation_date' => ['nullable','date'],
            'deadline' => ['nullable','date'],
            'amount' => ['nullable','string','max:30'],
            'quotation_status' => ['nullable', Rule::in(['draft','process','submitted','revision','approved','rejected','closed'])],

            'notes' => ['nullable','string'],
            'attachments' => ['nullable','array'],
            'attachments.*' => ['file','max:10240','mimes:jpg,jpeg,png,pdf'],
        ];
    }

    protected function maybeUniqueQuotationNumber($id)
    {
        if ($id) {
            return Rule::unique('inquiry_quotations', 'quotation_number')->ignore($id, 'id');
        }

        return Rule::unique('inquiry_quotations', 'quotation_number');
    }

    public function messages(): array
    {
        return [
            'inquiry_date.required' => 'Tanggal inquiry harus diisi.',
            'inquiry_date.date' => 'Format tanggal inquiry tidak valid.',
            'inquiry_by.required' => 'Sumber inquiry harus dipilih.',

            'client_name.required' => 'Nama klien harus diisi.',
            'client_email.email' => 'Format email klien tidak valid.',
            'client_logo.image' => 'Logo client harus berupa gambar.',
            'client_logo.mimes' => 'Logo client hanya boleh berformat JPG, JPEG, PNG, atau WEBP.',
            'client_logo.max' => 'Logo client maksimal 2MB.',
            'visibility.required' => 'Visibility portal harus dipilih.',
            'visibility.in' => 'Visibility portal tidak valid.',
            'iqm_user_ids.required_if' => 'Minimal satu Portal User wajib dipilih jika visibility private.',

            'subject.required' => 'Subjek harus diisi.',

            'quotation_number.unique' => 'Nomor quotation sudah digunakan.',
            'amount.max' => 'Jumlah maksimal 30 karakter.',
            'attachments.array' => 'Attachment harus berupa file.',
            'attachments.*.file' => 'Attachment harus berupa file yang valid.',
            'attachments.*.max' => 'Attachment maksimal 10MB per file.',
            'attachments.*.mimes' => 'Attachment hanya boleh berformat JPG, JPEG, PNG, atau PDF.',

            'quotation_status.in' => 'Status quotation tidak valid.',
            'site_survey_status.in' => 'Status survey tidak valid.',
        ];
    }
}
