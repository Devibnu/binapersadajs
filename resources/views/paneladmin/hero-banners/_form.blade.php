@csrf

<div class="row">
	  <div class="col-md-6">
	    <div class="form-group">
	      <label>Judul</label>
	      <input type="text" name="title" class="form-control" value="{{ old('title', $heroBanner->display_title) }}" required>
	    </div>
	  </div>
	  <div class="col-md-6">
	    <div class="form-group">
	      <label>Label Kecil</label>
	      <input type="text" name="small_text" class="form-control" value="{{ old('small_text', $heroBanner->display_small_text) }}">
	    </div>
	  </div>
	  <div class="col-md-12">
	    <div class="form-group">
	      <label>Deskripsi</label>
	      <textarea name="description" class="form-control" rows="3">{{ old('description', $heroBanner->display_description) }}</textarea>
	    </div>
	  </div>
	  <div class="col-md-4">
	    <div class="form-group">
	      <label>Teks Tombol</label>
	      <input type="text" name="button_text" class="form-control" value="{{ old('button_text', $heroBanner->display_button_text) }}">
	    </div>
	  </div>
	  <div class="col-md-5">
	    <div class="form-group">
	      <label>Link Tombol</label>
	      <input type="text" name="button_link" class="form-control" value="{{ old('button_link', $heroBanner->display_button_link) }}" placeholder="/contact">
	    </div>
	  </div>
		  <div class="col-md-3">
		    <div class="form-group">
		    <label>Urutan</label>
		      <input type="number" name="sort_order" class="form-control" min="0" value="{{ old('sort_order', $heroBanner->display_sort_order) }}" required>
		    </div>
		  </div>
		  <div class="col-md-4">
		    <div class="form-group">
		      <label>Posisi Teks</label>
		      <select name="content_position" class="form-control">
		        <option value="" {{ old('content_position', $heroBanner->content_position) === null ? 'selected' : '' }}>Otomatis</option>
		        <option value="center" {{ old('content_position', $heroBanner->content_position) === 'center' ? 'selected' : '' }}>Tengah</option>
		        <option value="left" {{ old('content_position', $heroBanner->content_position) === 'left' ? 'selected' : '' }}>Kiri</option>
		        <option value="right" {{ old('content_position', $heroBanner->content_position) === 'right' ? 'selected' : '' }}>Kanan</option>
		      </select>
		    </div>
		  </div>
		  <div class="col-md-8">
		    <div class="form-group">
	      <label>Gambar Background</label>
	      <input type="file" name="image" class="form-control" accept="image/*">
	      <small class="text-secondary">Gambar akan otomatis dioptimasi dan dikompres.</small>
	      @if($heroBanner->image || $heroBanner->gambar_background)
	        <img src="{{ $heroBanner->backgroundUrl() }}" alt="{{ $heroBanner->display_title }}" class="img-fluid border-radius-lg mt-3" style="max-height: 180px;">
	      @endif
	    </div>
	  </div>
		  <div class="col-md-4">
	    <div class="form-group">
	      <label>Status</label>
	      <select name="is_active" class="form-control" required>
	        <option value="1" {{ old('is_active', $heroBanner->display_is_active) ? 'selected' : '' }}>Aktif</option>
	        <option value="0" {{ ! old('is_active', $heroBanner->display_is_active) ? 'selected' : '' }}>Nonaktif</option>
	      </select>
	    </div>
	  </div>
</div>

<div class="d-flex justify-content-between">
  <a href="{{ route('paneladmin.hero-banners.index') }}" class="btn bg-gradient-secondary mb-0">Kembali</a>
  <button type="submit" class="btn bg-gradient-primary mb-0">Simpan</button>
</div>
