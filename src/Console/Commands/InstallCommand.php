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

        $this->call('migrate', [
            '--force' => true,
        ]);

        $this->installSidebarLink();

        $this->info('SEO Package installed successfully!');
    }

    protected function installSidebarLink(): void
    {
        if (! config('seo.admin.sidebar.auto_install', true)) {
            return;
        }

        $sidebarPath = config('seo.admin.sidebar.path', resource_path('views/admin/sidebar.blade.php'));

        if (! is_string($sidebarPath) || ! file_exists($sidebarPath) || ! is_writable($sidebarPath)) {
            $this->warn('Admin sidebar link was not installed automatically. Add @include(\'seo::admin.sidebar-link\') to your admin sidebar.');
            return;
        }

        $contents = file_get_contents($sidebarPath);
        $include = "@include('seo::admin.sidebar-link')";

        if ($contents === false || str_contains($contents, $include)) {
            return;
        }

        $snippet = "\n             {{-- Nirjon Laravel SEO sidebar link --}}\n             {$include}\n\n";

        $lastListClose = strrpos($contents, '</ul>');

        if ($lastListClose !== false) {
            $contents = substr_replace($contents, $snippet, $lastListClose, 0);
        } else {
            $contents .= $snippet;
        }

        file_put_contents($sidebarPath, $contents);
        $this->info('SEO Settings sidebar link installed.');
    }
}
