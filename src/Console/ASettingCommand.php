<?php

namespace AuroraWebSoftware\ASetting\Console;

use AuroraWebSoftware\ASetting\Facades\ASetting;
use Illuminate\Console\Command;

class ASettingCommand extends Command
{
    public $signature = 'asetting {group=null} {key=null}';

    public $description = 'My command';

    public function handle(): array|string|int|bool|null
    {
        $group = $this->argument('group');
        $key = $this->argument('key');

        try {
            if ($group != 'null' && $key != 'null') {
                return ASetting::group($group)->getValue($key);
            }
            if ($group != 'null' && $key == 'null') {
                return ASetting::group($group)->all();
            }

            if ($group == 'null') {
                return ASetting::all();
            }

            return null;
        } catch (\Exception $exception) {
            return $exception->getMessage();
        }
    }
}
