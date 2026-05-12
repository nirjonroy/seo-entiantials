<?php

namespace Nirjon\LaravelSeo\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Nirjon\LaravelSeo\Services\PageGenerationEngine;

class GeneratePagesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var int
     */
    protected $templateId;

    /**
     * Create a new job instance.
     *
     * @param int $templateId
     * @return void
     */
    public function __construct($templateId)
    {
        $this->templateId = $templateId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $engine = app(PageGenerationEngine::class);
        $engine->generatePages($this->templateId);
    }
}
