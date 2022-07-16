<?php

namespace App\Http\Controllers;

use App\Models\Mods;
use App\Models\Reviews;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use PhpParser\Node\Expr\AssignOp\Mod;

class ModsController extends Controller
{
    public function modsList(Request $request) {
        return Mods::package($request);
    }

    public function modPage(Request $request, $url) {
        return Mods::generateMod($request, $url);
    }

    public function modReview(Request $request, $id) {
        if(!Mods::sendReview($request, $id)) {
            return Ajax::valid(Mods::$error);
        }

        return Ajax::redirect(url()->previous());
    }

    public function modComment(Request $request, $id) {
        return Mods::sendComment($request, $id);
    }
}
