<?php

namespace App\Application\Services;

use App\Domain\Interfaces\Services\UserSearchServiceInterface;
use MailerLite\LaravelElasticsearch\Facade as Elasticsearch;
use Illuminate\Support\Facades\Log;

class UserSearchService implements UserSearchServiceInterface
{
    public function searchUsers(string $query, int $limit = 5): array
    {
        try {
            $response = Elasticsearch::search([
                'index' => config('elasticsearch.index_user'),
                'body' => [
                    'query' => [
                        'bool' => [
                            'should' => [
                                // Exact match on name with highest boost
                                [
                                    'match_phrase' => [
                                        'name' => [
                                            'query' => $query,
                                            'boost' => 3
                                        ]
                                    ]
                                ],
                                // Fuzzy match on name
                                [
                                    'match' => [
                                        'name' => [
                                            'query' => $query,
                                            'boost' => 2,
                                            'fuzziness' => 'AUTO',
                                            'operator' => 'and'
                                        ]
                                    ]
                                ],
                                // Prefix match on name
                                [
                                    'prefix' => [
                                        'name.keyword' => [
                                            'value' => $query,
                                            'boost' => 2.5
                                        ]
                                    ]
                                ],
                                // Exact match on email with high boost
                                [
                                    'match_phrase' => [
                                        'email' => [
                                            'query' => $query,
                                            'boost' => 2.5
                                        ]
                                    ]
                                ],
                                // Fuzzy match on email
                                [
                                    'match' => [
                                        'email' => [
                                            'query' => $query,
                                            'boost' => 1.5,
                                            'fuzziness' => 'AUTO'
                                        ]
                                    ]
                                ],
                                // Prefix match on email
                                [
                                    'prefix' => [
                                        'email.keyword' => [
                                            'value' => $query,
                                            'boost' => 2
                                        ]
                                    ]
                                ],
                                // Wildcard as fallback
                                [
                                    'wildcard' => [
                                        'name' => [
                                            'value' => "*" . strtolower($query) . "*",
                                            'boost' => 1
                                        ]
                                    ]
                                ],
                                [
                                    'wildcard' => [
                                        'email' => [
                                            'value' => "*" . strtolower($query) . "*",
                                            'boost' => 0.8
                                        ]
                                    ]
                                ]
                            ],
                            'minimum_should_match' => 1
                        ]
                    ],
                    'size' => $limit,
                    'sort' => [
                        '_score' => ['order' => 'desc']
                    ]
                ]
            ]);

            return $this->formatSearchResults($response);
        } catch (\Exception $e) {
            Log::error('Elasticsearch search error: ' . $e->getMessage());
            Log::error('Search query: ' . $query);
            return [];
        }
    }

    public function searchUsersWithPagination(string $query, int $perPage = 20): array
    {
        try {
            $response = Elasticsearch::search([
                'index' => config('elasticsearch.index_user'),
                'body' => [
                    'query' => [
                        'bool' => [
                            'should' => [
                                // Exact match on name with highest boost
                                [
                                    'match_phrase' => [
                                        'name' => [
                                            'query' => $query,
                                            'boost' => 3
                                        ]
                                    ]
                                ],
                                // Fuzzy match on name
                                [
                                    'match' => [
                                        'name' => [
                                            'query' => $query,
                                            'boost' => 2,
                                            'fuzziness' => 'AUTO',
                                            'operator' => 'and'
                                        ]
                                    ]
                                ],
                                // Prefix match on name
                                [
                                    'prefix' => [
                                        'name.keyword' => [
                                            'value' => $query,
                                            'boost' => 2.5
                                        ]
                                    ]
                                ],
                                // Exact match on email with high boost
                                [
                                    'match_phrase' => [
                                        'email' => [
                                            'query' => $query,
                                            'boost' => 2.5
                                        ]
                                    ]
                                ],
                                // Fuzzy match on email
                                [
                                    'match' => [
                                        'email' => [
                                            'query' => $query,
                                            'boost' => 1.5,
                                            'fuzziness' => 'AUTO'
                                        ]
                                    ]
                                ],
                                // Prefix match on email
                                [
                                    'prefix' => [
                                        'email.keyword' => [
                                            'value' => $query,
                                            'boost' => 2
                                        ]
                                    ]
                                ],
                                // Wildcard as fallback
                                [
                                    'wildcard' => [
                                        'name' => [
                                            'value' => "*" . strtolower($query) . "*",
                                            'boost' => 1
                                        ]
                                    ]
                                ],
                                [
                                    'wildcard' => [
                                        'email' => [
                                            'value' => "*" . strtolower($query) . "*",
                                            'boost' => 0.8
                                        ]
                                    ]
                                ]
                            ],
                            'minimum_should_match' => 1
                        ]
                    ],
                    'size' => $perPage,
                    'sort' => [
                        '_score' => ['order' => 'desc']
                    ]
                ]
            ]);

            return $this->formatSearchResults($response);
        } catch (\Exception $e) {
            Log::error('Elasticsearch search error: ' . $e->getMessage());
            Log::error('Search query: ' . $query);
            return [];
        }
    }

    private function formatSearchResults(array $response): array
    {
        $results = [];

        if (isset($response['hits']['hits'])) {
            foreach ($response['hits']['hits'] as $hit) {
                $source = $hit['_source'];
                Log::info($source['name']);
                // Use data directly from Elasticsearch
                $results[] = [
                    'id' => $source['id'],
                    'name' => $source['name'], // Already computed in UserElasticResource
                    'email' => $source['email'],
                    'avatar' => $source['avatar'] ?? null,
                ];
            }
        }

        return $results;
    }
}
