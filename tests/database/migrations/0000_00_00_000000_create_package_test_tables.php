<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePackageTestTables extends Migration
{
    public function up()
    {
        Schema::create('orderable_test_models', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('order')->nullable();
            $table->timestamps();
        });

        Schema::create('orderable_group_test_models', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('order')->nullable();
            $table->string('group');
            $table->timestamps();
        });

        Schema::create('orderable_group_changing_test_models', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('order')->nullable();
            $table->string('group_a');
            $table->string('group_b');
            $table->timestamps();
        });

        Schema::create('orderable_test_model_with_other_order_columns', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('non_default_order_column')->nullable();
            $table->timestamps();
        });

       Schema::create('primary_test_pivot_models', function (Blueprint $table) {
          $table->bigIncrements('id');
          $table->timestamps();
       });

       Schema::create('secondary_test_pivot_models', function (Blueprint $table) {
          $table->bigIncrements('id');
          $table->timestamps();
       });

       Schema::create('primary_secondary', function (Blueprint $table) {

          $table->bigIncrements('id');

          $table->bigInteger('primary_test_pivot_model_id')->unsigned();
          $table->foreign('primary_test_pivot_model_id')->references('id')->on('primary_test_pivot_models')->onDelete('cascade');
          $table->bigInteger('secondary_test_pivot_model_id')->unsigned();
          $table->foreign('secondary_test_pivot_model_id')->references('id')->on('secondary_test_pivot_models')->onDelete('cascade');

          $table->unsignedInteger('order')->nullable();

       });


    }

    public function down()
    {
    }
}
