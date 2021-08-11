<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400"></a></p>

# Các khái niệm liên quan

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

## Contracts

- Contract(Hợp đồng) là tập các Interface hay Abstract Class.Nhìn vào Contract, ta có thể biết được Framework (hay trong nhiều trường hợp cụ thể hơn các Service của Framework) có thể "làm được những gì".

# Design Pattern
## Repository Pattern

- Repository Pattern là lớp trung gian giữa tầng Business Logic và Data Access, giúp cho việc truy cập dữ liệu chặt chẽ và bảo mật hơn.

- Repository đóng vai trò là một lớp kết nối giữa tầng Business và Model của ứng dụng.

- Hiểu đơn giản thì khi t muốn truy xuất dữ liệu từ database, thay vì viết code xử lý trong controller thì ta tạo ra 1 thư mục là Repository rồi viết code xử lý vào đây. Sau đó chúng ta chỉ việc inject vào thông qua __construct.

## Facade pattern

- Facade có thể dịch đơn giản sang tiếng Việt là bề ngoài, mặt ngoài. Nó cho phép bạn truy cập đến các hàm bên trong các service được khai báo trong Service Container bằng cách gọi các hàm static.

- Việc gọi các hàm static của Facade thực tế sẽ được xử lý trong magic method __callStatic , rồi chuyển qua lời gọi hàm bình thường từ một instance đã được resolve ra từ trong Service Container.

- Facade giúp ẩn quá trình triển khai phương thức của một lớp, nói dễ hiểu là che đi phần mã của 1 lớp và chỉ thể hiện phần giao diện của lớp đó.

## Factory Pattern

- Riêng tên Pattern đã nói lên tất cả, Factory ở đây có thể hiểu nôm na là nhà máy. Theo định nghĩa từ Wikipedia, Factory chính là một đối tượng dùng để khởi tạo các đối tượng khác, thường thì factory sẽ là một hàm hay một phương thức trả về các đối tượng của một vài class khác nhau.

- Ví dụ về hàm view trong helpes.php Laravel sử dụng ViewFactory để trả về response.

```
function view($view = null, $data = [], $mergeData = [])
    {
        $factory = app(ViewFactory::class);

        if (func_num_args() === 0) {
            return $factory;
        }

        return $factory->make($view, $data, $mergeData);
    }
```

## Builder (Manager) Pattern

- Khi khai báo một lớp (Class) nào đó chúng ta biết đến khái niệm constructor. Một thực thể (Instance) của lớp được tạo ra bao giờ cũng được gọi hàm constructor để khởi tạo các thành phần (hay có thể hiểu như các thuộc tính) ban đầu. Như vậy các đối tượng được sinh ra từ một lớp nào đó với cùng một constructor sẽ có các thể hiện giống nhau.

    - Vấn đề đặt ra khi ta phải làm việc với các đối tượng phức tạp:

    - Được tạo ra từ nhiều thành phần nhỏ lắp ghép lại.
    - Trong các thành phần nhỏ tạo nên đối tượng có những thành phần bắt buộc và có những thành phần không bắt buộc.

- Do vậy, người ta mong muốn giao công việc này cho một đối tượng chịu trách nhiêm khởi tạo và chia việc khởi tạo đối tượng riêng lẽ, từng bước, để có thể tiến hành khởi tạo riêng biệt ở các hoàn cảnh khác nhau. Và giải pháp được đưa ra là sử dụng Builder Pattern như một người xây dựng.

```
// Illuminate\Mail\MailManager
class MailManager extends Manager
    {
    protected function createSmtpTransport()
    {
        // Code for building up a SmtpTransport class
    }
    protected function createMailgunTransport()
    {
        // Code for building up a MailgunTransport class
    }
    protected function createSparkpostTransport()
    {
        // Code for building up a SparkpostTransport class
    }
    protected function createLogTransport()
    {
        // Code for building up a LogTransport class
    }
    public function getDefaultTransport()
    {
        return $this->app['config']['mail.default'];
    }
}
```

- Lớp MailManager phụ trách việc khởi tạo đối tượng Transport tương ứng với cấu hình của người dùng trong file config/mail.php

## Strategy Pattern

- Strategy Pattern là một trong những Pattern thuộc nhóm hành vi (Behavior Pattern). Nó cho phép định nghĩa tập hợp các thuật toán, đóng gói từng thuật toán lại, và dễ dàng thay đổi linh hoạt các thuật toán bên trong object. Strategy cho phép thuật toán biến đổi độc lập khi người dùng sử dụng chúng.

- Ý nghĩa thực sự của Strategy Pattern là giúp tách rời phần xử lý một chức năng cụ thể ra khỏi đối tượng. Sau đó tạo ra một tập hợp các thuật toán để xử lý chức năng đó và lựa chọn thuật toán nào mà chúng ta thấy đúng đắn nhất khi thực thi chương trình. Mẫu thiết kế này thường được sử dụng để thay thế cho sự kế thừa, khi muốn chấm dứt việc theo dõi và chỉnh sửa một chức năng qua nhiều lớp con.

## Provider Pattern

- Sử dụng Provider thực hiện nhiệm vụ binding các dịch vụ vào trong service container

- Laravel sử dụng Service Provider để khởi tạo hầu hết các thành phần của ứng dụng.

## Singleton Pattern

- Singleton Pattern là một mẫu thiết kế (design pattern) được sử dụng để bảo đảm rằng mỗi một lớp (class) chỉ có được một thể hiện (instance) duy nhất và mọi tương tác đều thông qua thể hiện này. Việc áp dụng Singleton pattern vào thiết kế website sẽ đem lại hiệu quả cao trong hiệu xuất website, chỉ số thiết yếu của Google gần đây cũng nhắc đến vấn đền này.

- Singleton Pattern cung cấp một phương thức khởi tạo private, duy trì một thuộc tính tĩnh để tham chiếu đến một thể hiện của lớp Singleton này. Nó cung cấp thêm một phương thức tĩnh trả về thuộc tính tĩnh này.

```
//Illuminate/Container/Container.php
protected static $instance;

public static function getInstance()
    {
        if (is_null(static::$instance)) {
            static::$instance = new static;
        }

        return static::$instance;
    }
```

- Laravel sử dụng Container như một vùng chứa dịch vụ, bản chất là một Singleton Class.Việc này giúp Container luôn tồn tại duy nhất, có thể tham chiếu và sử dụng ở mọi nơi trong Laravel.