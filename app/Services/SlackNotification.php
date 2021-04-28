<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class SlackNotification {
    public $slackHookUrl;
    public $blocks = [];
    public $message = [];
    public $header;
    public $description;
    public $links;

    public function __construct($slackHookUrl)
    {
        $this->slackHookUrl = $slackHookUrl;
    }

    public function sendNotification($message)
    {
        return Http::post($this->slackHookUrl, $message)->throw(function ($response, $e) {
            dd($e);
        })->json();
    }

    public function prepareAndSendMessage($thumbnails, $dateUploaded, $photographer, $product)
    {
        // Create Header and Details Blocks for Slack Web Hook Message
        $this->blocks = [
            [
                "type" => "header",
                "text" => [
                    "type" => "plain_text",
                    "text" => "New Pictrues for $product",
                    "emoji" => true
                ]
            ],
            [
                "type" => "section",
                "fields" => [
                    [
                        "type" => "mrkdwn",
                        "text" => "*Date:*\n$dateUploaded"
                    ],
                    [
                        "type" => "mrkdwn",
                        "text" => "*Created by: *\n".$photographer
                    ]
                ]
            ]
        ];
        
        // Append Thumbnails into Block Array
        foreach($thumbnails as $key=>$thumbnail) {
            
            array_push($this->blocks, 
            [
                "type" => "image",
                "title" => [
                    "type" => "plain_text",
                    "text" => "Photo".($key + 1)." of $product"
                ],
                "block_id" => "image".($key + 1),
                "image_url" => $thumbnail,
                "alt_text" => "Photo".($key + 1)
            ]);
        }
        
        // Append Action Buttons Into Block Array
        array_push($this->blocks, 
        [
            "type" => "actions",
            "elements" => [
                [
                    "type" => "button",
                    "text" => [
                        "type" => "plain_text",
                        "emoji" => true,
                        "text" => "Approve"
                    ],
                    "url" => env('FRONTEND_URL')."/approve/product",
                    "style" => "primary",
                    "value" => "click_me_123"
                ],
                [
                    "type" => "button",
                    "text" => [
                        "type" => "plain_text",
                        "emoji" => true,
                        "text" => "Disapprove"
                    ],
                    "style" => "danger",
                    "value" => "click_me_123"
                ]
            ]
        ]);

        $response = $this->sendNotification([
            "blocks" => $this->blocks
        ]);

        return $response;
    }
}
