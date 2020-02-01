<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Models\Student;

class UpdateStudentTshirtXsToYl extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Student::where('tshirt','XS')->update(['tshirt' => 'YL']);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
	    Student::where('tshirt','YL')->update(['tshirt' => 'XS']);
    }
}
