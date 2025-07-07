<?php

namespace App\Application\Services;

use App\Domain\Interfaces\Repositories\UserRepositoryInterface;
use App\Domain\Interfaces\Services\ElasticSearchServiceInterface;
use App\Http\Resources\UserElasticResource;
use App\Infrastructure\Jobs\IndexElasticSearch;
use Spatie\Async\Pool;

class ElasticSearchService implements ElasticSearchServiceInterface
{
    const CONCURRENCY_NUMBER = 2;

    const TIMEOUT = 2000;

    const USER_QUERY = 1000;

    const CHUNK_BULK_DATA = 100;

    public function __construct(
        private UserRepositoryInterface $userRepository,
    ) {}

    public function index(): void
    {
        $pool = Pool::create()->concurrency(self::CONCURRENCY_NUMBER)->timeout(self::TIMEOUT);

        $this->userRepository->chunkAll(self::USER_QUERY, function ($users) use (&$pool) {
            if ($users->isEmpty()) {
                return;
            }

            $chunks = $users->chunk(self::CHUNK_BULK_DATA, false);

            foreach ($chunks as $batch) {
                $params = ['body' => []];

                foreach ($batch as $user) {
                    $params['body'][] = [
                        'index' => [
                            '_index' => config('elasticsearch.index_user'),
                            '_type' => '_doc',
                            '_id' => $user->id,
                        ],
                    ];
                    $params['body'][] = (new UserElasticResource($user))->toArray(request());
                }

                $pool[] = async(new IndexElasticSearch($params))->then(callback: function ($output) use ($pool) {
                    if ($output !== true) {
                        $pool->stop();
                    }
                });

                unset($params);
            }
            $pool->wait();
            unset($chunks);
        });

        $pool->wait();
    }
}
