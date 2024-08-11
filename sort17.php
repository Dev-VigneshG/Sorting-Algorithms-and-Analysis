<?php

function bubbleSort(&$array) {
    $n = count($array);
    for ($i = 0; $i < $n - 1; $i++) {
        for ($j = 0; $j < $n - $i - 1; $j++) {
            if ($array[$j] > $array[$j + 1]) {
                $temp = $array[$j];
                $array[$j] = $array[$j + 1];
                $array[$j + 1] = $temp;
            }
        }
    }
}

function selectionSort(&$array) {
    $n = count($array);
    for ($i = 0; $i < $n - 1; $i++) {
        $minIndex = $i;
        for ($j = $i + 1; $j < $n; $j++) {
            if ($array[$j] < $array[$minIndex]) {
                $minIndex = $j;
            }
        }
        $temp = $array[$i];
        $array[$i] = $array[$minIndex];
        $array[$minIndex] = $temp;
    }
}

function insertionSort(&$array) {
    $n = count($array);
    for ($i = 1; $i < $n; $i++) {
        $key = $array[$i];
        $j = $i - 1;
        while ($j >= 0 && $array[$j] > $key) {
            $array[$j + 1] = $array[$j];
            $j--;
        }
        $array[$j + 1] = $key;
    }
}

function quickSort(&$array, $low, $high) {
    if ($low < $high) {
        $pi = partition($array, $low, $high);
        quickSort($array, $low, $pi - 1);
        quickSort($array, $pi + 1, $high);
    }
}

function partition(&$array, $low, $high) {
    $pivot = $array[$high];
    $i = ($low - 1);
    for ($j = $low; $j < $high; $j++) {
        if ($array[$j] < $pivot) {
            $i++;
            $temp = $array[$i];
            $array[$i] = $array[$j];
            $array[$j] = $temp;
        }
    }
    $temp = $array[$i + 1];
    $array[$i + 1] = $array[$high];
    $array[$high] = $temp;
    return $i + 1;
}

function heapify(&$array, $n, $i) {
    $largest = $i;
    $left = 2 * $i + 1;
    $right = 2 * $i + 2;

    if ($left < $n && $array[$left] > $array[$largest]) {
        $largest = $left;
    }

    if ($right < $n && $array[$right] > $array[$largest]) {
        $largest = $right;
    }

    if ($largest != $i) {
        $temp = $array[$i];
        $array[$i] = $array[$largest];
        $array[$largest] = $temp;

        heapify($array, $n, $largest);
    }
}

function heapSort(&$array) {
    $n = count($array);

    for ($i = $n / 2 - 1; $i >= 0; $i--) {
        heapify($array, $n, $i);
    }

    for ($i = $n - 1; $i > 0; $i--) {
        $temp = $array[0];
        $array[0] = $array[$i];
        $array[$i] = $temp;

        heapify($array, $i, 0);
    }
}

function merge(&$array, $left, $middle, $right) {
    $n1 = $middle - $left + 1;
    $n2 = $right - $middle;

    $L = array_slice($array, $left, $n1);
    $R = array_slice($array, $middle + 1, $n2);

    $i = 0;
    $j = 0;
    $k = $left;

    while ($i < $n1 && $j < $n2) {
        if ($L[$i] <= $R[$j]) {
            $array[$k] = $L[$i];
            $i++;
        } else {
            $array[$k] = $R[$j];
            $j++;
        }
        $k++;
    }

    while ($i < $n1) {
        $array[$k] = $L[$i];
        $i++;
        $k++;
    }

    while ($j < $n2) {
        $array[$k] = $R[$j];
        $j++;
        $k++;
    }
}

function mergeSort(&$array, $left, $right) {
    if ($left < $right) {
        $middle = (int)(($left + $right) / 2);

        mergeSort($array, $left, $middle);
        mergeSort($array, $middle + 1, $right);

        merge($array, $left, $middle, $right);
    }
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $data = $_POST['data'];
    $algorithm = $_POST['algorithm'];

    $array = array_map('trim', explode(',', $data));
    $array = array_map('intval', $array);



    function findDuplicates($array) {
        $counts = array_count_values($array);
        return array_filter($counts, function($count) {
            return $count > 1;
        });
    }

    $duplicates = findDuplicates($array);
    $hasDuplicates = !empty($duplicates);

    $timings = [];

    function timeSort(&$array, $sortFunction) {
        $start = microtime(true);
        $sortFunction($array);
        $end = microtime(true);
        return ($end - $start) * 1000; 
    }

    $algorithms = [
        'bubble' => 'bubbleSort',
        'selection' => 'selectionSort',
        'insertion' => 'insertionSort',
        'quick' => 'quickSort',
        'heap' => 'heapSort',
        'merge' => 'mergeSort'
    ];

    foreach ($algorithms as $name => $func) {
        
        $arrayCopy = $array;
        if ($name === 'quick' || $name === 'merge') {
            $time = timeSort($arrayCopy, function(&$array) use ($func) { $func($array, 0, count($array) - 1); });
        } else {
            $time = timeSort($arrayCopy, $func);
        }
        $timings[$name] = $time;
    }

    $selectedAlgorithm = $algorithms[$algorithm];
    if ($algorithm === 'quick' || $algorithm === 'merge') {
        $selectedAlgorithm($array, 0, count($array) - 1);
    } else {
        $selectedAlgorithm($array);
    }

    $sortedArray = implode(', ', $array);
    $timingsJson = json_encode($timings);


    $minTime = min($timings);
    $bestAlgorithm = array_search($minTime, $timings);
    $suggestion = "The fastest sorting algorithm for your data is " . ucfirst($bestAlgorithm) . ".";
    if (count($array) > 1000) {
        $suggestion .= " For very large datasets, algorithms like Quick Sort, Heap Sort, and Merge Sort are generally more efficient.";
    }
    if ($hasDuplicates) {
        $suggestion .= " Note: Your data contains duplicates. Algorithms like Insertion Sort might handle duplicates well but may be slower for large datasets.";
    }
} else {
    $sortedArray = "";
    $timingsJson = "{}";
    $suggestion = "";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sorting Algorithm Selector</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
       * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f0f0f0;
            color: #333;
            line-height: 1.6;
        }
        h1 {
            text-align: center;
            color: #007bff;
            margin-bottom: 20px;
        }
        /* Container Styles */
        .container {
            width: 900px;
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
        }
        /* Form Styles */
        form {
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        input[type="text"] {
            padding: 10px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 5px;
            width: 100%;
            max-width: 600px;
            margin-bottom: 20px;
        }
        select {
            padding: 10px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 5px;
            margin-bottom: 20px;
            width: 100%;
            max-width: 300px;
        }
        input[type="submit"] {
            padding: 10px 20px;
            font-size: 16px;
            border: none;
            border-radius: 5px;
            background-color: #007bff;
            color: #fff;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        input[type="submit"]:hover {
            background-color: #0056b3;
        }
        .result {
            margin-top: 30px;
            padding: 20px;
            background-color: #e9ecef;
            border-radius: 10px;
            text-align: center;
            line-height: 1.8;
        }
        .result strong {
            color: #007bff;
        }
        /* Chart Styles */
        .chart-container {
            margin-top: 30px;
            width: 100%;
            max-width: 800px;
            height: 400px;
        }
        .suggestion {
            margin-top: 20px;
            font-size: 18px;
            text-align: center;
        }
        .suggestion i {
            color: #007bff;
            margin-right: 5px;
        }
        @media (max-width: 768px) {
            .container {
                width: 90%;
                padding: 15px;
            }
            input[type="text"],
            select {
                width: 100%;
            }
            input[type="submit"] {
                width: 100%;
            }
        }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <div class="container">
        <h1>Sorting Algorithms and Analysis</h1>
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
        Enter numbers separated by commas
            <input type="text" name="data" placeholder="Enter numbers separated by commas" value="<?php echo isset($data) ? htmlspecialchars($data) : ''; ?>" required>
            <select name="algorithm" required>
                <option value="bubble" <?php echo isset($algorithm) && $algorithm == 'bubble' ? 'selected' : ''; ?>>Bubble Sort</option>
                <option value="selection" <?php echo isset($algorithm) && $algorithm == 'selection' ? 'selected' : ''; ?>>Selection Sort</option>
                <option value="insertion" <?php echo isset($algorithm) && $algorithm == 'insertion' ? 'selected' : ''; ?>>Insertion Sort</option>
                <option value="quick" <?php echo isset($algorithm) && $algorithm == 'quick' ? 'selected' : ''; ?>>Quick Sort</option>
                <option value="heap" <?php echo isset($algorithm) && $algorithm == 'heap' ? 'selected' : ''; ?>>Heap Sort</option>
                <option value="merge" <?php echo isset($algorithm) && $algorithm == 'merge' ? 'selected' : ''; ?>>Merge Sort</option>
            </select>
            <input type="submit" value="Sort">
        </form>
        <?php if ($_SERVER["REQUEST_METHOD"] == "POST") : ?>
        <div class="result">
            <strong>Sorted Array:</strong><br>
            <?php echo $sortedArray; ?>
            <br>
            <strong>Running Time:</strong><br>
<ul>
    <?php

    $timingsArray = json_decode($timingsJson, true);
    foreach ($timingsArray as $algorithm => $time) {
        echo ucfirst($algorithm) . " Sort: " . number_format($time * 1000, 2) . " ms<br>";
    }
    ?>
</ul>

        </div>
        <div class="chart-container">
            <canvas id="timingChart"></canvas>
        </div>
        <div class="suggestion">
            <i class="fas fa-lightbulb"></i> <?php echo $suggestion; ?>
        </div>
        <script>
            const ctx = document.getElementById('timingChart').getContext('2d');
            const timings = <?php echo $timingsJson; ?>;
            const labels = Object.keys(timings).map(algorithm => algorithm.charAt(0).toUpperCase() + algorithm.slice(1) + ' Sort');
            const data = Object.values(timings);

            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Sorting Time (ms)',
                        data: data,
                        backgroundColor: 'rgba(0, 123, 255, 0.5)',
                        borderColor: 'rgba(0, 123, 255, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        </script>
        <?php endif; ?>
    </div>
</body>
</html>
