<?php

namespace App\Support;

/** Stable colours and initials for roles and users on the admin pages. */
class RoleStyle
{
    private const ROLES = [
        'admin' => '#f87171',
        'department_manager' => '#fbbf24',
        'department_supervisor' => '#fbbf24',
        'surveillance_supervisor' => '#a78bfa',
        'production_supervisor' => '#38bdf8',
        'mpfm_supervisor' => '#22d3ee',
        'mpfm_operator' => '#34d399',
        'operator' => '#34d399',
        'job_officer' => '#f472b6',
        'reservoir_engineer' => '#fb923c',
    ];

    private const AVATARS = [
        'linear-gradient(135deg,#3b82f6,#22d3ee)', 'linear-gradient(135deg,#f59e0b,#fbbf24)', 'linear-gradient(135deg,#a78bfa,#f472b6)',
        'linear-gradient(135deg,#10b981,#34d399)', 'linear-gradient(135deg,#f87171,#fb923c)', 'linear-gradient(135deg,#38bdf8,#818cf8)',
    ];

    public static function color(string $role): string
    {
        if (isset(self::ROLES[$role])) {
            return self::ROLES[$role];
        }
        $pal = ['#60a5fa', '#34d399', '#fbbf24', '#f472b6', '#a78bfa', '#22d3ee', '#fb923c'];

        return $pal[crc32($role) % count($pal)];
    }

    public static function label(string $role): string
    {
        return ucwords(str_replace('_', ' ', $role));
    }

    public static function avatar(int|string $seed): string
    {
        return self::AVATARS[abs((int) crc32((string) $seed)) % count(self::AVATARS)];
    }

    public static function initials(?string $name): string
    {
        $i = collect(preg_split('/\s+/', trim((string) $name)))->filter()->take(2)->map(fn ($w) => mb_strtoupper(mb_substr($w, 0, 1)))->implode('');

        return $i ?: 'U';
    }

    /** Group a permission name by its verb: view_users -> "view" */
    public static function group(string $permission): string
    {
        $v = explode('_', $permission)[0];

        return in_array($v, ['view', 'create', 'edit', 'delete', 'manage'], true) ? $v : 'other';
    }
}
