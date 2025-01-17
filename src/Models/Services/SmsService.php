<?php

namespace App\Models\Services;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use GuzzleHttp\Client;


class SmsService extends Model
{
    use HasFactory;

    const BASEURL = 'https://api.sms.ir/';

    /**
     * @var Client
     */
    private $client;
    public $LineNumber;
    

    public function __construct($line_number = null, $api_key = null) {

        
        $this->client = new Client([
            'verify' => false, //Disabled SSL
            'base_uri' => self::BASEURL,
            'headers' =>[
                'X-API-KEY' => $api_key??config('smsir.api-key'),
                'ACCEPT' => 'application/json',
                'Content-Type' => 'application/json'
            ]
        ]);
        $this->LineNumber = $line_number??config('smsir.line-number');
    }

    public function send($mobile, $template, $parameters){
        $params = [
            'Mobile' => $mobile,
            'TemplateId' => $template,
            'Parameters' => $parameters,
        ];


        $response = $this->client->post('/v1/send/verify', ['body' => json_encode($params)]);
        if ($response->getStatusCode() !== 200) {
            throw new HttpException(__('smsir.error.'.$response->getStatusCode()));
        }
/*        return json_decode($response->getBody()->getContents(), true, 512);*/
        return json_decode($response->getStatusCode(), true, 512);
    }
}
