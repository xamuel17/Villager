<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\follower;
use App\user;
use App\userdetail;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\follower as followerResources;
class FollowerController extends Controller
{
    //


    //follow user by userID(create)
    public function follow(Request $request){
        $details = New follower();
        $details->userID = $request->input('userID');
        $details->userFID = $request->input('userFID');

        //check if user id exists
        $userId = $request->input('userID');
        $followerId =$request->input('userFID');
        $matchThese = ['userID' => $userId, 'userFID' => $followerId];
        $state = follower::where( $matchThese)->get();
        if ($state[0]->blocked == 1){
            return response()->json(['error'=>'you have been blocked','responseCode' => -1001],401);

        };
        if(sizeof($state) != 0){
            return response()->json(['error'=>'User Already Followed','responseCode' => -1001],401);

        }else{
            $details->save();
            $follower = userdetail::where('userID', $userId)->get('followers');

            $setFollower=  $follower[0];

            DB::table('userdetails')->where('userID', $userId)->update(['followers' => $setFollower['followers'] + 1]);
            return response()->json(['success'=>'User Followed','responseCode' => 00],200);

        }


    }


    //unfollow user by userID(destroy)
    public function unfollow(Request $request){
        $details = New follower();
        $details->userID = $request->input('userID');
        $details->userFID = $request->input('userFID');

        $userId = $request->input('userID');
        $followerId =$request->input('userFID');

        $matchThese = ['userID' => $userId, 'userFID' => $followerId];
        $state = follower::where($matchThese)->get();

        if(sizeof($state) == 0){
            return response()->json(['error'=>'User Not Followed','responseCode' => -1001],401);

        }
        $details = follower::where('userFID',$followerId)->first();
        if($details->delete()){

            $follower = userdetail::where('userID', $userId)->get('followers');

            $setFollower=  $follower[0];

            DB::table('userdetails')->where('userID', $userId)->update(['followers' => $setFollower['followers'] - 1]);


            return response()->json(['success'=>'Unfollow successful','responseCode' => 00],200);

        }else{
            return response()->json(['failed'=>'Error','responseCode' => -1001],401);

        }

    }




        //User view followers
    public function viewFollowers($userID){
        $followers = follower::where('userID', $userID)->get();
        return new followerResources($followers);
    }

        //User block followers
        public function blockfollowers(Request $request){

            $follower = New follower();
            $follower->userID = $request->input('userID');
            $follower->userFID = $request->input('userFID');

            $userID = $request->input('userID');
            $userFID =$request->input('userFID');

            $matchThese = ['userID' => $userID, 'userFID' => $userFID];
            $follower =  follower::where('userID',$userID)->first();
            $follower->blocked = 1;
            $follower->save();
            $followers = follower::where('userID', $userID)->get();
            return  new followerResources($follower);

        }


        //User unblock followers
        public function unblockfollowers(Request $request){

            $follower = New follower();
            $follower->userID = $request->input('userID');
            $follower->userFID = $request->input('userFID');

            $userID = $request->input('userID');
            $userFID =$request->input('userFID');

            $matchThese = ['userID' => $userID, 'userFID' => $userFID];
            $follower =  follower::where('userID',$userID)->first();
            $follower->blocked = 0;
            $follower->save();
            $followers = follower::where('userID', $userID)->get();
            return  new followerResources($follower);

        }

}
