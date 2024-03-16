<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId("post_category_id");
            $table->foreignId("user");
            $table->string("image");
            $table->string("title");
            $table->string("slug");
            $table->text("body");
            $table->string("meta_description")->nullable();
            $table->string("meta_keywords")->nullable();
            $table->string("excerpt")->nullable();
            $table->tinyInteger("publish_status")->default(0);
            $table->dateTime("published_at")->nullable();
            $table->boolean("allow_comments")->default(1);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
