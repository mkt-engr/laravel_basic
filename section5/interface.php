<?php

//インターフェース

use NewsInterface as GlobalNewsInterface;

interface ProductInterface
{
  //変数、関数
  // public function echoProduct()
  // {
  //   echo "親クラスです。";
  // }

  //オーバーライド（上書き）(子クラスで上書きできる。)
  public function getProduct();
}

interface NewsInterface
{
  public function getNews();
}

// 子クラス
class Product implements ProductInterface, NewsInterface
{

  //アクセス修飾子 private（外からアクセスできない） ,protected（自分と継承したクラス）,public(公開)。デフォルトはpublic

  //変数
  private $product = [];

  //関数

  //初回に実行される

  function __construct($product)
  {
    $this->product = $product;
  }

  public function getProduct()
  {
    echo $this->product;
  }
  public function getNews()
  {
    echo "ニュースです";
  }
  public function addProduct($item)
  {
    //$thisはこのクラスの中という意味、productはprivateで宣言した$productのこと。
    //$がないことに気をつけよ
    $this->product .= $item;
  }

  //静的(static) インスタンス化しなくても使える。使い方(クラス名::関数名)
  public static function getStaticProduct($str)
  {
    echo $str;
  }
}

$instance = new Product("テスト");


var_dump($instance);
$instance->getProduct();
echo "<br>";

$instance->getNews();
echo "<br>";

$instance->addProduct("追加分");
echo "<br>";

$instance->getProduct();
echo "<br>";

Product::getStaticProduct("静的");
echo "<br>";
