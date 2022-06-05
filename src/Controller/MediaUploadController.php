<?php
// api/src/Controller/MediaUploadController.php

namespace App\Controller;

use App\Entity\Media;
use App\Factory\JsonResponseFactory;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Aws\S3\S3Client;
use Aws\S3\Exception\S3Exception;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Uid\Uuid;

class MediaUploadController extends AbstractController
{
    public function __construct(
        EntityManagerInterface $entityManager,
        JsonResponseFactory $jsonResponseFactory
    ) {
        $this->entityManager = $entityManager;
        $this->jsonResponseFactory = $jsonResponseFactory;
    }
    /**
     * @Route("/api/media_upload", methods={"POST"}, name="media_upload")
     * @return Response
     */
    public function __invoke(): Response
    {
        if (isset($_FILES['file'])) {
            $uuid = Uuid::v4();
            $file_name = $uuid . '_' . $_FILES['file']['name'];
            $temp_file_location = $_FILES['file']['tmp_name'];
        } else {
            throw new BadRequestHttpException('Aucun fichier trouvé');
        };

        $s3 = new S3Client([
            'region'  => 'eu-central-1',
            'version' => 'latest',
            'credentials' => [
                'key'    => $_ENV['AWS_ACCESS_ID'],
                'secret' => $_ENV['AWS_SECRET_KEY'],
            ]
        ]);
        try {
            $result = $s3->putObject([
                'Bucket' => 'swoome',
                'Key'    => 'avatars/' . $file_name,
                'SourceFile' => $temp_file_location
            ]);
        } catch (S3Exception $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        // Print the body of the result by indexing into the result object.


        $media = new Media();
        $media->setUrl($result->get('ObjectURL'));

        $this->entityManager->persist($media);
        $this->entityManager->flush();

        return $this->jsonResponseFactory->create($media);
    }
}
