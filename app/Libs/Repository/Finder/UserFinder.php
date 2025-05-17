<?php
namespace App\Libs\Repository\Finder;

use Auth;
use DB;
use App\Models\User as Model;

class UserFinder
{
    public function __construct()
    {
        $this->query = Model::select('users.*', 'media.name as media');
        $this->query->leftJoin('media', function ($join) {
            $join->on('media.table_name', '=', DB::raw("'users'"));
            $join->on('media.fk_id', '=', "users.id");
        });
    }

    public function setOnlyTrashed($isDeleted)
    {
        if ($isDeleted == 1){
            $this->query->onlyTrashed();
        }
    }

    private function filterByRole()
    {
        $user = Auth::user();
        if($user->role == 'member'){
            $this->query->where('users.id', $user->id);
        }
    }

    public function get()
    {
        $this->filterByRole();

        $result = $this->query->get();
        return $result;
    }
}
