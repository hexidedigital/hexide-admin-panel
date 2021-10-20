<?php

namespace HexideDigital\HexideAdmin\Models;


/**
 * HexideDigital\HexideAdmin\Models\Role
 *
 * @mixin \Eloquent
 * @property int $id
 * @property string $title
 * @property string|null $key
 * @property int $admin_access
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\HexideDigital\ModelPermissions\Models\Permission[] $permissions
 * @property-read int|null $permissions_count
 * @method static \Illuminate\Database\Eloquent\Builder|Role newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Role newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Role query()
 * @method static \Illuminate\Database\Eloquent\Builder|Role whereAdminAccess($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Role whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Role whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Role whereKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Role whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Role whereUpdatedAt($value)
 */
class Role extends \HexideDigital\ModelPermissions\Models\Role
{

}
