@extends('layouts.user_type.auth')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card mb-4">
      <div class="card-header pb-0 d-flex justify-content-between align-items-center">
        <h6>Detail Project</h6>
        <a href="{{ route('paneladmin.projects.edit', $project) }}" class="btn bg-gradient-primary mb-0">Edit Project</a>
      </div>
      <div class="card-body">
        <div class="row">
          <div class="col-lg-5 mb-4 mb-lg-0">
            <img src="{{ $project->featuredImageUrl() }}" class="img-fluid border-radius-lg" alt="{{ $project->title }}">
          </div>
          <div class="col-lg-7">
            <h4>{{ $project->title }}</h4>
            <p class="text-sm text-secondary">{{ $project->short_description ?: '-' }}</p>
            <p class="text-sm mb-1"><strong>Client:</strong> {{ $project->client_name ?: '-' }}</p>
            <p class="text-sm mb-1"><strong>Lokasi:</strong> {{ $project->project_location ?: '-' }}</p>
            <p class="text-sm mb-1"><strong>Tahun:</strong> {{ $project->project_year ?: '-' }}</p>
            <p class="text-sm mb-3"><strong>Kategori:</strong> {{ $project->categoryName() }}</p>
            <span class="badge badge-sm {{ $project->isActive() ? 'bg-gradient-success' : 'bg-gradient-secondary' }}">
              {{ $project->isActive() ? 'Aktif' : 'Nonaktif' }}
            </span>
          </div>
          @if($project->description)
            <div class="col-12 mt-4">
              <h6>Deskripsi Lengkap</h6>
              <p class="text-sm">{!! nl2br(e($project->description)) !!}</p>
            </div>
          @endif
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
