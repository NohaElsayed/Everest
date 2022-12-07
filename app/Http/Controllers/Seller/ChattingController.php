<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Model\Chatting;
use App\Model\Seller;
use App\Model\Shop;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ChattingController extends Controller
{
    public function chat()
    {
        if(auth('seller')->user()->added != null){
            $shop_id= auth('seller')->user()->added;}
            else{
        $shop_id = Shop::where('seller_id', auth('seller')->id())->first()->id;
            }
        $last_chat = Chatting::where('shop_id', $shop_id)
            ->orderBy('created_at', 'DESC')
            ->first();

        if (isset($last_chat)) {
            //get messages of last user chatting
            $chattings = Chatting::join('users', 'users.id', '=', 'chattings.user_id')
                ->select('chattings.*', 'users.f_name', 'users.l_name', 'users.image')
                ->where('chattings.shop_id', $shop_id)
                ->where('user_id', $last_chat->user_id)
                ->get();

            $chattings_user = Chatting::join('users', 'users.id', '=', 'chattings.user_id')
                ->select('chattings.*', 'users.f_name', 'users.l_name', 'users.image')
                ->where('chattings.shop_id', $shop_id)
                ->orderBy('chattings.created_at', 'desc')
                ->get()
                ->unique('user_id');

            return view('seller-views.chatting.chat', compact('chattings', 'chattings_user', 'last_chat'));
        }

        return view('seller-views.chatting.chat', compact('last_chat'));
    }

    public function message_by_user(Request $request)
    {

      if(auth('seller')->user()->added != null){
            $shop_id= auth('seller')->user()->added;}
            else{
        $shop_id = Shop::where('seller_id', auth('seller')->id())->first()->id;
            }
            if(auth('seller')->user()->added != null){
                $last_chat= auth('seller')->user()->added;}
                else{
        $last_chat = Chatting::where('seller_id', auth('seller')->id())
            ->where('user_id', $request->user_id)
            ->orderBy('created_at', 'DESC')
            ->first();
                }

        $last_chat->seen_by_seller = 0;
        $last_chat->save();

        $seen_chats = Chatting::where('seller_id', auth('seller')->id())
            ->where('user_id', $request->user_id)
            ->orderBy('created_at', 'DESC')
            ->get();

        foreach ($seen_chats as $seen_chat) {
            $seen = Chatting::find($seen_chat->id);
            $seen->seen_by_seller = 0;
            $seen->save();
        }
        $sellers = Chatting::join('users', 'users.id', '=', 'chattings.user_id')
            ->select('chattings.*', 'users.f_name', 'users.l_name', 'users.image')
            ->where('chattings.shop_id', $shop_id)
            ->where('chattings.user_id', $request->user_id)
            ->orderBy('created_at', 'ASC')
            ->get();


        return response()->json($sellers);
    }

    // Store massage
    public function seller_message_store(Request $request)
    {
        if ($request->message != '' || $request->hasFile('photo') || $request->hasFile('video') || $request->hasFile('audio')){
            if(auth('seller')->user()->added != null){
                $shop_id= auth('seller')->user()->added;}
            else{
                $shop_id = Shop::where('seller_id', auth('seller')->id())->first()->id;
            }
            $message = $request->message;
            $time = now();
            $image =null;
            $video =null;
            $audio =null;
            if ($request->hasFile('photo')) {
                $file =$request->file('photo');
                $extension = $file->getClientOriginalExtension();
                $filename = time().'.' . $extension;
                $file->move(public_path('uploads/photo/'), $filename);
                $image= url('public/uploads/photo/').'/'.$filename;
            }
            if ($request->hasFile('audio')) {
                $file =$request->file('audio');
                $extension = $file->getClientOriginalExtension();
                $filename = time().'.mp3';
                $file->move(public_path('uploads/audio/'), $filename);
                $audio= url('public/uploads/audio/').'/'.$filename;
            }
            if ($request->hasFile('video')) {
                $file =$request->file('video');
                $extension = $file->getClientOriginalExtension();
                $filename = time().'.' . $extension;
                $file->move(public_path('uploads/video/'), $filename);
                $video= url('public/uploads/video/').'/'.$filename;
            }
            DB::table('chattings')->insert([
                'user_id'        => $request->user_id, //user_id == seller_id
                'seller_id'      => auth('seller')->id(),
                'shop_id'        => $shop_id,
                'message'        => $request->message,
                'photo'          => $image,
                'audio'          => $audio,
                'video'          => $video,
                'type'           => $request->type,
                'sent_by_seller' => 1,
                'seen_by_seller' => 0,
                'created_at'     => now(),
            ]);

            return response()->json(['message' => $message , 'photo'=>$image , 'audio'=>$audio, 'video'=>$video , 'type'=>$request->type , 'time' => $time]);

        } else {
            Toastr::warning('Type Something!');
            return response()->json(['message' =>'type something!']);

        }
    }

}
