<?php

namespace App\Infrastructure\Services\Converters;

use App\Application\Service\FileUploader\S3\S3FileManager;

class PaginationToTemporaryLinks
{

    public function __construct(public S3FileManager $fileManager)
    {
    }

    public function convertPagination($items, $timeOfAccess)
    {
        $itemList = $items->getItems();

        foreach ($itemList as &$item) {

            $item['files'] = json_decode($item['files'], true);

            $fileList = [];
            foreach ($item['files'] as $file) {
                if ($file) {
                    $fileList[] = $this->fileManager->getTemporaryLink($file, $timeOfAccess);
                }

            }
            $item['files'] = $fileList;
        }
        return $itemList;
    }

    public function convertItem($item, $timeOfAccess)
    {
        $item['files'] = json_decode($item['files'], true);

        $fileList = [];
        foreach ($item['files'] as $file) {
            if ($file) {
                $fileList[] = $this->fileManager->getTemporaryLink($file, $timeOfAccess);
            }

        }
        $item['files'] = $fileList;
        return $item;
    }
}