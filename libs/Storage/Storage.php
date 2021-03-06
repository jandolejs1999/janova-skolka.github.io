<?php

namespace App\Storage;

use Nette\Utils\Json;
use Nette\Utils\Random;
use Nette\Utils\Strings;

class Storage
{

    private $path;

    public function __construct($path)
    {
        $this->path = $path;
    }

    public function findKeys()
    {

        $data = [];
        $files = scandir($this->path);

        foreach ($files as $file) {
            if (preg_match('/^\d{4}(-\d\d){5}-.+\.json$/', $file)) {
                $data[] = $file;
            }
        }

        return $data;
    }

    public function getByKey($key)
    {

        $content = file_get_contents($this->path . "/" . $key);
        $data = Json::decode($content, Json::FORCE_ARRAY);
        return $data;
    }

    public function save($name, $data)
    {
        $dataToWrite = Json::encode($data, Json::PRETTY);

        $filePath = $this->getNewFilePath($name);

        $isFileSaved = @file_put_contents($filePath, $dataToWrite);

        if ($isFileSaved === false) {
            throw new StorageException('Unable to save file ' . $filePath);
        }
    }

    private function getNewFilePath($name)
    {
        $outputFolder = $this->path;

        $date = date('Y-m-d-H-i-s');
        $name = $this->sanitizeName($name);
        $random = Random::generate(4);

        return "$outputFolder/$date-$name-$random.json";
    }

    private function sanitizeName($name)
    {
        $name = Strings::webalize($name);
        $name = Strings::truncate($name, '30', '');
        return $name;
    }
}
