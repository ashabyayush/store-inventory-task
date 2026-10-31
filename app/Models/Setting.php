<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $guarded = [];

    public static function get(string $key, $default = null)
    {
        return static::where('key', $key)->value('value') ?? $default;
    }

    public static function set(string $key, $value): void
    {
        static::updateOrCreate(['key' => $key], ['value' => $value]);
    }

    public static function currency(): array
    {
        $all = config('inventory.currencies');
        $code = static::get('currency', config('inventory.default_currency'));

        if (! isset($all[$code])) {
            $code = config('inventory.default_currency');
        }

        return ['code' => $code, 'symbol' => $all[$code]['symbol'], 'name' => $all[$code]['name']];
    }

    public static function symbol(): string
    {
        return static::currency()['symbol'];
    }
}