@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-md-8">
            <h2 class="h4 font-weight-bolder">Inquiry & Quotation Management</h2>
        </div>
        <div class="col-md-4 text-end">
            @can('inquiry-quotations.create')
            <a href="{{ route('admin.inquiries.create') }}" class="btn btn-sm btn-primary">
                <i class="fas fa-plus"></i> Tambah Inquiry
            </a>
            @endcan
        </div>
    </div>

    <!-- Filter Card -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.inquiries.index') }}" class="row g-3">
                <div class="col-md-3">
                    <input type="text" name="search" class="form-control" placeholder="No. Inquiry, Quotation, Client, Subject" value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <select name="inquiry_by" class="form-control">
                        <option value="">-- Inquiry By --</option>
                        <option value="email" @selected(request('inquiry_by') === 'email')>Email</option>
                        <option value="whatsapp" @selected(request('inquiry_by') === 'whatsapp')>WhatsApp</option>
                        <option value="phone" @selected(request('inquiry_by') === 'phone')>Telepon</option>
                        <option value="site_instruction" @selected(request('inquiry_by') === 'site_instruction')>Instruksi Lokasi</option>
                        <option value="meeting" @selected(request('inquiry_by') === 'meeting')>Meeting</option>
                        <option value="referral" @selected(request('inquiry_by') === 'referral')>Referral</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="site_survey_status" class="form-control">
                        <option value="">-- Survey Status --</option>
                        <option value="not_required" @selected(request('site_survey_status') === 'not_required')>Tidak Diperlukan</option>
                        <option value="scheduled" @selected(request('site_survey_status') === 'scheduled')>Dijadwalkan</option>
                        <option value="done" @selected(request('site_survey_status') === 'done')>Selesai</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="quotation_status" class="form-control">
                        <option value="">-- Quotation Status --</option>
                        <option value="draft" @selected(request('quotation_status') === 'draft')>Draft</option>
                        <option value="process" @selected(request('quotation_status') === 'process')>Proses</option>
                        <option value="submitted" @selected(request('quotation_status') === 'submitted')>Dikirim</option>
                        <option value="revision" @selected(request('quotation_status') === 'revision')>Revisi</option>
                        <option value="approved" @selected(request('quotation_status') === 'approved')>Disetujui</option>
                    </select>
                </div>
                <div class="col-md-1">
                    <button type="submit" class="btn btn-info w-100">Filter</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Table Card -->
    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>No. Inquiry</th>
                        <th>Tanggal</th>
                        <th>Client</th>
                        <th>Subject</th>
                        <th>Inquiry By</th>
                        <th>Survey</th>
                        <th>No. Quotation</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($inquiries as $inquiry)
                    <tr>
                        <td><strong>{{ $inquiry->inquiry_number }}</strong></td>
                        <td>{{ $inquiry->inquiry_date->format('d/m/Y') }}</td>
                        <td>{{ $inquiry->client_name }}</td>
                        <td>
                            <span title="{{ $inquiry->subject }}">
                                {{ Str::limit($inquiry->subject, 30) }}
                            </span>
                        </td>
                        <td>{{ $inquiry->inquiryByLabel() }}</td>
                        <td>
                            <span class="badge {{ $inquiry->surveyStatusBadgeClass() }}">
                                {{ $inquiry->surveyStatusLabel() }}
                            </span>
                        </td>
                        <td>{{ $inquiry->quotation_number ?? '-' }}</td>
                        <td>{{ $inquiry->formattedAmount() }}</td>
                        <td>
                            <span class="badge {{ $inquiry->quotationStatusBadgeClass() }}">
                                {{ $inquiry->quotationStatusLabel() }}
                            </span>
                        </td>
                        <td>
                            <div class="btn-group" role="group">
                                @can('inquiry-quotations.view')
                                <a href="{{ route('admin.inquiries.show', $inquiry) }}" class="btn btn-sm btn-info" title="Detail">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @endcan
                                @can('inquiry-quotations.update')
                                <a href="{{ route('admin.inquiries.edit', $inquiry) }}" class="btn btn-sm btn-warning" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                @endcan
                                @can('inquiry-quotations.delete')
                                <form action="{{ route('admin.inquiries.destroy', $inquiry) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                                @endcan
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" class="text-center py-4 text-muted">
                            Tidak ada data inquiry
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="card-footer d-flex justify-content-between align-items-center">
            <span class="text-muted small">Total: {{ $inquiries->total() }} inquiry</span>
            {{ $inquiries->links() }}
        </div>
    </div>
</div>
@endsection
