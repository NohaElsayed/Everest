<?php

namespace App\Http\Controllers\api\v2\seller;

use App\CPU\Helpers;
use App\Http\Controllers\Controller;
use App\Model\Chatting;
use App\Model\Shop;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Pusher\Pusher;
use function App\CPU\translate;

class ChatController extends Controller
{
    public function messages(Request $request)
    {
        $data = Helpers::get_seller_by_token($request);

        if ($data['success'] == 1) {
            $seller = $data['data'];
        } else {
            return response()->json([
                'auth-001' => translate('Your existing session token does not authorize you any more')
            ], 401);
        }

        try {
            $messages = Chatting::with(['seller_info', 'customer', 'shop'])->where('seller_id', $seller['id'])
                ->get();
            return response()->json($messages, 200);
        } catch (\Exception $e) {
            return response()->json(['errors' => $e], 403);
        }
    }

    public function send_message(Request $request)
    {
        $data = Helpers::get_seller_by_token($request);

        if ($data['success'] == 1) {
            $seller = $data['data'];
        } else {
            return response()->json([
                'auth-001' => translate('Your existing session token does not authorize you any more')
            ], 401);
        }
        if ($request->message != '' || $request->hasFile('photo') || $request->hasFile('video') || $request->hasFile('audio')) {

            $message = $request->message;
            $time = now();
            $image = null;
            $video = null;
            $audio = null;
            if ($request->hasFile('photo')) {
                $file = $request->file('photo');
                $extension = $file->getClientOriginalExtension();
                $filename = time() . '.' . $extension;
                $file->move(public_path('uploads/photo/'), $filename);
                $image = url('public/uploads/photo/') . '/' . $filename;
            }
            if ($request->hasFile('audio')) {
                $file = $request->file('audio');
                $extension = $file->getClientOriginalExtension();
                $filename = time() . '.mp3';
                $file->move(public_path('uploads/audio/'), $filename);
                $audio = url('public/uploads/audio/') . '/' . $filename;
            }
            if ($request->hasFile('video')) {
                $file = $request->file('video');
                $extension = $file->getClientOriginalExtension();
                $filename = time() . '.' . $extension;
                $file->move(public_path('uploads/video/'), $filename);
                $video = url('public/uploads/video/') . '/' . $filename;
            }
            $shop_id = Shop::where('seller_id', $seller['id'])->first()->id;

            $saved_message = new Chatting;
            $saved_message->user_id = $request->user_id; //user_id == seller_id
            $saved_message->seller_id = $seller['id'];
            $saved_message->shop_id = $shop_id;
            $saved_message->message = $request->message;
            $saved_message->photo = $image;
            $saved_message->audio = $audio;
            $saved_message->video = $video;
            $saved_message->type = $request->type;
            $saved_message->sent_by_seller = 1;
            $saved_message->seen_by_seller = 0;
            $saved_message->created_at = now();
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

            return response()->json(['message' => $message, 'photo' => $image, 'audio' => $audio, 'video' => $video, 'type' => $request->type, 'time' => $time]);

        } else {


            return response()->json(translate('type something!'), 200);

        }
    }
}
