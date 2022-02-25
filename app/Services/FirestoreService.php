<?php

namespace App\Services;

use Illuminate\Http\Request;
use Google\Cloud\Firestore\FirestoreClient;

class FireStoreService
{
    public static function connectCollection()
    {

        return app('firebase.firestore')->database();
    }

}