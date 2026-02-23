<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FacePlusPlusService
{
    /**
     * Detetar género numa imagem local usando Face++ Detect API.
     */
    public function detectGenderFromImage(string $imagePath): array
    {
        $endpoint = config('services.faceplusplus.endpoint');
        $apiKey = config('services.faceplusplus.api_key');
        $apiSecret = config('services.faceplusplus.api_secret');

        if (empty($endpoint) || empty($apiKey) || empty($apiSecret)) {
            Log::warning('Face++ não configurado. Ignorando deteção de género.');

            return [
                'gender' => null,
                'has_face' => null,
            ];
        }

        if (!is_file($imagePath) || !is_readable($imagePath)) {
            Log::warning('Ficheiro de imagem inválido para Face++.', ['image_path' => $imagePath]);

            return [
                'gender' => null,
                'has_face' => null,
            ];
        }

        for ($attempt = 1; $attempt <= 3; $attempt++) {
            try {
                $response = Http::timeout(30)
                    ->asMultipart()
                    ->post($endpoint, [
                        [
                            'name' => 'api_key',
                            'contents' => $apiKey,
                        ],
                        [
                            'name' => 'api_secret',
                            'contents' => $apiSecret,
                        ],
                        [
                            'name' => 'return_attributes',
                            'contents' => 'gender,mouthstatus,eyestatus,facequality,blur',
                        ],
                        [
                            'name' => 'image_file',
                            'contents' => fopen($imagePath, 'r'),
                            'filename' => basename($imagePath),
                        ],
                    ]);

                if (!$response->successful()) {
                    throw new \RuntimeException('Face++ HTTP ' . $response->status() . ': ' . $response->body());
                }

                $payload = $response->json();

                if (!is_array($payload)) {
                    throw new \RuntimeException('Resposta inválida da Face++.');
                }

                $faces = $payload['faces'] ?? [];

                if (empty($faces)) {
                    return [
                        'gender' => null,
                        'has_face' => false,
                    ];
                }

                $genderValue = data_get($faces, '0.attributes.gender.value');
                $attributes = data_get($faces, '0.attributes', []);
                $normalizedGender = $this->normalizeGender($genderValue);

                if ($this->shouldForceUnknownGender($attributes)) {
                    $normalizedGender = 'unknown';
                }

                return [
                    'gender' => $normalizedGender,
                    'has_face' => true,
                ];
            } catch (\Throwable $exception) {
                Log::warning('Tentativa Face++ falhou.', [
                    'attempt' => $attempt,
                    'error' => $exception->getMessage(),
                ]);

                if ($attempt < 3) {
                    usleep(500000);
                }
            }
        }

        Log::error('Face++ falhou após 3 tentativas.');

        return [
            'gender' => null,
            'has_face' => null,
        ];
    }

    private function normalizeGender(?string $gender): string
    {
        return match (strtolower((string) $gender)) {
            'male' => 'male',
            'female' => 'female',
            default => 'unknown',
        };
    }

    /**
     * Regras defensivas para reduzir falsos positivos de género.
     */
    private function shouldForceUnknownGender(array $attributes): bool
    {
        $maskThreshold = (float) config('services.faceplusplus.mask_threshold', 50);
        $eyeOcclusionThreshold = (float) config('services.faceplusplus.eye_occlusion_threshold', 70);
        $faceQualityMin = (float) config('services.faceplusplus.face_quality_min', 35);
        $blurMax = (float) config('services.faceplusplus.blur_max', 65);

        $maskScore = (float) data_get($attributes, 'mouthstatus.surgical_mask_or_respirator', 0);
        if ($maskScore >= $maskThreshold) {
            return true;
        }

        $leftEyeOcclusion = (float) data_get($attributes, 'eyestatus.left_eye_status.occlusion', 0);
        $rightEyeOcclusion = (float) data_get($attributes, 'eyestatus.right_eye_status.occlusion', 0);
        $maxEyeOcclusion = max($leftEyeOcclusion, $rightEyeOcclusion);
        if ($maxEyeOcclusion >= $eyeOcclusionThreshold) {
            return true;
        }

        $faceQuality = (float) data_get($attributes, 'facequality.value', 100);
        if ($faceQuality < $faceQualityMin) {
            return true;
        }

        $blur = (float) data_get($attributes, 'blur.blurness.value', 0);
        if ($blur > $blurMax) {
            return true;
        }

        return false;
    }
}
