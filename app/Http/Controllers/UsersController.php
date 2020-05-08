<?php

namespace App\Http\Controllers;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Exception;
use GuzzleHttp\Client;
use Laravel\Passport\Client as OClient;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;



use Illuminate\Support\Facades\Auth;
use Validator,Redirect,Response;

class UsersController extends Controller
{
    //
    public $successStatus = 200;
    /**
         * login api
         *
         * @return \Illuminate\Http\Response
         */
        public function login(){
            if(Auth::attempt(['email' => request('email'), 'password' => request('password')])){
                $user = Auth::user();

                $success['token'] =  $user->createToken('MyApp')-> accessToken;
                $success['responseCode']= 00;

                return response()->json(['success' => $success], $this-> successStatus);
            }
            else{
                return response()->json(['error'=>'Unauthorised','responseCode' => -1001], 401);
            }
        }
    /**
         * Register api
         *
         * @return \Illuminate\Http\Response
         */
        public function register(Request $request)
        {
            $validateData = $request->validate([
                'username' => 'required |min:6',
                'firstname'=>'required |min:2',
                'lastname' =>'required |min:2',
                'email' => 'required|email',
                'password' => 'required',
                'c_password' => 'required|same:password',
            ]);


          // $checkUsername = DB::table('users')->where('username',$request->query('username') )->first();
          $input = $request->all();
       $username = $input['username'];
       $checkUsername = DB::table('users')->where('username',$username )->get();

         if (sizeof($checkUsername) ==0){

            $input = $request->all();
            $input['password'] = bcrypt($input['password']);
            $user = User::create($input);

            $success['token'] =  $user->createToken('MyApp')-> accessToken;

            $success['username'] =  $user->username;
            $success['responseCode']= 00;
    return response()->json(['success'=>$success], $this-> successStatus);




         }else{
            return response()->json(['failed'=>'User Already Exists','responseCode' => -1001],401);
         }

        }
    /**
         * details api
         *
         * @return \Illuminate\Http\Response
         */
        public function details()
        {
            $user = Auth::user();
            return response()->json(['success' => $user], $this-> successStatus);
        }



        //Upload User Profile Pic
        public function userPic(Request $request, $userID)
        {



            $this->checkPic($userID);

            $input = $request->all();
            $extension = $request->file('photo')->extension();

            $fileName = time() . "." . $extension;
            $fileName = "userID(" . $userID . ")" . $fileName;
            $path = $request->file('photo')->move(public_path("/users-images"), $fileName);

            $photoURL = url('/' . $fileName);


            $data = [
                'pic' => $fileName,
            ];

            user::where('id', $userID)->update($data);

            return response()->json(['url' => $photoURL], 200);
        }


        //Check if Pic Exists and delete it
        public function checkPic($userID){

            $pic =user::where('id', $userID)->get('pic');
            $image_path = public_path("/public/users-images/".$pic[0]->pic);
            if ($pic != null){
                if(File::exists(public_path('users-images/'.$pic[0]->pic))){
                    File::delete(public_path('users-images/'.$pic[0]->pic));

                }else{

                }
            }
        }
}
