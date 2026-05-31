@php
    $entry = $entry ?? $inquiryQuotation ?? $item ?? null;
@endphp

<style>
    .iq-form .card { border-radius: 12px; }
    .iq-form .card-header { padding: 1rem 1.25rem 0; background: transparent; border-bottom: 0; }
    .iq-form .card-header h5 { font-size: 15px; font-weight: 700; margin: 0; }
    .iq-form .card-body { padding: 1rem 1.25rem 1.25rem; }
    .iq-form .form-label { font-size: 13px; font-weight: 600; color: #344767; margin-bottom: 0.35rem; }
    .iq-form .form-control, .iq-form .form-select { min-height: 42px; font-size: 13px; }
    .iq-form textarea.form-control { min-height: 120px; }
    .iq-form .form-text { font-size: 12px; }
    .iq-logo-preview { border: 1px solid #edf0f5; border-radius: 12px; padding: 12px; background: #f8fafc; display: inline-flex; align-items: center; gap: 12px; }
    .iq-logo-preview img { max-height: 72px; max-width: 160px; object-fit: contain; }
    @media (max-width: 575.98px) {
        .iq-form .card-body, .iq-form .card-header { padding-left: 1rem; padding-right: 1rem; }
    }
</style>

<div class="row iq-form">
    <div class="col-12">
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header">
                <h5>Data Inquiry</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label" for="inquiry_date">Tanggal Inquiry *</label>
                        <input type="date" id="inquiry_date" name="inquiry_date" class="form-control @error('inquiry_date') is-invalid @enderror" value="{{ old('inquiry_date', optional($entry?->inquiry_date)->format('Y-m-d')) }}" required>
                        @error('inquiry_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label" for="inquiry_by">Inquiry By *</label>
                        <select id="inquiry_by" name="inquiry_by" class="form-select @error('inquiry_by') is-invalid @enderror" required>
                            <option value="">-- Pilih --</option>
                            @foreach(['email' => 'Email', 'whatsapp' => 'WhatsApp', 'phone' => 'Telepon', 'site_instruction' => 'Instruksi Lokasi', 'meeting' => 'Meeting', 'referral' => 'Referral', 'other' => 'Lainnya'] as $value => $label)
                                <option value="{{ $value }}" @selected(old('inquiry_by', $entry?->inquiry_by) === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('inquiry_by') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label class="form-label" for="subject">Subjek Pekerjaan *</label>
                        <input type="text" id="subject" name="subject" class="form-control @error('subject') is-invalid @enderror" value="{{ old('subject', $entry?->subject) }}" maxlength="200" required>
                        @error('subject') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>
                <div class="row">
                    <div class="col-12 mb-3">
                        <label class="form-label" for="description">Description</label>
                        <textarea id="description" name="description" class="form-control @error('description') is-invalid @enderror" rows="3">{{ old('description', $entry?->description) }}</textarea>
                        @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header">
                <h5>Data Client</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="client_name">Nama Client *</label>
                        <input type="text" id="client_name" name="client_name" class="form-control @error('client_name') is-invalid @enderror" value="{{ old('client_name', $entry?->client_name) }}" maxlength="150" required>
                        @error('client_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="client_pic">Client PIC</label>
                        <input type="text" id="client_pic" name="client_pic" class="form-control" value="{{ old('client_pic', $entry?->client_pic) }}" maxlength="150">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="client_phone">Telepon</label>
                        <input type="text" id="client_phone" name="client_phone" class="form-control" value="{{ old('client_phone', $entry?->client_phone) }}" maxlength="30">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="client_email">Email</label>
                        <input type="email" id="client_email" name="client_email" class="form-control @error('client_email') is-invalid @enderror" value="{{ old('client_email', $entry?->client_email) }}" maxlength="150">
                        @error('client_email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        @if(!empty($entry?->client_logo))
                            <div class="mb-3">
                                <label class="form-label">Logo Saat Ini</label>
                                <div class="iq-logo-preview">
                                    <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($entry->client_logo) }}" alt="Logo {{ $entry->client_name }}" class="img-thumbnail">
                                    <div>
                                        <div class="text-sm font-weight-bold mb-1">Logo client / perusahaan</div>
                                        <div class="text-xs text-muted">Upload logo baru untuk mengganti.</div>
                                    </div>
                                </div>
                            </div>
                        @endif
                        <label class="form-label" for="client_logo">{{ !empty($entry?->client_logo) ? 'Ganti Logo' : 'Logo Client / Perusahaan' }}</label>
                        <input type="file" id="client_logo" name="client_logo" class="form-control @error('client_logo') is-invalid @enderror" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp">
                        <div class="form-text">Maksimal 2MB. Format JPG, JPEG, PNG, atau WEBP.</div>
                        @error('client_logo') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label" for="client_address">Alamat</label>
                    <textarea id="client_address" name="client_address" class="form-control" rows="3">{{ old('client_address', $entry?->client_address) }}</textarea>
                </div>
            </div>
        </div>

        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header">
                <h5>Portal Access</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="visibility">Visibility *</label>
                        <select id="visibility" name="visibility" class="form-select @error('visibility') is-invalid @enderror" required>
                            <option value="private" @selected(old('visibility', $entry?->visibility ?? 'private') === 'private')>Private</option>
                            <option value="public" @selected(old('visibility', $entry?->visibility ?? 'private') === 'public')>Public</option>
                        </select>
                        @error('visibility') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-12 mb-3" id="portal-users-panel">
                        <label class="form-label">Portal User / User IQM</label>
                        @php
                            $selectedIqmUserIds = collect(old('iqm_user_ids', $entry?->iqmUsers?->pluck('id')->all() ?? ($entry?->iqm_user_id ? [$entry->iqm_user_id] : [])))
                                ->map(fn($id) => (int) $id)
                                ->all();
                        @endphp
                        <div class="row g-2">
                            @foreach(($iqmUsers ?? collect()) as $portalUser)
                                <div class="col-md-6 col-xl-4">
                                    <label class="border rounded p-3 w-100 h-100 d-flex gap-2 align-items-start">
                                        <input type="checkbox" name="iqm_user_ids[]" value="{{ $portalUser->id }}" class="form-check-input mt-1 portal-user-checkbox" @checked(in_array((int) $portalUser->id, $selectedIqmUserIds, true))>
                                        <span>
                                            <span class="d-block fw-semibold">{{ $portalUser->company_name }}</span>
                                            <span class="d-block text-muted small">{{ $portalUser->pic_name }} - {{ $portalUser->email ?: $portalUser->username }}</span>
                                        </span>
                                    </label>
                                </div>
                            @endforeach
                        </div>
                        <div class="form-text">Private wajib memilih minimal satu Portal User. Public dapat dilihat semua user IQM.</div>
                        @error('iqm_user_ids') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                        @error('iqm_user_ids.*') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header">
                <h5>Site Survey</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label" for="site_survey_status">Site Survey Status *</label>
                        <select id="site_survey_status" name="site_survey_status" class="form-select @error('site_survey_status') is-invalid @enderror" required>
                            <option value="">-- Pilih --</option>
                            @foreach(['not_required' => 'Tidak Diperlukan', 'scheduled' => 'Dijadwalkan', 'done' => 'Selesai'] as $value => $label)
                                <option value="{{ $value }}" @selected(old('site_survey_status', $entry?->site_survey_status) === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('site_survey_status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label" for="site_survey_date">Tanggal Survey</label>
                        <input type="date" id="site_survey_date" name="site_survey_date" class="form-control @error('site_survey_date') is-invalid @enderror" value="{{ old('site_survey_date', optional($entry?->site_survey_date)->format('Y-m-d')) }}">
                        @error('site_survey_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label" for="site_survey_notes">Site Survey Notes</label>
                    <textarea id="site_survey_notes" name="site_survey_notes" class="form-control" rows="3">{{ old('site_survey_notes', $entry?->site_survey_notes) }}</textarea>
                </div>
            </div>
        </div>

        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header">
                <h5>Quotation</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label" for="quotation_number">Quotation Number</label>
                        <input type="text" id="quotation_number" name="quotation_number" class="form-control @error('quotation_number') is-invalid @enderror" value="{{ old('quotation_number', $entry?->quotation_number) }}" maxlength="50">
                        <div class="form-text">Biarkan kosong untuk auto-generate nomor quotation.</div>
                        @error('quotation_number') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label" for="quotation_date">Tanggal Quotation</label>
                        <input type="date" id="quotation_date" name="quotation_date" class="form-control @error('quotation_date') is-invalid @enderror" value="{{ old('quotation_date', optional($entry?->quotation_date)->format('Y-m-d')) }}">
                        @error('quotation_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label" for="deadline">Deadline</label>
                        <input type="date" id="deadline" name="deadline" class="form-control @error('deadline') is-invalid @enderror" value="{{ old('deadline', optional($entry?->deadline)->format('Y-m-d')) }}">
                        @error('deadline') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label" for="amount">Amount (Rp)</label>
                        <input type="text" id="amount" name="amount" class="form-control @error('amount') is-invalid @enderror" value="{{ old('amount', $entry?->amount ? (int) $entry->amount : '') }}" inputmode="numeric" autocomplete="off">
                        @error('amount') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label" for="quotation_status">Quotation Status *</label>
                        <select id="quotation_status" name="quotation_status" class="form-select @error('quotation_status') is-invalid @enderror" required>
                            <option value="">-- Pilih --</option>
                            @foreach(['draft' => 'Draft','process' => 'Proses','submitted' => 'Dikirim','revision' => 'Revisi','approved' => 'Disetujui','rejected' => 'Ditolak','closed' => 'Ditutup'] as $value => $label)
                                <option value="{{ $value }}" @selected(old('quotation_status', $entry?->quotation_status) === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('quotation_status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header">
                <h5>Additional Notes</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label" for="notes">Catatan Tambahan</label>
                    <textarea id="notes" name="notes" class="form-control" rows="3">{{ old('notes', $entry?->notes) }}</textarea>
                </div>
            </div>
        </div>

        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header">
                <h5>Attachments</h5>
            </div>
            <div class="card-body">
                @if($entry?->attachments?->isNotEmpty())
                    <div class="alert alert-secondary">
                        Attachment lama akan tetap ada. Tambahkan file baru jika perlu.
                    </div>
                @endif
                <div class="mb-3">
                    <label class="form-label" for="attachments">Upload Attachment</label>
                    <input type="file" id="attachments" name="attachments[]" class="form-control @error('attachments') is-invalid @enderror @error('attachments.*') is-invalid @enderror" multiple accept=".jpg,.jpeg,.png,.pdf">
                    <div class="form-text">Maksimal 10MB per file. JPG, JPEG, PNG, PDF.</div>
                    @error('attachments') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    @foreach ($errors->get('attachments.*', []) as $message)
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const visibility = document.getElementById('visibility');
    const panel = document.getElementById('portal-users-panel');
    const checkboxes = document.querySelectorAll('.portal-user-checkbox');

    function syncPortalAccess() {
        const isPublic = visibility && visibility.value === 'public';
        if (panel) {
            panel.classList.toggle('d-none', isPublic);
        }
        if (isPublic) {
            checkboxes.forEach((checkbox) => {
                checkbox.checked = false;
            });
        }
    }

    visibility?.addEventListener('change', syncPortalAccess);
    syncPortalAccess();
});
</script>
@endpush

<script>
document.addEventListener('DOMContentLoaded', function () {
    var amountInput = document.getElementById('amount');
    if (!amountInput) {
        return;
    }

    var formatRupiah = function (value) {
        var digits = String(value || '').replace(/\D/g, '');
        if (!digits) {
            return '';
        }
        return 'Rp ' + new Intl.NumberFormat('id-ID').format(Number(digits));
    };

    amountInput.value = formatRupiah(amountInput.value);
    amountInput.addEventListener('input', function () {
        amountInput.value = formatRupiah(amountInput.value);
    });

    var form = amountInput.closest('form');
    if (form) {
        form.addEventListener('submit', function () {
            amountInput.value = amountInput.value.replace(/\D/g, '');
        });
    }
});
</script>
