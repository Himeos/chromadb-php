<?php

namespace Himeos\ChromaDB\Embeddings;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class AzureOpenAIEmbeddingFunction implements EmbeddingFunction
{
    private Client $client;

    public function __construct(
        public readonly string $apiKey,
        public readonly string $apiUrl
    ) {
        $headers = [
            'api-key' => $this->apiKey,
            'Content-Type' => 'application/json',
        ];

        
        $this->client = new Client([
            'headers' => $headers,
        ]);
    }

    /**
     * Generates embeddings using Azure OpenAI API
     *
     * @param array $texts An array of strings to embed
     * @return array The generated embeddings
     */
    public function generate(array $texts): array
    {
    
        
        $payload = ['input' => $texts];

        try {
            
            $response = $this->client->post($this->apiUrl, [
                'json' => $payload
            ]);

            
            $result = json_decode($response->getBody()->getContents(), true);
            
            
            $embeddings = $result['data'];

            return array_map(fn($embedding) => $embedding['embedding'], $embeddings);

        } catch (RequestException $e) {
            throw new \RuntimeException("Error calling Azure OpenAI API: {$e->getMessage()}", 0, $e);
        }
    }
}
