<?php

use Illuminate\Database\Seeder;

class CityTableSeeder extends Seeder
{
    public function run()
    {
        $citySeed = file_get_contents(__DIR__ . "/city/city.txt");
        $separator = "\r\n";
        $line = strtok($citySeed, $separator);
        $existingCitiesArray = [];
        $newCitySeed = "";
        while ($line !== false) {
            $lineInfo = explode(', ', $line);
            $cityName = strtolower($lineInfo[2]. "_" . $lineInfo[4]);
            if (!in_array($cityName, $existingCitiesArray)) {
                $existingCitiesArray[] = $cityName;
                $newCitySeed .= $line . PHP_EOL;
            }
            $line = strtok($separator);
        }
        $newCitySeed = substr(trim($newCitySeed, PHP_EOL), 0, -1) . ";";

        DB::statement(
            "INSERT INTO `cities` (`id`, `parent_id`, `name`, `type`, `county_id`) VALUES"
            .$newCitySeed
        );

        DB::statement(
            "UPDATE cities
            SET name = CONCAT(UCASE(LEFT(name, 1)), LCASE(SUBSTRING(name, 2)))
            WHERE parent_id is null;"
        );
    }
}
?>
