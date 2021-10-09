<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use Illuminate\Http\Request;
use App\Http\Resources\ContactResource;

class ContactController extends Controller
{
    public function index(Request $request) {
        $data = Contact::list();
        return response()->json(
            [
                'list' => $data->all(),
                'totalPages' => $data->lastPage(),
                'currentPage' => $data->currentPage(),
            ]
        );
    }
}
