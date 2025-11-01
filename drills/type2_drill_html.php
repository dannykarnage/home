<div class="history-data-display">
    <div class="history-data-separator-two-column">
        <div class="two-column-table-container">
            <table>
                <tbody>
                    <tr>
                        <th class="date-column-header">Date</th>
                        <th class="data-column-header">Score</th>
                    </tr>
                    <?php $odd = true; ?>
                    <?php for ($i = 0; $i < count($data["timestamp"]); $i++): ?>
                        <tr>
                            <?php if($odd): ?>
                                <?php $odd = false; ?>
                                <th class="date-column-odd">
                                    <?php echo substr($data["timestamp"][$i], 0, 10); ?>
                                </th>
                                <th class="data-column-odd">
                                    <?php echo $data["score"][$i]; ?>
                                </th>
                            <?php else: ?>
                                <?php $odd = true; ?>
                                <th class="date-column-even">
                                    <?php echo substr($data["timestamp"][$i], 0, 10); ?>
                                </th>
                                <th class="data-column-even">
                                    <?php echo $data["score"][$i]; ?>
                                </th>
                            <?php endif; ?>
                        </tr>
                    <?php endfor; ?>
                </tbody>
            </table>
        </div>

        <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
        <div id="chart_div" class="chart-container">
            <script>
                google.charts.load('current', {'packages':['corechart']});
                google.charts.setOnLoadCallback(drawChart);
            
                function drawChart()
                {
                    var data = new google.visualization.DataTable();
                    data.addColumn('datetime', 'Date');
                    data.addColumn('number', 'Score');

                    data.addRows([
                        <?php
                            for($i = 0; $i < count($data["timestamp"]); $i++)
                            {
                                
                                $date_year = intval(substr($data["timestamp"][$i], 0, 4));
                                $date_month = intval(substr($data["timestamp"][$i], 5, 2));
                                $date_day = intval(substr($data["timestamp"][$i], 8, 2));
                                $date_hour = intval(substr($data["timestamp"][$i], 11, 2));
                                $date_minute = intval(substr($data["timestamp"][$i], 14, 2));
                                $date_second = intval(substr($data["timestamp"][$i], 17, 2));
                                $datestr = $date_year . ', ' . ($date_month - 1) . ', ' . $date_day . ', ' . $date_hour . ', ' . $date_minute . ', ' . $date_second;
                                echo "[new Date(" . $datestr . "), " . $data["score"][$i] . "]";
                                if(count($data["timestamp"]) == 1)
                                {
                                    $minDateStr = $date_year . ', ' . ($date_month - 1) . ', ' . $date_day . ', ' . $date_hour . ', ' . $date_minute . ', ' . ($date_second - 1);
                                    $maxDateStr = $date_year . ', ' . ($date_month - 1) . ', ' . $date_day . ', ' . $date_hour . ', ' . $date_minute . ', ' . ($date_second + 1);
                                }
                                else
                                {
                                    if($i == 0)
                                    {
                                        $minDateStr = $datestr;
                                        echo ", ";
                                    }
                                    elseif($i == (count($data["timestamp"]) - 1))
                                    {
                                        $maxDateStr = $datestr;
                                    }
                                    else
                                    {
                                        echo ", ";
                                    }
                                }
                            }
                        ?>
                    ]);

                    var options = {
                        width: 600,
                        height: 400,
                        legend: {position: 'none'},
                        pointSize: 5,
                        enableInteractivity: true,
                        chartArea: {
                            width: '90%'
                        },
                        hAxis: {
                            viewWindow: {
                                min: new Date(<?php echo $minDateStr; ?>),
                                max: new Date(<?php echo $maxDateStr; ?>)
                            },
                            gridlines: {
                                count: -1,
                                units: {
                                    days: {format: ['MMM dd']},
                                    hours: {format: ['HH:mm', 'ha']},
                                }
                            },
                            minorGridlines: {
                                units: {
                                    hours: {format: ['hh:mm:ss a', 'ha']},
                                    minutes: {format: ['HH:mm a Z', ':mm']}                                    
                                }
                            }
                        },
                        vAxis: {
                            minValue: <?php echo $min_score; ?>,
                            maxValue: <?php echo $max_score; ?>
                        }
                    };
                    
                    var chart = new google.visualization.LineChart(
                    document.getElementById('chart_div'));
                    chart.draw(data, options);
                }
            </script>
        </div>
    </div>
</div>