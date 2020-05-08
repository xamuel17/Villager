<?php

namespace App\Http\Controllers;

use App\comment;
use Illuminate\Http\Request;
use App\post;
use App\like;
use App\reply;
use App\view as views;
use App\User;
use App\Http\Resources\post as postResources;
use DateTime;
use Facade\Ignition\QueryRecorder\Query;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Concerns\InteractsWithInput as ConcernsInteractsWithInput;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;


class PostController extends Controller
{
    public $successStatus = 200;

    //create Post
    public function createPost(Request $request)
    {
        $validateData = $request->validate([
            'subID' => 'required',
            'userID' => 'required ',
            'title' => 'required |min:6',
            'content' => 'required ',

        ]);

        $input = $request->all();
        $userID = $input['userID'];
        $subID =  $input['subID'];
        $subCat = DB::table('subcategories')->where('subID', $subID)->get();

        $user = DB::table('users')->where('id', $userID)->get();
        if (sizeof($user) != 0 and sizeof($subCat) != 0) {
            $posts = post::create($validateData);
            $postID = $posts['id'];
            $postedArticle = post::where('postID', $postID)->get();
            return postResources::collection($postedArticle);
        } else {
            return response()->json(['failed' => 'Post Creation Failed', 'responseCode' => -1001], 401);
        }
    }


    //ViewPost
    public function viewPost(Request $request, $postID, $userID)
    {
        $user = DB::table('users')->where('id', $userID)->get();

        if (sizeof($user) != 0) {
            $token = $request->bearerToken();
            $matchThese = ['postID' => $postID, 'userID' => $userID];
            $views = views::where($matchThese)->get();



            $data = [
                'postID' => $postID,
                'userID' => $userID,
                'token' => $token

            ];
            if (sizeof($views) == 0) {
                DB::table('views')->insert($data);

                $views = post::where('postID', $postID)->get('views');
                $setView = 0;
                $setView =  $views[0];
                DB::table('posts')->where('postID', $postID)->update(['views' => $setView['views'] + 1]);

                $postedArticle = post::where('postID', $postID)->get();
                return postResources::collection($postedArticle);
            } else {
                return response()->json(['action' => 'viewed', 'responseCode' => 1000], 200);
            }
        } else {
            return response()->json(['failed' => 'Error', 'responseCode' => -1001], 401);
        }
    }




    //LikePost
    3






    //Comment on a Post
    public function commentPost(Request $request)
    {

        $input = $request->all();
        $userID =  $input['userID'];
        $postID = $input['postID'];
        $user = DB::table('users')->where('id', $userID)->get();

        if (sizeof($user) != 0) {

            $post = DB::table('posts')->where('postID', $postID)->get();
            if (sizeof($post) != 0) {
                $input = $request->all();
                $postID = $input['postID'];
                $userID =  $input['userID'];
                $replyID = $input['replyID'];
                $content =  $input['content'];

                $extension = $request->file('photo')->extension();

                $fileName = time() . "." . $extension;
                $fileName = "userID(" . $userID . ")" . $fileName;
                $path = $request->file('photo')->move(public_path("/comment-images"), $fileName);



                $data = [
                    'postID' => $postID,
                    'userID' =>  $userID,
                    'replyID' =>  $replyID,
                    'content' => $content,
                    'contentImg' =>  $fileName
                ];
                DB::table('comments')->insert($data);

                $comments = post::where('postID', $postID)->get('comments');
                $setComment = 0;
                $setComment =  $comments[0];
                DB::table('posts')->where('postID', $postID)->update(['comments' => $setComment['comments'] + 1]);

                $postedArticle = post::where('postID', $postID)->get();
                return postResources::collection($postedArticle);
            } else {
                return response()->json(['failed' => 'Error1', 'responseCode' => -1001], 401);
            }
        } else {
            return response()->json(['failed' => 'Error2', 'responseCode' => -1001], 401);
        }
    }


    //Fetch all Comments for a Post
    public function allComments($postID)
    {
        $comID = DB::table('comments')->where('postID', $postID)->get('comID');


        $comId = array();
        foreach ($comID as $tt) {
            array_push($comId, $tt->comID);
        }


        $allComments = comment::where('postID', $postID)->get();

        foreach ($comId as $key => $value) {



            $matchThese = ['postID' => $postID, 'comID' => $value];


            foreach ($allComments as $com) {

                if ($com->comID == $value) {

                    $com['replies'] = reply::where($matchThese)->get();
                }
            }
        }

        return $allComments;
    }





    //Fetch frontpage Posts
    public function fPost()
    {
        $posts = post::where('fpstatus', 1)->get();
        return postResources::collection($posts);
    }



    public function FpostImage(Request $request, $subID)
    {
        $input = $request->all();
        $extension = $request->file('photo')->extension();

        $fileName = time() . "." . $extension;
        $fileName = "SUBID(" . $subID . ")" . $fileName;
        $path = $request->file('photo')->move(public_path("/post-images"), $fileName);
        $photoURL = url('/' . $fileName);

        $imgName = "SUBID(" . $subID . ")" . $fileName;

        $data = [
            'img' => $fileName,
        ];

        post::where('subID', $subID)->get();
        post::update($data);
        return response()->json(['url' => $photoURL], 200);
    }



    //Fetch single User Posts
    public function Upost($userID){
        $posts =post::where('userID', $userID)->get();
        return postResources::collection($posts);

    }
}
