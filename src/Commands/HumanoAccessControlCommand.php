<?php

namespace Idoneo\HumanoAccessControl\Commands;

use Illuminate\Console\Command;

class HumanoAccessControlCommand extends Command
{
    public $signature = 'humano-access-control';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
