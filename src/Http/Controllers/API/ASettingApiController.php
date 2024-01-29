<?php

namespace AuroraWebSoftware\ASetting\Http\Controllers\API;

use AuroraWebSoftware\ASetting\Exceptions\SettingNotFoundException;
use AuroraWebSoftware\ASetting\Facades\ASetting;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Validator;

class ASettingApiController extends Controller
{
    public function getValue(string $group, string $key): \Illuminate\Http\JsonResponse
    {
        $validator = Validator::make(['group' => $group, 'key' => $key], [
            'group' => 'required|string',
            'key' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Geçersiz istek',
                'errors' => $validator->errors(),
            ], 400);
        }

        try {
            return response()->json([
                $key => ASetting::group($group)->getValue($key),
            ]);
        } catch (Exception|SettingNotFoundException $exception) {
            return response()->json([
                'message' => $exception->getMessage(),
            ], 500);
        }
    }

    public function getTitle(string $group, string $key): \Illuminate\Http\JsonResponse
    {
        $validator = Validator::make(['group' => $group, 'key' => $key], [
            'group' => 'required|string',
            'key' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Geçersiz istek',
                'errors' => $validator->errors(),
            ], 400);
        }

        try {
            return response()->json([
                $key => ASetting::group($group)->getTitle($key),
            ]);
        } catch (Exception|SettingNotFoundException $exception) {
            return response()->json([
                'message' => $exception->getMessage(),
            ], 500);
        }
    }

    public function isVisible(string $group, string $key): \Illuminate\Http\JsonResponse
    {
        $validator = Validator::make(['group' => $group, 'key' => $key], [
            'group' => 'required|string',
            'key' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Geçersiz istek',
                'errors' => $validator->errors(),
            ], 400);
        }

        try {
            return response()->json([
                $key => ASetting::group($group)->isVisible($key),
            ]);
        } catch (Exception|SettingNotFoundException $exception) {
            return response()->json([
                'message' => $exception->getMessage(),
            ], 500);
        }
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function get(string $group, string $key)
    {
        $validator = Validator::make(['group' => $group, 'key' => $key], [
            'group' => 'required|string',
            'key' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Geçersiz istek',
                'errors' => $validator->errors(),
            ], 400);
        }

        try {
            return ASetting::group($group)->get($key);
        } catch (Exception|SettingNotFoundException $exception) {
            return response()->json([
                'message' => $exception->getMessage(),
            ], 500);
        }
    }

    public function getValues(Request $request): \Illuminate\Http\JsonResponse
    {
        $group = $request->input('group');
        $keys = $request->input('key');

        $validator = Validator::make(['group' => $group, 'keys' => $keys], [
            'group' => 'required|string_or_array',
            'keys' => 'required|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Geçersiz istek',
                'errors' => $validator->errors(),
            ], 400);
        }

        try {
            if (is_array($group)) {
                return response()->json(
                    ASetting::groups($group)->getValues($keys)
                );
            }

            return response()->json(
                ASetting::group($group)->getValues($keys)
            );
        } catch (Exception|SettingNotFoundException $exception) {
            return response()->json([
                'message' => $exception->getMessage(),
            ], 500);
        }
    }

    public function set(Request $request): \Illuminate\Http\JsonResponse
    {
        $group = $request->input('group');
        $key = $request->input('key');
        $value = $request->input('value');
        $title = $request->input('title');
        $isVisible = $request->input('is_visible');

        $validator = Validator::make($request->all(), [
            'group' => 'required|string',
            'key' => 'required|string',
            'value' => 'required|string_or_array',
            'title' => 'nullable|string',
            'is_visible' => 'nullable|bool',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Geçersiz istek',
                'errors' => $validator->errors(),
            ], 400);
        }

        try {
            return response()->json(
                ASetting::group($group)->set($key, $value, $title, $isVisible)
            );
        } catch (Exception|SettingNotFoundException $exception) {
            return response()->json([
                'message' => $exception->getMessage(),
            ], 500);
        }
    }

    public function add(Request $request): \Illuminate\Http\JsonResponse
    {
        $group = $request->input('group');
        $key = $request->input('key');
        $value = $request->input('value');
        $type = $request->input('type');
        $title = $request->input('title');
        $isVisible = $request->input('is_visible');

        $validator = Validator::make($request->all(), [
            'group' => 'required|string',
            'key' => 'required|string',
            'value' => 'required|string_or_int_array_bool',
            'type' => 'required|string',
            'title' => 'required|string',
            'is_visible' => 'nullable|bool',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Geçersiz istek',
                'errors' => $validator->errors(),
            ], 400);
        }

        try {
            return response()->json(
                ASetting::add($group, $key, $value, $title, $type, $isVisible)
            );
        } catch (Exception|SettingNotFoundException $exception) {
            return response()->json([
                'message' => $exception->getMessage(),
            ], 500);
        }
    }

    public function all(?string $group = null): \Illuminate\Http\JsonResponse
    {
        $validator = Validator::make(['group' => $group], [
            'group' => 'sometimes|string|nullable',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Geçersiz istek',
                'errors' => $validator->errors(),
            ], 400);
        }

        try {
            if ($group != null) {
                return response()->json(
                    ASetting::group($group)->all()
                );
            }

            return response()->json(
                ASetting::all()
            );
        } catch (Exception|SettingNotFoundException $exception) {
            return response()->json([
                'message' => $exception->getMessage(),
            ], 500);
        }
    }

    public function delete(string $group, string $key): \Illuminate\Http\JsonResponse|bool
    {
        $validator = Validator::make(['group' => $group, 'key' => $key], [
            'group' => 'required|string',
            'key' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Geçersiz istek',
                'errors' => $validator->errors(),
            ], 400);
        }

        try {
            return ASetting::group($group)->delete($key);
        } catch (Exception|SettingNotFoundException $exception) {
            return response()->json([
                'message' => $exception->getMessage(),
            ], 500);
        }
    }

    public function destroy(string $group): \Illuminate\Http\JsonResponse|bool
    {
        $validator = Validator::make(['group' => $group], [
            'group' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Geçersiz istek',
                'errors' => $validator->errors(),
            ], 400);
        }

        try {
            return ASetting::group($group)->destroy();
        } catch (Exception|SettingNotFoundException $exception) {
            return response()->json([
                'message' => $exception->getMessage(),
            ], 500);
        }
    }
}
