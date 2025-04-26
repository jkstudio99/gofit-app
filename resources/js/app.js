import './bootstrap';
import Swal from 'sweetalert2';

window.Swal = Swal;

// Functions สำหรับการใช้งาน SweetAlert2
window.showSuccessAlert = function(message) {
    Swal.fire({
        title: 'สำเร็จ!',
        text: message,
        icon: 'success',
        confirmButtonText: 'ตกลง',
        confirmButtonColor: '#2DC679',
    });
};

window.showErrorAlert = function(message) {
    Swal.fire({
        title: 'ผิดพลาด!',
        text: message,
        icon: 'error',
        confirmButtonText: 'ตกลง',
        confirmButtonColor: '#FF4646',
    });
};

window.showWarningAlert = function(message) {
    Swal.fire({
        title: 'คำเตือน!',
        text: message,
        icon: 'warning',
        confirmButtonText: 'ตกลง',
        confirmButtonColor: '#FFB800',
    });
};

window.confirmAction = function(title, text, callback) {
    Swal.fire({
        title: title,
        text: text,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#2DC679',
        cancelButtonColor: '#FF4646',
        confirmButtonText: 'ยืนยัน',
        cancelButtonText: 'ยกเลิก'
    }).then((result) => {
        if (result.isConfirmed) {
            callback();
        }
    });
};
