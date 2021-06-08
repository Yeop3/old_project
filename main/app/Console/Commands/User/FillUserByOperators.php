<?php

namespace App\Console\Commands\User;

use App\Models\Operator;
use App\Services\Operator\User\CreateUserOperatorCommand;
use App\Services\Operator\User\UserOperatorDto;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

/**
 * Class FillUserByOperators.
 */
class FillUserByOperators extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'operator:fill-users';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @param CreateUserOperatorCommand $createUserOperatorCommand
     *
     * @return int
     */
    public function handle(CreateUserOperatorCommand $createUserOperatorCommand): int
    {
        Operator::chunk(100, function (Collection $collection) use ($createUserOperatorCommand) {
            $collection->each(function (Operator $operator) use ($createUserOperatorCommand) {
                if (!$operator->user_id) {
                    $user = $createUserOperatorCommand->execute($operator->seller, new UserOperatorDto(
                        $operator->name,
                        bcrypt('secret'),
                        Str::slug($operator->name).'@'.$operator->seller->domain
                    ));
                    $this->line(Str::slug($operator->name).'@'.$operator->seller->domain);

                    $operator->user_id = $user->id;
                    $operator->save();
                }
            });
        });

        return 0;
    }
}
