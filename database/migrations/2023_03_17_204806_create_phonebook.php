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
        Schema::create('phone_book_versions', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
        });

        Schema::create('phone_book_names', function (Blueprint $table) {
            $table->id();
            $table->string("name");
            $table->string("ruby") ->default("");
            $table->integer("version_id")->unsigned();
            $table->timestamps();

            $table->foreign("version_id")->references("id")->on("phone_book_versions")->onDelete("CASCADE");
        });

        Schema::create('phone_book_numbers', function (Blueprint $table) {
            $table->id();
            $table->string("type");
            $table->string("number");
            $table->integer("name_id")->unsigned();
            $table->timestamps();


            $table->foreign("name_id")->references("id")->on("phone_book_names")->onDelete("CASCADE");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('phone_book_versions');

        Schema::dropIfExists('phone_book_names');

        Schema::dropIfExists('phone_book_numbers');
    }
};
