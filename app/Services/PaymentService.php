<?php

namespace App\Services;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class PaymentService
{
    public function find(int $id): ?array
    {
        try {
            $response = $this->client()->post(
                config('services.phalapi.url'),
                [
                    'service' => 'Hello.GetCashDetail',
                    'id' => $id,
                ]
            );

            $data = $this->validateResponse($response);

            $item = $data['data']['item'] ?? null;

            return is_array($item) ? $item : null;

        } catch (\Throwable $e) {
            report($e);

            return null;
        }
    }

    protected function client()
    {
        return Http::asForm()
            ->timeout(10)
            ->retry(2, 500)
            ->withOptions([
                'verify' => false, // Disable SSL verification for expired certificates
            ])
            ->withHeaders([
                'X-ADMIN-TOKEN' => config('services.phalapi.token'),
            ]);
    }

    protected function validateResponse(Response $response): array
    {
        if (! $response->successful()) {
            throw new \RuntimeException(
                'HTTP Error: '.$response->body()
            );
        }

        $data = $response->json();

        if (! is_array($data) || ($data['ret'] ?? 500) !== 200) {
            throw new \RuntimeException(
                'PhalApi Error: '.$response->body()
            );
        }

        return $data;
    }

    // public function list(): array
    // {
    //     $response = $this->client()->post(
    //         config('services.phalapi.url'),
    //         [
    //             'service' => 'Hello.GetCashList',
    //         ]
    //     );

    //     $data = $this->validateResponse($response);
    //     // dd($data['data']['items']);
    //     return $data['data']['items'] ?? [];
    // }

    public function list(?int $status = null, int $page = 1, int $perPage = 10): array
    {
        $payload = [
            'service' => 'Hello.GetCashList',
            'page' => $page,
            'per_page' => $perPage,
        ];

        if ($status !== null) {
            $payload['status'] = $status;
        } else {
            $payload['status'] = -1;
        }

        $response = $this->client()->post(
            config('services.phalapi.url'),
            $payload
        );

        $data = $this->validateResponse($response);

        return [
            'items' => $data['data']['items'] ?? [],
            'meta' => $data['data']['meta'] ?? [
                'total' => 0,
                'current_page' => $page,
                'per_page' => $perPage,
                'last_page' => 1,
            ],
        ];
    }

    public function getEzgoList(): array
    {
        $response = $this->client()->post(
            config('services.phalapi.url'),
            [
                'service' => 'Hello.getEzGoList',
            ]
        );
        $data = $this->validateResponse($response);

        return $data['data']['items'] ?? [];
    }

    public function updateStatus(int $id, int $status): bool
    {
        try {

            $response = $this->client()->post(
                config('services.phalapi.url'),
                [
                    'service' => 'Hello.UpdateCashStatus',
                    'id' => $id,
                    'status' => $status,
                ]
            );

            $this->validateResponse($response);

            return true;

        } catch (\Throwable $e) {

            report($e);

            return false;
        }
    }

    public function getUser(int $uid): ?array
    {
        try {

            $response = $this->client()->post(
                config('services.phalapi.url'),
                [
                    'service' => 'User.GetUserInfo',
                    'uid' => $uid,
                ]
            );

            $data = $this->validateResponse($response);

            return $data['data']['info'] ?? null;

        } catch (\Throwable $e) {

            report($e);

            return null;
        }
    }
}