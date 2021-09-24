<?php
//test.phpのファイルの場所
echo __FILE__;


//パスワード（暗号化）
echo "<br>";
echo (password_hash("password123", PASSWORD_BCRYPT));
