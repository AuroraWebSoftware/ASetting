<?php

namespace AuroraWebSoftware\ASetting\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property array $value
 * @property string $group
 * @property string $key
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @method static find($role_id) : RoleModelAbacRule
 * @method static where(string $string, string $string1, int $id)
 * @method static destroy($ids)
 * @method update(array $attributes = [], array $options = [])
 * @method delete()
 * @method static whereIn(string $string, array $groups)
 * @method static create(array $array)
 * @method static get()
 */
class ASetting extends Model
{
    protected $table = 'asettings';

    protected $guarded = [];

    protected function value(): Attribute
    {
        return Attribute::make(
            get: function ($value) {
                return json_decode($value)[0];

            }, set: function ($value) {
                return json_encode([$value]);

            }
        );
    }
}
