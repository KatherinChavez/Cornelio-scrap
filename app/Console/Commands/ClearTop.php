<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use App\Models\TopReaction;

class ClearTop extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Cornelio:ClearTop';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Elimina el registro de los top que se encuentran registrado en la base';

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
        $last = Carbon::now()->subMonth(1);
        $top = TopReaction::whereBetween('created_at',[ $last, Carbon::now()])->get();

        foreach ($top as $topReaction){
            $delete = TopReaction::where('id', $topReaction->id)->delete();
        }
    }
}
