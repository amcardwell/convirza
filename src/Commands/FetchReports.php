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
                            {--start= : The date to start fetching reports}';

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

        $groups = ['@' => 'web', '#' => 'book'];

        $reportCount = $report_start->diffInMonths(now(), false) * count($groups);

        $bar = $this->output->createProgressBar($reportCount);

        foreach(array_keys($groups) as $group) {

            $report_start = $this->getStartDate();

            while($report_start->toDateString() !== now()->startOfMonth()->toDateString()) {

                $date = $report_start->copy();

                if(!$this->reportExists($date, $groups[$group])) {

                    $params = [
                        'count' => 'true',
                        'filtertype' => 'ha',
                        'filter' => 'c.campaign_name,ILIKE,' . $group . ',',
                        'report' => 'group_activity',
                        'limit' => '100',
                        'timezone' => 'America/New_York',
                        'offset' => '0',
                        'secondary' => 'campaign',
                        'start_date' => $date->startOfMonth()->toDateString(),
                        'end_date' => $date->endOfMonth()->toDateString(),
                    ];

                    $report = Convirza::getReport($params);

                    $this->saveReport($report, $date, $groups[$group]);

                }

                $bar->advance();

                $report_start->addMonth();
            }
        }

        $bar->finish();
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

    private function reportExists($date, $group): bool
    {
        return ConvirzaReport::where('report_group', $group)
                            ->where('start_date', $date->startOfMonth()->toDateString())
                            ->where('end_date', $date->endOfMonth()->toDateString())
                            ->exists();
    }

    private function saveReport($report, $date, $group)
    {
        $model = new ConvirzaReport($report);

        $model->fill([
            'report_group' => $group,
            'start_date' => $date->startOfMonth()->toDateString(),
            'end_date' => $date->endOfMonth()->toDateString(),
            'created_at' => now()
        ]);

        $model->save();
    }
}
