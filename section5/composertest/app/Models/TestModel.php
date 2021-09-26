<?php

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
