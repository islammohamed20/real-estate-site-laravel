<?php

use App\Models\Customer;
use App\Models\LeadSource;
use App\Models\Project;
use App\Models\Unit;
use Illuminate\Contracts\Console\Kernel;

require '/var/www/real-estate-site/vendor/autoload.php';
$app = require '/var/www/real-estate-site/bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();

echo 'LEAD SOURCES: ';
foreach (LeadSource::all() as $s) {
    echo "[{$s->id}] {$s->name} (sort:{$s->sort_order}, active:".($s->is_active ? 1 : 0).') ';
}
echo PHP_EOL;
echo 'CUSTOMER SOURCES USED: '.implode(', ', array_unique(array_filter(Customer::pluck('source')->all()))).PHP_EOL;
echo 'UNIT TYPES USED: '.implode(', ', array_unique(array_filter(Unit::pluck('unit_type')->all()))).PHP_EOL;
echo 'PROJECTS: '.Project::count().' UNITS: '.Unit::count().PHP_EOL;
// check customers show for edit button
$show = file_get_contents('/var/www/real-estate-site/resources/views/crm/customers/show.blade.php');
echo 'CUSTOMERS SHOW HAS EDIT BTN: '.(str_contains($show, 'customers.edit') ? 'YES' : 'NO').PHP_EOL;
// check public unit/project show for image display
$ushow = file_get_contents('/var/www/real-estate-site/resources/views/public/units/show.blade.php');
echo 'UNIT PUBLIC SHOW LINES: '.substr_count($ushow, "\n").PHP_EOL;
// check storage setup
echo 'STORAGE LINK: '.(is_link('/var/www/real-estate-site/public/storage') ? 'YES' : 'NO').PHP_EOL;
// uploads dir?
echo 'UPLOADS DIR: '.(is_dir('/var/www/real-estate-site/storage/app/public') ? 'EXISTS' : 'NO').PHP_EOL;
