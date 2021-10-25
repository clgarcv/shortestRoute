<?php

    $graph = array(
        array("Logroño", "Zaragoza", 4),
        array("Logroño", "Teruel", 6),
        array("Logroño", "Madrid", 8),
        array("Zaragoza", "Teruel", 2),
        array("Zaragoza", "Lleida", 2),
        array("Teruel", "Madrid", 3),
        array("Teruel", "Lleida", 5),
        array("Teruel", "Alicante", 7),
        array("Lleida", "Castellón", 4),
        array("Lleida", "Segovia", 8),
        array("Alicante", "Castellón", 3),
        array("Alicante", "Ciudad Real", 7),
        array("Castellón", "Ciudad Real", 6),
        array("Ciudad Real", "Segovia", 5)
    );

    function shortest_path_oneToOne($graph, $source, $target) {

        $vertices = array();
        $links = array();

        foreach ($graph as $edge) {
            array_push($vertices, $edge[0], $edge[1]);
            $links[$edge[0]][] = array('e' => $edge[1], 'c' => $edge[2]);
            $links[$edge[1]][] = array('e' => $edge[0], 'c' => $edge[2]);
        }
        // get unique vertices
        $vertices = array_unique($vertices);

        // initialize values
        foreach ($vertices as $vertex) {
            $distance[$vertex] = INF;
            $previous[$vertex] = NULL;
        }

        $distance[$source] = 0;
        $V = $vertices;
        while (count($V) > 0) {

            $distMin = INF;
            foreach ($V as $vertex){
                if ($distance[$vertex] < $distMin) {
                    $distMin = $distance[$vertex];
                    $u = $vertex;
                }
            }

            // delete processed vertex
            $V = array_diff($V, array($u));
            if ($distance[$u] == INF or $u == $target) {
                break;
            }

            if (isset($links[$u])) {
                foreach ($links[$u] as $arr) {
                    $alt = $distance[$u] + $arr['c'];
                    if ($alt < $distance[$arr['e']]) {
                        $distance[$arr['e']] = $alt;
                        $previous[$arr['e']] = $u;
                    }
                }
            }
        }
        $path = array();
        $u = $target;
        while (isset($previous[$u])) {
            array_unshift($path, $u);
            $u = $previous[$u];
        }
        array_unshift($path, $u);
        // add total cost in each step
        foreach ($path as $index => $value) {
            $path[$index] = [$path[$index], $distance[$value]];
        }
        return [$path,$distMin];
    }

    function shortest_path_oneToMany($graph, $source) {

        $vertices = array();
        $links = array();
        $routeCity = array();

        foreach ($graph as $edge) {
            array_push($vertices, $edge[0], $edge[1]);
            $links[$edge[0]][] = array('e' => $edge[1], 'c' => $edge[2]);
            $links[$edge[1]][] = array('e' => $edge[0], 'c' => $edge[2]);
        }
        // get unique vertices
        $vertices = array_unique($vertices);

        foreach ($vertices as $city) {
            // initialize values
            foreach ($vertices as $vertex) {
                $distance[$vertex] = INF;
                $previous[$vertex] = NULL;
            }

            $distance[$source] = 0;
            $V = $vertices;
            while (count($V) > 0) {

                $distMin = INF;
                foreach ($V as $vertex){
                    if ($distance[$vertex] < $distMin) {
                        $distMin = $distance[$vertex];
                        $u = $vertex;
                    }
                }

                // delete processed vertex
                $V = array_diff($V, array($u));
                if ($distance[$u] == INF or $u == $city) {
                    break;
                }

                if (isset($links[$u])) {
                    foreach ($links[$u] as $arr) {
                        $alt = $distance[$u] + $arr['c'];
                        if ($alt < $distance[$arr['e']]) {
                            $distance[$arr['e']] = $alt;
                            $previous[$arr['e']] = $u;
                        }
                    }
                }
            }
            $path = array();
            $u = $city;
            while (isset($previous[$u])) {
                array_unshift($path, $u);
                $u = $previous[$u];
            }
            array_unshift($path, $u);
            array_push($routeCity,array($city,implode(", ", $path),$distMin));
        }

        return $routeCity;

    }

    //Example of use

    $source = "Logroño";
    $target = "Ciudad Real";

    // Shortest route between two points
    [$path,$cost] = shortest_path_oneToOne($graph, $source, $target);

    foreach ($path as $index =>$stop){
        if ($index > 0){
            $lista[$index] = $stop[0]." (".$stop[1] - intval($path[$index-1][1]).")";
        } else {
            $lista[$index] = $stop[0]." (".$stop[1].")";
        }
    }
    echo "The shortest path between ".$source." y ".$target." is: ".implode(", ", $lista)." and the total distance is: ".$cost.".";


    $source2 = "Lleida";
    // Shortest path from one to all
    $routes = shortest_path_oneToMany($graph,$source2);
    echo "\n\nShortest paths from ".$source2."\n";
    foreach ($routes as $r) {
        if ($source2 != $r[0]) {
            echo "\nThe shortest path between ".$source2." y ".$r[0]." is: ".$r[1]." and the distance is: ".$r[2];
        }
    }


?>
