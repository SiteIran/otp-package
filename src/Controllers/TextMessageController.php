<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\PasswordReset;
use App\Models\TextMessage;
use Illuminate\Http\Request;

class TextMessageController extends Controller
{
    public function index()
    {
        $messages = TextMessage::latest()->paginate(25);
        return $messages;
    }

    public function destroy(TextMessage $message)
    {
        $message->delete();
        return 'پیامک مورد نظر حذف شد.';
    }

    public static function send($mobile, $body)
    {
            TextMessage::create([
                'mobile' => $mobile,
                'body'   => $body,
            ]);
            return true;
    }

}
