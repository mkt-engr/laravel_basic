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
