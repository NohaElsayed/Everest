<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Model\Chatting;
use Auth;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Pusher\Pusher;

class ChattingController extends Controller
{
    public function chat_with_seller(Request $request)
    {
        // $last_chat = Chatting::with('seller_info')->where('user_id', auth('customer')->id())
        //     ->orderBy('created_at', 'DESC')
        //     ->first();
        $last_chat = Chatting::with(['shop'])->where('user_id', auth('customer')->id())
            ->orderBy('created_at', 'DESC')
            ->first();

        if (isset($last_chat)) {
            $chattings = Chatting::join('shops', 'shops.id', '=', 'chattings.shop_id')
                ->select('chattings.*', 'shops.name', 'shops.image')
                ->where('chattings.user_id', auth('customer')->id())
                ->where('shop_id', $last_chat->shop_id)
                ->get();

            $unique_shops = Chatting::join('shops', 'shops.id', '=', 'chattings.shop_id')
                ->select('chattings.*', 'shops.name', 'shops.image')
                ->where('chattings.user_id', auth('customer')->id())
                ->orderBy('chattings.created_at', 'desc')
                ->get()
                ->unique('shop_id');

            return view('web-views.users-profile.profile.chat-with-seller', compact('chattings', 'unique_shops', 'last_chat'));
        }
        return view('web-views.users-profile.profile.chat-with-seller');

    }
    public function messages(Request $request)
    {
        $last_chat = Chatting::where('user_id', auth('customer')->id())
            ->where('shop_id', $request->shop_id)
            ->orderBy('created_at', 'DESC')
            ->first();

        //$last_chat->seen_by_customer = 0;
        $last_chat->save();

        $shops = Chatting::join('shops', 'shops.id', '=', 'chattings.shop_id')
            ->select('chattings.*', 'shops.name', 'shops.image')
            ->where('user_id', auth('customer')->id())
            ->where('chattings.shop_id', json_decode($request->shop_id))
            ->orderBy('created_at', 'ASC')
            ->get();

        return response()->json($shops);
    }

    public function messages_store(Request $request)
    {

        if ($request->message != '' || $request->hasFile('photo') || $request->hasFile('video') || $request->hasFile('audio')){
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
            $saved_message->user_id       = auth('customer')->id(); //user_id == seller_id
            $saved_message->seller_id    = $request->seller_id;
            $saved_message->shop_id        = $request->shop_id;
            $saved_message->message       = $request->message;
            $saved_message->photo         = $image;
            $saved_message->audio        = $audio;
            $saved_message->video          = $video;
            $saved_message->type           = $request->type;
            $saved_message->sent_by_seller = 1;
            $saved_message->seen_by_seller = 0;
            $saved_message->created_at     = now();
            $saved_message->save();
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

            $pusher->trigger('everest22', 'sendMessegeByCustomer', $saved_message);

            return response()->json(['message' => $message , 'photo'=>$image , 'audio'=>$audio, 'video'=>$video , 'type'=>$request->type , 'time' => $time]);
        } else {
            Toastr::warning('Type Something!');
            return response()->json('type something!');

        }
    }

}
