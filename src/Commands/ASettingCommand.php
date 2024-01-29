<?php

namespace AuroraWebSoftware\ASetting\Commands;

use AuroraWebSoftware\ASetting\Facades\ASetting;
use Illuminate\Console\Command;
use Illuminate\Http\JsonResponse;

class ASettingCommand extends Command
{
    public $signature = 'asetting {group=null} {key=null}';

    public $description = 'My command';

    /**
     * @return string
     */
    public function handle()
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

            if ($group == 'null'){
                return ASetting::all();
            }
        }catch (\Exception $exception){
            return $exception->getMessage();
        }
    }
}
