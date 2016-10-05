<?php
/**
 *  Copyright (C) 2016 SURFnet.
 *
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU Affero General Public License as
 *  published by the Free Software Foundation, either version 3 of the
 *  License, or (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU Affero General Public License for more details.
 *
 *  You should have received a copy of the GNU Affero General Public License
 *  along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */
namespace SURFnet\VPN\Common;

use RuntimeException;

class FileIO
{
    public static function readFile($filePath)
    {
        if (false === $fileData = @file_get_contents($filePath)) {
            throw new RuntimeException(sprintf('unable to read file "%s"', $filePath));
        }

        return $fileData;
    }

    public static function readJsonFile($filePath)
    {
        $fileData = self::readFile($filePath);
        $jsonData = json_decode($fileData, true);
        if (JSON_ERROR_NONE !== json_last_error()) {
            throw new RuntimeException(sprintf('unable to decode JSON from file "%s"', $filePath));
        }

        return $jsonData;
    }

    public static function writeFile($filePath, $fileData)
    {
        if (false === @file_put_contents($filePath, $fileData)) {
            throw new RuntimeException(sprintf('unable to write file "%s"', $filePath));
        }
    }

    public static function writeJsonFile($filePath, $fileJsonData)
    {
        $fileData = json_encode($fileJsonData);
        if (JSON_ERROR_NONE !== json_last_error()) {
            throw new RuntimeException(sprintf('unable to encode JSON for file "%s"', $filePath));
        }

        self::writeFile($filePath, $fileData);
    }

    public static function deleteFile($filePath)
    {
        if (false === @unlink($filePath)) {
            throw new RuntimeException(sprintf('unable to delete file "%s"', $filePath));
        }
    }

    public static function createDir($dirPath, $mode = 0711)
    {
        if (!@file_exists($dirPath)) {
            if (false === @mkdir($dirPath, $mode, true)) {
                throw new RuntimeException(sprintf('unable to create directory "%s"', $dirPath));
            }
        }
    }
}
