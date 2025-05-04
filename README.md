# <img src="public/images/gofit-logo-text-white.svg" alt="GoFit" height="40" style="background-color: #2DC679; padding: 8px; border-radius: 8px;"> 

# GoFit - แอปพลิเคชันจัดการกิจกรรมการวิ่งและสุขภาพ

![Version](https://img.shields.io/badge/version-1.0.0-blue)
![Laravel](https://img.shields.io/badge/Laravel-10.x-red)
![PHP](https://img.shields.io/badge/PHP-8.1-purple)
![License](https://img.shields.io/badge/license-MIT-green)

GoFit เป็นแพลตฟอร์มการจัดการกิจกรรมการวิ่งและการดูแลสุขภาพที่ครบวงจร ช่วยให้ผู้ใช้สามารถติดตามกิจกรรมการออกกำลังกายประจำวัน สมัครเข้าร่วมกิจกรรมวิ่ง และแชร์ความสำเร็จกับเพื่อนๆ 

## ✨ คุณสมบัติหลัก

- **ติดตามกิจกรรมการวิ่ง**: บันทึกเส้นทาง ระยะทาง เวลา และแคลอรี่ที่เผาผลาญระหว่างวิ่ง
- **รายงานสุขภาพ**: สรุปข้อมูลสุขภาพและการออกกำลังกายแบบรายวัน รายสัปดาห์ และรายเดือน
- **กิจกรรมวิ่ง**: ค้นหาและสมัครเข้าร่วมกิจกรรมวิ่งที่จัดขึ้นในพื้นที่ใกล้เคียง
- **บทความสุขภาพ**: เข้าถึงบทความและข้อมูลเกี่ยวกับการออกกำลังกายและสุขภาพ
- **ระบบรางวัล**: รับเหรียญตราและแต้มสะสมเมื่อทำภารกิจสำเร็จ
- **โซเชียลฟีเจอร์**: แชร์ความสำเร็จกับเพื่อนและสร้างความท้าทายร่วมกัน

## 🛠️ การติดตั้ง

### ความต้องการของระบบ
- PHP 8.1+
- Composer
- MySQL หรือ MariaDB
- Node.js และ NPM

### ขั้นตอนการติดตั้ง

1. **โคลนโปรเจค**
   ```bash
   git clone https://github.com/yourusername/gofit-app.git
   cd gofit-app/gofit
   ```

2. **ติดตั้ง Dependencies**
   ```bash
   composer install
   npm install
   ```

3. **ตั้งค่าไฟล์สภาพแวดล้อม**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **แก้ไขการตั้งค่าฐานข้อมูลใน .env**
   ```
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=gofit
   DB_USERNAME=your_username
   DB_PASSWORD=your_password
   ```

5. **รันการ Migration และ Seed ข้อมูล**
   ```bash
   php artisan migrate
   php artisan db:seed
   ```

6. **สร้าง Symbolic Link สำหรับ Storage**
   ```bash
   php artisan storage:link
   ```

7. **คอมไพล์ Asset**
   ```bash
   npm run dev
   # หรือ
   npm run build
   ```

8. **เริ่มต้นเซิร์ฟเวอร์**
   ```bash
   php artisan serve
   ```

## 🚀 การใช้งาน

1. เข้าสู่ระบบด้วยบัญชีผู้ใช้ที่สร้างขึ้นระหว่างติดตั้ง:
   - **ผู้ดูแลระบบ**: admin@gofit.com / password
   - **ผู้ใช้ทั่วไป**: user@gofit.com / password

2. สำรวจคุณสมบัติต่างๆ ของแอปพลิเคชัน:
   - บันทึกกิจกรรมการวิ่ง
   - ดูข้อมูลสุขภาพ
   - ลงทะเบียนเข้าร่วมกิจกรรม
   - อ่านบทความสุขภาพ

## 📊 โครงสร้างโปรเจค

```
gofit/
├── app/                     # โค้ด PHP หลัก
│   ├── Http/
│   │   ├── Controllers/     # Controllers
│   │   └── Middleware/      # Middleware
│   └── Models/              # Models
├── config/                  # ไฟล์การตั้งค่า
├── database/                # Migrations และ Seeders
├── public/                  # Assets สาธารณะ
│   ├── css/
│   ├── js/
│   └── images/
├── resources/               # ทรัพยากรฝั่ง Client
│   ├── js/
│   ├── css/
│   └── views/               # Blade Templates
└── routes/                  # การกำหนดเส้นทาง
```

## 👥 ทีมผู้พัฒนา

- นักพัฒนาหลัก - [ชื่อของคุณ](https://github.com/yourusername)
- ผู้ออกแบบ UI/UX - [ชื่อนักออกแบบ](https://github.com/designerusername)

## 📝 ใบอนุญาต

โครงการนี้เผยแพร่ภายใต้ใบอนุญาต MIT - ดูรายละเอียดเพิ่มเติมได้ที่ [LICENSE](LICENSE)

## 🙏 ขอบคุณ

- [Laravel](https://laravel.com/) - PHP Framework ที่ใช้ในการพัฒนา
- [Bootstrap](https://getbootstrap.com/) - CSS Framework
- [Leaflet](https://leafletjs.com/) - JavaScript Library สำหรับแผนที่แบบโต้ตอบ
