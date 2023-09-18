<?php

namespace Tests;

use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class TestSeeder extends Seeder {
    public function run(): void {
        Schema::create("users", function (Blueprint $table) {
            $table->id();
            $table->string("username", 30);
            $table->string("password", 255);
        });

        Schema::create("bills", function (Blueprint $table) {
            $table->id();
            $table->foreignIDFor(User::class, "user_id");
            $table->float("payment_amount", 5, 2);
            $table->date("payment_date");
        });

        DB::table("users")->insert([
            "username" => "OzzyTheGiant",
            "password" => '$argon2id$v=19$m=65536,t=3,p=4$ZeYABY+RVIf42Dx31DVhwg$Q4JkBb+fAIuFRq0ZN4b3GnP05U6Nw7XQWDTkBR2rtyk'
        ]);

        DB::table("bills")->insert([
            "user_id" => 1,
            "payment_amount" => 50.99,
            "payment_date" => "2022-01-01"
        ]);
    }
}
