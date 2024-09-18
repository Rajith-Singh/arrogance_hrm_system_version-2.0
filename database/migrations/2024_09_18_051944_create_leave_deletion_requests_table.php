<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLeaveDeletionRequestsTable extends Migration
{
    public function up()
    {
        Schema::create('leave_deletion_requests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('leave_id');
            $table->text('reason'); // Reason for deletion
            $table->string('attachment')->nullable(); // Optional attachment
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('leave_deletion_requests');
    }
}

