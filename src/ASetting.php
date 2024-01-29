<?php

namespace AuroraWebSoftware\ASetting;

use AuroraWebSoftware\ASetting\Exceptions\GroupNotFoundException;
use AuroraWebSoftware\ASetting\Exceptions\InvalidArgumentException;
use AuroraWebSoftware\ASetting\Exceptions\MissingGroupException;
use AuroraWebSoftware\ASetting\Exceptions\NullArrayException;
use AuroraWebSoftware\ASetting\Exceptions\NullStringException;
use AuroraWebSoftware\ASetting\Exceptions\SettingAlreadyExistsException;
use AuroraWebSoftware\ASetting\Exceptions\SettingNotFoundException;
use Illuminate\Support\Facades\Validator;

class ASetting
{
    const DATA_TYPES = ['string', 'integer', 'boolean', 'array'];

    public $model;

    public $group;

    public function __construct()
    {
        $this->group = null;
        $this->model = null;
    }

    /**
     * @return $this
     *
     * @throws NullStringException
     */
    public function group(string $group): self
    {
        if ($group == null) {
            throw new NullStringException('The string cannot be null or empty.');
        }
        $this->group = $group;
        $this->model = \AuroraWebSoftware\ASetting\Models\ASetting::where('group', $group);

        return $this;
    }

    /**
     * @return $this
     *
     * @throws NullArrayException
     */
    public function groups(array $groups): self
    {
        if (count($groups) == 0) {
            throw new NullArrayException('The array cannot be null or empty.');
        }
        //todo array null olamaz
        $this->group = $groups;
        $this->model = \AuroraWebSoftware\ASetting\Models\ASetting::whereIn('group', $groups);

        return $this;

    }

    /**
     * /**
     * @throws \Throwable
     */
    public function getValue(string $key): int|array|bool|string|null|SettingNotFoundException
    {
        if ($this->group === null || is_array($this->group)) {
            throw new MissingGroupException('You must call group() function before calling getValue().');
        }

        if ($key == null) {
            throw new NullStringException('The string cannot be null or empty.');
        }

        throw_if($this->model->count() == 0, new GroupNotFoundException('Group Not Found!'));
        $setting = $this->model->where('key', $key)->first();
        throw_if($setting == null, new SettingNotFoundException('Setting Not Found!'));
        $variableType = $setting->type;

        return match ($variableType) {
            'integer' => (int) $setting->value,
            'string' => (string) $setting->value,
            'boolean' => (bool) $setting->value,
            'float' => (float) $setting->value,
            'array' => json_decode($setting->value, true),
            default => $setting->value,
        };
    }

    /**
     * @throws MissingGroupException
     * @throws NullStringException
     * @throws \Throwable
     */
    public function getTitle(string $key): string
    {
        if ($this->group === null || is_array($this->group)) {
            throw new MissingGroupException('You must call group() function before calling getTitle().');
        }

        if ($key == null) {
            throw new NullStringException('The string cannot be null or empty.');
        }

        throw_if($this->model->count() == 0, new GroupNotFoundException('Group Not Found!'));
        $setting = $this->model->where('key', $key)->first();
        throw_if($setting == null, new SettingNotFoundException('Setting Not Found!'));

        return $setting->title;
    }

    /**
     * @throws MissingGroupException
     * @throws NullStringException
     * @throws \Throwable
     */
    public function isVisible(string $key): bool
    {
        if ($this->group === null || is_array($this->group)) {
            throw new MissingGroupException('You must call group() function before calling isVisible().');
        }

        if ($key == null) {
            throw new NullStringException('The string cannot be null or empty.');
        }

        throw_if($this->model->count() == 0, new GroupNotFoundException('Group Not Found!'));
        $setting = $this->model->where('key', $key)->first();
        throw_if($setting == null, new SettingNotFoundException('Setting Not Found!'));

        return $setting->is_visible;
    }

    /**
     * @throws MissingGroupException
     * @throws NullStringException
     * @throws \Throwable
     */
    public function get(string $key): array
    {
        if ($this->group === null || is_array($this->group)) {
            throw new MissingGroupException('You must call group() function before calling get().');
        }

        if ($key == null) {
            throw new NullStringException('The string cannot be null or empty.');
        }

        throw_if($this->model->count() == 0, new GroupNotFoundException('Group Not Found!'));
        $setting = $this->model->where('key', $key)->first();
        throw_if($setting == null, new SettingNotFoundException('Setting Not Found!'));

        return [
            'group' => $setting->group,
            'key' => $setting->key,
            'value' => $setting->value,
            'title' => $setting->title,
            'is_visible' => $setting->is_visible,
            'type' => $setting->type,
            'updated_at' => $setting->updated_at,
        ];
    }

    /**
     * @throws \Throwable
     */
    public function getValues(array $keys): SettingNotFoundException|array
    {
        if ($this->group === null || (is_array($this->group) && count($this->group) == 0)) {
            throw new MissingGroupException('You must call group() or groups() function before calling getValues().');
        }

        if (count($keys) == 0) {
            throw new NullStringException('The string cannot be null or empty.');
        }

        throw_if($this->model->count() == 0, new GroupNotFoundException('Group Not Found!'));

        return $this->model->whereIn('key', $keys)->get()->groupBy('group')->map(function ($groupItems) {
            return $groupItems->pluck('value', 'key')->toArray();
        })->toArray();
    }

    /**
     * @throws MissingGroupException
     * @throws \Throwable
     */
    public function set(string $key, string|int|bool|array $value, ?string $title = null, ?bool $isVisible = null): bool|array
    {
        if ($this->group === null || is_array($this->group)) {
            throw new MissingGroupException('You must call group() function before calling set().');
        }

        throw_if($this->model->count() == 0, exception: new GroupNotFoundException('Group Not Found!'));
        $validator = Validator::make([
            'group' => $this->group,
            'key' => $key,
            'value' => $value,
            'title' => $title,
            'is_visible' => $isVisible,
        ], [
            'group' => 'required|string',
            'key' => 'required|string',
            'value' => 'required|string_or_array',
            'title' => 'nullable|string',
            'is_visible' => 'nullable|bool',
        ]);

        $setting = $this->model->where('key', $key)->first();

        throw_if($setting == null, new SettingNotFoundException('Setting Not Found!'));

        return $setting->update(
            [
                'value' => $value,
                'title' => $title != null ? $title : $setting->title,
                'is_visible' => $isVisible ?? $setting->is_visible,
            ]);
    }

    /**
     * @throws \Throwable
     */
    public function delete(string $key): bool|SettingNotFoundException
    {
        if ($this->group === null || is_array($this->group)) {
            throw new MissingGroupException('You must call group() function before calling getValue().');
        }
        throw_if($this->model->count() == 0, new GroupNotFoundException('Group Not Found!'));
        $setting = $this->model->where(
            [
                'key' => $key,
            ])->first();

        throw_if($setting == null, new SettingNotFoundException('Setting Not Found!'));

        return $setting->delete();
    }

    public function all(): array
    {
        if ($this->model != null) {
            throw_if($this->model->count() == 0, new GroupNotFoundException('Group Not Found!'));

            return $this->model->get()->groupBy('group')->map(function ($groupItems) {
                return $groupItems->pluck('value', 'key')->toArray();
            })->toArray();
        }

        return \AuroraWebSoftware\ASetting\Models\ASetting::get()->groupBy('group')->map(function ($groupItems) {
            return $groupItems->pluck('value', 'key')->toArray();
        })->toArray();
    }

    /**
     * @throws \Throwable
     */
    public function destroy(): bool|SettingNotFoundException
    {
        throw_if($this->model->count() == 0, new GroupNotFoundException('Group Not Found!'));
        $settings = $this->model;

        throw_if($settings->count() == 0, new SettingNotFoundException('Settings Not Found!'));

        return $settings->delete();
    }

    /**
     * @throws InvalidArgumentException
     * @throws \Throwable
     */
    public static function add(string $group, string $key, string|int|bool|array $value, ?string $title = null, string $type = 'string', ?bool $isVisible = null): \AuroraWebSoftware\ASetting\Models\ASetting|SettingNotFoundException
    {
        if (! in_array($type, self::DATA_TYPES)) {
            throw new InvalidArgumentException('Geçersiz veri türü belirtildi.');
        }
        $setting = \AuroraWebSoftware\ASetting\Models\ASetting::where('key', $key)->where('group', $group)->first();

        throw_if($setting != null, new SettingAlreadyExistsException('Setting Already Exists!'));

        $convertedValue = match ($type) {
            'integer' => (int) $value,
            'string' => (string) $value,
            'boolean' => (bool) $value,
            'float' => (float) $value,
            'array' => json_encode($value, 1),
            default => $value,
        };

        return \AuroraWebSoftware\ASetting\Models\ASetting::create(
            [
                'group' => $group,
                'key' => $key,
                'type' => $type,
                'value' => $convertedValue,
                'title' => $title,
                'is_visible' => $isVisible,
            ]);
    }
}
