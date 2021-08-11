<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400"></a></p>

## Polymorphic Relationships

- Mối quan hệ đa hình cho phép model con thuộc về nhiều model khác nhau.Ví dụ một trang web gồm có các post và video.Trong ứng dụng như vậy một comment có thể thuộc về cả post và video.
- Ví dụ: [Model Post,Video,Tags](https://github.com/binhphancreator/demo-docker/tree/master/app/Models).

## Queue

- Một số tác vụ mất nhiều thời gian như tải tệp lên, gửi email,... v.v có thể ảnh hưởng tới trải nghiệm người dùng. Hàng đợi trong Laravel tạo ra các công việc (Job) và xử lý chúng ở chế độ nền bằng cách chuyển các tác vụ đó vào hàng đợi.

- Tạo Job : php artisan make:job ProcessPodcast

- Cách cấu hình Dockerfile, docker-compose.yml để chạy trình giám sát supervisor để chạy hàng đợi laravel đã demo ở đây.

## Scope in model

- Scope trong Laravel giúp bạn có thể thêm ràng buộc tới tất cả các truy vấn của model đã cho.

- Cách viết Scope: Định nghĩa class và implements Illuminate\Database\Eloquent\Scope. Sau đó viết điều kiện ràng buộc trong phương thức apply.

- Phía trong Model thêm vào static::addGlobalScope(new AncientScope) trong phương thức booted.

- Ví dụ về **[model Tag](https://github.com/binhphancreator/demo-docker/blob/master/app/Scopes/TagScope.php)** với điều kiện ràng buộc là id > 3 

## Validation in Request Class

- Đối với các trường hợp phức tạp, chúng ta có thể tạo riêng class để thực hiện nhiệm vụ xác thực input của người dùng.

- Cách tạo : php artisan make:request AddTagRequest.

- Lớp vừa tạo sẽ được đặt trong thư mục app/Http/Requests.

- Có 2 phương thức rules chịu trách nhiệm xác thực người dùng và rules trả về các quy tắc áp dụng cho dữ liệu được yêu cầu.

- Ví dụ về **[Request Class](https://github.com/binhphancreator/demo-docker/blob/master/app/Http/Requests/AddTagRequest.php)**.

## Trait

- Có thể hiểu như một class, là nơi tập hợp nhóm các phương thức mà chúng ta muốn sử dụng cho các class khác.

- Trait có thể sử dụng cho nhiều class thông qua câu lệnh use, tránh việc trùng lặp code.

- Laravel sử dụng trait tương đối nhiều. Model sử dụng trait Has Factory.Controller dùng các trait ValidatesRequests, DispatchesJobs, AuthorizesRequests,... v.v

## Transformer

- Thông thường khi xây dựng api, chúng ta chỉ lấy nội dung từ cơ sở dữ liệu và trả về qua json_encode() nhưng nếu chúng được sử dụng public hoặc cho các ứng dụng mobile sẽ khiến đầu ra không nhất quán.

- Cần một lớp chuyển đổi dữ liệu cho đầu ra.

- Ta có thể dử dụng API Resource trong Laravel hoặc thư viện Fractal.

- Fractal cung cấp một lớp trình bày, nó cho phép chúng ta tạo ra một lớp chuyển đổi mới cho các models trước khi trả về chúng như là một response. Cách làm này rất linh hoạt và dễ dàng tích hợp vào bất kỳ ứng dụng hoặc framework nào. Nhất là đối với các API sử dụng "public" hay các ứng dụng dành cho mobile.

- Cài đặt : composer require league/fractal

- Các hướng dẫn có trên trang [Fractal](https://fractal.thephpleague.com/transformers/)

