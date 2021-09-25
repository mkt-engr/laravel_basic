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
