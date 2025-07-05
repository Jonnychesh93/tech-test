<?php

namespace App\Services;

use App\Models\Title;
use Exception;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class HomeownerCsvUploadService {
    private Collection $titles;
    public function __construct() {
        $this->titles = Title::pluck('display_title', 'csv_title');
    }

    /**
     * Processes the uploaded CSV file.
     *
     * @param UploadedFile $file
     * @param bool $hasHeader
     * @return array
     * @throws Exception
     */
    public function processCsv(UploadedFile $file, bool $hasHeader = true): array {
        $handle = fopen($file->getRealPath(), 'r');

        if(!$handle) {
            throw new Exception('Failed to open file.');
        }

        $headerRow  = true;
        $array = [];

        while (($row = fgetcsv($handle)) !== FALSE) {
            if ($hasHeader && $headerRow) {
                $headerRow = false;
                continue;
            }

            $rowData = trim($row[0]);
            if (empty($rowData)) {
                continue;
            }

            if(str::contains($rowData, [' and ', '&'], true)) {
                $twoPeople = $this->processRowWithTwoPeople($rowData);
                $array = array_merge($array, $twoPeople);
            } else {
                $array[] = $this->processSinglePerson($rowData);
            }
        }

        fclose($handle);

        return $array;
    }

    /**
     * Processes a single person row.
     * @param $row
     * @return array
     * @throws Exception
     */
    private function processSinglePerson($row): array {
        return $this->personArray($row);
    }

    /**
     * Processes a row with two people, splitting by ' and ' or '&'.
     * @param $row
     * @return array
     * @throws Exception
     */
    private function processRowWithTwoPeople($row): array {
        $people = preg_split('/(\s*and\s*|\s*&\s*)/', $row);

        if(count($people) !== 2) {
            throw new Exception("Expected exactly two people, found: " . count($people));
        }

        $returnArray[0] = $this->personArray($people[0], false);
        $emptyInitialFirstName = empty($returnArray[0]['first_name']) && empty($returnArray[0]['initial']);
        $returnArray[1] = $this->personArray($people[1], false, $emptyInitialFirstName);

        if(is_null($returnArray[0]['last_name'])) {
            $returnArray[0]['last_name'] = $returnArray[1]['last_name'];
        }

        return $returnArray;
    }

    /**
     *
     * Validates the title against the Database Table.
     * @param string $title
     * @return string
     * @throws Exception
     */
    private function titleValidation(string $title): string {
        if(!$this->titles->has(Str::lower($title))) {
            throw new Exception("Unable to find title: $title");
        }

        return $this->titles->get(Str::lower($title));
    }

    /**
     * Checks if the middle section is an Initial or firstname
     *
     * @param string $person
     * @return array
     */
    private function initialOrFirstName(string $person): array {
        if(Str::length($person) == 1 || (Str::length($person) === 2 && Str::endsWith($person, '.'))) {
            return ['initial' => Str::rtrim($person, '.')];
        } else {
            return ['first_name' => $person];
        }
    }

    /**
     * Does all the Split processing.
     * @param string $row
     * @param bool $isSinglePerson
     * @param bool $firstPersonEmpty
     * @return array
     * @throws Exception
     */
    private function personArray(string $row, bool $isSinglePerson = true, bool $firstPersonEmpty = false): array {
        $personExplode = explode(' ', trim($row));

        $returnArray = [
            'title'      => null,
            'first_name' => null,
            'initial'    => null,
            'last_name'  => null,
        ];

        $splitCount = count($personExplode);

        if($splitCount == 1) {
            if($isSinglePerson) {
                throw new Exception("Only one word found - Unable to match details correctly: " . $personExplode[0]);
            } else {
                $returnArray['title'] = $this->titleValidation($personExplode[0]);
            }

        } elseif ($splitCount == 2) {
            $returnArray['title']     = $this->titleValidation($personExplode[0]);
            $returnArray['last_name'] = $personExplode[1];

        } elseif ($splitCount == 3) {
            $returnArray['title'] = $this->titleValidation($personExplode[0]);

            // Not sure what the process would be normally with the Joe bloggs, I can explain why i've done a check here..
            $middleSection = $this->initialOrFirstName($personExplode[1]);
            if(!$firstPersonEmpty && !empty($middleSection)) {
                $returnArray = array_merge($returnArray, $middleSection);
            }

            $returnArray['last_name'] = $personExplode[2];

        } else {
            throw new Exception("Unexpected number - unable to map details correctly: " . $row);
        }

        return $returnArray;
    }
}
