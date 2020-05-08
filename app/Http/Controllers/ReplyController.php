<?php

namespace App\Http\Controllers;
use App\reply;
use Illuminate\Http\Request;
use App\User;
use App\comment;
use App\Http\Resources\comment as commentResources;

class ReplyController extends Controller
{
    //


    //Reply a comment
    public function create(Request $request){

        $details = New reply();
        $details->repID = $request->input('repID');
        $details->postID = $request->input('postID');
        $details->comID = $request->input('comID');
        $details->userID = $request->input('userID');
        $details->content = $request->input('content');
        $details->contentImg = $request->input('contentImg');


        //check if user id exists
        $usId = $request->input('userID');
        $id = user::where('id' ,$usId )->get();
        if(sizeof($id) == 0){
            return response()->json(['failed'=>'User Does Not Exist','responseCode' => -1001],401);

        }

        //Add Content image


        $details->save();
        $comID = $request->input('comID');
        $allComments = comment::where('comID', $comID)->get();
        $allComments['replies'] = reply::where('comID', $comID)->get();

        return commentResources::collection($allComments);

    }

    //Like a Reply
    public function likeReply(Request $request, $postID, $userID)
    {
        $user = DB::table('users')->where('id', $userID)->get();

        if (sizeof($user) != 0) {
            $token = $request->bearerToken();


            $matchThese = ['postID' => $postID, 'userID' => $userID];
            $likes = like::where($matchThese)->get();



            $data = [
                'postID' => $postID,
                'userID' => $userID,
                'token' => $token

            ];
            if (sizeof($likes) == 0) {
                DB::table('likes')->insert($data);

                $likes = post::where('postID', $postID)->get('likes');
                $setLike = 0;
                $setLike =  $likes[0];
                DB::table('posts')->where('postID', $postID)->update(['likes' => $setLike['likes'] + 1]);

                $postedArticle = post::where('postID', $postID)->get();
                return postResources::collection($postedArticle);
            } else {
                return response()->json(['action' => 'liked', 'responseCode' => 1000], 200);
            }
        } else {
            return response()->json(['failed' => 'Error', 'responseCode' => -1001], 401);
        }
    }
}
