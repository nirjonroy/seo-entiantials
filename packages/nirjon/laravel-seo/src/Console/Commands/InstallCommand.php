<?php

namespace Nirjon\LaravelSeo\Console\Commands;

use Illuminate\Console\Command;

class InstallCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'seo:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install the Nirjon Laravel SEO package';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Installing SEO Package...');

        $this->call('vendor:publish', [
            '--tag' => 'seo-config',
            '--force' => true,
        ]);

        if ($this->confirm('Do you want to run the migrations now?')) {
            $this->call('migrate');
        }

        $this->info('SEO Package installed successfully!');
    }
}
