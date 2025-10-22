<?php

namespace App\Mail;

use App\Models\MarkingRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class MarkingRequestMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public $markingRequest;
    public $data;
    public function __construct(MarkingRequest $markingRequest, $data = [])
    {
        $this->markingRequest = $markingRequest;
        $this->data = $data;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $address = 'support@abt-assessments.com';
        $name = 'A.B.T Identity';
        $subject = 'Marking Request';
        $markingRequest = $this->markingRequest;
        $data = $this->data;
        return $this->view('emails.marking_request', compact('markingRequest', 'data'))
            ->from($address, $name)
//            ->cc('SUPPORT@ABT-ASSESSMENTS.COM', $name)
//            ->cc('relationship@abt-assessments.com', $name)
//            ->cc('Operations@abt-assessments.com', $name)
//            ->replyTo('SUPPORT@ABT-ASSESSMENTS.COM', $name)
            ->subject($subject);
    }
}
