<?php

declare(strict_types=1);

namespace App\Jobs;

class SendSmsNotification extends BaseNotification
{
    const TYPE = 'sms';

    /**
     * SendSmsNotification constructor.
     *
     * @param $userId
     */
    public function __construct($userId)
    {
        parent::__construct($userId);

        $this->payload['phone'] = $this->user->contact()->get()->first()->phone;
        $this->payload['username'] = $this->user->name;
    }
}
