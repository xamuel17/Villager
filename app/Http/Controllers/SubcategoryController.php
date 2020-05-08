<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\category;
use App\subcategory;
use App\Http\Resources\subcategory as subcategoryResources;

class SubcategoryController extends Controller
{
    //


    public function fetchAllSubCategories(){


        $categories = subcategory::all();

        return subcategoryResources::collection($categories);

    }


    public function fetchsubCategories($catID)
    {
        $ca = category::where('catID', $catID)->get();

        $categoryID = $ca[0]->catID;

     $subcategories = subcategory::where('catID', $categoryID)->get();
        return subcategoryResources::collection($subcategories);
    }
}
