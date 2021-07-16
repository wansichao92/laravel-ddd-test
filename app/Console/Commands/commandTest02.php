<?php

namespace App\Console\Commands;

use App\Domains\User\Domain\Models\User\CommonUser;
use Illuminate\Console\Command;
use App\Domains\User\Base\Repository\UserRepository;

class commandTest02 extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:test02 {data?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '计划任务测试02';

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
        \Log::channel('commandtest')->info(['计划任务测试02start', time()]);

        $commonUser = new CommonUser();
        $commonUser->setName('任务添加');
        $commonUser->setPassword('commandpassword');
        $commonUser->setStatus('ON');
        $commonUser->setCreatedAt(new \DateTime());
        $commonUser->setUpdatedAt(new \DateTime());

        $userRepository = new UserRepository;
        $userRepository->create($commonUser);
        $userRepository->flush();

        \Log::channel('commandtest')->info(['计划任务测试02end', time()]);
    }
}
