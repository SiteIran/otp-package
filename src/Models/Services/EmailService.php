<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Mail;


class EmailService extends Model
{
    use HasFactory;

    public function send($email, $parameters)
    {
//        dd($parameters);
        $data = $parameters;

        Mail::send('email.verify', $data, function ($message) use ($email){
            $message->from('noreply@bidartebnoor.com', 'Hamerz Medical');

            $response = $message->to($email)->subject('Verification Code');
//            $message->to($email)->cc('siteiran@gmail.com');
        });
    }

}
