<?php
// api/src/Controller/CreateBookPublication.php

namespace App\Controller;

use App\Entity\Balance;
use App\Factory\JsonResponseFactory;
use App\Entity\Group;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Aws\S3\S3Client;

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
     * @param Request $request
     * @return Response
     */
    public function __invoke(Request $request): Response
    {
        if (isset($_FILES['file'])) {
            $file_name = $_FILES['file']['name'];
            $temp_file_location = $_FILES['file']['tmp_name'];
        }

        $s3 = new S3Client([
            'region'  => 'eu-central-1',
            'version' => 'latest',
            'credentials' => [
                'key'    => $_ENV['AWS_ACCESS_ID'],
                'secret' => $_ENV['AWS_SECRET_KEY'],
            ]
        ]);

        $result = $s3->putObject([
            'Bucket' => 'arn:aws:s3:::swoome/avatars',
            'Key'    => $file_name,
            'SourceFile' => $temp_file_location
        ]);

        // Print the body of the result by indexing into the result object.
        var_dump($result);

        /** @var User $user */
        $user = $this->getUser();

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $this->jsonResponseFactory->create($user);
    }
}
