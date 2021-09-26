<?php

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
