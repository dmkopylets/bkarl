<?php

declare(strict_types=1);

namespace App\Events\Dispatcher\Message;

/**
 * Class Message
 *
 * @package App\Events\Dispatcher\Message
 */
class Message
{
    /**
     * @var string
     */
    private string $message;

    /**
     * Message constructor.
     *
     * @param string $message
     */
    public function __construct(string $message)
    {
        $this->message = $message;
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }
}