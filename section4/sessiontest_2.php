<?php
session_start();
?>

<html lang="en">

<head>

</head>

<body>
  <?php
  echo "セッションを破棄しました";

  $_SESSION = [];

  if (isset($_COOKIE["PHPSESSID"])) {
    setcookie("PHPSESSID", "", time()  - 1800, "/");
  }

  session_destroy();

  echo "セッション";

  echo "<pre>";
  echo var_dump($_SESSION);
  echo "</pre>";
  ?>
</body>

</html>