<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ProdcutMail extends Mailable
{
    use Queueable, SerializesModels;

    public $order;
    public $product_link;
    public $store;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($order, $product_link, $store)
    {
        $this->order        = $order;
        $this->product_link = $product_link;
        $this->store        = $store;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('emails.productMail')->with(
            [
                'order' => $this->order,
                'product_link' => $this->product_link,
                'store' => $this->store,
            ]
        );
    }
}
