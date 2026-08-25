<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Awcodes\Curator\Models\Media as CuratorMedia;

class Media extends CuratorMedia
{
    use HasUuids;
    
}
