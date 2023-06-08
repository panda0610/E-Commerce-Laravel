<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OrderMail extends Mailable
{
    use Queueable, SerializesModels;

    public $order;
    public $store;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($order, $store)
    {
        $this->order = $order;
        $this->store = $store;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('emails.orderMail')->with(
            [
                'order' => $this->order,
                'store' => $this->store,
            ]
        );
    }
}
