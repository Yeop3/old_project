<?php

namespace App\Console\Commands\Settings;

use App\Models\Seller;
use App\Services\Seller\Settings\InitSellerSettingsCommand;
use Illuminate\Console\Command;

/**
 * Class SellerSettingsInit.
 */
class SellerSettingsInit extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'settings:seller:init';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * @param InitSellerSettingsCommand $command
     */
    public function handle(InitSellerSettingsCommand $command): void
    {
        $sellers = Seller::all();

        foreach ($sellers as $seller) {
            $command->execute($seller);
        }
    }
}
