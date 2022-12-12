<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use App\Model\Chatting;
use App\Model\Shop;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Pusher\Pusher;
use function App\CPU\translate;

class ChatController extends Controller
{
    public function chat_with_seller(Request $request)
    {
        try {
            $last_chat = Chatting::with(['seller_info', 'customer', 'shop'])->where('user_id', $request->user()->id)
                ->orderBy('created_at', 'DESC')
                ->first();

            if (isset($last_chat)) {

                $chattings = Chatting::with(['seller_info', 'customer', 'shop'])->join('shops', 'shops.id', '=', 'chattings.shop_id')
                    ->select('chattings.*', 'shops.name', 'shops.image')
                    ->where('chattings.user_id', $request->user()->id)
                    ->where('shop_id', $last_chat->shop_id)
                    ->get();

                $unique_shops = Chatting::with(['seller_info', 'shop'])
                    ->where('user_id', $request->user()->id)
                    ->orderBy('created_at', 'DESC')
                    ->get()
                    ->unique('shop_id');

                $store = [];
                foreach ($unique_shops as $shop) {
                    array_push($store, $shop);
                }

                // $unique_shops = Chatting::with(['seller_info', 'shop'])->groupBy('shop_id')->get();

                return response()->json([
                    'last_chat' => $last_chat,
                    'chat_list' => $chattings,
                    'unique_shops' => $store,
                ], 200);
            } else {
                return response()->json($last_chat, 200);
            }

        } catch (\Exception $e) {
            return response()->json(['errors' => $e], 403);
        }
    }

    public function messages(Request $request)
    {
        try {
            $messages = Chatting::with(['seller_info', 'customer', 'shop'])->where('user_id', $request->user()->id)
                ->where('shop_id', $request->shop_id)
                ->get();

            return response()->json($messages, 200);
        } catch (\Exception $e) {
            return response()->json(['errors' => $e], 403);
        }
    }

    public function messages_store(Request $request)
    {
        try {
            if ($request->message != '' || $request->hasFile('photo') || $request->hasFile('video') || $request->hasFile('audio')){
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
                $shop = Shop::find($request->shop_id);
                $saved_message = new Chatting;
                $saved_message->user_id       = auth('customer')->id(); //user_id == seller_id
                $saved_message->seller_id    = $shop->seller_id;
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

                return response()->json(['message' => translate('sent')], 200);
            } else {
                return response()->json(translate('type something!'));
            }
        } catch (\Exception $e) {
            return response()->json(['errors' => $e], 403);
        }
    }
}
