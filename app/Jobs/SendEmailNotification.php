<?php

declare(strict_types=1);

namespace App\Jobs;

class SendEmailNotification extends BaseNotification
{
    const TYPE = 'email';

    /**
     * SendEmailNotification constructor.
     *
     * @param $userId
     * @param  string  $event
     */
    public function __construct($userId, $event = 'new-user')
    {
        parent::__construct($userId);

        $this->payload['type'] = self::TYPE;
        $this->payload['event'] = $event;
        $this->payload['email'] = $this->user->email;
        $this->payload['username'] = $this->user->name;
    }
}
