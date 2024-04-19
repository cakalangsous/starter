<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class AdminController extends Controller
{
    public $data = [
        "parent_menu" => "",
        "active_menu" => "",
        "title"       => "Laraku"
    ];

    public function __construct()
    {
        if (!Schema::hasTable('migrations')) {
            return redirect()->to(url('/install'))->send();
        }
    }

    public function view($view='', $data=[]): View
    {
        $data = $this->data;
        // $data['site_settings'] = SiteSettings::all();
        return view('admin.'.$view, $data);
    }
}
