<?php

namespace Skidaatl\Convirza\Commands;

use Illuminate\Console\Command;
use Convirza;
use Skidaatl\Convirza\ConvirzaReport;

class FetchReports extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'convirza:fetchReports
                            {--start= : The date to start fetching reports}
                            {--limit=100 : Limit the returned results}
                            {--duration=30 : Filter by call duration}
                            {--trackingNumber= : Filter by tracking number}
                            {--groupId= : Filter the results by Group ID}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch monthly convirza reports and save them to the database';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $report_start = $this->getStartDate();

        while($report_start->lessThanOrEqualTo(now()->startOfMonth())) {

            $date = $report_start->copy();

            $params = [
                'filter' => sprintf('call_started>=%s,call_started<=%s,duration>=%d',
                    $date->startOfMonth()->toDateString(),
                    $date->endOfMonth()->toDateString(),
                    $this->option('duration')
                ),
                'limit' => $this->option('limit'),
                'timezone' => 'America/New_York',
                'offset' => '0',
                'secondary' => 'campaign',
            ];

            if($this->option('groupId')) {
                $params['filter'] .= ',group_id='.$this->option('groupId');
            }

            if($this->option('trackingNumber')) {
                $params['filter'] .= ',tracking_number='.$this->option('trackingNumber');
            }

            $report = Convirza::getReport($params);

            $report_start->addMonth();
        }

        dd($report);
    }

    public function getStartDate()
    {
        if(!$this->option('start')) {
            $start_date = \Carbon\Carbon::now()->startOfMonth();
        } else {
            $start_date = \Carbon\Carbon::parse($this->option('start'))->startOfMonth();
        }

        return $start_date;
    }
}
