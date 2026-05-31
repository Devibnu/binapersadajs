@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <!-- Header dengan Action Buttons -->
    <div class="row mb-4">
        <div class="col-md-8">
            <h2 class="h4 font-weight-bolder">Detail Inquiry & Quotation</h2>
        </div>
        <div class="col-md-4 text-end">
            @can('inquiry-quotations.update')
            <a href="{{ route('admin.inquiries.edit', $inquiry) }}" class="btn btn-sm btn-warning">
                <i class="fas fa-edit"></i> Edit
            </a>
            @endcan
            @can('inquiry-quotations.delete')
            <form action="{{ route('admin.inquiries.destroy', $inquiry) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus inquiry ini?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-sm btn-danger">
                    <i class="fas fa-trash"></i> Hapus
                </button>
            </form>
            @endcan
            <a href="{{ route('admin.inquiries.index') }}" class="btn btn-sm btn-secondary">Kembali</a>
        </div>
    </div>

    <div class="row">
        <!-- Main Content -->
        <div class="col-md-8">
            <!-- Inquiry Information Card -->
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <h6 class="mb-0 font-weight-bold">Informasi Inquiry</h6>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <span class="text-muted small">Nomor Inquiry</span>
                            <h6 class="font-weight-bold">{{ $inquiry->inquiry_number }}</h6>
                        </div>
                        <div class="col-md-4">
                            <span class="text-muted small">Tanggal</span>
                            <h6 class="font-weight-bold">{{ $inquiry->inquiry_date->format('d/m/Y') }}</h6>
                        </div>
                        <div class="col-md-4">
                            <span class="text-muted small">Inquiry By</span>
                            <h6 class="font-weight-bold">{{ $inquiry->inquiryByLabel() }}</h6>
                        </div>
                    </div>

                    <hr>

                    <div class="row mb-3">
                        <div class="col-md-12">
                            <span class="text-muted small">Subject</span>
                            <p class="mb-0">{{ $inquiry->subject }}</p>
                        </div>
                    </div>

                    @if($inquiry->description)
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <span class="text-muted small">Deskripsi</span>
                            <p class="mb-0">{{ $inquiry->description }}</p>
                        </div>
                    </div>
                    @endif

                    @if($inquiry->pic_internal)
                    <div class="row">
                        <div class="col-md-12">
                            <span class="text-muted small">PIC Internal</span>
                            <p class="mb-0">{{ $inquiry->pic_internal }}</p>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Client Information Card -->
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <h6 class="mb-0 font-weight-bold">Informasi Klien</h6>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <span class="text-muted small">Nama Klien</span>
                            <h6 class="font-weight-bold">{{ $inquiry->client_name }}</h6>
                        </div>
                        <div class="col-md-6">
                            <span class="text-muted small">PIC Klien</span>
                            <p class="mb-0">{{ $inquiry->client_pic ?? '-' }}</p>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <span class="text-muted small">Telepon</span>
                            <p class="mb-0">
                                @if($inquiry->client_phone)
                                    <a href="{{ $inquiry->whatsappUrl() }}" target="_blank" class="btn btn-sm btn-success">
                                        <i class="fab fa-whatsapp"></i> {{ $inquiry->client_phone }}
                                    </a>
                                @else
                                    -
                                @endif
                            </p>
                        </div>
                        <div class="col-md-6">
                            <span class="text-muted small">Email</span>
                            <p class="mb-0">
                                @if($inquiry->client_email)
                                    <a href="mailto:{{ $inquiry->client_email }}">{{ $inquiry->client_email }}</a>
                                @else
                                    -
                                @endif
                            </p>
                        </div>
                    </div>

                    @if($inquiry->client_address)
                    <div class="row">
                        <div class="col-md-12">
                            <span class="text-muted small">Alamat</span>
                            <p class="mb-0">{{ $inquiry->client_address }}</p>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Site Survey Card -->
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <h6 class="mb-0 font-weight-bold">Site Survey</h6>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <span class="text-muted small">Status</span>
                            <p class="mb-0">
                                <span class="badge {{ $inquiry->surveyStatusBadgeClass() }}">
                                    {{ $inquiry->surveyStatusLabel() }}
                                </span>
                            </p>
                        </div>
                        <div class="col-md-6">
                            <span class="text-muted small">Tanggal Survey</span>
                            <p class="mb-0">{{ $inquiry->site_survey_date?->format('d/m/Y') ?? '-' }}</p>
                        </div>
                    </div>

                    @if($inquiry->site_survey_notes)
                    <div class="row">
                        <div class="col-md-12">
                            <span class="text-muted small">Catatan</span>
                            <p class="mb-0">{{ $inquiry->site_survey_notes }}</p>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Quotation Card -->
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <h6 class="mb-0 font-weight-bold">Quotation</h6>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <span class="text-muted small">Nomor Quotation</span>
                            <p class="font-weight-bold">{{ $inquiry->quotation_number ?? 'Belum ada' }}</p>
                        </div>
                        <div class="col-md-6">
                            <span class="text-muted small">Status</span>
                            <p class="mb-0">
                                <span class="badge {{ $inquiry->quotationStatusBadgeClass() }}">
                                    {{ $inquiry->quotationStatusLabel() }}
                                </span>
                            </p>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <span class="text-muted small">Tanggal Quotation</span>
                            <p class="mb-0">{{ $inquiry->quotation_date?->format('d/m/Y') ?? '-' }}</p>
                        </div>
                        <div class="col-md-4">
                            <span class="text-muted small">Deadline</span>
                            <p class="mb-0">{{ $inquiry->deadline?->format('d/m/Y') ?? '-' }}</p>
                        </div>
                        <div class="col-md-4">
                            <span class="text-muted small">Amount</span>
                            <p class="font-weight-bold">{{ $inquiry->formattedAmount() }}</p>
                        </div>
                    </div>

                    @if($inquiry->notes)
                    <div class="row">
                        <div class="col-md-12">
                            <span class="text-muted small">Catatan</span>
                            <p class="mb-0">{{ $inquiry->notes }}</p>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Attachments Card -->
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <h6 class="mb-0 font-weight-bold">Lampiran</h6>
                </div>
                <div class="card-body">
                    @if($inquiry->attachments->count() > 0)
                        <div class="row">
                            @foreach($inquiry->attachments as $attachment)
                            <div class="col-md-6 mb-3">
                                <div class="border rounded p-3">
                                    <div class="mb-2">
                                        @if($attachment->isImage())
                                            <img src="{{ $attachment->fileUrl() }}" alt="{{ $attachment->original_name }}" class="img-fluid rounded" style="max-height: 150px;">
                                        @elseif($attachment->isPdf())
                                            <div class="bg-light text-center p-4 rounded">
                                                <i class="fas fa-file-pdf fa-3x text-danger"></i>
                                            </div>
                                        @else
                                            <div class="bg-light text-center p-4 rounded">
                                                <i class="fas fa-file fa-3x text-secondary"></i>
                                            </div>
                                        @endif
                                    </div>

                                    <small class="text-muted d-block">{{ $attachment->attachmentTypeLabel() }}</small>
                                    <small class="text-muted d-block">{{ $attachment->original_name }}</small>
                                    <small class="text-muted d-block">{{ $attachment->formattedSize() }}</small>

                                    <div class="mt-2">
                                        @if($attachment->isPdf())
                                            <a href="{{ route('admin.inquiry-attachments.download', $attachment) }}" class="btn btn-sm btn-primary">
                                                <i class="fas fa-download"></i> Download
                                            </a>
                                        @else
                                            <a href="{{ $attachment->fileUrl() }}" class="btn btn-sm btn-primary" target="_blank">
                                                <i class="fas fa-external-link-alt"></i> View
                                            </a>
                                        @endif
                                        @can('inquiry-quotations.update')
                                        <form action="{{ route('admin.inquiry-attachments.delete', $attachment) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger">
                                                <i class="fas fa-trash"></i> Hapus
                                            </button>
                                        </form>
                                        @endcan
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted mb-0">Tidak ada attachment</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-md-4">
            <!-- Metadata Card -->
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <h6 class="mb-0 font-weight-bold">Metadata</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <span class="text-muted small">ID</span>
                        <p class="mb-0">{{ $inquiry->id }}</p>
                    </div>

                    <div class="mb-3">
                        <span class="text-muted small">Dibuat Oleh</span>
                        <p class="mb-0">{{ $inquiry->created_by ?? 'System' }}</p>
                    </div>

                    <div class="mb-3">
                        <span class="text-muted small">Dibuat Pada</span>
                        <p class="mb-0">{{ $inquiry->created_at->format('d/m/Y H:i') }}</p>
                    </div>

                    <div>
                        <span class="text-muted small">Diupdate Pada</span>
                        <p class="mb-0">{{ $inquiry->updated_at->format('d/m/Y H:i') }}</p>
                    </div>
                </div>
            </div>

            <!-- Activity Timeline Card (Simple) -->
            <div class="card">
                <div class="card-header bg-light">
                    <h6 class="mb-0 font-weight-bold">Timeline</h6>
                </div>
                <div class="card-body">
                    <div class="timeline-item mb-3">
                        <div class="timeline-marker bg-success"></div>
                        <div>
                            <small class="text-muted">Dibuat</small>
                            <p class="mb-0">{{ $inquiry->created_at->format('d/m/Y H:i') }}</p>
                        </div>
                    </div>

                    @if($inquiry->updated_at->ne($inquiry->created_at))
                    <div class="timeline-item">
                        <div class="timeline-marker bg-info"></div>
                        <div>
                            <small class="text-muted">Terakhir Diupdate</small>
                            <p class="mb-0">{{ $inquiry->updated_at->format('d/m/Y H:i') }}</p>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .timeline-marker {
        width: 12px;
        height: 12px;
        border-radius: 50%;
        display: inline-block;
        margin-right: 10px;
    }

    .timeline-item {
        padding-left: 25px;
        position: relative;
    }

    .timeline-item::before {
        content: '';
        position: absolute;
        left: 5px;
        top: 15px;
        width: 2px;
        height: 25px;
        background: #e0e0e0;
    }

    .timeline-item:last-child::before {
        display: none;
    }
</style>
@endsection
