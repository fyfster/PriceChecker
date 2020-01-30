<?php

use Illuminate\Database\Seeder;

class CountyTableSeeder extends Seeder
{
    public function run()
    {
        DB::statement(
            "INSERT INTO `counties` (`id`, `country_id`, `name`, `code`) VALUES
                (1, 183, 'Teleorman', 'TR'),
                (2, 183, 'Olt', 'OT'),
                (3, 183, 'Dolj', 'DJ'),
                (4, 183, 'Giurgiu', 'GR'),
                (5, 183, 'Calarasi', 'CL'),
                (6, 183, 'Mehedinti', 'MH'),
                (7, 183, 'Constanta', 'CT'),
                (8, 183, 'Ilfov', 'IF'),
                (9, 183, 'Arges', 'AG'),
                (10, 183, 'Ialomita', 'IL'),
                (11, 183, 'Dambovita', 'DB'),
                (12, 183, 'Valcea', 'VL'),
                (13, 183, 'Gorj', 'GJ'),
                (14, 183, 'Caras-Severin', 'CS'),
                (15, 183, 'Prahova', 'PH'),
                (16, 183, 'Braila', 'BR'),
                (17, 183, 'Buzau', 'BZ'),
                (18, 183, 'Timis', 'TM'),
                (19, 183, 'Tulcea', 'TL'),
                (20, 183, 'Hunedoara', 'HD'),
                (21, 183, 'Vrancea', 'VN'),
                (22, 183, 'Brasov', 'BR'),
                (23, 183, 'Galati', 'GL'),
                (24, 183, 'Sibiu', 'SB'),
                (25, 183, 'Covasna', 'CV'),
                (26, 183, 'Alba', 'AB'),
                (27, 183, 'Arad', 'AR'),
                (28, 183, 'Vaslui', 'VL'),
                (29, 183, 'Harghita', 'HR'),
                (30, 183, 'Bacau', 'BC'),
                (31, 183, 'Mures', 'MS'),
                (32, 183, 'Bihor', 'BH'),
                (33, 183, 'Cluj', 'CJ'),
                (34, 183, 'Neamt', 'NT'),
                (35, 183, 'Bistrita-Nasaud', 'BN'),
                (36, 183, 'Iasi', 'IS'),
                (37, 183, 'Salaj', 'SJ'),
                (38, 183, 'Suceava', 'SV'),
                (39, 183, 'Maramures', 'MM'),
                (40, 183, 'Satu Mare', 'SM'),
                (41, 183, 'Botosani', 'BT'),
                (42, 183, 'Bucuresti', 'B');"
            );
    }
}
