<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\comment;
use App\post;
use App\like;
use App\reply;
use App\view as views;
use App\User;
use App\userdetail;
use App\Http\Resources\userdetail as detailResources;


use DateTime;
use Facade\Ignition\QueryRecorder\Query;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Concerns\InteractsWithInput as ConcernsInteractsWithInput;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class UserdetailController extends Controller
{
    //


    //create details
    public function create(Request $request){
        $details = New userdetail();
        $details->userID = $request->input('userID');
        $details->gender = $request->input('gender');
        $details->bio = $request->input('bio');
        $details->martialStatus = $request->input('martialStatus');
        $details->lowerQualification = $request->input('lowerQualification');
        $details->higherQualification1 = $request->input('higherQualification1');
        $details->higherQualification2 = $request->input('higherQualification2');
        $details->skills = $request->input('skills');
        $details->work = $request->input('work');

        //check if user id exists
        $usId = $request->input('userID');
        $id = user::where('id' ,$usId )->get();
        if(sizeof($id) == 0){
            return response()->json(['failed'=>'User Does Not Exist','responseCode' => -1001],401);

        }
        //check if more details exists
        $moreDetails = userdetail::where('userID' ,$usId )->get();

        if(sizeof($moreDetails) != 0 ){
            return response()->json(['failed'=>'More User Details  Already  Exists','responseCode' => -1001],401);

        }else{
        $details->save();
       //response()->json( $details);
       $details = userdetail::where('userID', $usId)->get();
        return detailResources::collection($details);

        }
    }


    //read details
    public function view($userID){
        $userdetail = userdetail::where('userID', $userID)->get();
        return new detailResources($userdetail);


    }


    //Update details
    public function update(Request $request, $userID){

        $details =  userdetail::where('userID',$userID)->first();
        $details->gender = $request->input('gender');
        $details->bio = $request->input('bio');
        $details->martialStatus = $request->input('martialStatus');
        $details->lowerQualification = $request->input('lowerQualification');
        $details->higherQualification1 = $request->input('higherQualification1');
        $details->higherQualification2 = $request->input('higherQualification2');
        $details->skills = $request->input('skills');
        $details->work = $request->input('work');
        $details->save();
       return  new detailResources($details);

    }

    //destroy details
    public function destroy($userID){
        $details = userdetail::where('userID',$userID)->first();
        if($details->delete()){
            return response()->json(['success'=>'Delete successful','responseCode' => 00],200);

        }else{
            return response()->json(['failed'=>'Error','responseCode' => -1001],401);

        }
    }


}
