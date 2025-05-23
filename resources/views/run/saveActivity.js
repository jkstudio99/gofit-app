// ตัวแปรป้องกันการส่งข้อมูลซ้ำ
let isSaving = false;

// ฟังก์ชันบันทึกกิจกรรมการวิ่ง
function saveActivity() {
    // ป้องกันการบันทึกซ้ำซ้อน
    if (isSaving) {
        console.log('กำลังบันทึกข้อมูล โปรดรอสักครู่...');
        return;
    }

    // ตั้งค่าสถานะว่ากำลังบันทึก
    isSaving = true;

    // แสดง loading state
    Swal.fire({
        title: 'กำลังบันทึก',
        text: 'กำลังบันทึกข้อมูลการวิ่ง...',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    // เตรียมข้อมูลเส้นทาง
    let routeDataFormatted = [];
    try {
        // เตรียมข้อมูลเส้นทางในรูปแบบที่ถูกต้อง
        routeDataFormatted = routePoints.map(point => ({
            lat: point[0],
            lng: point[1],
            timestamp: Date.now()
        }));
    } catch (error) {
        console.error('Error formatting route data:', error);
        routeDataFormatted = [];
    }

    // แปลงเป็น JSON string
    const routeDataJSON = JSON.stringify(routeDataFormatted);

    // บันทึก endpoint ที่ใช้สำหรับการ debug
    console.log('Using endpoint:', runStoreUrl);

    // ส่งข้อมูลไปบันทึกที่ API endpoint
    fetch(runStoreUrl, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        },
        body: JSON.stringify({
            distance: currentDistance,
            duration: elapsedSeconds,
            calories_burned: totalCalories,
            average_speed: currentSpeed,
            route_data: routeDataJSON,
            is_test: useSimulation
        })
    })
    .then(response => {
        // อ่านข้อมูล response ไม่ว่าจะสำเร็จหรือไม่
        return response.text().then(text => {
            console.log('Response status:', response.status);
            console.log('Response text:', text.substring(0, 200) + (text.length > 200 ? '...' : ''));

            try {
                // พยายามแปลงเป็น JSON
                return JSON.parse(text);
            } catch (e) {
                console.error('Error parsing JSON response:', e);
                // กรณีที่ response ไม่ใช่ JSON ที่ถูกต้อง
                return {
                    status: 'error',
                    message: 'ไม่สามารถอ่านข้อมูลจากเซิร์ฟเวอร์ได้: ' + text.substring(0, 100)
                };
            }
        });
    })
    .then(data => {
        // รีเซ็ตสถานะการบันทึก
        isSaving = false;

        // ปิด loading
        Swal.close();

        if (data.status === 'success' || data.success === true) {
            // เมื่อบันทึกเสร็จแล้ว แสดงข้อความสำเร็จ
            Swal.fire({
                icon: 'success',
                title: 'บันทึกสำเร็จ!',
                text: 'บันทึกข้อมูลการวิ่งเรียบร้อยแล้ว',
                timer: 2000,
                showConfirmButton: false
            }).then(() => {
                // ปิด modal
                const modal = bootstrap.Modal.getInstance(document.getElementById('activitySummaryModal'));
                if (modal) {
                    modal.hide();
                }

                // รีเฟรชหน้าเพื่ออัปเดตประวัติการวิ่ง
                window.location.reload();
            });
        } else {
            // แสดงข้อผิดพลาด
            Swal.fire({
                icon: 'error',
                title: 'เกิดข้อผิดพลาด',
                text: data.message || 'ไม่สามารถบันทึกข้อมูลการวิ่งได้',
                confirmButtonColor: '#3085d6'
            });
        }
    })
    .catch(error => {
        // รีเซ็ตสถานะการบันทึก
        isSaving = false;

        // ปิด loading
        Swal.close();

        console.error('Error saving run:', error);

        // แสดงข้อผิดพลาด
        Swal.fire({
            icon: 'error',
            title: 'เกิดข้อผิดพลาด',
            text: 'เกิดข้อผิดพลาดในการบันทึกข้อมูล: ' + error.message,
            confirmButtonColor: '#3085d6'
        });
    });
}
