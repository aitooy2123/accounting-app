<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

@if (session('success'))
  <script>
    Swal.fire({
      icon: 'success',
      title: '{{ session('success') }}',
      showConfirmButton: false,
      timer: 1500
    });
  </script>
@elseif(session('error'))
  <script>
    Swal.fire({
      icon: 'error',
      title: '{{ session('error') }}',
      showConfirmButton: false,
      timer: 1500
    });
  </script>
@endif
