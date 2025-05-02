// ฟังก์ชันตรวจสอบกิจกรรมที่ยังไม่เสร็จสิ้น
function checkForActiveActivity() {
    fetch('/run/check-active', {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.has_active && data.activity_id) {
            // แสดงการแจ้งเตือนว่ามีกิจกรรมที่ค้างอยู่
            Swal.fire({
                icon: 'warning',
                title: 'พบกิจกรรมที่ยังไม่เสร็จสิ้น',
                text: 'คุณมีกิจกรรมการวิ่งที่ยังไม่เสร็จสิ้น ต้องการดำเนินการอย่างไร?',
                showCancelButton: true,
                showDenyButton: true,
                confirmButtonText: 'จบกิจกรรม',
                denyButtonText: 'ดำเนินการต่อ',
                cancelButtonText: 'ไม่ทำอะไร',
                confirmButtonColor: '#3085d6', // Primary color from design system
                denyButtonColor: '#28a745', // Success/green color
                cancelButtonColor: '#6c757d' // Secondary/gray color
            }).then((result) => {
                if (result.isConfirmed) {
                    // จบกิจกรรมที่ค้างอยู่
                    finishPendingActivity(data.activity_id);
                } else if (result.isDenied) {
                    // ดำเนินการต่อกับกิจกรรมที่ค้างอยู่
                    resumePendingActivity(data.activity_id);
                }
            });
        }
    })
    .catch(error => {
        console.error('Error checking for active activities:', error);
    });
}

// ฟังก์ชันบันทึกกิจกรรมการวิ่ง
function saveActivity() {
    // แสดง loading state
    Swal.fire({
        title: 'กำลังบันทึก',
        text: 'กำลังบันทึกข้อมูลการวิ่ง...',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        },
        confirmButtonColor: '#3085d6' // Primary color from design system
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

    // ส่งข้อมูลไปบันทึกที่ API endpoint
    fetch('/run/store', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
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
        // ปิด loading
        Swal.close();

        if (data.status === 'success' || data.success === true) {
            // เมื่อบันทึกเสร็จแล้ว แสดงข้อความสำเร็จ
            Swal.fire({
                icon: 'success',
                title: 'บันทึกสำเร็จ!',
                text: 'บันทึกข้อมูลการวิ่งเรียบร้อยแล้ว',
                timer: 2000,
                showConfirmButton: false,
                confirmButtonColor: '#3085d6', // Primary color from design system
                iconColor: '#28a745' // Success green color for icon
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
                confirmButtonColor: '#3085d6' // Primary color from design system
            });
        }
    })
    .catch(error => {
        // ปิด loading
        Swal.close();

        console.error('Error saving run:', error);

        // แสดงข้อผิดพลาด
        Swal.fire({
            icon: 'error',
            title: 'เกิดข้อผิดพลาด',
            text: 'เกิดข้อผิดพลาดในการบันทึกข้อมูล: ' + error.message,
            confirmButtonColor: '#3085d6' // Primary color from design system
        });
    });
}
