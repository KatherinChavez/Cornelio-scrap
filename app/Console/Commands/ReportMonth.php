<?php

namespace App\Console\Commands;

use App\Models\Company;
use Illuminate\Console\Command;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;

class ReportMonth extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Cornelio:ReportMes';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Link del reporte mensual';
    public $subject;

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
        $start_time = Carbon::now()->subMonths(1);
        $end_time = Carbon::now();

        $start_time_for_query = Carbon::parse($start_time);
        $end_time_for_query = Carbon::parse($end_time);
        $start_time_for_query=date_format($start_time_for_query,"Y-m-d");
        $end_time_for_query=date_format($end_time_for_query,"Y-m-d");

        $x=base64_encode($start_time_for_query);
        $y=base64_encode($end_time_for_query);

        $comp = Company::All();

        foreach ($comp as $c) {
            $email = $c->emailCompanies;

            $company_encry=base64_encode($c->id);
            $url_app=env("APP_URL");

            $link = "{$url_app}SendReportLink/{$company_encry}/$x/$y";
            $this->subject="Reporte mensual con corte de $start_time_for_query al $end_time_for_query ";
            $data=array(
                'link'=>$link,
                'fecha1'=>$start_time_for_query,
                'fecha2'=>$end_time_for_query,
                'tipo'=>2,
            );
            Mail::send('Notification.Month', $data, function ($message) use ($email) {
                $message->to($email)->subject($this->subject);
                $message->cc('hosmara@publicidadweb.cr')->subject($this->subject);
            });
        }
        dd(['Notificacion mes']);
    }
}
