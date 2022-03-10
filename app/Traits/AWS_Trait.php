<?php


namespace App\Traits;

use Aws\Comprehend\ComprehendClient;

trait AWS_Trait
{
    public function query_AWS()
    {
        $client = new ComprehendClient([
            'credentials' => [
                'key' => config('services.comprehend.key'),
                'secret' => config('services.comprehend.secret'),
            ],
            'region' => 'us-east-1',
            'version' => 'latest',
        ]);
        return $client;
    }
}
