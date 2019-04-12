<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePackageTestTables extends Migration{

   public function up() {
      Schema::create('orderable_test_models', function (Blueprint $table) {
         $table->bigIncrements('id');
         $table->unsignedInteger('order')->nullable();
         $table->timestamps();
      });

      Schema::create('orderable_group_test_models',function(Blueprint $table){
         $table->bigIncrements('id');
         $table->unsignedInteger('order')->nullable();
         $table->string('group');
         $table->timestamps();
      });

      Schema::create('orderable_group_changing_test_models',function(Blueprint $table){
         $table->bigIncrements('id');
         $table->unsignedInteger('order')->nullable();
         $table->string('group_a');
         $table->string('group_b');
         $table->timestamps();
      });

      Schema::create('orderable_test_model_with_other_order_columns',function(Blueprint $table){
         $table->bigIncrements('id');
         $table->unsignedInteger('non_default_order_column')->nullable();
         $table->timestamps();
      });

   }

   public function down() {

   }
}