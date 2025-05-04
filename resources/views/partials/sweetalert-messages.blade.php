@if(session('success') && strpos(session('success'), 'ลบเหรียญตรา') !== false)
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                title: 'สำเร็จ!',
                text: "{{ session('success') }}",
                icon: 'success',
                confirmButtonColor: '#2DC679',
                confirmButtonText: 'ตกลง'
            });
        });
    </script>
@elseif(session('success'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                title: 'สำเร็จ!',
                text: "{{ session('success') }}",
                icon: 'success',
                confirmButtonColor: '#2DC679',
                confirmButtonText: 'ตกลง'
            });
        });
    </script>
@endif

@if(session('error'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                title: 'ผิดพลาด!',
                text: "{{ session('error') }}",
                icon: 'error',
                confirmButtonColor: '#2DC679',
                confirmButtonText: 'ตกลง'
            });
        });
    </script>
@endif

@if(session('warning'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                title: 'คำเตือน!',
                text: "{{ session('warning') }}",
                icon: 'warning',
                confirmButtonColor: '#2DC679',
                confirmButtonText: 'ตกลง'
            });
        });
    </script>
@endif

@if(session('info'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                title: 'ข้อมูล',
                text: "{{ session('info') }}",
                icon: 'info',
                confirmButtonColor: '#2DC679',
                confirmButtonText: 'ตกลง'
            });
        });
    </script>
@endif

@if(session('badge_unlocked'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                title: 'ยินดีด้วย!',
                html: `
                    <div class="text-center mb-4">
                        <img src="{{ asset('storage/' . session('badge_unlocked.image')) }}" alt="Badge" style="max-height: 150px;">
                        <h4 class="mt-3">ปลดล็อคเหรียญตรา: {{ session('badge_unlocked.badge_name') }}</h4>
                        <div class="badge bg-success px-3 py-2 mt-2">
                            <i class="fas fa-coins me-1"></i> +{{ session('badge_unlocked.points') }} คะแนน
                        </div>
                    </div>
                `,
                icon: 'success',
                confirmButtonColor: '#2DC679',
                confirmButtonText: 'ตกลง'
            });
        });
    </script>
@endif
