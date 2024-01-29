<?php

use AuroraWebSoftware\ASetting\Facades\ASetting;
use AuroraWebSoftware\ASetting\Http\Controllers\API\ASettingApiController;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;

beforeEach(function () {
    Artisan::call('migrate:fresh');
    Schema::dropAllTables();
    Schema::create('asettings', function (Blueprint $table) {
        $table->id();

        $table->string('group')->default('general');
        $table->enum('type', ['string', 'integer', 'boolean', 'json', 'array', 'date'])->default('string');
        $table->string('key')->nullable(false);
        $table->jsonb('value')->nullable(false);
        $table->string('title')->nullable(false);
        $table->boolean('is_visible')->default(true);

        $table->timestamps();
    });

    \AuroraWebSoftware\ASetting\Tests\Models\ASetting::create([
        'id' => 1,
        'group' => 'general',
        'type' => 'integer',
        'key' => 'date',
        'value' => 1556,
        'title' => 'Title 1',
        'is_visible' => true,
    ]);

    \AuroraWebSoftware\ASetting\Tests\Models\ASetting::create([
        'id' => 2,
        'group' => 'magento',
        'type' => 'string',
        'key' => 'api_key',
        'value' => 'asd',
        'title' => 'Title 1',
        'is_visible' => true,
    ]);

    \AuroraWebSoftware\ASetting\Tests\Models\ASetting::create([
        'id' => 3,
        'group' => 'mikro',
        'type' => 'string',
        'key' => 'api_key_',
        'value' => 'asdddd',
        'title' => 'Title 1',
        'is_visible' => true,
    ]);

    \AuroraWebSoftware\ASetting\Tests\Models\ASetting::create([
        'id' => 4,
        'group' => 'mikro',
        'type' => 'boolean',
        'key' => 'keyy',
        'value' => true,
        'title' => 'Title 1',
        'is_visible' => true,
    ]);

    Route::middleware(array_merge([\AuroraWebSoftware\ASetting\Http\Middleware\BearerTokenAuth::class], config('asetting.api_middleware')))->group(function () {
        Route::get('/api/asetting/getValue/{group}/{key}', [ASettingApiController::class, 'getValue'])->name('asetting.getValue');
        Route::get('/api/asetting/getTitle/{group}/{key}', [ASettingApiController::class, 'getTitle'])->name('asetting.getTitle');
        Route::get('/api/asetting/get/{group}/{key}', [ASettingApiController::class, 'get'])->name('asetting.get');
        Route::get('/api/asetting/isVisible/{group}/{key}', [ASettingApiController::class, 'isVisible'])->name('asetting.isVisible');
        Route::delete('/api/asetting/delete/{group}/{key}', [ASettingApiController::class, 'delete'])->name('asetting.delete');
        Route::delete('/api/asetting/destroy/{group}', [ASettingApiController::class, 'destroy'])->name('asetting.destroy');
        Route::get('/api/asetting/all/{group?}', [ASettingApiController::class, 'all'])->name('asetting.all');
        Route::post('/api/asetting/getValues', [ASettingApiController::class, 'getValues'])->name('asetting.getValues');
        Route::post('/api/asetting/add', [ASettingApiController::class, 'add'])->name('asetting.add');
        Route::put('/api/asetting/set', [ASettingApiController::class, 'set'])->name('asetting.set');
    });
    Artisan::command('asetting {group=null} {key=null}', function () {
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
        } catch (\Exception $exception) {
            return $exception->getMessage();
        }
    });
});

it('get getValue api unauthorized token', function () {
    $response = $this->withHeaders([
        'Authorization' => 'Bearer ',
    ])->get('/api/asetting/getValue/group1/api_key');

    expect($response->json())->toBeArray()->toEqual([
        'message' => 'Unauthorized',
    ]);
});

it('get getValue api invalid token', function () {
    $response = $this->withHeaders([
        'Authorization' => 'Bearer asda',
    ])->get('/api/asetting/getValue/group1/api_key');

    expect($response->json())->toBeArray()->toEqual([
        'message' => 'Invalid Token',
    ]);
});

it('get getValue api invalid group', function () {
    $response = $this->withHeaders([
        'Authorization' => 'Bearer YOUR_BEARER_TOKEN_HERE',
    ])->get('/api/asetting/getValue/group1/api_key');

    expect($response->json())->toBeArray()->toEqual([
        'message' => 'Group Not Found!',
    ]);
});

it('get getValue api invalid key', function () {
    $response = $this->withHeaders([
        'Authorization' => 'Bearer YOUR_BEARER_TOKEN_HERE',
    ])->get('/api/asetting/getValue/general/asd');
    expect($response->json())->toBeArray()->toEqual([
        'message' => 'Setting Not Found!',
    ]);
});

it('get getValue api success response', function () {
    \AuroraWebSoftware\ASetting\Tests\Models\ASetting::create([
        'group' => 'group1',
        'type' => 'string',
        'key' => 'api_key1',
        'value' => 'value1',
        'title' => 'Title 1',
        'is_visible' => true,
    ]);
    $response = $this->withHeaders([
        'Authorization' => 'Bearer YOUR_BEARER_TOKEN_HERE',
    ])->get('/api/asetting/getValue/group1/api_key1');
    expect($response->json())->toBeArray()->toEqual([
        'api_key1' => 'value1',
    ]);
});

it('get getTitle api success response', function () {
    \AuroraWebSoftware\ASetting\Tests\Models\ASetting::create([
        'group' => 'group1',
        'type' => 'string',
        'key' => 'api_key1',
        'value' => 'value1',
        'title' => 'Title 1',
        'is_visible' => true,
    ]);
    $response = $this->withHeaders([
        'Authorization' => 'Bearer YOUR_BEARER_TOKEN_HERE',
    ])->get('/api/asetting/getTitle/group1/api_key1');
    expect($response->json())->toBeArray()->toEqual([
        'api_key1' => 'Title 1',
    ]);
});

it('get isVisible api success response', function () {
    \AuroraWebSoftware\ASetting\Tests\Models\ASetting::create([
        'group' => 'group1',
        'type' => 'string',
        'key' => 'api_key1',
        'value' => 'value1',
        'title' => 'Title 1',
        'is_visible' => true,
    ]);
    $response = $this->withHeaders([
        'Authorization' => 'Bearer YOUR_BEARER_TOKEN_HERE',
    ])->get('/api/asetting/isVisible/group1/api_key1');
    expect($response->json())->toBeArray()->toEqual([
        'api_key1' => true,
    ]);
});

it('put set api success', function () {
    \AuroraWebSoftware\ASetting\Tests\Models\ASetting::create([
        'group' => 'group1',
        'type' => 'string',
        'key' => 'api_key1',
        'value' => 'value1',
        'title' => 'Title 1',
        'is_visible' => true,
    ]);
    $response = $this->withHeaders([
        'Authorization' => 'Bearer YOUR_BEARER_TOKEN_HERE',
    ])->put('/api/asetting/set', [
        'group' => 'group1',
        'key' => 'api_key1',
        'value' => 'changed_value',
        'title' => 'Changed Title',
        'is_visible' => false,
    ]);

    $setting = \AuroraWebSoftware\ASetting\Models\ASetting::where([
        'group' => 'group1',
        'key' => 'api_key1',
    ])->first();

    expect($setting->group)->toBe('group1');
    expect($setting->key)->toBe('api_key1');
    expect($setting->value)->toBe('changed_value');
    expect($setting->title)->toBe('Changed Title');
    expect($setting->is_visible)->toBe(0);
});

it('post getValue api invalid group', function () {
    \AuroraWebSoftware\ASetting\Tests\Models\ASetting::create([
        'group' => 'group1',
        'type' => 'string',
        'key' => 'api_key1',
        'value' => 'value1',
        'title' => 'Title 1',
        'is_visible' => true,
    ]);
    \AuroraWebSoftware\ASetting\Tests\Models\ASetting::create([
        'group' => 'group1',
        'type' => 'string',
        'key' => 'api_key2',
        'value' => 'value2',
        'title' => 'Title 1',
        'is_visible' => true,
    ]);
    $response = $this->withHeaders([
        'Authorization' => 'Bearer YOUR_BEARER_TOKEN_HERE',
    ])->post('/api/asetting/getValues', [
        'group' => 'group1',
        'key' => [
            'api_key1',
            'api_key2',
            'api_value',
        ],
    ]);

    expect($response->json())->toBeArray()->toEqual(
        ['group1' => [
            'api_key1' => 'value1',
            'api_key2' => 'value2',
        ]]);
});

//all
it('get all settings without specifying group', function () {
    $response = $this->withHeaders([
        'Authorization' => 'Bearer YOUR_BEARER_TOKEN_HERE',
    ])->get('/api/asetting/all');

    expect($response->status())->toBe(200);
    expect($response->json())->toBeArray()->not()->toBeEmpty();
});

it('get all settings for a specific group', function () {
    $response = $this->withHeaders([
        'Authorization' => 'Bearer YOUR_BEARER_TOKEN_HERE',
    ])->get('/api/asetting/all/general');

    expect($response->status())->toBe(200);
    expect($response->json())->toBeArray()->not()->toBeEmpty();
});

it('get all settings for a non-existent group', function () {
    $response = $this->withHeaders([
        'Authorization' => 'Bearer YOUR_BEARER_TOKEN_HERE',
    ])->get('/api/asetting/all/nonexistentgroup');

    expect($response->json())->toBeArray()->toEqual([
        'message' => 'Group Not Found!',
    ]);
});

//add
it('add new setting', function () {
    $response = $this->withHeaders([
        'Authorization' => 'Bearer YOUR_BEARER_TOKEN_HERE',
    ])->post('/api/asetting/add', [
        'group' => 'general',
        'key' => 'new_key',
        'value' => 'new_value',
        'type' => 'string',
        'title' => 'Title 1',
        'is_visible' => false,
    ]);

    expect($response->status())->toBe(200);
    expect($response->json())->toBeArray()->toHaveKeys(['id', 'group', 'key', 'value', 'title', 'is_visible', 'type']);
});

it('add new setting with invalid data', function () {
    $response = $this->withHeaders([
        'Authorization' => 'Bearer YOUR_BEARER_TOKEN_HERE',
    ])->post('/api/asetting/add', [
        'group' => 'general',
        'key' => 'new_key',
        'value' => 'new_value',
        'type' => 'invalid_type', // invalid type
    ]);

    expect($response->status())->toBe(400);
    expect($response->json())->toHaveKey('errors');
});

it('add new setting with missing required fields', function () {
    $response = $this->withHeaders([
        'Authorization' => 'Bearer YOUR_BEARER_TOKEN_HERE',
    ])->post('/api/asetting/add', [
        'group' => 'general',
        'value' => 'new_value', // missing 'key' field
        'type' => 'string',
    ]);

    expect($response->status())->toBe(400);
    expect($response->json())->toHaveKey('errors');
});

//delete
it('delete existing setting', function () {
    \AuroraWebSoftware\ASetting\Tests\Models\ASetting::create([
        'group' => 'group1',
        'type' => 'string',
        'key' => 'api_key1',
        'value' => 'value1',
        'title' => 'Title 1',
        'is_visible' => true,
    ]);
    $response = $this->withHeaders([
        'Authorization' => 'Bearer YOUR_BEARER_TOKEN_HERE',
    ])->delete('/api/asetting/delete/group1/api_key1');

    expect($response->status())->toBe(200);
    expect($response->json())->toBe(1);
});

it('delete non-existing setting', function () {
    $response = $this->withHeaders([
        'Authorization' => 'Bearer YOUR_BEARER_TOKEN_HERE',
    ])->delete('/api/asetting/delete/general/nonexistent_key');

    expect($response->json())->toBe([
        'message' => 'Setting Not Found!',
    ]);

});

it('delete setting with invalid group', function () {
    $response = $this->withHeaders([
        'Authorization' => 'Bearer YOUR_BEARER_TOKEN_HERE',
    ])->delete('/api/asetting/delete/invalid_group/new_key');

    expect($response->json())->toBe([
        'message' => 'Group Not Found!',
    ]);
});

it('delete setting with invalid key', function () {
    $response = $this->withHeaders([
        'Authorization' => 'Bearer YOUR_BEARER_TOKEN_HERE',
    ])->delete('/api/asetting/delete/general/');

    expect($response->status())->toBe(404);
});

//destroy
it('destroy settings for existing group', function () {
    $response = $this->withHeaders([
        'Authorization' => 'Bearer YOUR_BEARER_TOKEN_HERE',
    ])->delete('/api/asetting/destroy/general');

    expect($response->status())->toBe(200);
    expect($response->json())->toBe(1);
});

it('destroy settings for non-existing group', function () {
    $response = $this->withHeaders([
        'Authorization' => 'Bearer YOUR_BEARER_TOKEN_HERE',
    ])->delete('/api/asetting/destroy/nonexistent_group');

    expect($response->json())->toBe([
        'message' => 'Group Not Found!',
    ]);
});
