<?php

namespace App\Http\Controllers\Seller;

use App\Events\MessageSent;
use App\Http\Controllers\Controller;
use App\Model\Chatting;
use App\Model\Seller;
use App\Model\Shop;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Pusher\Pusher;

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
            $saved_message = new Chatting;
            $saved_message->user_id       = $request->user_id; //user_id == seller_id
            $saved_message->seller_id    = auth('seller')->id();
            $saved_message->shop_id        = $shop_id;
            $saved_message->message       = $request->message;
            $saved_message->photo         = $image;
            $saved_message->audio        = $audio;
            $saved_message->video          = $video;
            $saved_message->type           = $request->type;
            $saved_message->sent_by_seller = 1;
            $saved_message->seen_by_seller = 0;
            $saved_message->created_at     = now();
            $saved_message->save();
            //return response()->json($saved_message,500);
            $options = array(
                'cluster' => 'eu',
                'useTLS' => true
            );
            $pusher = new Pusher(
                '245cb07e668ce2464e06',
                '2bd8a824c0658910a83e',
                '1523623',
                $options
            );

            $pusher->trigger('everest22', 'sendMessege', $saved_message);

            return response()->json(['message' => $message , 'photo'=>$image , 'audio'=>$audio, 'video'=>$video , 'type'=>$request->type , 'time' => $time]);

        } else {
            Toastr::warning('Type Something!');
            return response()->json(['message' =>'type something!']);

        }
    }

}
