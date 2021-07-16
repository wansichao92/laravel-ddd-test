<?php

namespace App\Domains\User\Command;

use Illuminate\Console\Command;

class commandTest01 extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:test01 {id?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '计划任务测试01';

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
     * @return int
     */
    public function handle()
    {
        $id = $this->argument('id');
        if($id){
            \Log::channel('commandtest')->info([$id, '计划任务测试01', time()]);
        }else{
            \Log::channel('commandtest')->info(['计划任务测试01', time()]);
        }
    }
}
