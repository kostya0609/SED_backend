<?php
namespace SED\Documents\Common\Seeders\Initial;

use SED\Documents\Common\Models\DocumentType;
use SED\Documents\Common\Seeders\SeederInterface;

class DocumentTypeSeeder implements SeederInterface
{
    function run()
    {
        $data = [
            [
                'id' => 1,
                'title' => 'ЭСЗ',
            ],
            [
                'id' => 2,
                'title' => 'Поручение',
            ],
            [
                'id' => 3,
                'title' => 'Ознакомление',
            ],
        ];

        DocumentType::query()->upsert($data, ['id'], ['title']);
    }
}