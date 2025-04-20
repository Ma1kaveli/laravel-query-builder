<!-- TODO:
    $points = array_map(
        fn($p) => "{$p['lon']} {$p['lat']}",
        $value['points']
    );

    $query->whereRaw(
        "ST_Within(
            ST_GeomFromText(CONCAT('POINT(', {$field}->>'$.lon', ' ', {$field}->>'$.lat', ')')),
            ST_GeomFromText(?)
        )",
        ["POLYGON((".implode(', ', $points)."))"]
    );
-->