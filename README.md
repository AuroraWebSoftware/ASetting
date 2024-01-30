# ASetting Laravel Package
![A2AD6A93-7ED8-4793-BB24-F9CEDDDD45F4-21632-000042AB3BFCB4B4.png](assets%2FA2AD6A93-7ED8-4793-BB24-F9CEDDDD45F4-21632-000042AB3BFCB4B4.png)![A2AD6A93-7ED8-4793-BB24-F9CEDDDD45F4-21632-000042AB3BFCB4B4.png](..%2F..%2F..%2FDownloads%2FA2AD6A93-7ED8-4793-BB24-F9CEDDDD45F4-21632-000042AB3BFCB4B4.png)



## Introduction
ASetting is a Laravel package that allows you to manage settings dynamically in your Laravel applications. With this package, you can organize settings in groups stored in the database, supporting various types of settings for easy management.
## Features
-  Introducing an interactive blade page where users can view and modify application settings. With this feature, users will be able to customize the behavior and preferences of the application according to their needs.
## Installation

You can add the ASetting package to your Laravel project by following the steps below.

### Installation via Composer

Add the package to your project using Composer.

```bash
composer require aurorawebsoftware/asetting
```

### Publish Configuration

To publish the configuration file, run the following command:

```bash
php artisan vendor:publish --tag=asetting-config
```

This will add the `config/asetting.php` file to your project, which contains the configuration settings for the ASetting package.

### Migration

Run the migration to create the database table used to store settings:

```bash
php artisan migrate
```

## Usage

### ASetting Facade

The ASetting Facade allows you to easily manage settings. Below are some basic functions and features provided by the Facade.

#### `group(string $group): self`

Used to work with settings in a specific group.

```php
ASetting::group('site')->getValue('title');
```

#### `groups(array $groups): self`

Used to work with settings in multiple groups.

```php
ASetting::groups(['site', 'mail'])->all();
```

#### `getValue(string $key): int|array|bool|string|null|SettingNotFoundException`

Used to get the value of a specific setting.

```php
ASetting::group('site')->getValue('title');
```

#### `getTitle(string $key): string`

Used to get the title of a specific setting.

```php
ASetting::group('site')->getTitle('title');
```

#### `isVisible(string $key): bool`

Used to check the visibility status of a specific setting.

```php
ASetting::group('site')->isVisible('title');
```

#### `get(string $key): array`

Used to get all information about a specific setting.

```php
ASetting::group('site')->get('title');
```

#### `getValues(array $keys): SettingNotFoundException|array`

Used to get the values of a specific setting.

```php
ASetting::group('site')->getValues(['title','name']);
```

#### `getValues(array $keys): SettingNotFoundException|array`

Used to get the values of a specific settings.

```php
ASetting::groups(['site','general'])->getValues(['title','name']);
```

#### `set(string $key, string|int|bool|array $value, string|null $title = null, bool|null $isVisible = null): bool|array`

Used to update or create the value of a specific setting.

```php
ASetting::group('site')->set('title', 'New Title', 'Site Title', true);
```

#### `delete(string $key): bool|SettingNotFoundException`

Used to delete a specific setting.

```php
ASetting::group('site')->delete('title');
```

#### `all(?string $group = null): array`

Used to get all settings grouped by groups or under a specific group.

```php
ASetting::all(); // Get all settings
ASetting::group('site')->all(); // Get all settings under a specific group
```

#### `destroy(string $group): bool|SettingNotFoundException`

Used to delete all settings under a specific group.

```php
ASetting::group('site')->destroy();
ASetting::groups(['site','general'])->destroy();
```

#### `add(string $group, string $key, string|int|bool|array $value, string $title = null, string $type = 'string', bool $isVisible = null): ASettingModel|SettingNotFoundException`

Used to add a new setting.

```php
ASetting::add('site', 'new_setting', 'Value', 'Setting Title', 'string', true);
```

## API Usage
### Config
You can define tokens for APIs. You can also configure it by adding your own middleware.
```php
return [
'api_token' => ['YOUR_BEARER_TOKEN_HERE'],
'api_middleware' => [
// YourMiddlewareClass::class
]
];
```
### Endpoint Details


### getValue

- **Endpoint:** `/api/asetting/getValue/{group}/{key}`
- **Method:** GET
- **Parameters:** `group` (string), `key` (string)

### getTitle

- **Endpoint:** `/api/asetting/getTitle/{group}/{key}`
- **Method:** GET
- **Parameters:** `group` (string), `key` (string)

### isVisible

- **Endpoint:** `/api/asetting/isVisible/{group}/{key}`
- **Method:** GET
- **Parameters:** `group` (string), `key` (string)

### get

- **Endpoint:** `/api/asetting/get/{group}/{key}`
- **Method:** GET
- **Parameters:** `group` (string), `key` (string)

### getValues

- **Endpoint:** `/api/asetting/getValues`
- **Method:** POST
- **Parameters:** `group` (string|array), `keys` (array)

### set

- **Endpoint:** `/api/asetting/set`
- **Method:** PUT
- **Parameters:** `group` (string), `key` (string), `value` (string|array), `title` (string, optional), `is_visible` (bool, optional)

### add

- **Endpoint:** `/api/asetting/add`
- **Method:** POST
- **Parameters:** `group` (string), `key` (string), `value` (string|array|bool|int), `type` (string), `title` (string), `is_visible` (bool, optional)

### all

- **Endpoint:** `/api/asetting/all/{group?}`
- **Method:** GET
- **Parameters:** `group` (string, optional)

### delete

- **Endpoint:** `/api/asetting/delete/{group}/{key}`
- **Method:** DELETE
- **Parameters:** `group` (string), `key` (string)

### destroy

- **Endpoint:** `/api/asetting/destroy/{group}`
- **Method:** DELETE
- **Parameters:** `group` (string)

### Error Handling

In case of invalid requests or errors, the API will return a JSON response with a corresponding message and, if applicable, validation errors.

Feel free to copy and paste this documentation into your README file. Adjust the formatting as needed.


## ASetting Command

This command allows you to interact with the ASetting package.

### Usage

php artisan asetting {group=null} {key=null}

This command allows you to interact with the ASetting package.


1. Fetch a specific setting value:

```plaintext
php artisan asetting myGroup myKey
```

2. Fetch all settings in a specific group:

```plaintext
php artisan asetting myGroup
```

3. Fetch all settings:

```plaintext
php artisan asetting
```

### Note

- If the specified group or key is not found, an exception message will be displayed.
- The command returns the setting value, which can be a string, array, or other types, based on the configuration.


## License

The ASetting package is licensed under the MIT License.

--- 

