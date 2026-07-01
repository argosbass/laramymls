<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use App\Mail\RossPriceReportMail;
use App\Filament\Pages\RossExternalPriceReport;

class SendRossPriceReport extends Command
{
    protected $signature = 'ross:send-price-report';
    protected $description = 'Send ROSS price report by email';

    public function handle(): int
    {
        $report = new RossExternalPriceReport();
        $report->loadRows();

        Mail::to('oceansurfandsun2@gmail.com')
       //  Mail::to('argosbass@gmail.com')
            ->send(new RossPriceReportMail($report->rows));

        $this->info('ROSS price report sent.');

        return self::SUCCESS;
    }
}
