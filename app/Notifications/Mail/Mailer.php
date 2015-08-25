<?php

namespace App\Notifications\Mail;

use Illuminate\Contracts\Mail\Mailer as IlluminateMailer;

class Mailer
{
    /**
     * @var IlluminateMailer
     */
    protected $mailer;

    /**
     * @param IlluminateMailer $mailer
     */
    public function __construct(IlluminateMailer $mailer)
    {
        $this->mailer = $mailer;
    }

    /**
     * Base send method
     *
     * @param            $to
     * @param            $subject
     * @param            $view
     * @param array      $data
     * @param array|null $cc
     * @param array|null $bcc
     *
     * @return mixed
     */
    public function send($to, $subject, $view, $data = [], $cc = [], $bcc = [])
    {
        return $this->mailer->queue($view, $data, function ($m) use ($to, $subject, $cc, $bcc) {
            $m->to($to);

            if ($cc) {
                $m->cc($cc);
            }

            if ($bcc) {
                $m->bcc($bcc);
            }

            $m->subject(sprintf("[%s] %s", config('project.name'), $subject));
        });
    }
}