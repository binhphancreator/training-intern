<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400"></a></p>

# Design Pattern

## Laravel Request Lifecycle

- Request của Laravel dựa trên mô hình MVC.

- Người dùng thực hiện request trên trình duyệt. Webserver(Nginx,Apache,...) sẽ đẩy request vào public/index.php.File index.php sẽ là đầu vào mọi request.

```
$kernel = $app->make(Kernel::class);

$response = tap($kernel->handle(
    $request = Request::capture()
))->send();

$kernel->terminate($request, $response);
```

- Đầu tiên, Laravel framework kết nối với file bootstrap/app.php để chuẩn bị sẵn sàng cho việc chạy ứng dụng.

- Mở file bootstrap/app.php các bạn sẽ thấy tại đây file này làm 3 nhiệm vụ thiết yếu để sẵn sàng cho việc bootstrap:

```
$app = new Illuminate\Foundation\Application(
    $_ENV['APP_BASE_PATH'] ?? dirname(__DIR__)
);

$app->singleton(
    Illuminate\Contracts\Http\Kernel::class,
    App\Http\Kernel::class
);

$app->singleton(
    Illuminate\Contracts\Console\Kernel::class,
    App\Console\Kernel::class
);

$app->singleton(
    Illuminate\Contracts\Debug\ExceptionHandler::class,
    App\Exceptions\Handler::class
);

return $app;
```

- Đầu tiên là khởi tạo ứng dụng với tham số là đường dẫn gốc của thư mục để tiện cho việc include, require.
- Đăng ký các interface quan trọng (Bind important interfaces) bao gồm Http, Console, Handler.
- Trả về đối tượng ứng dụng.
- Chạy ứng dụng.

```
$kernel = $app->make(Kernel::class);

$response = tap($kernel->handle(
    $request = Request::capture()
))->send();

$kernel->terminate($request, $response);
```

- Đoạn này sẽ nhận object app vừa trả về và thực hiện 2 công đoạn chính: Chụp và xử lý request, Trả về response.

- Tiếp theo Request sẽ đến được Http Kernel hay Console Kernel phụ thuộc vào loại Request.

- HTTP Kernel kế thừa class Illuminate\Foundation\Http\Kernel, class này sẽ thực việc các công việc trước khi request được thực thi như cấu hình xử lý lỗi, cấu hình logger, xác định môi trường ứng dụng và một số công việc khác.

- HTTP Kernel cũng thực hiện một số middleware mặc định của Laravel buộc các request phải vượt qua trước khi được ứng dụng xử lý như kiểm tra ứng dụng có đang ở chế độ bảo trì không, xác thực CSRF (sẽ tìm hiểu ở các tập sau), thao tác với HTTP session...

- Một trong những công việc quan trọng nhất của HTTP Kernel đó chính là load các service provider. Tất cả các service provider được cấu hình trong file config/app.php. Quá trình load các service provider sẽ trải qua hai quá trình:

  - Đăng ký service provider (Register service provider)
  - Khởi động service provider (Bootstrap service provider)

- Các service provider khởi động nhiều thành phần khác nhau của framework như database, validation, router... Chính vì thế mà nó đóng vai trò thiết yếu trong quá trình chạy ứng dụng Laravel.

- Sau khi hoàn tất load service provider, các request sẽ được gửi đến router.Router sẽ kiểm tra các route xem có khớp với request được gửi dến hay không. Nếu có Router sẽ đưa request vượt qua các middleware trước khi gọi đến controller xử lý và trả về response.

## Dependency Injection

- Dependency Injection là cách tổ chức source code, sao cho có thể inject (tiêm) các đối tượng dependency vào trong đối tượng mà nó dependent. 

- Có thể hiểu đơn giản là nếu class A có phương thức khởi tạo hoặc setter có tham số là object kiểu B thì A phụ thuộc vào B.

```
class Monitor {}
class Keyboard {}
class Computer
{
    protected $monitor;
    protected $keyboard;
    public function __construct($monitor, $keyboard)
    {
        $this->monitor = $monitor;
        $this->keyboard = $keyboard;
    }
}

$computer = new Computer(new Monitor(), new Keyboard());
```

- Như ở ví dụ trên ta có thể thấy class Computer cần các dependency là instance của Monitor và Keyboard. Thay vì khởi tạo các dependency này bên trong constructor của class Computer, ta sẽ inject chúng vào khi gọi new Computer.

## Service Container

- Service Container trong Laravel là nơi quản lý class dependency và thực hiện inject class indepent.

### Binding & Resolving

- Đăng ký 1 class hay interface với container.

- Singleton Binding: Chỉ được resolve một lần, những lần gọi tiếp theo sẽ không tạo ra instance mới mà chỉ trả về instance đã được resolve từ trước.

```
$this->app->singleton('NameClass', function ($app) {
    return new NameClass($app->make('HttpClient'));
});
```

- Instance Binding: Cũng giống như Singleton Binding, chúng ta có một instance đang tồn tại và chúng ta bind nó vào Service Container. Mỗi lần lấy ra chúng ta sẽ nhận lại được đúng instance đó.

- Interface binding: Nếu bind một Interface với một Implementation của nó thì ta sẽ có thể type-hint được Interface đó.

```
class MailerImplementation implements MailerInterface {}
// Binding Interface với một Implementation của nó
app()->bind('MailerInterface', 'MailerImplementation');
// Trong constructor của một class nào đó ta có thể type-hint theo interface
public function __construct(MailerInterface $mailer)
{
    $this->mailer = $mailer;
}
```

- Contextual Binding giúp bạn giải quyết được vấn đề sử dụng nhiều Implementation trong service của mình. Chẳng hạn như bạn có đến 2, 3 class là implementation của một Interface. Tuy nhiên trong một trường hợp bạn cần inject implementation này và trong trường hợp khác bạn lại cần implementation khác, khi đó bạn sẽ cần đến Contextual Binding.

- Resolving: Lấy ra các instance từ container.

- Service Container là thành phần trung tâm của Laravel, và nó đang được sử dụng ở mọi ngóc ngách trong project.

- Hầu hết các phần quan trọng khác của Laravel như Controller ,Middleware, Listener, Queue ... đều được resolve từ Service Container.

```
$obj = resolve('NameClass');
//hoặc $obj = app('NameClass');
```

### Service Provider

- Các Service Provider đều được extend từ một abstract class mà Laravel cung cấp, đó là Illuminate\Support\ServiceProvider. Nếu bạn vào tìm hiểu code của class này thì sẽ thấy nó bao gồm một abstract function là register, điều đó có nghĩa là Service Provider của bạn viết sẽ bắt buộc phải khai báo method register này. Và đây chính là nơi bạn thực hiện việc binding vào Service Container.

- register: Nơi thực hiện việc binding.

- boot: Nơi cho phép truy cập đến các services đã được đăng ký trong register.

```
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {

    }
}
```

- Service Provider chính là chìa khoá cho quá trình bootstrapping một Laravel Application. Hãy tưởng tượng application của bạn như một cái Container, và khi khởi chạy, nó sẽ tiến hành đưa các service cần thiết vào trong container đó, rồi những gì bạn cần làm là lấy ra những service cần thiết vào thời điểm cần thiết từ container để xử lý một request gửi đến.

## Facade

- Facade có thể dịch đơn giản sang tiếng Việt là bề ngoài, mặt ngoài. Nó cho phép bạn truy cập đến các hàm bên trong các service được khai báo trong Service Container bằng cách gọi các hàm static.

- Việc gọi các hàm static của Facade thực tế sẽ được xử lý trong magic method __callStatic , rồi chuyển qua lời gọi hàm bình thường từ một instance đã được resolve ra từ trong Service Container.

## Contracts

- Contract(Hợp đồng) là tập các Interface hay Abstract Class.Nhìn vào Contract, ta có thể biết được Framework (hay trong nhiều trường hợp cụ thể hơn các Service của Framework) có thể "làm được những gì".

## Repository

- Repository Pattern là lớp trung gian giữa tầng Business Logic và Data Access, giúp cho việc truy cập dữ liệu chặt chẽ và bảo mật hơn.

- Repository đóng vai trò là một lớp kết nối giữa tầng Business và Model của ứng dụng.

- Hiểu đơn giản thì khi t muốn truy xuất dữ liệu từ database, thay vì viết code xử lý trong controller thì ta tạo ra 1 thư mục là Repository rồi viết code xử lý vào đây. Sau đó chúng ta chỉ việc inject vào thông qua __construct.