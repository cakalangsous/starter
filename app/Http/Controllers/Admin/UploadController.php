<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TemporaryFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class UploadController extends Controller
{
    public function upload(Request $req)
    {
        $req->validate([
            "file" => "file|mimes:png,jpg,jpeg"
        ]);

        $link = $req->file("file")->store("editor/images");
        return response()->json(["success" => true, "message" => "Upload success", "link" => asset("storage/" . $link)]);
    }

    public function delete(Request $req)
    {
        $req->validate([
            "src" => "required",
            "data-url" => "required"
        ]);

        if (!File::exists($req->src)) {
            return response()->json(["success" => false, "message" => "File not exists."]);
        }

        File::delete($req->src);

        return response()->json(["status" => true, "message" => "File deleted"]);
    }

    public function list(Request $req)
    {
        $files = File::files(public_path('storage/editor/images'));
        $images = [];

        foreach ($files as $file) {
            $images[] = [
                "url" => asset("storage/editor/images/" . $file->getRelativePathname()),
                "thumb" => asset("storage/editor/images/" . $file->getRelativePathname())
            ];
        }

        return response()->json($images);
    }

    public function temp_upload(Request $req)
    {
        if (!$req->hasFile("filepond")) {
            return response()->json(["message" => "Unknown request"], 404);
        }

        $req->validate([
            "filepond" => "file|mimes:png,jpg,jpeg"
        ]);


        $filename = sha1(uniqid() . now()->timestamp) . "." . $req->file("filepond")->getClientOriginalExtension();
        $folder = uniqid() . now()->timestamp;
        $req->file("filepond")->storeAs("temp/" . $folder, $filename);

        TemporaryFile::create([
            "folder" => $folder,
            "filename" => $filename
        ]);

        return $folder;

    }

    public function temp_delete(Request $req)
    {
        $temp = TemporaryFile::where('folder', $req->getContent())->first();

        if (!$temp) {
            return false;
        }

        File::deleteDirectory(public_path("storage/temp/" . $temp->folder));

        $temp->delete();
        return true;

    }

    public function getUploaded(Request $request)
    {
        $request->validate([
            "load" => "required"
        ]);

        return response()->json([
            "filename" => asset("storage/" . $request->load)
        ]);
    }
}
