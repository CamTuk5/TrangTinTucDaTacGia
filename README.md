1. Thông tin chung
- Tên dự án: Trang tin tức đa tác giả.
- Thành viên thực hiện:
+ Nguyễn Hồ Cẩm Tú
+ Nguyễn Đỗ Bảo Ngọc
+ Nguyễn Thục Linh Nhi
+ Võ Thị Ngân
+ Đặng Thị Diễm My
- Mô tả: Hệ thống cho phép nhiều tác giả đăng tải bài viết, tích hợp cơ chế kiểm duyệt nội dung, tính toán độ nóng của tin tức và mô hình kinh doanh bài viết Premium qua cổng thanh toán quốc tế.
3. Công nghệ sử dụng
- Backend: Laravel Framework 12.x.
- Hệ quản trị CSDL: MySQL.
- Frontend: Bootstrap 5, Blade Template, Chart.js.
- Xác thực & Phân quyền: Laravel Breeze, Laravel Policies.
- Thanh toán: PayPal Payment Gateway (Sandbox).
- Công cụ hỗ trợ: Summernote (Editor), Carbon (Time handling), Vite.
4. Hướng dẫn cài đặt và chạy dự án
Bước 1: Tải mã nguồn vào terminal
- git clone https://github.com/CamTuk5/TintucUED.git
- cd TintucUED
Bước 2: Cài đặt các thư viện cần thiết
- composer install
- npm install
Bước 3: Cấu hình môi trường
- Chạy lệnh copy .env.example .env
- Chạy lệnh php artisan key:generate
- Tạo cơ sở dữ liệu MySQL có tên: tintucued.
Bước 4: Khởi tạo dữ liệu
- php artisan migrate
Bước 5: Khởi chạy dự án
- php artisan serve
