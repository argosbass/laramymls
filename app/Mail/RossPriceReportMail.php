<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;

class RossPriceReportMail extends Mailable
{
    public function __construct(
        public array $rows
    ) {}

    public function build()
    {
        $csv = $this->makeCsv($this->rows);

        return $this
            ->subject('ROSS Price Report')
            ->html(view('emails.ross-price-report', [
                'rows' => $this->rows,
            ])->render())
            ->attachData($csv, 'ross-price-report-' . now()->format('Ymd_His') . '.csv', [
                'mime' => 'text/csv',
            ]);
    }

    private function makeCsv(array $rows): string
    {
        $handle = fopen('php://temp', 'r+');

        fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

        fputcsv($handle, [
            'Status',
            'MLS Property',
            'Reference Link',
            'MLS Property Status',
            'ROSS Property Status',
            'MLS Price',
            'ROSS Price',
        ]);

        foreach ($rows as $row) {
            fputcsv($handle, [
                $row['status'],
                $row['title'],
                $row['url'],
                $row['mlsPropertyStatus'],
                $row['rossPropertyStatus'],
                $row['local_price'],
                $row['external_price'],
            ]);
        }

        rewind($handle);

        $csv = stream_get_contents($handle);

        fclose($handle);

        return $csv;
    }
}
