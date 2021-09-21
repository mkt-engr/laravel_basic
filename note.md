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
