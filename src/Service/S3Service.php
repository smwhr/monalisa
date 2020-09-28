<?php 

namespace App\Service;

use Aws\S3\S3Client;
use Aws\CloudFront\CloudFrontClient;

class S3Service{

  public function __construct(S3Client $s3, $bucket){
    $this->s3 = $s3;
    $this->bucket = $bucket;
  }


  public function upload($file_data, $destination,  $content_type = 'application/octet-stream', $acl = 'public-read')
  { 
    return $this->s3->putObject([
                'Bucket'      => $this->bucket,
                'Key'         => $destination,
                'Body'        => $file_data,
                'ACL'         => $acl,
                'ContentType' => $content_type,
                'CacheControl' => 'max-age=604800'
                ]);
  }
}