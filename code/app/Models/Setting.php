<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Setting extends Model
{
    protected $fillable = [
        'key',
        'value',
        'type',
        'description'
    ];

    /**
     * Get a setting value by key
     */
    public static function get(string $key, $default = null)
    {
        $setting = static::where('key', $key)->first();

        if (!$setting) {
            return $default;
        }

        return static::castValue($setting->value, $setting->type);
    }

    /**
     * Set a setting value
     */
    public static function set(string $key, $value, string $type = 'string', string $description = null): void
    {
        $setting = static::where('key', $key)->first();

        if ($setting) {
            $setting->update([
                'value' => is_array($value) ? json_encode($value) : $value,
                'type' => $type,
                'description' => $description ?? $setting->description
            ]);
        } else {
            static::create([
                'key' => $key,
                'value' => is_array($value) ? json_encode($value) : $value,
                'type' => $type,
                'description' => $description
            ]);
        }
    }

    /**
     * Cast value based on type
     */
    protected static function castValue($value, string $type)
    {
        return match($type) {
            'boolean' => (bool) $value,
            'json' => json_decode($value, true),
            'integer' => (int) $value,
            'float' => (float) $value,
            default => $value
        };
    }

    /**
     * Get university logo path
     */
    public static function getUniversityLogo(): ?string
    {
        $logoPath = static::get('university_logo');

        if ($logoPath && Storage::disk('public')->exists($logoPath)) {
            return Storage::url($logoPath);
        }

        return null;
    }

    /**
     * Get university information
     */
    public static function getUniversityInfo(): array
    {
        return [
            'name_ar' => static::get('university_name_ar', 'جامعة أحمد بوڤرة - بومرداس'),
            'name_fr' => static::get('university_name_fr', "Université M'Hamed BOUGARA - Boumerdes"),
            'faculty_ar' => static::get('faculty_name_ar', 'كلية العلوم'),
            'faculty_fr' => static::get('faculty_name_fr', 'Faculté des Sciences'),
            'department_ar' => static::get('department_name_ar', 'قسم الاعلام الآلي'),
            'department_fr' => static::get('department_name_fr', 'Département : Informatique'),
            'ministry_ar' => static::get('ministry_name_ar', 'وزارة التعليم العالي و البحث العلمي'),
            'ministry_fr' => static::get('ministry_name_fr', "Ministère de l'Enseignement Supérieur et de la Recherche Scientifique"),
            'republic_ar' => static::get('republic_name_ar', 'الجمهورية الجزائرية الديمقراطية الشعبية'),
            'republic_fr' => static::get('republic_name_fr', 'RÉPUBLIQUE ALGÉRIENNE DÉMOCRATIQUE ET POPULAIRE'),
        ];
    }
}
