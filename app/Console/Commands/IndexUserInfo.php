<?php

namespace App\Console\Commands;

use App\Application\Services\ElasticSearchService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use MailerLite\LaravelElasticsearch\Facade as Elasticsearch;

class IndexUserInfo extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:index';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Index user info';

    /**
     * The elastic search service instance.
     *
     * @var ElasticSearchService
     */
    protected ElasticSearchService $elasticSearchService;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(ElasticSearchService $elasticSearchService)
    {
        parent::__construct();
        $this->elasticSearchService = $elasticSearchService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            if (!Elasticsearch::indices()->exists([
                'index' => config('elasticsearch.index_user'),
            ])) {
                $params = [
                    'index' => config('elasticsearch.index_user'),
                    'body' => ['mappings' => config('elasticsearchMapping')],
                ];
                Elasticsearch::indices()->create($params);
            }

            $this->elasticSearchService->index();

            Log::info('End index user at: ');
        } catch (\Exception $ex) {
            Log::error($ex);
            Log::info('Index user Error: ' . $ex);
            Log::info('Index user faild: ');
        }
    }
}
