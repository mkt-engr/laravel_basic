<?php
//$_POSTの連想配列
function validation($request)
{
  $errors = [];
  if (empty($request["your_name"])) {
    $errors[] = "指名は必須です。";
  }
  return $errors;
}
