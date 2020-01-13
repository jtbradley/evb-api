<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Project extends JsonResource
{

    public function toArray($request)
    {
        // return parent::toArray($request);

        $curl = curl_init();
        $id = $request->id;
        $key = config('trello.key');
        $token = config('trello.token');

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.trello.com/1/batch/?urls=/boards/$id,/boards/$id/cards ",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
                "Authorization: OAuth oauth_consumer_key=\"$key\",
                oauth_token=\"$token\",
                oauth_signature_method=\"HMAC-SHA1\",
                oauth_timestamp=\"1578328909\",
                oauth_nonce=\"BOoDqaUrGkG\",
                oauth_version=\"1.0\",
                oauth_signature=\"eWiH1yrdp1OcSdCfiBZxDMSJDfw%3D\""
            ),
        ));

         $response = json_decode(curl_exec($curl));
        curl_close($curl);

        $cards = $response[1]->{200};
        $tasks = [];

        foreach ($cards as $card) {
                $task = (object)[
                    "name" => $card->name,
                    "url" => $card->url,
                    "due" => $card->due,
                    "dueComplete" => $card->dueComplete,
                    "checkItems" => $card->{'badges'}->checkItems,
                    "checkItemsChecked" => $card->{'badges'}->checkItemsChecked
                ];
                array_push($tasks, $task);
        }

        return [

            'id' => $response[0]->{200}->id,
            'name' => $response[0]->{200}->name,
            'url' => $response[0]->{200}->url,
            'desc' => $response[0]->{200}->desc,

            // 'cards' => $response[1]->{200}[0]->name,
            'tasks' => $tasks
        ];

    }
}
