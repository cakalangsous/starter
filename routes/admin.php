<?php

use App\Http\Controllers\Admin\AccessController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\PostCategoryController;
use App\Http\Controllers\Admin\PostController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\TagController;
use App\Http\Controllers\Admin\UploadController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;


Route::get('/dashboard', [DashboardController::class, "index"])->name('dashboard');

Route::get("post_categories/data", [PostCategoryController::class, "post_categories_data"])->name("post_categories.data");
Route::resource("post_categories", PostCategoryController::class)->except("show", "edit", "create");
Route::get("tags/data", [TagController::class, "tags_data"])->name("tags.data");
Route::resource("tags", TagController::class)->except("show", "edit", "create");
Route::get("posts/data", [PostController::class, "posts_data"])->name("posts.data");
Route::resource("posts", PostController::class);
Route::resource("comments", PostCategoryController::class)->except("show", "edit", "create");

Route::post("users/ban/{user}", [UserController::class, 'user_ban'])->name("users.ban");
Route::get("users/data", [UserController::class, 'user_data'])->name("users.data");
Route::resource("users", UserController::class)->except("show");

Route::get("roles/data", [RoleController::class, "roles_data"])->name("roles.data");
Route::resource('roles', RoleController::class)->except("show", "edit", "create");

Route::get("permissions/data", [PermissionController::class, "permissions_data"])->name("permissions.data");
Route::resource('permissions', PermissionController::class)->except("show", "edit", "create");

Route::put("access/update/{role}", [AccessController::class, 'update'])->name("access.update");
Route::get("access", [AccessController::class, 'index'])->name("access.index");
Route::post("access/role/{role}", [AccessController::class, 'getAccessByRole'])->name("access.role");

Route::post("editor/upload", [UploadController::class, "upload"])->name("editor_upload");
Route::delete("editor/delete", [UploadController::class, "delete"])->name("editor_delete");
Route::get("editor/list", [UploadController::class, "list"])->name("editor_image_manager");

Route::delete("temp-upload", [UploadController::class, "temp_delete"])->name("temp_upload_delete");
Route::post("temp-upload", [UploadController::class, "temp_upload"])->name("temp_upload");
Route::get("temp-upload", [UploadController::class, "getUploaded"])->name("get_image_edit_post");
