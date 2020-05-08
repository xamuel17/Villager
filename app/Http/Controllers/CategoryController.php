<?php

namespace App\Http\Controllers;
use App\Http\Resources\category as categoryResources;
use Illuminate\Http\Request;
use App\category;
class CategoryController extends Controller
{
    //
    public $successStatus = 200;





    public function fetchCategories(){


        $categories = category::all();

        return categoryResources::collection($categories);

    }

    public function showCategory($catID){


        $category = category::where('catID' ,$catID )->get();

        return categoryResources::collection($category);
    }
}
