# セクション２

php を途中で止める

```php
exit;
```

`$_`から始まる物をスーパーグローバル変数という（９種類）

- クリックジャッキングを防ぐ

https://deep-blog.jp/engineer/9514/

```php
<?php header("X-FRAME-OPTIONS: DENY"); ?>
```

- CSRF 対策

各ページでトークンを持ち回ってそれが一致していればセキュリティ的に安全。

```php
<?php
session_start();

//初回アクセス時に
// 1. ramdom_bytes(32)で32バイトのバイナリトークンを発効し
// 2. bin2hex()で16進数に変換する
if (!isset($_SESSION["csrfToken"])) {
    $csrfToken = bin2hex(random_bytes(32));
    $_SESSION["csrfToken"] = $csrfToken;
  }
$token = $_SESSION["csrfToken"];

//各ページでPOSTでトークンを送ったものと、セッションに保存したトークンが一緒ならページを表示させる。
<?php if ($_POST["csrf"] === $_SESSION["csrfToken"]) : ?>
　<p>なんか出す</p>
<?php endif; ?>
?>
```

- 日本語の文字列の長さを知りたい場合

```php
strlen("あああ") //6 バイト数を取得している
mb_strlen("あああ") //3 文字数を取得している。半角も全角も１文字は１文字と扱う。
```

`empty(fefefe)`は fefefe が 0 の時にも true にしてしまう。値の存在は`isset`メソッドを使う。

- バリデーションをする時に役立つメソッド
  URL や Email が正しい形式になっているかを判定する。正しい形式になっていたら true を返す。

```php
//filter_var(＜バリデーションしたい変数＞,＜バリデーションのパターン（メアドとかURLとか）＞)
filter_var($request["email"], FILTER_VALIDATE_EMAIL);
filter_var($request["url"], FILTER_VALIDATE_URL);
```

## ベーシック認証

.htaccess ファイルで指定する。

- パスワード作成

```php
password_hash("password123", PASSWORD_BCRYPT);
//password_hast(＜ハッシュ貸したいパスワード＞,＜ハッシュ化の方法＞):
```

- .htaccess

```txt
AuthType Basic
AuthName "メッセージ"
AuthUserFile ＜パスワードを保存したファイルのパス＞
require valid-user
```

- パスワードを保存したファイル

```txt
admin:＜ハッシュ化したパスワード＞
//＜ユーザー名＞:＜ハッシュ化したパスワード＞
```

## ファイル操作

ファイル操作の方法

- ファイル名を指定してファイル丸ごと操作する
  - file_get_contents,file_input_contents
- ストリーム型（一行ごと）
  - fopen,fclose,fgets ,fwrite
- オブジェクト型（オブジェクトとして）
  - SplFileObject

```php
//ファイルインポート
$contactFile = ".contact.dat";

//ファイル丸ごと読み込み
$fileContents = file_get_contents($contactFile);

//ファイルに書き込み（上書き）
//file_put_contents(＜書き込みたいファイル＞, ＜書き込みたい内容＞);
file_put_contents($contactFile, "test");

//ファイルに書き込み（追記）第３引数をつける。
file_put_contents($contactFile, $addText, FILE_APPEND);
```

### CSV 操作

読み込むファイル(contact.dat)

```CSV
タイトル1,本文1,日付,カテゴリ
タイトル2,本文2,日付,カテゴリ
タイトル3,本文3,日付,カテゴリ
```

```php
//配列 file , 区切る explode, foreach
$allData = file($contactFile);


foreach ($allData as $lineData) {
//explodeはJSでいうsplitみたいなやつ
$lines = explode(",", $lineData);
  echo $lines[0] . "<br>";
  echo $lines[1] . "<br>";
  echo $lines[2] . "<br>";
}
```

# section4

## セッションとクッキーの使い方その１

- `$_SESSION` はサーバ側で値を管理するのでブラウザを変えてもログインできる。
- `$_COOKIE` はブラウザで保存する。

```php

<?php
//セッションを始める
session_start();
?>
//セッションは連想配列
$_SESSION["visited"] = 1;

//クッキー
//setcookie(＜キー＞,＜バリュー＞,＜期限＞,??);
setcookie("id", "aaa", ,"/");
```

## セッションとクッキーの破棄の仕方

```php
<?php
echo "セッションを破棄しました";
//空の配列を渡すことでセッションを破棄する
$_SESSION = [];

//クッキーの有効期限を過去にすることでクッキーを破棄する
setcookie("PHPSESSID", "", time()  - 1800, "/");
```

## 便利ないろいろ

### 型をつける

```php
declare(strict_types=1); //強い型指定

//PHP7から関数の後ろに`:string`とすることで返り値にも型をつけられる
function typeTest(string $string):string // 引数stringの他に、array, callable, bool, float, int, object, クラス名、インターフェース名
{
    var_dump($string);
    return $string;
}

typeTest(['配列文字']);
// 引数にstringと指定しているのに配列->こちらはエラー
```

### array_map

引数に関数を取ることができる。

```php
$parameters = ['  空白あり ', '  配列 ', ' 空白あり  '];
//array_map(＜関数名(ここではPHPの組み込みの関数trimを使っているが自作でも良い)＞,＜操作したい変数＞)
$trimedParameters = array_map('trim', $parameters);
//['空白あり', '配列', '空白あり'];
```

# section5

モダン PHP

- PSR-1,2,4(PHP コーディング規約)

## class

- アクセス修飾子
  - private（外からアクセスできない）
  - protected（自分と継承したクラスがアクセスできる）
  - public(他のクラスからでもアクセスできる)

```php
class Product
{
  //変数
  private $product = [];
    //初回に実行される

  //コンストラクタ
  function __construct($product)
  {
    $this->product = $product;
  }

  //関数
  public function getProduct()
  {
    //$thisはこのクラスの中という意味、productはprivateで宣言した$productのこと。
    //$がないことに気をつけよ
    echo $this->product;
  }

  //静的関数
  //静的(static) インスタンス化しなくても使える。使い方(クラス名::関数名)
  public static function getStaticProduct($str)
  {
    echo $str;
  }
}
//インスタンス化
$instance = new Product("テスト");

//関数実行
$instance->getProduct();

//静的関数実行
Product::getStaticProduct("静的");
```

## 継承

```php
//親クラス
class BaseProduct
{
  //変数、関数
  public function echoProduct()
  {
    echo "親クラスです。";
  }

  //オーバーライド（上書き）(子クラスで上書きできる。)
  public function getProduct()
  {
    echo "親の関数です";
  }
}
```

## 抽象クラス

- 接頭辞に`abstract`をつける。
- 抽象クラスで書かれた抽象メソッドは必ず子クラスで実装する必要がある。
- 抽象クラスで書かれた抽象メソッドにはメソッド名しか書けない。具体的な処理は書けない
- インターフェースと違い具体的なメソッドもかける。
- 抽象クラスは普通のクラスとインターフェースの中間のイメージ

```php
//抽象クラス　// 設定するメソッドを強制する。
abstract class ProductAbstract
{
  //変数、関数
  public function echoProduct()
  {
    echo "親クラスです。";
  }

  //抽象メソッド
  abstract public function getProduct();
}

// 子クラス
class Product extends ProductAbstract
{
  private $product = [];

  function __construct($product)
  {
    $this->product = $product;
  }

  //抽象クラスで書かれた抽象メソッドを子クラスで実装している。
  public function getProduct()
  {
    echo $this->product;
  }
}
```

## インターフェース

- 接頭辞に`abstract`をつける。
- 抽象クラスと違い具体的なメソッドはかけない。
- クラスは継承は１つしかできないが、インターフェースは複数`implements`できる。

デザインパターン

- https://qiita.com/yuka-12/items/435c64888b8ba6ecf136

## トレイト

初めて聞いた。PHP で多重継承するイメージ

- 接頭辞に`trait`をつける。
- `use ＜trait名＞`で使える。

```php
<?php

trait ProductTrait
{
  public function getProduct()
  {
    echo "プロダクト";
  }
}

trait NewsTrait
{
  public function getNews()
  {
    echo "ニュース";
  }
}

class Product
{
  use ProductTrait;
  use NewsTrait;

  public function getInformation()
  {
    echo "クラスです。";
  }
}

$product = new Product();

$product->getInformation();
echo "<br>";

$product->getProduct();
echo "<br>";

$product->getNews();
echo "<br>";
```

## Composer の使い方

NameSpace

- composer の初期化

```
composer init
```

これで composer.json ができる。

autoload ができるように composer.json を書き換える。

```json
{
  "autoload": {
    "psr-4": {
      "App\\": "app/"
    }
  }
}
```

これができたら autoload のするためにいろいろインストールするコマンドを入力する。

```
composer install
```

これで vender フォルダができる。

## NameSpace を実際に使ってみる。

- ディレクトリ構成

```
/app
 - Controllers
   - TestController.php
 - Models
   - TestModel.php
```

- TestModel.php

```php
//ここのAppはcomposer.jsonでかかれた`"App\\": "app/"`と関連している
namespace App\Models;

//ファイル名とクラス名を一致させる必要がある。（１ファイル１クラス）
class TestModel
{
  private $text = "hello world";

  public function getHello()
  {
    return $this->text;
  }
}
```

- TestController.php
  TestModel.php で定義した class TestModel を使う。

```php
// App\はcomposer.jsonで指定した`"App\\"="app"`のこと
// 自分の存在を示す的な
namespace App\Controllers;

//jsのimportみたいな感じ
use App\Models\TestModel;

class TestController
{
  public function run()
  {
    $model = new TestModel;
    echo $model->getHello();
  }
}
```

- index.php
  - require_once をするのはこのファイルだけ
  - composer コマンドで作成した autoload.php を読み込む

```php
require_once __DIR__ . "/vendor/autoload.php";

use App\Controllers\TestController;

$app = new TestController;
$app->run();

```

## Carbon(composer のライブラリ)

Carbon:日付操作のライブラリ

Carbon ライブラリのインストール

```
composer require nesbot/carbon
```

これで composer.json が書き換えられる。package.json みたいな感じか。

- 使い方(index.php)

```php
//import
use Carbon\Carbon;
echo Carbon::now();
```

# せくしょん 6 Laravel 入門

- larabel インストール

```
composer create-project laravel/laravel task_test --prefer-dist "6.*"
```

- サーバ立てる

```
php artisan serve
```

## Laravel の初期設定

### タイムゾーン、言語設定

- `config/app.php`
  - "timezone"=>"Asia/Tokyo";
  - "locale" => "ja";

### データベースの文字コード

- config/database.php
  - "charset" => "utf8"

### デバックバー

表示されている画面の情報や DB とのやりとりをするのに使える。

```
composer require barryvdh/laravel-debugbar
```

## キャッシュクリアの方法

.env の`APP_DEBUG=true`でデバックのためのバーが出る。これを false にしても出てしまう場合はキャッシュクリアする。

```
php artisan cache:clear
php artisan config:clear
```

## DB 設定

.env に以下の記述があるので書き換える。

```
DB_CONNECTION=fefefe
DB_HOST=fefefe
DB_PORT=fefefe
DB_DATABASE=fefefe
DB_USERNAME=fefefe
DB_PASSWORD=fefefe
```

- 接続できたかのコマンド

```
php artisan migrate
```

## Laravel 概要

- MVC
  - Model：データベースとやりとり
  - View：見た目
  - Controller：処理
  - Routing：アクセスの振り分け
  - Migration：DB テーブルの管理

### ルーティング

- web.php

views ディレクトリの`welcome.blade.php`を呼び出す。

```php
Route::get('/', function () {
    return view('welcome');
});
```

### モデル

Laravel では DB のやりとりを PHP で書ける。

Eloquent(OR マッパーの名前)
ORM/OR マッパー

以下のコマンドを入力すると`app\Models`の下にモデルができる

```
php artisan make:model Models/Test
```

- Test.php

名前空間も書いてくれてる。

```
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Test extends Model
{
    //
}
```

## Laravel マイグレーション

マイグレーション：DB テーブルの履歴管理

```
php artisan make:migration create_tests_table
```

これをすることで`database/migrations/YYYY_MM_DD_create_tests_table.php`が作成される。
参考：https://readouble.com/laravel/6.x/ja/migrations.html

- YYY_MM_DD_create_tests_table.php(の一部)

```php
    public function up()
    {
        Schema::create('tests', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string("text", 100);//追加
            $table->timestamps();
        });
    }

```

これで`id`とか`text`を書いたテーブルが作成できる。テーブル作成コマンドは

```
php artisan migrate
```

## tinker(DB 簡易接続)

ターミナルで DB に接続できる。

```
php artisan tinker
```

これを叩くと以下のようにコマンドが打てる。
`>>>`が自分が入力したところである。

- save()で DB にある table に insert
- all()で select \* from ~~~

```
>>> $test = new App\Models\Test;
=> App\Models\Test {#3377}
>>> $test->text= "aaa";
=> "aaa"
>>> $test -> save();
=> true
>>> App\Models\Test::all()
=> Illuminate\Database\Eloquent\Collection {#4100
     all: [
       App\Models\Test {#4099
         id: 1,
         text: "aaa",
         created_at: "2021-09-29 23:17:24",
         updated_at: "2021-09-29 23:17:24",
       },
     ],
   }
>>>
```

## コントローラー

コントローラーを作成するコマンド

```
php artisan make:controller TestController
//php artisan make:controller ＜コントローラーの名前＞
```

これで`app/Http/Controllers/TestController.php`が作成される。

## MVC のモデルの記述方法 1

web.php に書く。第１引数の URL にきた時に第２引数のコントローラーのメソッドを呼び出す。

```php
Route::get("tests/test", "TestController@index");
//Route::get(＜URL＞, ＜コントローラー名@メソッド名＞);
```

- TestController.php

```php
class TestController extends Controller
{
    //
    public function index()
    {
      // resource/view/tests/test.blade.phpのテンプレートが読み込まれる。
        return view("tests.test");
    }
}
```

読み込むテンプレートは`＜名前＞.blade.php`のように`blade`を必ず入れる。

## MVC のモデルの記述方法 2

DB から値を引っ張ってきてテンプレートに表示する。

.env の DB 情報を変更した時はもう１度`php artisan serve`をする。

- TestController.php

* `compact`で View に変数を渡す。
* `compact`の中身は引数は幾つでも良い
* `compact`の中身は渡したい変数の`$`を除いたもの

```php
$values = Test::all();//select * from testsみたいな感じ
//dd($values);//値をブラウザに表示して処理を停止する。
return view("tests.test", compact('values'));
//return view(＜viewの格納先＞)
```

- test.blade.php
  実際に View に表示する。

```php
//$valueはコントローラーから渡した値。
@foreach($values as $value)
{{$value->id}}<br>
{{$value->text}}<br>
@endforeach
```

## ヘルパ関数

Laravel が用意している関数

`return view`とか

https://readouble.com/laravel/6.x/ja/helpers.html

## コレクション型

https://readouble.com/laravel/6.x/ja/collections.html

配列を拡張した型、Laravel 特有のもの

## クエリビルダ

https://readouble.com/laravel/6.x/ja/queries.html

SQL の代わりに PHP の構文で書く。以下は select 文っぽい。

```php
$tests = DB::table("tests")->select("id")->get();
```

## ファサード

https://shimooka.hateblo.jp/entry/20141215/1418620292

Laravel:https://readouble.com/laravel/6.x/ja/facades.html

## Laravel 起動処理 DI とサービスコンテナ

https://qiita.com/namizatork/items/801da1d03dc322fad70c

`public/index.php`が Laravel を起動した時に最初に動く。`autoload`とかもこのファイルにある。

## Blade

https://readouble.com/laravel/6.x/ja/blade.html

- `@if @foreach`などの`@`から始まるものが使える。
- `{{name}}`で PHP の`htmlspecialchars`が既に施された状態になり XSS 攻撃を防ぐ
- `@csrf`で CSRF 対策ができる。

## FrontEnd

- laravel-ui : Laravel6.x から
- laravel-mix : webpack のラッパー
- webpack.mix.ks : laravel-mix に設定ファイル

## Laravel-ui 認証

スカフォールド（足場）：https://readouble.com/laravel/6.x/ja/frontend.html

フロントエンドで使われる SCSS とか Vue とかがまとめられたもの。

インストール方法

```
composer require laravel/ui:^1.0 --dev
```

composer.json に laravel-ui の記述があれば良い。

続いて Bootstrap とログイン/ユーザー登録スカフォールドを作成する。

```
php artisan ui bootstrap --auth
```

- `routes/web.php`に`Auth::routes();`が追加されている。
- `Http/Controllers/Auth`したにいろいろ追加されている。
- `Http/user.php`にも追加されている。

* Bootstrap の追加は`npm i`

## フォームのエラーメッセージの日本語化

`resource/lang/validation.php`にメッセージがある。

メッセージを日本語化した GitHub のリポジトリ：https://github.com/minoryorg/laravel-resources-lang-ja

`/config/app.php`の`'locale' => 'ja',`を変更する。

- resource/lang/ja/validation.php
  password という文字を変更したい場合は以下のように変更する。

```php
'attributes' => [
    "password" => "パスワード" //追加
],
```

### URL の設定

ファイルの一覧をみたい時

```
php artisan route:list
```

テキストに出力したい場合は以下のコマンド

```
php artisan route:list
```

マルチログイン機能：https://coinbaby8.com/laravel-udemy-multilogin.html

## Blade のドキュメント読んでみた。

https://readouble.com/laravel/7.x/ja/blade.html

ここで出てくるディレクティブ

- `@section`
- `@yield`
- `@show`
- `@parent`

マスターレイアウト（大枠）を作ってその中に別のレイアウトを埋め込む。
