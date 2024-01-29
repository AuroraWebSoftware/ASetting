<?php

namespace AuroraWebSoftware\ASetting\Facades;

use AuroraWebSoftware\ASetting\Exceptions\SettingNotFoundException;
use Illuminate\Support\Facades\Facade;

/**
 * @see \AuroraWebSoftware\ASetting\ASetting
 *
 * @method  bool|array set(string $key, string|int|bool|array $value, string|null $title = null, bool|null $isVisible = null)
 * @method  array<string> getValues(array $keys)
 * @method  int|array|bool|string|null getValue(string $key)
 * @method  bool delete(string $key)
 * @method  string getTitle(string $key)
 * @method  bool isVisible(string $key)
 * @method  array get(string $key)
 * @method  bool destroy()
 * @method static \AuroraWebSoftware\ASetting\Models\ASetting add(string $group, string $key, string|int|bool|array $value, string $title = null, string $type = 'string', bool $isVisible = null)
 * @method static array all()
 * @method static self groups(array $groups)
 * @method static self group(string $group)
 */
class ASetting extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \AuroraWebSoftware\ASetting\ASetting::class;
    }
}
