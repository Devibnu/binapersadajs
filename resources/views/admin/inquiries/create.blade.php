@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header pb-0 pt-3">
                    <h6 class="text-uppercase text-muted ls-2 mb-3">
                        @if(Route::is('admin.inquiries.edit'))
                            Edit Inquiry & Quotation
                        @else
                            Tambah Inquiry & Quotation
                        @endif
                    </h6>
                </div>
                <div class="card-body">
                    <form method="POST" action="@if(Route::is('admin.inquiries.edit')){{ route('admin.inquiries.update', $inquiry) }}@else{{ route('admin.inquiries.store') }}@endif" enctype="multipart/form-data">
                        @csrf
                        @if(Route::is('admin.inquiries.edit'))
                            @method('PUT')
                            @php $inquiry = $inquiry ?? null; @endphp
                        @endif

                        <!-- Section 1: Data Inquiry -->
                        <div class="section-wrapper mb-4">
                            <h6 class="text-muted font-weight-bold mb-3">
                                <i class="fas fa-info-circle"></i> Section 1: Data Inquiry
                            </h6>

                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label for="inquiry_date" class="form-label">Tanggal Inquiry *</label>
                                    <input type="date" class="form-control @error('inquiry_date') is-invalid @enderror" id="inquiry_date" name="inquiry_date" value="{{ old('inquiry_date', isset($inquiry) ? $inquiry->inquiry_date?->format('Y-m-d') : '') }}" required>
                                    @error('inquiry_date') <small class="invalid-feedback">{{ $message }}</small> @enderror
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="inquiry_by" class="form-label">Inquiry By *</label>
                                    <select class="form-select @error('inquiry_by') is-invalid @enderror" id="inquiry_by" name="inquiry_by" required>
                                        <option value="">-- Pilih --</option>
                                        <option value="email" @selected(old('inquiry_by', $inquiry->inquiry_by ?? null) === 'email')>Email</option>
                                        <option value="whatsapp" @selected(old('inquiry_by', $inquiry->inquiry_by ?? null) === 'whatsapp')>WhatsApp</option>
                                        <option value="phone" @selected(old('inquiry_by', $inquiry->inquiry_by ?? null) === 'phone')>Telepon</option>
                                        <option value="site_instruction" @selected(old('inquiry_by', $inquiry->inquiry_by ?? null) === 'site_instruction')>Instruksi Lokasi</option>
                                        <option value="meeting" @selected(old('inquiry_by', $inquiry->inquiry_by ?? null) === 'meeting')>Meeting</option>
                                        <option value="referral" @selected(old('inquiry_by', $inquiry->inquiry_by ?? null) === 'referral')>Referral</option>
                                        <option value="other" @selected(old('inquiry_by', $inquiry->inquiry_by ?? null) === 'other')>Lainnya</option>
                                    </select>
                                    @error('inquiry_by') <small class="invalid-feedback">{{ $message }}</small> @enderror
                                </div>
                            </div>

                            <!-- Client Information -->
                            <h6 class="text-muted font-weight-bold mb-3 mt-4">Informasi Klien</h6>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="client_name" class="form-label">Nama Klien *</label>
                                    <input type="text" class="form-control @error('client_name') is-invalid @enderror" id="client_name" name="client_name" placeholder="PT. Bina Persada" value="{{ old('client_name', $inquiry->client_name ?? '') }}" required maxlength="150">
                                    @error('client_name') <small class="invalid-feedback">{{ $message }}</small> @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="client_pic" class="form-label">PIC Klien</label>
                                    <input type="text" class="form-control" id="client_pic" name="client_pic" placeholder="Nama Kontak" value="{{ old('client_pic', $inquiry->client_pic ?? '') }}" maxlength="150">
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="client_phone" class="form-label">Telepon</label>
                                    <input type="text" class="form-control" id="client_phone" name="client_phone" placeholder="+62 812-3456-7890" value="{{ old('client_phone', $inquiry->client_phone ?? '') }}" maxlength="30">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="client_email" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="client_email" name="client_email" placeholder="email@example.com" value="{{ old('client_email', $inquiry->client_email ?? '') }}" maxlength="150">
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <label for="client_address" class="form-label">Alamat</label>
                                    <textarea class="form-control" id="client_address" name="client_address" rows="2" placeholder="Jalan, RT/RW, Kelurahan, Kecamatan, Kota, Provinsi">{{ old('client_address', $inquiry->client_address ?? '') }}</textarea>
                                </div>
                            </div>

                            <!-- Subject & Description -->
                            <h6 class="text-muted font-weight-bold mb-3 mt-4">Subjek & Deskripsi</h6>
                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <label for="subject" class="form-label">Subject *</label>
                                    <input type="text" class="form-control @error('subject') is-invalid @enderror" id="subject" name="subject" placeholder="Pembangunan Gedung, Renovasi Rumah, etc" value="{{ old('subject', $inquiry->subject ?? '') }}" required maxlength="200">
                                    @error('subject') <small class="invalid-feedback">{{ $message }}</small> @enderror
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="description" class="form-label">Deskripsi</label>
                                    <textarea class="form-control" id="description" name="description" rows="3" placeholder="Deskripsi detail tentang inquiry...">{{ old('description', $inquiry->description ?? '') }}</textarea>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="pic_internal" class="form-label">PIC Internal</label>
                                    <input type="text" class="form-control" id="pic_internal" name="pic_internal" placeholder="Nama PIC dari internal" value="{{ old('pic_internal', $inquiry->pic_internal ?? '') }}" maxlength="150">
                                </div>
                            </div>
                        </div>

                        <hr>

                        <!-- Section 2: Site Survey -->
                        <div class="section-wrapper mb-4">
                            <h6 class="text-muted font-weight-bold mb-3">
                                <i class="fas fa-map-location-dot"></i> Section 2: Site Survey
                            </h6>

                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label for="site_survey_status" class="form-label">Status Site Survey *</label>
                                    <select class="form-select @error('site_survey_status') is-invalid @enderror" id="site_survey_status" name="site_survey_status" required>
                                        <option value="">-- Pilih --</option>
                                        <option value="not_required" @selected(old('site_survey_status', $inquiry->site_survey_status ?? 'not_required') === 'not_required')>Tidak Diperlukan</option>
                                        <option value="scheduled" @selected(old('site_survey_status', $inquiry->site_survey_status ?? null) === 'scheduled')>Dijadwalkan</option>
                                        <option value="done" @selected(old('site_survey_status', $inquiry->site_survey_status ?? null) === 'done')>Selesai</option>
                                    </select>
                                    @error('site_survey_status') <small class="invalid-feedback">{{ $message }}</small> @enderror
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="site_survey_date" class="form-label">Tanggal Survey</label>
                                    <input type="date" class="form-control @error('site_survey_date') is-invalid @enderror" id="site_survey_date" name="site_survey_date" value="{{ old('site_survey_date', isset($inquiry) && $inquiry->site_survey_date ? $inquiry->site_survey_date->format('Y-m-d') : '') }}">
                                    @error('site_survey_date') <small class="invalid-feedback">{{ $message }}</small> @enderror
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <label for="site_survey_notes" class="form-label">Catatan Survey</label>
                                    <textarea class="form-control" id="site_survey_notes" name="site_survey_notes" rows="2" placeholder="Catatan atau hasil site survey...">{{ old('site_survey_notes', $inquiry->site_survey_notes ?? '') }}</textarea>
                                </div>
                            </div>
                        </div>

                        <hr>

                        <!-- Section 3: Quotation -->
                        <div class="section-wrapper mb-4">
                            <h6 class="text-muted font-weight-bold mb-3">
                                <i class="fas fa-file-invoice-dollar"></i> Section 3: Quotation
                            </h6>

                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label for="quotation_date" class="form-label">Tanggal Quotation</label>
                                    <input type="date" class="form-control @error('quotation_date') is-invalid @enderror" id="quotation_date" name="quotation_date" value="{{ old('quotation_date', isset($inquiry) && $inquiry->quotation_date ? $inquiry->quotation_date->format('Y-m-d') : '') }}">
                                    @error('quotation_date') <small class="invalid-feedback">{{ $message }}</small> @enderror
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="deadline" class="form-label">Deadline</label>
                                    <input type="date" class="form-control @error('deadline') is-invalid @enderror" id="deadline" name="deadline" value="{{ old('deadline', isset($inquiry) && $inquiry->deadline ? $inquiry->deadline->format('Y-m-d') : '') }}">
                                    @error('deadline') <small class="invalid-feedback">{{ $message }}</small> @enderror
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="amount" class="form-label">Amount (Rp)</label>
                                    <input type="number" class="form-control @error('amount') is-invalid @enderror" id="amount" name="amount" placeholder="1000000" value="{{ old('amount', $inquiry->amount ?? '') }}" min="0">
                                    @error('amount') <small class="invalid-feedback">{{ $message }}</small> @enderror
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="quotation_status" class="form-label">Status Quotation *</label>
                                    <select class="form-select @error('quotation_status') is-invalid @enderror" id="quotation_status" name="quotation_status" required>
                                        <option value="">-- Pilih --</option>
                                        <option value="draft" @selected(old('quotation_status', $inquiry->quotation_status ?? 'draft') === 'draft')>Draft</option>
                                        <option value="process" @selected(old('quotation_status', $inquiry->quotation_status ?? null) === 'process')>Proses</option>
                                        <option value="submitted" @selected(old('quotation_status', $inquiry->quotation_status ?? null) === 'submitted')>Dikirim</option>
                                        <option value="revision" @selected(old('quotation_status', $inquiry->quotation_status ?? null) === 'revision')>Revisi</option>
                                        <option value="approved" @selected(old('quotation_status', $inquiry->quotation_status ?? null) === 'approved')>Disetujui</option>
                                        <option value="rejected" @selected(old('quotation_status', $inquiry->quotation_status ?? null) === 'rejected')>Ditolak</option>
                                        <option value="closed" @selected(old('quotation_status', $inquiry->quotation_status ?? null) === 'closed')>Ditutup</option>
                                    </select>
                                    @error('quotation_status') <small class="invalid-feedback">{{ $message }}</small> @enderror
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <label for="notes" class="form-label">Catatan</label>
                                    <textarea class="form-control" id="notes" name="notes" rows="2" placeholder="Catatan tambahan mengenai quotation...">{{ old('notes', $inquiry->notes ?? '') }}</textarea>
                                </div>
                            </div>
                        </div>

                        <hr>

                        <!-- Section 4: Lampiran -->
                        <div class="section-wrapper mb-4">
                            <h6 class="text-muted font-weight-bold mb-3">
                                <i class="fas fa-paperclip"></i> Section 4: Lampiran
                            </h6>
                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <label for="attachments" class="form-label">Upload File (PDF, JPG, PNG, WebP) - Max 10MB per file</label>
                                    <input type="file" class="form-control @error('attachments.*') is-invalid @enderror" id="attachments" name="attachments[]" multiple accept=".pdf,.jpg,.jpeg,.png,.webp">
                                    <small class="text-muted">Anda dapat upload multiple file sekaligus</small>
                                    @error('attachments.*') <small class="invalid-feedback">{{ $message }}</small> @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Buttons -->
                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary">
                                @if(Route::is('admin.inquiries.edit'))
                                    Perbarui Inquiry
                                @else
                                    Buat Inquiry
                                @endif
                            </button>
                            <a href="{{ route('admin.inquiries.index') }}" class="btn btn-secondary">Batal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .section-wrapper {
        padding: 1rem 0;
    }
</style>
@endsection
