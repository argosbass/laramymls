<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;

class RossPriceReportMail extends Mailable
{
    public array $rows;

    public function __construct(array $rows)
    {
        $this->rows = $rows;
    }

    public function build()
    {
        return $this
            ->subject('ROSS Price Report')
            ->view('emails.ross-price-report')
            ->with([
                'rows' => $this->rows,
            ]);
    }
}
