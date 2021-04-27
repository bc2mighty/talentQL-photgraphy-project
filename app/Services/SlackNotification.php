<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class SlackNotification {
    public $slackHookUrl;
    public $message;

    public function __construct($slackHookUrl)
    {
        $this->slackHookUrl = $slackHookUrl;
    }

    public function sendNotification()
    {

    }

    public function generatePostData($thumbnails, $photographer, $timeUploaded) {
        $this->message = [
            "blocks" => [
                    [
                        "type" => "header",
                        "text" => [
                            "type" => "plain_text",
                            "text" => "New request",
                            "emoji" => true
                        ]
                    ],
                    [
                        "type" => "section",
                        "fields" => [
                            [
                                "type" => "mrkdwn",
                                "text" => "*Type:*\nPaid Time Off"
                            ],
                            [
                                "type" => "mrkdwn",
                                "text" => "*Created by: ".$photographer
                            ]
                        ]
                    ],
                    [
                        "type" => "section",
                        "fields" => [
                            [
                                "type" => "mrkdwn",
                                "text" => "*When:*\nAug 10 - Aug 13"
                            ]
                        ]
                    ],
                    [
                    "type" => "image",
                    "title" => [
                        "type" => "plain_text",
                        "text" => "Please enjoy this photo of a kitten"
                    ],
                    [
                    "block_id" => "image4",
                    "image_url" => "http://placekitten.com/500/500",
                    "alt_text" => "An incredibly cute kitten."
                    ],
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
                                "url" => "https://api.slack.com/block-kit",
                                "style" => "primary",
                                "value" => "click_me_123"
                            ],
                            [
                                "type" => "button",
                                "text" => [
                                    "type" => "plain_text",
                                    "emoji" => true,
                                    "text" => "Reject"
                                ],
                                "style" => "danger",
                                "value" => "click_me_123"
                            ]
                        ]
                    ]
                ]
            ]
        ];
    }
}