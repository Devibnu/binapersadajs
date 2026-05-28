<?php
$autoload = __DIR__ . '/../vendor/autoload.php';
if (! file_exists($autoload)) {
	echo "autoload not found: $autoload\n";
	exit(1);
}
require $autoload;
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
$schema = Illuminate\Support\Facades\Schema::getFacadeRoot();
$hasLabel = Illuminate\Support\Facades\Schema::hasColumn('homepage_settings', 'project_section_label') ? 'yes' : 'no';
$hasTitle = Illuminate\Support\Facades\Schema::hasColumn('homepage_settings', 'project_section_title') ? 'yes' : 'no';
$cols = Illuminate\Support\Facades\Schema::getColumnListing('homepage_settings');
echo "project_section_label: $hasLabel\n";
echo "project_section_title: $hasTitle\n";
echo "columns count: " . count($cols) . "\n";
echo "columns sample: " . implode(', ', array_slice($cols, 0, 50)) . "\n";
