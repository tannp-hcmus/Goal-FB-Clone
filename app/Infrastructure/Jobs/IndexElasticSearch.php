<?php

namespace App\Infrastructure\Jobs;

use Elasticsearch;
use Illuminate\Support\Facades\Log;
use Spatie\Async\Task;

class IndexElasticSearch extends Task
{
    protected array $dataUsers;

    /**
     * Constructor
     *
     * @param  array  $companyIdChecked
     */
    public function __construct(
        array $dataUsers
    ) {
        $this->dataUsers = $dataUsers;
    }

    public function configure() {}

    public function run()
    {
        $app = require __DIR__ . '/../../../bootstrap/app.php';
        $app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

        return $this->bulkImportToElasticsearch($this->dataUsers);
    }

    /**
     * Perform bulk import of job data into Elasticsearch.
     *
     * @param  array  $dataUsers  The job data to be imported.
     * @return bool Returns true on success, false on failure.
     */
    public function bulkImportToElasticsearch($dataUsers)
    {
        if (empty($dataUsers)) {
            Log::warning('No data provided for Elasticsearch bulk import.');

            return false;
        }

        try {
            sleep(1);
            $responses = Elasticsearch::bulk($dataUsers);

            if (!empty($responses['errors'])) {
                Log::error('Elasticsearch bulk import error:', $responses);
                unset($responses);

                return false;
            }

            unset($responses);

            return true;
        } catch (\Exception $e) {
            Log::error('Elasticsearch bulk import exception: ' . $e->getMessage());

            return false;
        }
    }
}
