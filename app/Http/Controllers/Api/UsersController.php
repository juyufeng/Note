<?php

namespace App\Http\Controllers\Api;

use App\Helpers\Handler\ImageUploadHandler;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UsersController extends ApiController
{
    public function __construct()
    {
        parent::__construct();

        $this->middleware('auth:api');
    }

    public function uploadAvatar(Request $request){

        $this->validate($request,[
            'img' => 'required|image'

        ]);

        $user = Auth::user();

        $imageHander = new ImageUploadHandler();


        $avatar = $request->file('img');
        $result = $imageHander->uploadAvatar($avatar,$user);

        $avatarPath = '/'.$imageHander->avatarPath().'/'.$result['filename'];

        $user->avatar = $avatarPath;
        $user->save();

        return $this->respondWithSuccess([
            'avatar' => $avatarPath
        ]);

    }
}
