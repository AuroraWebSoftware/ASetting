<?php

use AuroraWebSoftware\ASetting\Exceptions\GroupNotFoundException;
use AuroraWebSoftware\ASetting\Exceptions\MissingGroupException;
use AuroraWebSoftware\ASetting\Exceptions\NullArrayException;
use AuroraWebSoftware\ASetting\Exceptions\NullStringException;
use AuroraWebSoftware\ASetting\Exceptions\SettingAlreadyExistsException;
use AuroraWebSoftware\ASetting\Exceptions\SettingNotFoundException;
use AuroraWebSoftware\ASetting\Facades\ASetting;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Artisan;
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
        $table->unique(['group', 'key']); // Bu satır eklenmiştir.

        $table->timestamps();
    });

    \AuroraWebSoftware\ASetting\Tests\Models\ASetting::create([
        'id' => 1,
        'group' => 'general',
        'type' => 'integer',
        'key' => 'date',
        'value' => 1556,
        'title' => 'Title 1',
        'is_visible' => false,
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

});

// group
it('group can call the function and return an instance of the class', function () {
    $result = \AuroraWebSoftware\ASetting\Facades\ASetting::group('magento');
    $this->assertInstanceOf(AuroraWebSoftware\ASetting\ASetting::class, $result);
});

it('does not accept an empty string as group name for group method', function () {
    expect(function () {
        \AuroraWebSoftware\ASetting\Facades\ASetting::group();
    })->toThrow(ArgumentCountError::class);
});

it('does not accept an null string as group name for group method', function () {
    expect(function () {
        \AuroraWebSoftware\ASetting\Facades\ASetting::group('');
    })->toThrow(NullStringException::class);
});

it('prepares the model query correctly for group method', function () {
    $setting = \AuroraWebSoftware\ASetting\Facades\ASetting::group('magento');

    expect($setting->model)->toBeInstanceOf(Builder::class);
});

it('sets the group name correctly for group method', function () {
    $setting = \AuroraWebSoftware\ASetting\Facades\ASetting::group('magento');

    expect($setting->group)->toBe('magento');
});

it('throws exception if key is empty for group method', function () {
    expect(function () {
        \AuroraWebSoftware\ASetting\Facades\ASetting::group();
    })->toThrow(ArgumentCountError::class);
});

//groups
it('sets the groups correctly for groups method', function () {
    $groups = ['magento', 'wordpress'];
    $setting = \AuroraWebSoftware\ASetting\Facades\ASetting::groups($groups);

    expect($setting->group)->toBe($groups);
});

it('does not accept an empty array as groups for groups method', function () {
    expect(function () {
        \AuroraWebSoftware\ASetting\Facades\ASetting::groups([]);
    })->toThrow(NullArrayException::class);
});

it('throws exception if key is empty for groups method', function () {
    expect(function () {
        \AuroraWebSoftware\ASetting\Facades\ASetting::groups();
    })->toThrow(ArgumentCountError::class);
});

it('prepares the model query correctly for groups method', function () {
    $setting = \AuroraWebSoftware\ASetting\Facades\ASetting::groups(['magento']);
    expect($setting->model)->toBeInstanceOf(Builder::class);
});

it('groups can call the function and return an instance of the class for groups method', function () {
    $result = \AuroraWebSoftware\ASetting\Facades\ASetting::groups(['magento']);
    $this->assertInstanceOf(AuroraWebSoftware\ASetting\ASetting::class, $result);
});

//getValue
it('throws exception if group function is not called before getValue for getValue method', function () {
    expect(function () {
        \AuroraWebSoftware\ASetting\Facades\ASetting::getValue('some_key');
    })->toThrow(MissingGroupException::class);
});

it('throws exception if key is null for getValue method', function () {
    $setting = \AuroraWebSoftware\ASetting\Models\ASetting::create([
        'group' => 'test_group',
        'key' => 'test_key',
        'value' => '123',
        'type' => 'integer',
        'title' => 'Title 1',
        'is_visible' => true,
    ]);

    expect(function () {
        \AuroraWebSoftware\ASetting\Facades\ASetting::group('test_group')->getValue('');
    })->toThrow(NullStringException::class);
});

it('throws exception if key is not found for getValue method', function () {
    $setting = \AuroraWebSoftware\ASetting\Models\ASetting::create([
        'group' => 'test_group',
        'key' => 'test_key',
        'value' => '123',
        'type' => 'integer',
        'title' => 'Title 1',
        'is_visible' => true,
    ]);
    expect(function () {
        \AuroraWebSoftware\ASetting\Facades\ASetting::group('non_existent_group')->getValue('some_key_');
    })->toThrow(GroupNotFoundException::class);
});

it('throws exception if group is not found for getValue method', function () {
    expect(function () {
        \AuroraWebSoftware\ASetting\Facades\ASetting::group('non_existent_group')->getValue('some_key');
    })->toThrow(GroupNotFoundException::class);
});

it('returns correct value based on variable type for getValue method', function () {
    $setting = \AuroraWebSoftware\ASetting\Models\ASetting::create([
        'group' => 'test_group',
        'key' => 'test_key',
        'value' => '123',
        'type' => 'integer',
        'title' => 'Title 1',
        'is_visible' => true,
    ]);

    $value = \AuroraWebSoftware\ASetting\Facades\ASetting::group('test_group')->getValue('test_key');
    expect($value)->toBeInt()->toEqual(123);
});

it('correctly converts and returns value based on variable type for getValue method', function () {
    $setting = \AuroraWebSoftware\ASetting\Models\ASetting::create([
        'group' => 'test_group',
        'key' => 'test_key',
        'value' => '123',
        'type' => 'integer',
        'title' => 'Title 1',
        'is_visible' => true,
    ]);

    $value = \AuroraWebSoftware\ASetting\Facades\ASetting::group('test_group')->getValue('test_key');

    expect($value)->toBeInt()->toEqual(123);

    $settingType = $setting->type;
    switch ($settingType) {
        case 'integer':
            expect($value)->toBeInt();
            break;
        case 'string':
            expect($value)->toBeString();
            break;
        case 'boolean':
            expect($value)->toBeBool();
            break;
        case 'float':
            expect($value)->toBeFloat();
            break;
        case 'array':
            expect($value)->toBeArray();
            break;
        default:
            expect($value)->toEqual($setting->value);
            break;
    }
});

it('returns correct value based on variable type for key found in multiple groups for getValue method', function () {
    // İki farklı grupta aynı isimle kaydedilmiş iki farklı ayar oluşturalım
    $setting1 = \AuroraWebSoftware\ASetting\Models\ASetting::create([
        'group' => 'group1',
        'key' => 'test_key',
        'value' => '123',
        'type' => 'integer',
        'title' => 'Title 1',
        'is_visible' => true,
    ]);

    $setting2 = \AuroraWebSoftware\ASetting\Models\ASetting::create([
        'group' => 'group2',
        'key' => 'test_key',
        'value' => '456',
        'type' => 'integer',
        'title' => 'Title 1',
        'is_visible' => true,
    ]);

    expect(function () {
        \AuroraWebSoftware\ASetting\Facades\ASetting::groups(['group1', 'group2'])->getValue('test_key');
    })->toThrow(MissingGroupException::class);
});

//getValues
it('throws exception if group function is not called before getValues', function () {
    // group fonksiyonu çağrılmadan önce getValues fonksiyonunu çağırmak
    // MissingGroupException hatası bekleniyor
    expect(function () {
        \AuroraWebSoftware\ASetting\Facades\ASetting::getValues(['test_key']);
    })->toThrow(MissingGroupException::class);

});

it('throws exception if keys array is null or empty', function () {
    // Geçersiz (boş) anahtar dizisi ile getValues fonksiyonunu çağırmak
    // NullStringException hatası bekleniyor
    expect(function () {
        \AuroraWebSoftware\ASetting\Facades\ASetting::group('test_group')->getValues([]);
    })->toThrow(NullStringException::class);
});

it('throws exception if group is not found', function () {
    $setting = \AuroraWebSoftware\ASetting\Models\ASetting::create([
        'group' => 'test_group',
        'key' => 'test_key',
        'value' => 'test_value',
        'type' => 'string',
        'title' => 'Title 1',
        'is_visible' => true,
    ]);
    // Grup bulunamadığı durumu simüle edilir
    // SettingNotFoundException hatası bekleniyor
    expect(function () {
        \AuroraWebSoftware\ASetting\Facades\ASetting::group('non_existent_group')->getValues(['test_key']);
    })->toThrow(GroupNotFoundException::class);
});

it('returns correct values based on variable type for multiple keys found in multiple groups', function () {
    // İki farklı grupta aynı isimle kaydedilmiş iki farklı ayar oluşturulur
    $setting1 = \AuroraWebSoftware\ASetting\Models\ASetting::create([
        'group' => 'group1',
        'key' => 'test_key1',
        'value' => 123,
        'type' => 'integer',
        'title' => 'Title 1',
        'is_visible' => true,
    ]);

    $setting2 = \AuroraWebSoftware\ASetting\Models\ASetting::create([
        'group' => 'group2',
        'key' => 'test_key2',
        'value' => 456,
        'type' => 'integer',
        'title' => 'Title 1',
        'is_visible' => true,
    ]);

    $values = \AuroraWebSoftware\ASetting\Facades\ASetting::groups(['group1', 'group2'])->getValues(['test_key1', 'test_key2']);

    expect($values['group1']['test_key1'])->toBeInt()->toEqual(123);
    expect($values['group2']['test_key2'])->toBeInt()->toEqual(456);
});

it('returns correct values based on variable type for multiple keys found in group', function () {
    // İki farklı grupta aynı isimle kaydedilmiş iki farklı ayar oluşturulur
    $setting1 = \AuroraWebSoftware\ASetting\Models\ASetting::create([
        'group' => 'group1',
        'key' => 'test_key1',
        'value' => 123,
        'type' => 'integer',
        'title' => 'Title 1',
        'is_visible' => true,
    ]);

    $setting2 = \AuroraWebSoftware\ASetting\Models\ASetting::create([
        'group' => 'group1',
        'key' => 'test_key2',
        'value' => 456,
        'type' => 'integer',
        'title' => 'Title 1',
        'is_visible' => true,
    ]);

    $values = \AuroraWebSoftware\ASetting\Facades\ASetting::group('group1')->getValues(['test_key1', 'test_key2']);

    expect($values['group1']['test_key1'])->toBeInt()->toEqual(123);
    expect($values['group1']['test_key2'])->toBeInt()->toEqual(456);
});

it('throws exception if group is not found for getValues method', function () {
    // groups fonksiyonu çağrılarak bir grup seçilir. Grup bulunamadıysa
    \AuroraWebSoftware\ASetting\Facades\ASetting::group('test_group2');

    expect(function () {
        \AuroraWebSoftware\ASetting\Facades\ASetting::group('test_group2')->set('non_existent_key', 'non_existent_key');
    })->toThrow(GroupNotFoundException::class);
});

//set
it('throws exception if group function is not called before set', function () {
    // group veya groups fonksiyonları çağrılmadan set fonksiyonunu çağırmak
    // MissingGroupException hatası bekleniyor
    expect(function () {
        \AuroraWebSoftware\ASetting\Facades\ASetting::set('test_key', 'test_value');
    })->toThrow(MissingGroupException::class);
});

it('throws exception if setting is not found', function () {
    $setting = \AuroraWebSoftware\ASetting\Models\ASetting::create([
        'group' => 'test_group',
        'key' => 'test_key',
        'value' => 'test_value',
        'type' => 'string',
        'title' => 'Title 1',
        'is_visible' => true,
    ]);
    // group fonksiyonu çağrılarak bir grup seçilir
    // Var olmayan bir ayar için set fonksiyonunu çağırmak
    // SettingNotFoundException hatası bekleniyor
    expect(function () {
        \AuroraWebSoftware\ASetting\Facades\ASetting::group('test_group')->set('non_existent_key', 'test_value');
    })->toThrow(SettingNotFoundException::class);
});

it('throws exception if groups function is not called before set', function () {
    // groups fonksiyonu çağrılarak bir grup seçilir
    // Ayarı oluşturalım
    $setting = \AuroraWebSoftware\ASetting\Models\ASetting::create([
        'group' => 'test_group',
        'key' => 'test_key',
        'value' => 'old_value',
        'type' => 'string',
        'title' => 'Title 1',
        'is_visible' => true,
    ]);

    expect(function () {
        // Yeni değeri ayara atayalım
        \AuroraWebSoftware\ASetting\Facades\ASetting::groups(['test_group'])->set('test_key', 'new_value');
    })->toThrow(MissingGroupException::class);

});

it('updates setting value successfully', function () {
    // groups fonksiyonu çağrılarak bir grup seçilir
    // Ayarı oluşturalım
    $setting = \AuroraWebSoftware\ASetting\Models\ASetting::create([
        'group' => 'test_group',
        'key' => 'test_key',
        'value' => 'old_value',
        'type' => 'string',
        'title' => 'Title 1',
        'is_visible' => true,
    ]);

    // Yeni değeri ayara atayalım
    \AuroraWebSoftware\ASetting\Facades\ASetting::group('test_group')->set('test_key', 'new_value');

    // Ayarın değerinin güncellendiğini doğrulayalım
    $updatedSetting = \AuroraWebSoftware\ASetting\Models\ASetting::where('key', 'test_key')->first();
    expect($updatedSetting->value)->toEqual('new_value');
});

it('throws exception if group is not found for set method', function () {
    // groups fonksiyonu çağrılarak bir grup seçilir. Grup bulunamadıysa
    \AuroraWebSoftware\ASetting\Facades\ASetting::group('test_group2');

    expect(function () {
        \AuroraWebSoftware\ASetting\Facades\ASetting::group('test_group2')->set('non_existent_key', 'non_existent_key');
    })->toThrow(GroupNotFoundException::class);
});

//delete
it('throws exception if group function is not called before delete', function () {
    // group fonksiyonu çağrılmadan delete fonksiyonunu çağırmak
    // MissingGroupException hatası bekleniyor
    expect(function () {
        \AuroraWebSoftware\ASetting\Facades\ASetting::delete('test_key');
    })->toThrow(MissingGroupException::class);
});

it('throws exception if setting is not found for delete method', function () {
    // groups fonksiyonu çağrılarak bir grup seçilir
    $setting = \AuroraWebSoftware\ASetting\Models\ASetting::create([
        'group' => 'test_group',
        'key' => 'test_key',
        'value' => 'test_value',
        'type' => 'string',
        'title' => 'Title 1',
        'is_visible' => true,
    ]);
    \AuroraWebSoftware\ASetting\Facades\ASetting::group('test_group');

    // Var olmayan bir ayar için delete fonksiyonunu çağırmak
    // SettingNotFoundException hatası bekleniyor
    expect(function () {
        \AuroraWebSoftware\ASetting\Facades\ASetting::delete('non_existent_key');
    })->toThrow(SettingNotFoundException::class);
});

it('throws exception if group is not found for delete method', function () {
    // groups fonksiyonu çağrılarak bir grup seçilir
    \AuroraWebSoftware\ASetting\Facades\ASetting::group('test_group2');

    // Var olmayan bir ayar için delete fonksiyonunu çağırmak
    // SettingNotFoundException hatası bekleniyor
    expect(function () {
        \AuroraWebSoftware\ASetting\Facades\ASetting::group('test_group2')->delete('non_existent_key');
    })->toThrow(GroupNotFoundException::class);
});

it('deletes setting successfully', function () {
    // groups fonksiyonu çağrılarak bir grup seçilir
    \AuroraWebSoftware\ASetting\Facades\ASetting::group('test_group');

    // Ayarı oluşturalım
    $setting = \AuroraWebSoftware\ASetting\Models\ASetting::create([
        'group' => 'test_group',
        'key' => 'test_key',
        'value' => 'test_value',
        'type' => 'string',
        'title' => 'Title 1',
        'is_visible' => true,
    ]);

    // delete fonksiyonunu çağırarak ayarı sil
    $result = \AuroraWebSoftware\ASetting\Facades\ASetting::delete('test_key');

    // Ayarın başarıyla silindiğini doğrulayalım
    expect($result)->toBeTrue();
    expect(\AuroraWebSoftware\ASetting\Models\ASetting::where('key', 'test_key')->exists())->toBeFalse();
});

it('returns all settings if group or groups method is not called', function () {
    \AuroraWebSoftware\ASetting\Models\ASetting::truncate();

    // Herhangi bir grup veya gruplar belirtilmeden tüm ayarları getir
    $setting1 = \AuroraWebSoftware\ASetting\Models\ASetting::create([
        'group' => 'test_group',
        'key' => 'test_key1',
        'value' => 'test_value1',
        'type' => 'string',
        'title' => 'Title 1',
        'is_visible' => true,
    ]);

    $setting2 = \AuroraWebSoftware\ASetting\Models\ASetting::create([
        'group' => 'test_group',
        'key' => 'test_key2',
        'value' => 'test_value2',
        'type' => 'string',
        'title' => 'Title 1',
        'is_visible' => true,
    ]);

    $setting3 = \AuroraWebSoftware\ASetting\Models\ASetting::create([
        'group' => 'another_group',
        'key' => 'another_key',
        'value' => 'another_value',
        'type' => 'string',
        'title' => 'Title 1',
        'is_visible' => true,
    ]);

    // group veya groups fonksiyonları çağrılmadan tüm ayarları al
    $settings = \AuroraWebSoftware\ASetting\Facades\ASetting::all();

    // Tüm ayarların gruplarla birlikte alındığını ve doğru şekilde döndürüldüğünü kontrol et
    expect($settings)->toBeArray()->toEqual([
        'test_group' => [
            'test_key1' => 'test_value1',
            'test_key2' => 'test_value2',
        ],
        'another_group' => [
            'another_key' => 'another_value',
        ],
    ]);
});

it('returns settings for specified group if group method is called', function () {
    // Bir grup belirtildiğinde bu gruba ait tüm ayarları getir
    $setting1 = \AuroraWebSoftware\ASetting\Models\ASetting::create([
        'group' => 'test_group',
        'key' => 'test_key1',
        'value' => 'test_value1',
        'type' => 'string',
        'title' => 'Title 1',
        'is_visible' => true,
    ]);

    $setting2 = \AuroraWebSoftware\ASetting\Models\ASetting::create([
        'group' => 'test_group',
        'key' => 'test_key2',
        'value' => 'test_value2',
        'type' => 'string',
        'title' => 'Title 1',
        'is_visible' => true,
    ]);

    $setting3 = \AuroraWebSoftware\ASetting\Models\ASetting::create([
        'group' => 'another_group',
        'key' => 'another_key',
        'value' => 'another_value',
        'type' => 'string',
        'title' => 'Title 1',
        'is_visible' => true,
    ]);

    // group fonksiyonu çağrılarak bir grup belirtilir
    \AuroraWebSoftware\ASetting\Facades\ASetting::group('test_group');

    // Belirtilen gruba ait tüm ayarları al
    $settings = \AuroraWebSoftware\ASetting\Facades\ASetting::all();

    // Belirtilen gruba ait ayarların alındığını ve doğru şekilde döndürüldüğünü kontrol et
    expect($settings)->toBeArray()->toEqual([
        'test_group' => [
            'test_key1' => 'test_value1',
            'test_key2' => 'test_value2',
        ],
    ]);
});

it('returns settings grouped by group if groups method is called', function () {
    // Birden fazla grup belirtilirse bu gruplara ait tüm ayarları getir
    $setting1 = \AuroraWebSoftware\ASetting\Models\ASetting::create([
        'group' => 'test_group1',
        'key' => 'test_key1',
        'value' => 'test_value1',
        'type' => 'string',
        'title' => 'Title 1',
        'is_visible' => true,
    ]);

    $setting2 = \AuroraWebSoftware\ASetting\Models\ASetting::create([
        'group' => 'test_group1',
        'key' => 'test_key2',
        'value' => 'test_value2',
        'type' => 'string',
        'title' => 'Title 1',
        'is_visible' => true,
    ]);

    $setting3 = \AuroraWebSoftware\ASetting\Models\ASetting::create([
        'group' => 'test_group2',
        'key' => 'test_key3',
        'value' => 'test_value3',
        'type' => 'string',
        'title' => 'Title 1',
        'is_visible' => true,
    ]);

    // groups fonksiyonu çağrılarak birden fazla grup belirtilir
    \AuroraWebSoftware\ASetting\Facades\ASetting::groups(['test_group1', 'test_group2']);

    // Belirtilen gruplara ait tüm ayarları al
    $settings = \AuroraWebSoftware\ASetting\Facades\ASetting::all();

    // Belirtilen gruplara ait ayarların gruplarla birlikte alındığını ve doğru şekilde döndürüldüğünü kontrol et
    expect($settings)->toBeArray()->toEqual([
        'test_group1' => [
            'test_key1' => 'test_value1',
            'test_key2' => 'test_value2',
        ],
        'test_group2' => [
            'test_key3' => 'test_value3',
        ],
    ]);
});

//destroy
it('deletes all settings in the group if group or groups method is called', function () {
    // Bir grup belirtildiğinde bu gruba ait tüm ayarları oluştur
    $setting1 = \AuroraWebSoftware\ASetting\Models\ASetting::create([
        'group' => 'test_group',
        'key' => 'test_key1',
        'value' => 'test_value1',
        'type' => 'string',
        'title' => 'Title 1',
        'is_visible' => true,
    ]);

    $setting2 = \AuroraWebSoftware\ASetting\Models\ASetting::create([
        'group' => 'test_group',
        'key' => 'test_key2',
        'value' => 'test_value2',
        'type' => 'string',
        'title' => 'Title 1',
        'is_visible' => true,
    ]);

    // group fonksiyonu çağrılarak bir grup belirtilir
    \AuroraWebSoftware\ASetting\Facades\ASetting::group('test_group');

    // Gruba ait tüm ayarların silinip silinmediğini kontrol et
    expect(function () {
        \AuroraWebSoftware\ASetting\Facades\ASetting::destroy();
    })->not->toThrow(SettingNotFoundException::class);

    expect(\AuroraWebSoftware\ASetting\Models\ASetting::where('group', 'test_group')->exists())->toBeFalse();
});

it('deletes all settings in the groups if groups method is called', function () {
    // Birden fazla grup belirtilirse bu gruplara ait tüm ayarları oluştur
    $setting1 = \AuroraWebSoftware\ASetting\Models\ASetting::create([
        'group' => 'test_group1',
        'key' => 'test_key1',
        'value' => 'test_value1',
        'type' => 'string',
        'title' => 'Title 1',
        'is_visible' => true,
    ]);

    $setting2 = \AuroraWebSoftware\ASetting\Models\ASetting::create([
        'group' => 'test_group2',
        'key' => 'test_key2',
        'value' => 'test_value2',
        'type' => 'string',
        'title' => 'Title 1',
        'is_visible' => true,
    ]);

    // groups fonksiyonu çağrılarak birden fazla grup belirtilir
    \AuroraWebSoftware\ASetting\Facades\ASetting::groups(['test_group1', 'test_group2']);

    // Gruplara ait tüm ayarların silinip silinmediğini kontrol et
    expect(function () {
        \AuroraWebSoftware\ASetting\Facades\ASetting::destroy();
    })->not->toThrow(SettingNotFoundException::class);

    expect(\AuroraWebSoftware\ASetting\Models\ASetting::whereIn('group', ['test_group1', 'test_group2'])->exists())->toBeFalse();
});

//add
it('adds a new setting successfully', function () {
    // add metodu statik olarak çağrıldığında ve group veya groups fonksiyonları çağrılmadan çalışır
    // Bir ayar ekle
    $newSetting = ASetting::add('test_group', 'test_key', 'test_value', 'Title 1', 'string', true);

    // Ayarın doğru şekilde oluşturulup oluşturulmadığını kontrol et
    expect($newSetting)->toBeInstanceOf(\AuroraWebSoftware\ASetting\Models\ASetting::class);
    expect($newSetting->group)->toBe('test_group');
    expect($newSetting->key)->toBe('test_key');
    expect($newSetting->value)->toBe('test_value');
    expect($newSetting->type)->toBe('string');
    expect($newSetting->title)->toBe('Title 1');
    expect($newSetting->is_visible)->toBe(true);
});

it('throws exception if setting already exists', function () {
    // add metodu statik olarak çağrıldığında
    // Önceden aynı anahtar ve gruba sahip bir ayar oluştur
    \AuroraWebSoftware\ASetting\Tests\Models\ASetting::create([
        'group' => 'test_group',
        'key' => 'test_key',
        'value' => 'old_value',
        'type' => 'string',
        'title' => 'Title 1',
        'is_visible' => true,
    ]);

    // Aynı anahtar ve gruba sahip bir ayar eklemeye çalıştığında bir istisna fırlatılıp fırlatılmadığını kontrol et
    expect(function () {
        ASetting::add('test_group', 'test_key', 'new_value', 'string');
    })->toThrow(SettingAlreadyExistsException::class);
});

it('throws exception for invalid data type', function () {
    // add metodu statik olarak çağrıldığında ve group veya groups fonksiyonları çağrılmadan çalışır
    // Geçersiz bir veri türü belirtilirse bir istisna fırlatılıp fırlatılmadığını kontrol et
    expect(function () {
        ASetting::add('test_group', 'test_key', 'test_value', 'Title 1', true, 'invalid_type');
    })->toThrow(\AuroraWebSoftware\ASetting\Exceptions\InvalidArgumentException::class);
});
