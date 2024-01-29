<?php

namespace AuroraWebSoftware\ASetting\Tests\Models;

use App\Models\AStart\OrganizationScope;
use AuroraWebSoftware\AAuth\Facades\AAuth;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class ASetting extends Model
{
    protected $table = 'asettings';

    protected function value(): Attribute
    {
        return Attribute::make(
            get: function ($value) {
                return json_decode($value)[0];
            },
            set: function ($value) {
                return json_encode([$value]);
        }
        );
    }

    protected $guarded = [];
}
