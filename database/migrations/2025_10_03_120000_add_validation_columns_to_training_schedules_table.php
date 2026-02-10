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
        Schema::table('tb_training_schedules', function (Blueprint $table) {
            // Kolom untuk validasi kehadiran
            $table->string('attendance_validation_status', 50)->default('pending')->after('status');
            $table->timestamp('attendance_validated_at')->nullable()->after('attendance_validation_status');
            $table->unsignedBigInteger('validated_by')->nullable()->after('attendance_validated_at');
            $table->text('validation_notes')->nullable()->after('validated_by');

            // Kolom untuk tracking peserta
            $table->integer('total_participants')->default(0)->after('validation_notes');
            $table->integer('attended_participants')->default(0)->after('total_participants');
            $table->integer('absent_participants')->default(0)->after('attended_participants');
            $table->integer('reschedule_needed')->default(0)->after('absent_participants');

            // Kolom untuk status training
            $table->string('training_status', 50)->default('scheduled')->after('reschedule_needed');
            $table->timestamp('training_completed_at')->nullable()->after('training_status');
            $table->timestamp('training_cancelled_at')->nullable()->after('training_completed_at');
            $table->text('cancellation_reason')->nullable()->after('training_cancelled_at');

            // Kolom untuk sertifikat
            $table->boolean('certificates_issued')->default(false)->after('cancellation_reason');
            $table->integer('certificates_count')->default(0)->after('certificates_issued');
            $table->timestamp('certificates_issued_at')->nullable()->after('certificates_count');

            // Foreign key untuk validated_by
            $table->foreign('validated_by')->references('id')->on('users')->onDelete('set null');

            // Index untuk performa
            $table->index(['attendance_validation_status', 'training_status']);
            $table->index(['validated_by', 'attendance_validated_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tb_training_schedules', function (Blueprint $table) {
            $table->dropForeign(['validated_by']);
            $table->dropIndex(['attendance_validation_status', 'training_status']);
            $table->dropIndex(['validated_by', 'attendance_validated_at']);

            $table->dropColumn([
                'attendance_validation_status',
                'attendance_validated_at',
                'validated_by',
                'validation_notes',
                'total_participants',
                'attended_participants',
                'absent_participants',
                'reschedule_needed',
                'training_status',
                'training_completed_at',
                'training_cancelled_at',
                'cancellation_reason',
                'certificates_issued',
                'certificates_count',
                'certificates_issued_at'
            ]);
        });
    }
};
