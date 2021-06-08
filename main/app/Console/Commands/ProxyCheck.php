<?php

namespace App\Console\Commands;

use App\Models\Proxy;
use App\Services\Proxy\CheckProxyCommand;
use App\Services\Proxy\VO\ProxyVO;
use App\VO\Ip;
use Illuminate\Console\Command;

/**
 * Class ProxyCheck.
 */
class ProxyCheck extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'proxy:check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): ?int
    {
        $proxies = Proxy::all();

        foreach ($proxies as $proxy) {
            if (app(CheckProxyCommand::class)->execute(
                new ProxyVO(
                    new Ip($proxy->ip),
                    $proxy->port,
                    $proxy->proxy_type,
                    $proxy->username,
                    $proxy->password
                )
            )) {
                $proxy->is_working = 1;
                $proxy->save();
                $this->info('working');
            } else {
                $proxy->is_working = 0;
                $proxy->save();
                $this->error('not work');
            }
        }

        return 0;
    }
}
