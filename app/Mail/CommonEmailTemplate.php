<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class CommonEmailTemplate extends Mailable
{
    use Queueable, SerializesModels;

    public $template;
    public $store;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($template, $store)
    {
        $this->template = $template;
        $this->store = $store;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
       
        $from = !empty($this->store['mail_from_address']) ? $this->store['mail_from_address'] : $this->template->from;
       
        return $this->from($this->store['email'], $from)->markdown('emails.common_email_template')->subject($this->template->subject)->with(
            [
                'content' => $this->template->content,
                'mail_header' => $this->store['name'],
            ]
        );
    }
}
