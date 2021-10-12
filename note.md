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
- `@extends`

マスターレイアウト（大枠）を作ってその中に別のレイアウトを埋め込む。

- app.blade.php(マスターレイアウト)

```php
<html>
<head>
  <title>アプリ名 - @yield('title')</title>
</head>
<body>
  @section('sidebar')
  ここがメインのサイドバー
  @show

  <div class="container">
    @yield('content')
  </div>
</body>
</html>
```

- マスターレイアウトの`@yield("title")`は子供のレイアウトで`@section("title","埋め込む内容")`と使う。
- めちゃわかりにくいけどここでは`@section`を`@show`で閉じている。（`@endsectionShow`）とかでええやんと今は思う。
  - 親のレイアウトで`@show`をつけると子供のレイアウトで`@parent`と書くことで親のレイアウトの中身が出せる
    - ここでは「ここがメインのサイドバー」と子供のレイアウトで表示できる。
    - 子供のレイアウトで`@parent`を書かないと親のレイアウトの中身は子のレイアウトの中身で上書かれる。

```php
@section('sidebar')
ここがメインのサイドバー
@show
```

- child.blade.php(子供のレイアウト)
  - `@extend`で親のレイアウトを読み込む
  - `@section`の第 1 引数は親のレイアウトで定義してある。
  - `@section`の第 2 引数は親のレイアウトでに埋め込むもの

```php
@extends('sample.app')

@section('title', 'Page Title AAA')

@section('sidebar')
 @parent

<p>ここはメインのサイドバーに追加される</p>
@endsection

@section('content')
<p>ここが本文のコンテンツ</p>
@endsection
```

# セクション 7 簡易ウェブアプリ（CRUD/RESTful）

Model →Controller 　 → Route → 　 View の順で作る

- Model の作成
  -m でマイグレーションファイルを作成する(テーブル作成の履歴的な、バージョンコントロールのような機能ですって laravel のドキュメントに書いてあった。)

```
php artisan make:model Models/ContactForm -m
```

上のコマンドを打って作成したマイグレーションファイルに追記してカラムを作成する

- まいぐれーしょんふぁいる

https://readouble.com/laravel/6.x/ja/migrations.html
これの「カラム作成」をみる

```php
//氏名、メールアドレス、url,性別、年齢、お問い合わせ内容
$table->string("your_name", 20);
$table->string("email", 255);
$table->longText("url")->nullable($value = true);
$table->tinyInteger("gender");
$table->string("contact", 200);
```

これを元にマイグレーションを実行する。

```
php artisan migrate
```

phpmyadmin で確認すると contact_forms テーブルができている

## マイグレーションとロールバック

### カラムの追加

`--table=＜テーブル名＞`で存在するテーブルに追加できる。

```
php artisan make:migration add_title_to_contact_forms_table --table=contact_forms
```

作成したマイグレーションファイルに以下を追加する。

```php
$table->string("title", 50)->after("your_name");
```

`->after`修飾子でカラムを挿入したい位置を指定できる。

```
php artisan migrate
```

と入力しテーブルにカラムを追加する。

```
php artisan migrate:status
```

でマイグレーションファイルをみることができる。

### ロールバック

```
php artisan migrate:rollback
```

でロールバックできる。

## RestFul なコントローラー

https://readouble.com/laravel/6.x/ja/controllers.html

`--resource`をつけることで Controller の中を REST 用に作ってくれる。（いろいろなメソッドを作ってくれる。上の参考の URL にある index から destroy までの８個を作ってくれる。）

```
php artisan make:controller ContactFormController --resource
```

- web.php
  コマンドで作られた８個のメソッドの内利用するものを選ぶ（または使わないものを除く）

```php
Route::resource('photos', 'PhotoController')->only([
    'index', 'show'
]);

Route::resource('photos', 'PhotoController')->except([
    'create', 'store', 'update', 'destroy'
]);
```

## ルーティング(グループ・認証)

https://readouble.com/laravel/6.x/ja/routing.html

### ルートグループ

```
php artisan make:controller ContactFormController --resource
```

これで REST のメソッドを８個作ったがそれをルーティングする処理を書こうとすると以下のようにいちいち`contact/`と書く必要があり冗長になる。

```php
Route::get("contact/index", "ContactFormController@index");
Route::get("contact/create", "ContactFormController@create");
Route::get("contact/edit", "ContactFormController@edit");
Route::get("contact/delete", "ContactFormController@delete");
```

これを防ぐのがルーティンググループ

```php
//["prefix"=>＜URLにまとめてまとめてつけるプレフィックス＞,"middleware"=>＜使いたいミドルウェア＞]//今回は認証系のミドルウェアを使っている。
Route::group(["prefix" => "contact", "middleware" => "auth"], function () {
    //この場合だとcontact/indexにアクセスした時にContactFormControllerのindexメソッドにアクセスする。
    //->nameでエイリアスをつけられる
    Route::get("index", "ContactFormController@index")->name("contact.index");
});
```

## layout.blade.php を読む

- `@auth`と`@guest`ディレクティブは、現在のユーザーが認証されているか、もしくはゲストであるかを簡単に判定するために使用します。

```php
@auth
    // ユーザーは認証済み
@endauth

@guest
    // ユーザーは認証されていない
@endguest
```

## login.blade.php

web.php で書いた`->(contact.index)`はビューテンプレートの中で使える。

- login.blade.php

```php
<form method="POST" action="{{ route('login') }}">
<form method="POST" action="{{ route('contact.index') }}">
```

- form のなかには`@csrf`を必ず入れる必要がある。

## 新規登録

- web.php

ビューテンプレートで呼び出す名前を定義する。(`->name(＜ビューで使う名前＞)`)

```php
Route::group(["prefix" => "contact", "middleware" => "auth"], function () {
    Route::get("index", "ContactFormController@index")->name("contact.index");
    Route::get("create", "ContactFormController@create")->name("contact.create");
});
```

- index.blade.php
  web.php で定義した`->name(fefefe)`をここで使う。

```php
<form method="GET" action="{{ route('contact.create') }}">
 <button type="submit" class="btn btn-primary">新規登録</button>
</form>
```

## データの保存の実装の前に

https://readouble.com/laravel/8.x/ja/requests.html

データの保存は Controller の store メソッドで行う。`Request`が import されており、それを使うことでフォームの値を受け取れる。

- web.php
  POST の store を追加

```php
Route::group(["prefix" => "contact", "middleware" => "auth"], function () {
    Route::get("index", "ContactFormController@index")->name("contact.index");
    Route::get("create", "ContactFormController@create")->name("contact.create");
    Route::post("store", "ContactFormController@store")->name("contact.store");
});
```

```php
public function store(Request $request)
{
    $your_name = $request->input('your_name');
    // = $request->input(＜formのname属性＞);

}
```

フォームで入力された全てのデータの取得は all で行う。

```php
$input = $request->all();
```

## Store 保存

- ContactFormController.php

Model をインポートしてそいつに値を詰める

```php
use App\Models\ContactForm;

//~~~~~
$contact = new ContactForm;

//->＜テーブルのカラム＞
$contact->your_name = $request->input('your_name');
$contact->title = $request->input('title');
$contact->email = $request->input('email');
$contact->url = $request->input('url');
$contact->gender = $request->input('gender');
// $contact->age = $request->input('age');
$contact->contact = $request->input('contact');

//テーブルに更新をかける。
$contact->save()

//indexページへ戻る
return redirect("contact/index");
```

## DB に保存されている値の取得

- ContactFormController.php

  - エロクワント（OR マッパー）

```php
$contacts= ContactForm::all();//DBにある全てのレコードを取得
```

- クエリビルダー

* ContactFormController.php

```php
//DBを操作するモジュールをインポート
use Illuminate\Support\Facades\DB;

//必要なデータを取得
$contacts = DB::table('contact_forms')->select("id", "your_name", "title", "created_at")->get();

//compact関数を使ってView側にデータを送る
return view("contact.index", compact("contacts"));
```

- index.blade.php(View 側)

```php
<tbody>
@foreach ($contacts as $contact)
<tr>
<th>{{ $contact->id }}</th>
<td>{{ $contact->your_name }}</td>
<td>{{ $contact->title }}</td>
<td>{{ $contact->created_at }}</td>
</tr>
@endforeach
</tbody>
```

## show 表示画面

- 名前付きルート(https://readouble.com/laravel/6.x/ja/routing.html)
  ルーティングの時にパラメタを渡す。

- ContactFormController.php

```php
//エロクワントで取得するselect * from ~ where id = $idみたいな感じ
$contact =   ContactForm::find($id);
if($contact->gender === 0){
   $gender = "男性";
}else if($contact->gender ===1){
   $gender = "女性";
}
//compact関数で変数をViewに渡す
return view("contact.show",compact("contact","gender"));
```

- index.blade.php
  ルーティングで変数を渡す

```php
//route(＜ルーティング先＞,['＜ルーティング先で使う変数の名前＞'=>＜ルーティング先に送りたい値＞])
<td><a href="{{ route('contact.show',['id'=>$contact->id])}}">詳細をみる</a></td>
```

## edit 編集画面

- web.php(ルーティング)
  追加する

```php
Route::get("edit/{id}", "ContactFormController@edit")->name("contact.edit");
```

- ContactFormController.php

```php
public function edit($id)
{
    //
    $contact =   ContactForm::find($id)
    return view("contact.edit",compact("contact"));
}
```

- edit.blade.php

* `value={{＜連想配列の取り出し＞}}`
* `@if($contact->gender === 0)checked @endif`タグの中で if 文使える

```php
<form  method="POST" action="">
  @csrf
  <input type="text" name="your_name" value="  {{$contact->your_name}}">
  <br>
  件名
  <input type="text" name="title" value="  {{$contact->title}}">
  <br>
  メールアドレス
  <input type="text" name="email" value="{{$contact->email}}">
  <br>
  ホームページ
  <input type="text" name="url" value="{{$contact->url}}">
  <br>
  性別
  <input type="radio" name="gender" id="" value="0"
  @if($contact->gender === 0)checked @endif>男
  <input type="radio" name="gender" id="" value="1" @if($contact->gender === 1) checked @endif>女
  <br>
  お問い合わせ内容
  <textarea name="contact" id="">{{$contact->contact}}</textarea>
  <br>
  <input type="submit" name="btn btm-info" value="更新する">
</form>
```

## update 更新画面

- web.php
  ルーティングを設定する。

```php
Route::group(["prefix" => "contact", "middleware" => "auth"], function () {
  ~~~
  Route::post("update/{id}", "ContactFormController@update")->name("contact.update");
  //Route::post("＜ブラウザでアクセスした時のURL/パラメータ＞", "＜ブラウザからの指示を受け取るコントローラ@メソッド名＞")->name(＜View側で使うルーティングの名前＞);
});
```

- edit.blade.php

```php
<form method="POST" action="{{route('contact.update',['id'=>$contact->id])}}">
//action="{{route(＜web.phpでつけたルーティングの名前＞,[＜コントローラで受け取る名前＞=>＜コントローラに渡したい名前＞])}}"
 @csrf
 <input type="text" name="your_name" value="{{ $contact->your_name }}">
 <br>
 件名
 <input type="text" name="title" value="{{ $contact->title }}">
 <br>
 メールアドレス
 <input type="text" name="email" value="{{ $contact->email }}">
 <br>
 ホームページ
 <input type="text" name="url" value="{{ $contact->url }}">
 <br>
 性別
 <input type="radio" name="gender" id="" v
 $ontact->gender === 0) checked @endif>男
 <input type="radio" name="gender" id="" v
 $ontact->gender === 1) checked @endif>女
 <br>
 お問い合わせ内容
 <textarea name="contact" id="">{{ $contact->contact }}</textarea>
 <br>
 <input type="submit" name="btn btm-info" value="更新する">
</form>
```

- ContactFormController.php

```php
public function update(Request $request, $id)
{
    $contact = ContactForm::find($id);
    //idを元にエンティティ的なものを作成

    $contact->your_name = $request->input('your_name');
    //＜DBに保存するカラム名＞ = $request->input(＜View側で書いたname属性＞)
    $contact->title = $request->input('title');
    $contact->email = $request->input('email');
    $contact->url = $request->input('url');
    $contact->gender = $request->input('gender');
    $contact->contact = $request->input('contact');
    //DB更新
    $contact->save();

    return redirect("contact/index");
}
```

## destroy 削除機能

form で使う場合は GET か POST しか使えない。

- web.php

```php
Route::group(["prefix" => "contact", "middleware" => "auth"], function () {
  Route::post("destroy/{id}", "ContactFormController@destroy")->name("contact.destroy");
```

あとはほぼ update と同じ

## サービスへの切り離し（ファットコントローラー防止）

コントローラに記述が多くなること：ファットコントローラ

サービスに分離する。

## バリデーション

フォームリクエストバリデーション：https://readouble.com/laravel/6.x/ja/validation.html

```
php artisan make:request StoreContactForm
```

これで`App\Requests\StoreContactForm.php`が作成される。ここにバリデーションを書いていく。一旦`authorize`メソッドの返り値は`true`にしておく。

- StoreContactForm.php

```php
public function authorize()
{
    return true;
}
```

`rules`メソッドにバリデーションを書く。

`使用可能なバリデーションルール`のセクションの中に書き方が書いてる。

- 入力値が明日以降かバリデーションする。

```php
'start_date' => 'required|date|after:tomorrow'
```

- StoreContactForm.php

```php
public function rules()
{
    return [
        //フォームの中のname属性
        "your_name"=>"required|string|max:20",
        "title"=>"required|string|max:50",
        "email"=>"required|email|unique:user|max:255",
        "url"=>"url|nullable",
        "gender"=>"required",
        "age"=>"required",
        "contact"=>"required|string|max:200",
        "caution"=>"required|accepted",//acceptedはチェックを入れているかどうか
    ];
}
```

- ContactFormController.php

`store`メソッドにバリデーションを書く。引数を`StoreContactForm`に変更すると自動的にバリデーションをしてくれる。

### エラーメッセージの出し方

- create.blade.php

Thymeleaf っぽい。View 側で`$errors`の変数が使える。`$errors->all()`でエラーの内容が配列で取り出せる。

```php
@if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
```

- validation.php
  バリデーションのメッセージに関するファイル。エラーメッセージの「~~~は必須です」などの~~~の部分を日本語に変えたい時は以下のようにする。

```php
'attributes' => [
    "password" => "パスワード", //追加
    "email" => "メールアドレス", //追記
],
```

## ダミーデータの作り方

- シーディング：https://readouble.com/laravel/6.x/ja/seeding.html
- シーダ：初期設定のこと

```
php artisan make:seeder UsersTableSeeder
```

これで`database/seeds/UsersTableSeeder.php`が作成される。ここにダミーデータを作成していく。

- UsersTableSeeder.php

`run`メソッドにダミーデータを書く。`insert`メソッドの中に配列でデータを書いていく。

```php
public function run()
{
    DB::table('users')->insert(
        [
            'name' => Str::random(10),
            'email' => Str::random(10) . '@gmail.com',
            'password' => Hash::make('password'),
        ],
        [
            'name' => Str::random(10),
            'email' => Str::random(10) . '@gmail.com',
            'password' => Hash::make('password'),
        ]
    );
}
```

これを`DatabaseSeeder.php`で以下のように呼び出す必要がある。

```php
/**
 * データベース初期値設定の実行
 *
 * @return void
 */
public function run()
{
    $this->call([
        UsersTableSeeder::class,
        PostsTableSeeder::class,
        CommentsTableSeeder::class,
    ]);
}
```

シーダクラスを書くと Composer のオートローダを再生成する必要がある。その時には以下のコマンドを実行する。ざっくりいうとシーダクラスの再読み込みみたいな感じかな。

```
composer dump-autoload
```

シードクラスの実行

```
php artisan db:seed
```

これでエラーが出た場合はデータを削除して insert 文を実行する。

```
php artisan migrate:refresh --seed
```

## 大量のダミーデータの作成（Factory ＆ Faker）

データベースのテスト：https://readouble.com/laravel/6.x/ja/database-testing.html

ファクトリの作成コマンド

```
php artisan make:factory ContactFormFactory
```

`database/factories/ContactFormFactory.php`が作成される。デフォルトで作成されたものだと

```php
use App\Model;
```

となっているがこれを自分が利用したい Model にする必要がある。ここでは ContactFormMModel。書き換えると以下のようになる。

```php
use App\Models\ContactForm;
use Faker\Generator as Faker;

//defineのクラス名もContactFormModel::classにする必要がある。
$factory->define(ContactFormModel::class, function (Faker $faker) {
    return [
        //
    ];
});
```

`fakerphp/faker`で検索かけてみる。

faker の使い方：https://shingo-sasaki-0529.hatenablog.com/entry/how_to_use_php_faker

- app.php

faker の日本語化

```php
'faker_locale' => 'ja_JP',
```

- ContactFormFactory.php

  - `name`は人名を出すっぽい
  - `realText(XXX)`は日本語を XXX 文字出す。

```php
use App\Models\ContactForm;
use Faker\Generator as Faker;

$factory->define(ContactFormModel::class, function (Faker $faker) {
    return [
        "your_name" => $faker->name,
        "title" => $faker->realText(50), //日本語を出すのはrealText()
        "email" => $faker->unique()->email,
        "url" => $faker->url,
        "gender" => $faker->randomElement([0, 1]),
        "contact" => $faker->realText(200),
    ];
});
```

これを実行する Seeder を作成する。

```
php artisan make:seeder ContactFormSeeder
```

- ContactFormSeeder.php

```php
public function run()
{
  //factory(＜ダミーデータを作りたいクラス名::class＞,＜個数＞)->create();
  factory(ContactForm::class, 200)->create(); //200個のダミーデータを作る。
}
```

- DatabaseSeeder.php
  ContactFormSeeder.php を実行するメソッドを書く。

```php
public function run()
{
    $this->call(UsersTableSeeder::class);
    $this->call(ContactFormSeeder::class);
}
```

Seeder を変更したので以下を実行

```
composer dump-autoload
```

以下を実行して一度データを削除してからデータを流す。

```
php artisan migrate:fresh --seed
```

## ページネーション

ペジネーション(名前笑)：https://readouble.com/laravel/6.x/ja/pagination.html

Laravel 側で用意してくれてめっちゃ楽

- ContactFormController.php

```php
 $contacts = DB::table('contact_forms')->select("id", "your_name", "title", "created_at")->orderBy("created_at", "asc")->paginate(20);
```

`->paginate(20)`をつけるだけとかマジで楽。

- index.blade.php
  ページネーションの出し方

```php
 {{ $contacts->links() }}
```

楽すぎ〜

## 検索フォーム

- `mb_convert_kana`:https://www.php.net/manual/ja/function.mb-convert-kana.php

  - 第２引数を's'にすると全角スペースを半角スペースに変える。ちなみに'S'にすると半角スペースを全角スペースに変える。

- `preg_split`:https://www.php.net/manual/ja/function.preg-split.php
  ```php
  $search_split2 = preg_split("/[\s]+/", $search_split, -1, PREG_SPLIT_NO_EMPTY);
  preg_split(＜分割方法（正規表現）＞,＜分割する文字＞,＜分割する文字数（0,-1にすると制限なし）＞,＜flag（今回のフラグの場合は空文字列でないものだけがreturnされる。）＞)
  ```

* index.blade.php
  input タグの type="search"を作る。

```html
<form class="form-inline my-2 my-lg-0" method="GET" action={{ route('contact.index') }}>
<input class="form-control mr-sm-2" type="search" placeholder="検索" aria-label="Search"1 name="search">
<button class="btn btn-outline-success my-2 my-sm-0" type="submit">検索する</button>
</form>
```

- ContactFormController.php
  フォームの値を受け取ってクエリビルダでクエリを作る。

```php
public function index(Request $request)
{

//inputのname属性がsearchの値を受け取る。
$search = $request->input("search");

//検索フォーム
$query = DB::table('contact_forms');

//nullじゃなかったらクエリを作る。
if ($search !== null) {
//全角スペースを半角に
$search_split = mb_convert_kana($search, "s");

//空白で区切る
$search_split2 = preg_split("/[\s]+/", $search_split, -1,PREG_SPLIT_NO_EMPTY);

//単語をループで回す。
foreach ($search_split2 as $value) {
            $query->where("your_name", "like", "%" . $value . "%");
}
      }


$query->select("id", "your_name", "title", "created_at");
$query->orderBy("created_at", "asc");
$contacts = $query->paginate(20);
return view("contact.index", compact("contacts"));
```

# セクション 7

要件定義の記事：https://qiita.com/digisaku_tamori/items/741fcf0f40dd989ee4f8

## RDB その１

`-m`でマイグレーションファイルを更新する。

```
php artisan make:model Models/Area -m
php artisan make:model Models/Shop -m
php artisan make:model Models/Route -m
php artisan make:seed RouteSeeder
php artisan make:seed ShopSeeder
php artisan make:seed AreaSeeder
```

- Shop テーブルにカラムを入れる。(マイグレーションファイル)

```php
public function up()
{
    Schema::create('shops', function (Blueprint $table) {
        $table->bigIncrements('id');
        $table->string('shop_name', 20);
        $table->unsignedBigInteger('area_id');
        $table->timestamps();
    });
}
```

- Route(路線かな？)テーブルにカラムを入れる。

```php
public function up()
{
    Schema::create('routes', function (Blueprint $table) {
        $table->bigIncrements('id');
        $table->string('name', 20);
        $table->integer('sort_no');
        $table->timestamps();
    });
}
```

Area テーブルと Shop テーブルにダミーデータ挿入

- AreaSeeder.php

```php
public function run()
{
    DB::table('areas')->insert([
        ["id"=>1,"name"=>"東京","sort_no"=>1],
        ["id"=>2,"name"=>"大阪","sort_no"=>2],
        ["id"=>3,"name"=>"福岡","sort_no"=>3],
    ]);
}
```

- ShopSeeder.php

```php
public function run()
{
    DB::table('Shop')->insert([
            ["id" => 1, "shop_name" => "高級食パン屋", "area_id" => 1],
            ["id" => 2, "shop_name" => "高級食クロワッサン屋", "area_id" => 2],
            ["id" => 3, "shop_name" => "高級コッペパン屋", "area_id" => 3],
            ["id" => 4, "shop_name" => "高級メロンパン屋", "area_id" => 4]
        ]);
}
```

- DatabaseSeeder.php
  ２つの Seeder を実行するコードを書く

```php
public function run()
{
    $this->call(UsersTableSeeder::class);
    $this->call(ContactFormSeeder::class);
    $this->call(AreaSeeder::class);
    $this->call(ShopSeeder::class);
}
```

Seeder を書き換えたので以下を実行

```
composer dump-autoload
php artisan migrate:fresh --seed
```

## RDB その 2

リレーション：https://readouble.com/laravel/6.x/ja/eloquent-relationships.html

モデルの Area と Shop を作る。
Area：Shop = 1 : 多

２つのテーブルを紐づける。

- Models/Area.php
  １つのエリアの中にたくさんの店があるので`hasMany()`を使う。

```php
class Area extends Model
{
    public function shops()
    {
        return $this->hasMany("App\Models\Shop");
    }
}
```

- Models/Shop.php
  １つの店は１つのエリアの中に存在するので`belongsTo()`を使う。

```php
class Shop extends Model
{
    public function area()
    {
        return $this->belongsTo("App\Models\Area");
    }
}
```

Shop のコントローラを作る

```
php artisan make:controller ShopController
```

```php
public function index()
{
    //主->従
    //Shopsの中でAreaのIDが１のものを探す
    $area_tokyo = Area::find(1)->shops;
    //主<-従
    //ShopのIDが2のものの名前を表示する
    $shop = Shop::find(2)->area->name;
    dd($area_tokyo, $shop);
}
```

## 外部キー制約

外部キー制約を付けようと思うとマイグレーションファイルを変更する必要がある。

shops テーブルの`area_id`は Area テーブルにあるものしか入れることができないようにする。それが外部キー制約。

外部キー制約：https://readouble.com/laravel/6.x/ja/migrations.html

- マイグレーションファイル

```php
public function up()
  {
      Schema::create('shops', function (Blueprint $table) {
          $table->bigIncrements('id');
          $table->string('shop_name', 20);
          $table->unsignedBigInteger('area_id');//FK(Foreign Key)
          $table->timestamps();

          //$table->foreign(＜外部キー制約を付けたいカラム名(子テーブルの持ち物)＞)->reference(＜外部キー(親テーブルの持ち物)＞)->(＜親テーブル名＞)
          $table->foreign('area_id')->references('id')->on('areas');
      });
  }
```
