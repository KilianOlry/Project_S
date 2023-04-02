<?php

namespace App\Service;

use Symfony\Component\Mime\Part\DataPart;
use Symfony\Component\Mime\Part\Multipart\FormDataPart;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class YouSignService
{
    // Chemin des PDF
    private const PATHFILE = __DIR__ . '/../../public/assets/uploads/contrats/';
    // On utilise le hhtp_client de symfony qui utilise les paramètres du fichier http_client grace à "$yousignClient"
    public function __construct(
        private HttpClientInterface $yousignClient,
    ) {
    }
    // Création d'une function en POST qui a en deuxième paramètre l'url se référer au http_client
    public function signatureRequest(): array
    {
        $response = $this->yousignClient->request(
            'POST',
            'signature_requests',
            [
                // Information relatives à l'API détails supplémentaires
                'body' => <<<JSON
                        {
                            "name": "Contrat de location",
                            "delivery_mode": "email",
                            "timezone": "Europe/Paris"
                        }
                        JSON,
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
            ]
        );
        // Test functionnel
        $statusCode = $response->getStatusCode();

        if ($statusCode != 201) {
            throw new \Exception('Error while creating signature request');
        }

        return $response->toArray();
    }

    //2 upload a document
    // Création d'une function qui en paramètre réupère le nom du fichier + ID de la signature 
    public function uploadDocument(string $signatureRequestId, string $filename): array
    {
        // Création tableau de 2 champs le premier documentation Yousign puis le fichier à uploader
        $formFields = [
            'nature' => 'signable_document',
            'file' => DataPart::fromPath(self::PATHFILE . $filename, $filename, 'application/pdf'),

        ];
        // datapart "mulipart" c'est documentation Yousign
        $formData = new FormDataPart($formFields);
        $headers = $formData->getPreparedHeaders()->toArray();

        // On fait la requête en POST 
        $response = $this->yousignClient->request(
            'POST',
            sprintf('signature_requests/%s/documents', $signatureRequestId),
            [
                'headers' => $headers,
                'body' => $formData->bodyToIterable(),
            ]
        );
        // Test functionnal
        $statusCode = $response->getStatusCode();

        if ($statusCode != 201) {
            throw new \Exception('Error while uploading document');
        }

        return $response->toArray();
    }

    // 3 - Ajouter un signataire 
    // Création fuction qui a pour paramètre ...
    public function addSigner(
        string $signatureRequestId,
        string $documentId,
        string $email,
        string $prenom,
        string $nom
    ): array {
        // On prépare la requête en POST sur l'URL de base
        $response = $this->yousignClient->request(
            'POST',
            sprintf('signature_requests/%s/signers', $signatureRequestId),
            [
                'body' => <<<JSON
            {
                "info": {
                    "first_name": "$prenom",
                    "last_name": "$nom",
                    "email": "$email",
                    "locale": "fr"
                },
                "fields": [
                    {
                        "type": "signature",
                        "document_id": "$documentId",
                        // Positionnement de la signature
                        "page": 1,
                        "x": 77,
                        "y": 581
                    }
                ],
                // On spécifie le niveau de signature (légalité)
                "signature_level": "electronic_signature",
                "signature_authentication_mode": "no_otp"
            }
JSON,
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
            ]
        );
        // Test functionnel
        $statusCode = $response->getStatusCode();

        if ($statusCode != 201) {
            throw new \Exception('Error while adding signer');
        }

        return $response->toArray();
    }


    //4 send the signature request

    public function activateSignatureRequest(string $signatureRequestId): array
    {
        $response = $this->yousignClient->request(
            'POST',
            sprintf('signature_requests/%s/activate', $signatureRequestId)
        );

        $statusCode = $response->getStatusCode();

        if ($statusCode != 201) {
            throw new \Exception('Error while activating signature request');
        }

        return $response->toArray();
    }
}
