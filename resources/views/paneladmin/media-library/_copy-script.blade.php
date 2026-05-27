@push('scripts')
<script>
  document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.js-copy-media-url').forEach(function (button) {
      button.addEventListener('click', function () {
        navigator.clipboard.writeText(button.dataset.url).then(function () {
          Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'success',
            title: 'URL media berhasil disalin.',
            showConfirmButton: false,
            timer: 2000,
            timerProgressBar: true
          });
        });
      });
    });
  });
</script>
@endpush
