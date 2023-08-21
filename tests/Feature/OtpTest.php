<?php

declare(strict_types=1);

namespace Feature;

use App\Mail\OtpWasGenerated;
use Faker\Factory;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use TestCase;

class OtpTest extends TestCase
{
    /**
     * @test
     */
    public function it_can_generate_otp_success()
    {
        Mail::fake();

        $faker = Factory::create();

        $this
            ->json('POST', '/api/v1/otp/generate', [
                'identifier' => $faker->email
            ])->assertStatus(Response::HTTP_OK);

        Mail::assertSent(OtpWasGenerated::class);
    }

    /**
     * @test
     */
    public function it_can_generate_otp_fails()
    {
        $randStr = Str::random(10);

        Log::shouldReceive('debug')
            ->once()
            ->with(
                'Error sending OTP email',
                [
                    'identifier' => $randStr,
                    'errorMsg' => 'Email "' . $randStr . '" does not comply with addr-spec of RFC 2822.'
                ]
            );

        $this->withoutExceptionHandling()
            ->json('POST', '/api/v1/otp/generate', [
                'identifier' => $randStr
            ])
            ->assertExactJson([
                "message" => "There was an error processing this request. Please try again."
            ]);
    }

    /**
     * @dataProvider OtpValidationDataProvider
     * @test
     */
    public function it_can_validate_a_token(?string $identifier, ?string $token, array $response)
    {
        $this
            ->json('POST', '/api/v1/otp/validate', [
                'identifier' => $identifier,
                'token' => $token
            ])
            ->assertStatus(Response::HTTP_OK)
            ->assertExactJson($response);
    }

    public static function OtpValidationDataProvider(): array
    {
        $faker = Factory::create();

        return [
            'With null identifier and token' => [
                null,
                null,
                [
                    'status' => false,
                    'message' => 'OTP does not exist'
                ]
            ],
            'With null token only' => [
                $faker->email,
                null,
                [
                    'status' => false,
                    'message' => 'OTP does not exist'
                ]
            ],
            'With null identifier only' => [
                null,
                (string)$faker->randomNumber(6),
                [
                    'status' => false,
                    'message' => 'OTP does not exist'
                ]
            ],
            'Identifier and token not matching db' => [
                $faker->email,
                (string)$faker->randomNumber(6),
                [
                    'status' => false,
                    'message' => 'OTP does not exist'
                ]
            ],
        ];
    }
}
