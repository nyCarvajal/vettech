<?php

namespace App\Services\Marketing;

use App\Models\MarketingCampaignLog;
use App\Models\User;
use App\Services\WhatsApp\OneMsgClient;
use Illuminate\Support\Collection;
use Throwable;

class MarketingCampaignSender
{
    public function __construct(
        private readonly InactivePatientCampaignService $campaignService,
        private readonly OneMsgClient $whatsAppClient,
    ) {
    }

    public function send(string $campaignType, Collection $recipients, string $template, string $clinicName, User $user): array
    {
        $results = [
            'sent' => 0,
            'failed' => 0,
            'stubbed' => 0,
        ];

        $canDispatch = filled(config('services.onemsg.channel_id')) && filled(config('services.onemsg.token'));

        foreach ($recipients as $recipient) {
            $message = $this->campaignService->renderMessage($template, $recipient, $clinicName);
            $status = 'sent';
            $response = 'Mensaje enviado por 1MSG.';

            try {
                if (blank($recipient->contact_phone)) {
                    throw new \RuntimeException('El tutor no tiene un teléfono o WhatsApp utilizable.');
                }

                if ($canDispatch) {
                    $this->whatsAppClient->sendMessage($recipient->contact_phone, $message);
                    $results['sent']++;
                } else {
                    $status = 'stubbed';
                    $response = 'Integración de WhatsApp no configurada. Se registró el envío en modo stub.';
                    $results['stubbed']++;
                }
            } catch (Throwable $exception) {
                $status = 'failed';
                $response = $exception->getMessage();
                $results['failed']++;
            }

            MarketingCampaignLog::create([
                'patient_id' => $recipient->patient_id,
                'owner_id' => $recipient->owner_id,
                'campaign_type' => $campaignType,
                'message' => $message,
                'sent_at' => now(),
                'status' => $status,
                'response' => $response,
                'created_by' => $user->id,
            ]);
        }

        return $results;
    }
}
