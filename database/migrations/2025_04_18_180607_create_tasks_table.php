<?php

use App\Enums\TaskStatus;
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
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('status', TaskStatus::values())->default(TaskStatus::PENDING->value);
            $table->dateTime('due_date')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Adding indexes for better performance
            $table->index('status');
            $table->index('assigned_to');
            $table->index('due_date');
            $table->index('deleted_at');
            $table->index(['id', 'deleted_at'], 'tasks_id_deleted_at_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
