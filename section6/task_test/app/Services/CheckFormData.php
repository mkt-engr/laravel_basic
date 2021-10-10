<?php

namespace App\Services;

use Illuminate\Http\Request;
use App\Models\ContactForm;
use Illuminate\Support\Facades\DB;

class CheckFormData
{

  public static function checkGender($data)
  {
    if ($data->gender === 0) {
      $gender = "男性";
    } else if ($data->gender === 1) {
      $gender = "女性";
    }
    return $gender;
  }
  public static function checkAge()
  {
  }
}
