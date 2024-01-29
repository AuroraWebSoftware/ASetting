<?php

namespace AuroraWebSoftware\ASetting\Facades;

use AuroraWebSoftware\ASetting\Exceptions\SettingNotFoundException;
use Illuminate\Support\Facades\Facade;

/**
 * @see \AuroraWebSoftware\ASetting\ASetting
 * @method set(string $key, string|int|bool|array $value, string|null $title = null, bool|null $isVisible = null): bool|array
 * @method array<string> getValues(array $keys): SettingNotFoundException|array
 * @method getValue(string $key): int|array|bool|string|null|SettingNotFoundException
 * @method delete(string $key): bool|SettingNotFoundException
 * @method getTitle(string $key): string
 * @method isVisible(string $key): bool
 * @method get(string $key): array
 * @method destroy(): bool|SettingNotFoundException
 * @method static self add(string $group, string $key, string|int|bool|array $value, string $title = null, string $type = 'string', bool $isVisible = null): \AuroraWebSoftware\ASetting\Models\ASetting|SettingNotFoundException
 * @method static self all():array
 * @method all():array
 * @method static self array<string> groups(array $groups): self
 * @method static self group(string $group): self
 * @method static self groups(array $groups): self
 *
 */
class ASetting extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \AuroraWebSoftware\ASetting\ASetting::class;
    }
}
