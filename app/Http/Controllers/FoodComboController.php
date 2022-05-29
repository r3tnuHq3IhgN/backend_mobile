<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FoodCombo;

class FoodComboController extends Controller
{
    public function index() {
        return $this->responseData(FoodCombo::all(), 200);
    }
}
