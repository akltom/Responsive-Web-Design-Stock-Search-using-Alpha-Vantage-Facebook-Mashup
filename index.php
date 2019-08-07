<!DOCTYPE html>
<html>
	<head>
    	<title>HW6</title>
		<style type="text/css">
    		.stockSearchTable { 
				align: center;
				border: 1.00px solid;
				border-color: #e1e1e1;
				background-color: #f4f4f4;
				font-family: Times New Roman;
				width: 390px; 
			}
			.stockSearchText {
				font-size: 28px;
				font-style: italic;
				font-weight: bold;
				text-align: center;
				vertical-align: bottom;
			}
			hr {
				background-color: lightgray;
				border: 0.45px;
				color: lightgray;
				height:1px;
				margin-top: -0.2em;
			}
			.twoButton {
				display: block; 
				margin-left: 200px;  
			}
			.italicText {
				background-color: #f2f2f2;
				font-family:Times New Roman;
				font-size:17px;
				font-style:italic;
				text-align:left;
			}
			.informationTable {
				border: 2px solid lightgray;
				border-collapse: collapse;
				padding: 5px;
			}
			td.tableFields {
				font-size:13px;
				height:20px;
				border: 1px solid #dfdfdf;
				text-align: left;
				font-family: sans-serif;
				width: 320px;
				background-color: #F3F3F3;
			}
			td.tableValues {
				height:20px;
				font-size:13px;
				border: 1px solid #dfdfdf;
				font-family: sans-serif;
				text-align: center;
				width: 720px;
				background-color: #fbfbfb;
			}
			#container { 
				border: 1.85px solid #dfdfdf; 
				margin-left:auto; 
				margin-right:auto; 
			}
			.blueToBlackIndicator {
				color: blue;
				text-decoration: none;
			}
			.blueToBlackIndicator:focus {
				outline: 0
			}
			.blueToBlackIndicator:hover {
				color: black
			}
			.blueToBlack {
				color: blue;
				text-decoration: none;
			}
			.blueToBlack:focus {
				color: black
			}
			.blueToBlack:hover {
				color: black
			}
			.blueToBlack:visited {
				color: blue; 
				text-decoration: none; 
			}
			button {
                background-color: #ffffff;
                border: none;
				color: #bbbbbb;
				font-size: 7.1px;
				margin-top: -10px;
            }
			.clickButtonLogo {
				height: 24px;
				margin-top: 8px;
				width: 40px;
			}
			.newsInner {
				 border: 1px solid #ececec; 
				 font-family: Helvetica; 
				 font-size: 14px;
				 height: 25px; 
			}
			.newsTable {
				border: 2px solid #d6d6d6; 
				border-collapse: collapse; 
				background-color: #fbfbfb; 
				width: 1045px; 
				position: relative; 
				top: -13px;
			}
            #stockNews{
				border-color: #ffffff;
				border-style: solid; 
                border-width: 1px;
				height: 111px;
                margin-left: 0;
                margin-top: -24.2px;
                width: 100%;
            }
			</style>
	
			<script>
			function clearOperation() { 
				document.getElementById("text").value = "";
				document.getElementById("output").innerHTML = "";
			}
			</script>
	</head>    
    
	<body>
    	<center>
        	<form id = "stockSearch" method = "POST" > 
				<table class = "stockSearchTable">
				<tr>
					<td class = "stockSearchText" colspan = 2>Stock Search</td>
				</tr>
				<tr>
					<td colspan = 2><hr></td>
				</tr>
				<tr>
					<td class = "text">Enter Stock Ticket Symbol:* &nbsp <input type = "text" id="text" name = "text" value = "<?php echo isset($_POST["text"]) ? $_POST["text"] : "" ?>" oninvalid = "alert('Please enter a symbol');" required><br></td>
				<tr>	
					<td class = "twoButton">
						<input type = "submit" value = "Search" name = "submit">
						<input type = "button" value = "Clear" name = "clear" onclick = "clearOperation()"></td>
				</tr>
				<tr>
					<td class = "italicText">* - Mandatory fields.</td>
				</tr>
				<tr>
					<td><br></td>
				</tr>
				</table>
            
            	<br>
			</form>
          
	<?php if (isset($_POST['submit'])): ?>
        <div id = "output" style =" margin: 0 auto; position: relative"> 
			<div>
            	<?php
            		$text = $_POST["text"];			
					$theURL = "https://www.alphavantage.co/query?function=TIME_SERIES_DAILY&symbol=".$text."&outputsize=full&apikey=IBML9TEYLJVP1G94";
                	$jsonDatas = file_get_contents($theURL, true); 
                	$phpDatas = json_decode($jsonDatas);
					if (isset($phpDatas->{"Error Message"})) {
						echo "<table border=1 class=\"informationTable\">";
						echo "<tr><td class=\"tableFields\">Error</td> <td class=\"tableValues\">Error: NO record has been found, please enter a valid symbol</td></tr>";
						echo "</table>";
						die();
					}

                    echo "<table border = 1 class=\"informationTable\">";
                    $stockSymbol = $phpDatas->{"Meta Data"}->{"2. Symbol"};
                    echo "<tr><td class=\"tableFields\">Stock Ticket Symbol</td> <td class=\"tableValues\">$stockSymbol</td></tr>";
                    //date_default_timezone_set ('UTC');
					ini_alter('date.timezone','America/New_York');

    				$lastRefreshed = $phpDatas->{"Meta Data"}->{"3. Last Refreshed"}; 
    
                    $timeTemp = new DateTime($lastRefreshed);  
                    $currenttimetemp=$timeTemp->format('Y-m-d');
                            
					$currentLoopDate = $phpDatas->{"Time Series (Daily)"}->$currenttimetemp; 
                    $closePrice = $currentLoopDate->{"4. close"};
                            
                    echo "<tr><td class=\"tableFields\">Close</td> <td class=\"tableValues\">$closePrice</td></tr>";

                    $openPrice = $currentLoopDate->{"1. open"}; 
                            
                    echo "<tr><td class=\"tableFields\">Open</td> <td class=\"tableValues\">$openPrice</td></tr>";
       
                    $lastDate = date($lastRefreshed); 
                    $previousDay = (date("Y-m-d",strtotime($lastDate)-24*60*60));
                  
                    while (!isset($phpDatas->{"Time Series (Daily)"}->$previousDay)) { 
                    	$previousDay = (date("Y-m-d",strtotime($previousDay)-24*60*60)); 
                    }
                       
                    $previousDayString = (string)$previousDay;         
                    $previousClosePrice = $phpDatas->{"Time Series (Daily)"}->$previousDayString->{"4. close"};
    
                    echo "<tr><td class=\"tableFields\">Previous Close</td> <td class=\"tableValues\">$previousClosePrice</td></tr>";
    				$change = $closePrice-$previousClosePrice;
                    $roundChange = round($change, 2);
    				if ($roundChange < 0) {
                    	echo "<tr><td class=\"tableFields\">Change</td> <td class=\"tableValues\">".$roundChange."<img src=\"http://cs-server.usc.edu:45678/hw/hw6/images/Red_Arrow_Down.png\" width=\"14.5\" height=\"14.5\" /></td></tr>";
                    }
                    else if ($roundChange > 0) {
                        echo "<tr><td class=\"tableFields\">Change</td> <td class=\"tableValues\">".$roundChange."<img src=\"http://cs-server.usc.edu:45678/hw/hw6/images/Green_Arrow_Up.png\" width=\"14.5\" height=\"14.5\" /></td></tr>";
                    }
                    else {
                        echo "<tr><td class=\"tableFields\">Change</td> <td class=\"tableValues\">".$roundChange."</td></tr>";
                    }

                    $changePercent = ($change/$previousClosePrice)*100;
                    $roundChangePercent = round($changePercent, 2);
                    if ($roundChangePercent < 0) {
                    	echo "<tr><td class=\"tableFields\">Change Percent</td> <td class=\"tableValues\">".$roundChangePercent."%<img src=\"http://cs-server.usc.edu:45678/hw/hw6/images/Red_Arrow_Down.png\" width=\"14.5\" height=\"14.5\" /></td></tr>";
                    }
                    else if ($roundChangePercent > 0){
                        echo "<tr><td class=\"tableFields\">Change Percent</td> <td class=\"tableValues\">".$roundChangePercent."%<img src=\"http://cs-server.usc.edu:45678/hw/hw6/images/Green_Arrow_Up.png\" width=\"14.5\" height=\"14.5\" /></td></tr>";
                    }  
                    else {
                        echo "<tr><td class=\"tableFields\">Change Percent</td> <td class=\"tableValues\">".$roundChangePercent."%</td></tr>";
                    }
                    $low = $currentLoopDate->{"3. low"};
                    $high = $currentLoopDate->{"2. high"};
                            
                    echo "<tr><td class=\"tableFields\">Day's Range</td> <td class=\"tableValues\">".$low."-".$high."</td></tr>";
                            
    				$volume = $currentLoopDate->{"5. volume"};
                    $commaVolume = number_format($volume);
                    echo "<tr><td class=\"tableFields\">Volume</td> <td class=\"tableValues\">".$commaVolume."</td></tr>";
                    $timestamp = new DateTime($lastRefreshed);
                    echo "<tr><td class=\"tableFields\">Timestamp</td> <td class=\"tableValues\">".$timestamp->format('Y-m-d')."</td></tr>";
                    echo "<tr><td class=\"tableFields\">Indicators</td> <td class=\"tableValues\">";
                    echo "<a class = \"blueToBlackIndicator\" href=\"javascript:void(0)\" onclick=\"firstGraph()\">Price</a>&nbsp&nbsp&nbsp&nbsp";   
                    echo "<a class = \"blueToBlackIndicator\" href=\"javascript:void(0)\" onclick=\"secondGraph()\">SMA</a>&nbsp&nbsp&nbsp&nbsp";
                    echo "<a class = \"blueToBlackIndicator\" href=\"javascript:void(0)\" onclick=\"thirdGraph()\">EMA</a>&nbsp&nbsp&nbsp&nbsp";
                    echo "<a class = \"blueToBlackIndicator\" href=\"javascript:void(0)\" onclick=\"fourthGraph()\">STOCH</a>&nbsp&nbsp&nbsp&nbsp";
                    echo "<a class = \"blueToBlackIndicator\" href=\"javascript:void(0)\" onclick=\"fifthGraph()\">RSI</a>&nbsp&nbsp&nbsp&nbsp";
                    echo "<a class = \"blueToBlackIndicator\" href=\"javascript:void(0)\" onclick=\"sixthGraph()\">ADX</a>&nbsp&nbsp&nbsp&nbsp";
                    echo "<a class = \"blueToBlackIndicator\" href=\"javascript:void(0)\" onclick=\"seventhGraph()\">CCI</a>&nbsp&nbsp&nbsp&nbsp";
                    echo "<a class = \"blueToBlackIndicator\" href=\"javascript:void(0)\" onclick=\"eighthGraph()\">BBANDS</a>&nbsp&nbsp&nbsp&nbsp";
                    echo "<a class = \"blueToBlackIndicator\" href=\"javascript:void(0)\" onclick=\"ninthGraph()\">MACD</a>&nbsp&nbsp&nbsp&nbsp";
                    echo "</td></tr></table>";
                	echo "<br>";
                
                       
                    $arrayDayReverse=array();
                    $arrayValueReverse=array();
                    $arrayVolumeReverse=array();
                    
                    $thisCounter=0;
                    foreach($phpDatas->{"Time Series (Daily)"} as $aKey => $aValue) {
                                
                    	$dayInDate = (date("m/d",strtotime($aKey)));
                        array_push($arrayDayReverse,$dayInDate);
                        $aValueAmount = (float)$aValue->{"4. close"};
                        array_push($arrayValueReverse,$aValueAmount);
                        $aVolumeAmount = (float)$aValue->{"5. volume"};
                        array_push($arrayVolumeReverse,$aVolumeAmount);
						$thisCounter++;
						if ($thisCounter > 120) {
                        	break;
						}
                    }
                            
    				$arrayDay = array_reverse($arrayDayReverse);
                    $arrayValue = array_reverse($arrayValueReverse);
                    $arrayVolume = array_reverse($arrayVolumeReverse);
    
                    json_encode($arrayDay);
                    json_encode($arrayValue);
                    json_encode($arrayVolume);
                     
           		?>
			</div>    
        
        	<script src="https://code.highcharts.com/highcharts.js"></script>
        
        	<script src="https://code.highcharts.com/modules/exporting.js"></script>

        	<div id = "container" style = "position: relative; width: 1045px; height: 400px; margin: 0 auto"></div>
		
        	<div>
        		<script>
            
				var arrayDayJava = <?php echo json_encode($arrayDay);?>;
				var arrayValueJava = <?php echo json_encode($arrayValue);?>;
				var arrayVolumeJava = <?php echo json_encode($arrayVolume);?>;

				var lengthOfDay = arrayDayJava.length;
				var priceMinimum = Math.min.apply(null, arrayValueJava);
				var priceMaximum = Math.max.apply(null, arrayValueJava);

				var volMaximum = Math.max.apply(null, arrayVolumeJava) * 6;

				var temp = priceMaximum - priceMinimum;
				priceMinimum = priceMinimum - 0.15 * temp;
				priceMaximum = priceMaximum + 0.15 * temp;

				var today = new Date();
				var dd = today.getDate();
				var mm = today.getMonth()+1; //January is 0!
				var yyyy = today.getFullYear();

				if(dd < 10){
					dd='0'+dd;
				} 
				if(mm < 10){
					mm='0'+mm;
				} 
				var today = mm+'/'+dd+'/'+yyyy;
				firstGraph();

				function firstGraph() {
                
					var text = '<?php echo $text ?>';
					Highcharts.chart('container', {
					chart: {
						type: 'area',
						zoomType: 'xy'
					},
					title: {
						text: 'Stock Price ('+today+')'
					},
					subtitle: {
						text: '<a class = "blueToBlack" href="https://www.alphavantage.co/" target="_blank">Source: Alpha Vantage</a>', 
						useHTML: true
					},
                	"xAxis": {
						categories:arrayDayJava,
						labels: {
                        	style: {
                            	fontSize: '9px'
                        	}
                    	},
                    	tickInterval: 5
                	},  
                	yAxis: [ { 
                    	labels: {
							style: {
								color: Highcharts.getOptions().colors[1],
								fontSize: '8px'
                        	}
                    	},
						min: priceMinimum,
						max: priceMaximum,
                    	title: {
                        	text: 'Stock Price',
                        	style: {
								color: Highcharts.getOptions().colors[1],
								fontSize: '10px'
                        	}
                    	},
						tickAmount: 8
                	}, { 
                		labels: {
                        	style: {
                            	color: Highcharts.getOptions().colors[1],
                            	fontSize: '8px'
                        	}
                		},
                		title: {
                    		text: 'Volume',
                    		style: {
                        		color: Highcharts.getOptions().colors[1],
                        		fontSize: '10px'
                    		}
                		},
                		
                		max: volMaximum,
						opposite: true,
						tickAmount: 8
            			}
					],
            
            		legend: { 
						align: 'right',
                		layout: 'vertical',
                		itemStyle: {
                    		fontSize:'10px' 
                		},
						verticalAlign: 'middle'
            		},
            		series: [ {
						color: '#e55040',
                		data: arrayValueJava,
                		fillOpacity:0.65,
						name: text,
                		tooltip: {
                    		valueDecimals: 2
                		}
					}, {
						color: '#ffffff',
						data: arrayVolumeJava,
						name: text + 'Volume',
						type: 'column',
						yAxis: 1
						
            		}]
        		});
            	}
            
            
            
            	function secondGraph() {
                
                var text = '<?php echo $text ?>';
                var url = "https://www.alphavantage.co/query?function=SMA&symbol="+text+"&interval=daily&time_period=10&series_type=close&apikey=IBML9TEYLJVP1G94";   
               
                var jsonObj;
                var arraySMAValue = [];
                var xmlhttp = new XMLHttpRequest();
                xmlhttp.onreadystatechange = function() {
                    if (this.readyState == 4 && this.status == 200) {
                        jsonObj = JSON.parse(xmlhttp.responseText);
                        var counter = 0;
                        for(var x in jsonObj["Technical Analysis: SMA"]){
                            arraySMAValue.push(parseFloat(jsonObj["Technical Analysis: SMA"][x]["SMA"]));
                            counter++;
                            if(counter >= lengthOfDay) {
                                break;
                            }
                        }
                	arraySMAValue.reverse();
                        
					Highcharts.chart('container', {
					chart: {
						zoomType: 'xy'
					},
					title: {
						text: 'Simple Moving Average (SMA)'
					},
					subtitle: {
						text: '<a class = "blueToBlack" href="https://www.alphavantage.co/" target="_blank">Source: Alpha Vantage</a>', 
						useHTML: true
					},
                
					xAxis: {
						categories: arrayDayJava,
						tickInterval: 5
					},  

                	yAxis: [{   
						labels: {
							style: {
								color: Highcharts.getOptions().colors[1],
								fontSize: '8px'
							}
						},
						tickAmount: 11,
						
						title: {
							style: {
								color: Highcharts.getOptions().colors[1],
								fontSize: '10px'
							},
							text: 'SMA'
						}

                	}],
                	legend: {
						align: 'right',
                    	layout: 'vertical',
                    	verticalAlign: 'middle',
                    	itemStyle: {
                        	fontSize:'10px' 
                    	}
                	},
                	series: [{
						color: 'red',
                    	data: arraySMAValue,
                    	lineWidth: 1,
                    	marker:{
							enable: true,
							symbol: 'square',
							radius: 2
                    	},
						name: text
                	}]
            	});
                                         
                }
                };
                xmlhttp.open("GET", url, true);
                xmlhttp.send();
            }
            
            
            function thirdGraph() {
                var text = '<?php echo $text ?>';
                var url = "https://www.alphavantage.co/query?function=EMA&symbol="+text+"&interval=daily&time_period=10&series_type=close&apikey=IBML9TEYLJVP1G94";   
                var jsonObj;
                var arrayEMAValue = [];
                var xmlhttp = new XMLHttpRequest();
                xmlhttp.onreadystatechange = function() {
                    if (this.readyState == 4 && this.status == 200) {
                        jsonObj = JSON.parse(xmlhttp.responseText);
                        var counter = 0;
                        for(var x in jsonObj["Technical Analysis: EMA"]){
                            arrayEMAValue.push(parseFloat(jsonObj["Technical Analysis: EMA"][x]["EMA"]));
                            counter++;
                            if(counter >= lengthOfDay){
                                break;
                            }
                        }
                arrayEMAValue.reverse();      
                Highcharts.chart('container', {
                chart: {
                    zoomType: 'xy'
                },
                title: {
                    text: 'Exponential Moving Average (EMA)'
                },
                subtitle: {
                    text: '<a class = "blueToBlack" href="https://www.alphavantage.co/" target="_blank">Source: Alpha Vantage</a>', 
					useHTML: true
                },
                xAxis: {
                    categories: arrayDayJava,
                    tickInterval: 5
                },  
                yAxis: [{   
                    labels: {
                        style: {
                            color: Highcharts.getOptions().colors[1],
                            fontSize: '8px'
                        }
                    },
					tickAmount: 10,
					
                    title: {
                        text: 'EMA',
                        style: {
                            color: Highcharts.getOptions().colors[1],
                            fontSize: '10px'
                        }
                    }
                }],
               
                legend: { 
					align: 'right',
                    layout: 'vertical',
                    itemStyle: {
                        fontSize:'10px' 
                    },
					verticalAlign: 'middle'
                    
                },
                series: [{
                    color: 'red',
                    data: arrayEMAValue,
                    lineWidth: 1,
                    marker:{
                        enable: true,
                        symbol: 'square',
                        radius: 2
                        
                    },
					name: text
                }]
            });
                                         
            }
            };
            xmlhttp.open("GET", url, true);
            xmlhttp.send();
            }
            
            function fourthGraph() {  
                var text = '<?php echo $text ?>';
				var url = "https://www.alphavantage.co/query?function=STOCH&symbol="+text+"&interval=daily&time_period=10&slowkmatype=1&slowdmatype=1&series_type=close&apikey=IBML9TEYLJVP1G94";
				
                var jsonObj;
                var arraySTOCHValueD = [];
                var arraySTOCHValueK = [];
                var xmlhttp = new XMLHttpRequest();
                xmlhttp.onreadystatechange = function() {
                    if (this.readyState == 4 && this.status == 200) {
                        jsonObj = JSON.parse(xmlhttp.responseText);
                        var counter = 0;
                        for(var x in jsonObj["Technical Analysis: STOCH"]){
                            arraySTOCHValueD.push(parseFloat(jsonObj["Technical Analysis: STOCH"][x]["SlowD"]));
                            arraySTOCHValueK.push(parseFloat(jsonObj["Technical Analysis: STOCH"][x]["SlowK"]));
                            counter++;
                            if(counter >= lengthOfDay){
                                break;
                            }
                        }
                arraySTOCHValueD.reverse();
                arraySTOCHValueK.reverse();
                        
                Highcharts.chart('container', {
                chart: {
                    zoomType: 'xy'
                },
                title: {
                    text: 'Stochastic Oscillator (STOCH)'
                },
                subtitle: {
                    text: '<a class = "blueToBlack" href="https://www.alphavantage.co/" target="_blank">Source: Alpha Vantage</a>', 
					useHTML: true
                },
                xAxis: {
                    categories:arrayDayJava,
                    labels: {
                        style: {
                            fontSize: '9px'
                        }
                    },
                    tickInterval: 5  
                },  
                yAxis: [{   
                    labels: {
                        style: {
                            color: Highcharts.getOptions().colors[1],
                            fontSize: '8px'
                        }
                    },
					tickAmount: 11,
                    title: {
                        text: 'STOCH',
                        style: {
                            color: Highcharts.getOptions().colors[1],
                            fontSize: '10px'
                        }
                    }
                }],
                legend: { 
					align: 'right',
                    layout: 'vertical',
                    itemStyle: {
                        fontSize:'10px' 
                    },
					verticalAlign: 'middle'
                    
                },
                series: [
                    {
                    color: 'red',
                    data: arraySTOCHValueK,
                    lineWidth: 1,
                    marker: {
                        enable: true,
						radius: 2,
                        symbol: 'square'
                    },
					name: text + "SlowK"
                    
                },{
                    color: '#9ec8ef',
                    data: arraySTOCHValueD,
                    lineWidth: 1,
                    marker: {
                        enable: true,
                        symbol: 'square',
                        radius: 2
                    },
					name: text + "SlowD"  
                }

                ]
            	});                 
                }
                };
                xmlhttp.open("GET", url, true);
                xmlhttp.send();
            }
            
            function fifthGraph() {
                var text = '<?php echo $text ?>';
                var url = "https://www.alphavantage.co/query?function=RSI&symbol="+text+"&interval=daily&time_period=10&series_type=close&apikey=IBML9TEYLJVP1G94";   
                var jsonObj;
                var arrayRSIValue = [];
                var xmlhttp = new XMLHttpRequest();
                xmlhttp.onreadystatechange = function() {
                    if (this.readyState == 4 && this.status == 200) {
                        jsonObj = JSON.parse(xmlhttp.responseText);
                        var counter = 0;
                        for(var x in jsonObj["Technical Analysis: RSI"]){
                            arrayRSIValue.push(parseFloat(jsonObj["Technical Analysis: RSI"][x]["RSI"]));
                            counter++;
                            if(counter >= lengthOfDay){
                                break;
                            }
                        }
                arrayRSIValue.reverse();      
                Highcharts.chart('container', {
                chart: {
                    zoomType: 'xy'
                },
                title: {
                    text: 'Relative Strength Index (RSI)'
                },
                subtitle: {
                    text: '<a class = "blueToBlack" href="https://www.alphavantage.co/" target="_blank">Source: Alpha Vantage</a>', 
					useHTML: true
                },
                xAxis: {
                    categories: arrayDayJava,
                    tickInterval: 5
                },  
                yAxis: [{   
                    labels: {
                        style: {
                            color: Highcharts.getOptions().colors[1],
                            fontSize: '8px'
                        }
                    },
					tickAmount: 8,
                    title: {
                        text: 'RSI',
                        style: {
                            color: Highcharts.getOptions().colors[1],
                            fontSize: '10px'
                        }
                    }
                }],
                legend: { 
					align: 'right',
                    layout: 'vertical',
                    verticalAlign: 'middle',
                    itemStyle: {
                        fontSize:'10px' 
                    }
                },
                series: [{
					color: 'red',
                    name: text,
                    data: arrayRSIValue,
                    lineWidth: 1,
                    marker:{
                        enable: true,
                        symbol: 'square',
                        radius: 2
                    }
                }]
            	});                      
                }
                };
                xmlhttp.open("GET", url, true);
                xmlhttp.send();
            }
          
            function sixthGraph() {
                var text = '<?php echo $text ?>';
                var url = "https://www.alphavantage.co/query?function=ADX&symbol="+text+"&interval=daily&time_period=10&series_type=close&apikey=IBML9TEYLJVP1G94";   
                var jsonObj;
                var arrayADXValue = [];
                var xmlhttp = new XMLHttpRequest();
                xmlhttp.onreadystatechange = function() {
                    if (this.readyState == 4 && this.status == 200) {
                        jsonObj = JSON.parse(xmlhttp.responseText);
                        var counter = 0;
                        for(var x in jsonObj["Technical Analysis: ADX"]){
                            arrayADXValue.push(parseFloat(jsonObj["Technical Analysis: ADX"][x]["ADX"]));
                            counter++;
                            if(counter >= lengthOfDay){
                                break;
                            }
                        }
                arrayADXValue.reverse();      
                Highcharts.chart('container', {
                chart: {
                    zoomType: 'xy'
                },
                title: {
                    text: 'Average Directional movement IndeX (ADX)'
                },
                subtitle: {
                    text: '<a class = "blueToBlack" href="https://www.alphavantage.co/" target="_blank">Source: Alpha Vantage</a>', 
					useHTML: true
                },
                xAxis: {
                    categories: arrayDayJava,
                    tickInterval: 5
                },  
                yAxis: [{   
                    labels: {
                        style: {
                            color: Highcharts.getOptions().colors[1],
                            fontSize: '8px'
                        }
                    },
					tickAmount: 8,
                    title: {
                        text: 'ADX',
                        style: {
                            color: Highcharts.getOptions().colors[1],
                            fontSize: '10px'
                        }
                    }
                }],
                legend: {
                    align: 'right',
					layout: 'vertical',
                    verticalAlign: 'middle',
                    itemStyle: {
                        fontSize:'10px' 
                    }
                },
                series: [{
					color: 'red',
					data: arrayADXValue,
                    name: text,
                    lineWidth: 1,
                    marker:{
                        enable: true,
                        symbol: 'square',
                        radius: 2
                    }
                }]
            });                       
                 }
                };
                xmlhttp.open("GET", url, true);
                xmlhttp.send();
            }
          
            function seventhGraph() {
                var text = '<?php echo $text ?>';
                var url = "https://www.alphavantage.co/query?function=CCI&symbol="+text+"&interval=daily&time_period=10&series_type=close&apikey=IBML9TEYLJVP1G94";   
                var jsonObj;
                var arrayCCIValue = [];
                var xmlhttp = new XMLHttpRequest();
                xmlhttp.onreadystatechange = function() {
                    if (this.readyState == 4 && this.status == 200) {
                        jsonObj = JSON.parse(xmlhttp.responseText);
                        var counter = 0;
                        for(var x in jsonObj["Technical Analysis: CCI"]){
                            arrayCCIValue.push(parseFloat(jsonObj["Technical Analysis: CCI"][x]["CCI"]));
                            counter++;
                            if(counter >= lengthOfDay){
                                break;
                            }
                        }
                arrayCCIValue.reverse();      
                Highcharts.chart('container', {
                chart: {
                    zoomType: 'xy'
                },
                title: {
                    text: 'Commodity Channel Index (CCI)'
                },
                subtitle: {
                    text: '<a class = "blueToBlack" href="https://www.alphavantage.co/" target="_blank">Source: Alpha Vantage</a>', 
					useHTML: true
                },
                xAxis: {
                    categories: arrayDayJava,
                    tickInterval: 5
                },  
                yAxis: [{   
                    labels: {
                        style: {
                            color: Highcharts.getOptions().colors[1],
                            fontSize: '8px'
                        }
                    },
                    title: {
                        text: 'CCI',
                        style: {
                            color: Highcharts.getOptions().colors[1],
                            fontSize: '10px'
                        }
                    }
                }],
                legend: {
                    layout: 'vertical',
                    align: 'right',
                    verticalAlign: 'middle',
                    itemStyle: {
                        fontSize:'10px' 
                    }
                    
                },
                series: [ {
					color: 'red',
					data: arrayCCIValue,
                    name: text,
                    lineWidth: 1,
                    marker:{
                        enable: true,
                        symbol: 'square',
                        radius: 2
                    }
                }]
            });
				}
                };
                xmlhttp.open("GET", url, true);
                xmlhttp.send();
            }
            
             function eighthGraph() {  
                var text = '<?php echo $text ?>';
                  
				var url = "https://www.alphavantage.co/query?function=BBANDS&symbol="+text+"&interval=daily&time_period=5&series_type=close&nbdevup=3&nbdevdn=3&apikey=IBML9TEYLJVP1G94";
                var jsonObj;
				 
                var arrayBBANDSValueLow = [];
				var arrayBBANDSValueMiddle = [];
                var arrayBBANDSValueHigh = [];
                var xmlhttp = new XMLHttpRequest();
                xmlhttp.onreadystatechange = function() {
                    if (this.readyState == 4 && this.status == 200) {
                        jsonObj = JSON.parse(xmlhttp.responseText);
                        var counter = 0;
                        for(var x in jsonObj["Technical Analysis: BBANDS"]){
                            arrayBBANDSValueLow.push(parseFloat(jsonObj["Technical Analysis: BBANDS"][x]["Real Lower Band"]));
                            arrayBBANDSValueMiddle.push(parseFloat(jsonObj["Technical Analysis: BBANDS"][x]["Real Middle Band"]));
							arrayBBANDSValueHigh.push(parseFloat(jsonObj["Technical Analysis: BBANDS"][x]["Real Upper Band"]));
                            counter++;
                            if(counter >= lengthOfDay){
                                break;
                            }
                        }
                arrayBBANDSValueLow.reverse();
                arrayBBANDSValueMiddle.reverse();
                arrayBBANDSValueHigh.reverse();     
                Highcharts.chart('container', {
                chart: {
                    zoomType: 'xy'
                },
                title: {
                    text: 'Bollinger Bands (BBANDS)'
                },
                subtitle: {
                    text: '<a class = "blueToBlack" href="https://www.alphavantage.co/" target="_blank">Source: Alpha Vantage</a>', 
					useHTML: true
                },
                xAxis: {
                    categories:arrayDayJava,
                    labels: {
                        style: {
                            fontSize: '9px'
                        }
                    },
                    tickInterval: 5  
                },  
                yAxis: [{   
                    labels: {
                        style: {
                            color: Highcharts.getOptions().colors[1],
                            fontSize: '8px'
                        }
                    },
                    title: {
                        text: 'BBANDS',
                        style: {
                            color: Highcharts.getOptions().colors[1],
                            fontSize: '10px'
                        }
                    }
                }], 
                legend: { 
					align: 'right',
                    layout: 'vertical',
                    verticalAlign: 'middle',
                    itemStyle: {
                        fontSize: '10px' 
                    }
                },
                series: [{
					color: 'red',
					data: arrayBBANDSValueMiddle,
                    name: text + "Real Middle Band",
                    lineWidth:1,
                    marker: {
                        enable: true,
                        symbol: 'square',
                        radius: 2
                    }
                },{
					color: 'black',
					data: arrayBBANDSValueHigh,
                    name: text + "Real Upper Band",
                    lineWidth: 1,
                    marker: {
                        enable: true,
                        symbol: 'square',
                        radius: 2
                    }
                },{
					color: '#02ff02',
					data: arrayBBANDSValueLow,
                    name: text + "Real Lower Band",
                    lineWidth: 1,
                    marker: {
                        enable: true,
                        symbol: 'square',
                        radius: 2
                    }
                }
                ]
            	});
					}
                };
                xmlhttp.open("GET", url, true);
                xmlhttp.send();
            }

			function ninthGraph() {  
                var text = '<?php echo $text ?>';
                var url = "https://www.alphavantage.co/query?function=MACD&symbol="+text+"&interval=daily&time_period=10&series_type=close&apikey=IBML9TEYLJVP1G94";   
                var jsonObj;
                var arrayMACDValue = [];
				var arrayMACDValueSignal = [];
                var arrayMACDValueHist = [];
                var xmlhttp = new XMLHttpRequest();
                xmlhttp.onreadystatechange = function() {
                    if (this.readyState == 4 && this.status == 200) {
                        jsonObj = JSON.parse(xmlhttp.responseText);
                        var counter = 0;
                        for(var x in jsonObj["Technical Analysis: MACD"]){
                            arrayMACDValue.push(parseFloat(jsonObj["Technical Analysis: MACD"][x]["MACD"]));
                            arrayMACDValueSignal.push(parseFloat(jsonObj["Technical Analysis: MACD"][x]["MACD_Signal"]));
							arrayMACDValueHist.push(parseFloat(jsonObj["Technical Analysis: MACD"][x]["MACD_Hist"]));
                            counter++;
                            if(counter >= lengthOfDay){
                                break;
                            }
                        }
                arrayMACDValue.reverse();
                arrayMACDValueSignal.reverse();
                arrayMACDValueHist.reverse();     
                Highcharts.chart('container', {
                chart: {
                    zoomType: 'xy'
                },
                title: {
                    text: 'Moving Average Convergence/Divergence (MACD)'
                },
                subtitle: {
                    text: '<a class = "blueToBlack" href="https://www.alphavantage.co/" target="_blank">Source: Alpha Vantage</a>', 
					useHTML: true
                },
                xAxis: {
                    categories:arrayDayJava,
                    labels: {
                        style: {
                            fontSize: '9px'
                        }
                    },
                    tickInterval: 5  
                },  
                yAxis: [{   
                    labels: {
                        style: {
                            color: Highcharts.getOptions().colors[1],
                            fontSize: '8px'
                        }
                    },
                    title: {
                        text: 'MACD',
                        style: {
                            color: Highcharts.getOptions().colors[1],
                            fontSize: '10px'
                        }
                    }
                }],
                legend: { 
					align: 'right',
                    layout: 'vertical',
                    verticalAlign: 'middle',
                    itemStyle: {
                        fontSize:'10px' 
                    }
                    
                },
                series: [{
					color: 'red',
					data: arrayMACDValue,
                    name: text + "MACD",
                    lineWidth: 1,
                    marker: {
                        enable: true,
                        symbol: 'square',
                        radius: 2
                    }
                    
                },{
					color: '#e6ad7c',
					data: arrayMACDValueHist,
                    name: text + "MACD_Hist",
                    lineWidth: 1,
                    marker: {
                        enable: true,
                        symbol: 'square',
                        radius: 2
                    }
                },{
					color: '#9097df',
					data: arrayMACDValueSignal,
                    name: text + "MACD_Signal",
                    lineWidth: 1,
                    marker: {
                        enable: true,
                        symbol: 'square',
                        radius: 2
                    }
                }
                
                ]
            	});
                }
                };
                xmlhttp.open("GET", url, true);
                xmlhttp.send();
            }
			
    	</script>
       
       
        </div>    
   
        <div>
            <!-- Extract the data for the news' table -->
            <?php
			    if (isset($_POST['submit'])) {
					$urlFirst = rawurlencode("https://seekingalpha.com/api/sa/combined/");
                    $urlThird = rawurlencode(".xml");
                    $urlSecond = urlencode($text);
    
                    $urlAll = $urlFirst.$urlSecond.$urlThird; 
					
                    $xmlObj = @simplexml_load_file($urlAll); 
					if ($xmlObj !== false) {
							$arrayTitleReverse=array(); 
							$arrayLinkReverse=array(); 
							$arrayDateReverse=array();   

							$arrayLengthCount = count($xmlObj->channel->item);
							$numArticle = 0;
							for ($i = 0; $i < $arrayLengthCount; $i++)  {
								$linkString = (string)$xmlObj->channel->item[$i]->link; 
								if (strpos($linkString,'article') !== FALSE) { 
									$titleString = (string)$xmlObj->channel->item[$i]->title;
									array_push($arrayTitleReverse,$titleString);
									array_push($arrayLinkReverse,$linkString);
									$pubDateString = (string)$xmlObj->channel->item[$i]->pubDate;
									$dayInRightFormat = (date("D, d M Y H:i:s",strtotime($pubDateString)));
									array_push($arrayDateReverse,$dayInRightFormat);
									$numArticle++;
									if($numArticle == 5) {
										break;
									}

								}
                   			}
			   					
							json_encode($arrayTitleReverse);
							json_encode($arrayLinkReverse);
							json_encode($arrayDateReverse);
					}
					else {
					}
                
                    
                }
                ?>
     
            </div>
			
        	<!-- Botton table and Logo -->
			
			<div id = "displayButton"><button type = "button" style = "font-size: 13px" onclick = "displayNews()">click to show stock news<br/><img class = "clickButtonLogo" src = "http://cs-server.usc.edu:45678/hw/hw6/images/Gray_Arrow_Down.png"></button>
			</div>
			
			
			</br>
			<div id = "stockNews"></div> 
		
        	<div>
				<script>
				
					var arrayTitleJava = <?php echo json_encode($arrayTitleReverse);?>;
					var arrayLinkJava = <?php echo json_encode($arrayLinkReverse);?>;
					var arrayDateJava = <?php echo json_encode($arrayDateReverse);?>;
					arrayTitleJava.reverse();
					arrayLinkJava.reverse();
					arrayDateJava.reverse();  
				
					function emptyNews() {
						document.getElementById("displayButton").innerHTML = "<button type = 'button' style = 'font-size: 13px' onclick = 'displayNews()'>click to show stock news<br/><img class = 'clickButtonLogo' src = 'http://cs-server.usc.edu:45678/hw/hw6/images/Gray_Arrow_Down.png'></button>";
						document.getElementById("stockNews").innerHTML = "";
					}
			
					function displayNews() {
						var displayPicAndWords = "</br>";
				   		document.getElementById("displayButton").innerHTML = "<button type = 'button' style = 'font-size: 13px' onclick = 'emptyNews()'>click to hide stock news<br/><img class = 'clickButtonLogo' src = 'http://cs-server.usc.edu:45678/hw/hw6/images/Gray_Arrow_Up.png'></button>";
						displayPicAndWords = displayPicAndWords + "<table class = 'newsTable'>";
						displayPicAndWords = displayPicAndWords + "<tr><td class = 'newsInner'><a class = 'blueToBlack' href = " +arrayLinkJava[4] + " target = '_blank'>"+arrayTitleJava[4]+"</a>"+"&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp"+"Publicated Time: " + arrayDateJava[4] + " </tr>";
						displayPicAndWords = displayPicAndWords + "<tr><td class = 'newsInner'><a class = 'blueToBlack' href = " +arrayLinkJava[3] + " target = '_blank'>"+arrayTitleJava[3]+"</a>"+"&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp"+"Publicated Time: " + arrayDateJava[3] + " </tr>";
						displayPicAndWords = displayPicAndWords + "<tr><td class = 'newsInner'><a class = 'blueToBlack' href = " +arrayLinkJava[2] + " target = '_blank'>"+arrayTitleJava[2]+"</a>"+"&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp"+"Publicated Time: " + arrayDateJava[2] + " </tr>";
						displayPicAndWords = displayPicAndWords + "<tr><td class = 'newsInner'><a class = 'blueToBlack' href = " +arrayLinkJava[1] + " target = '_blank'>"+arrayTitleJava[1]+"</a>"+"&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp"+"Publicated Time: " + arrayDateJava[1] + " </tr>";
						displayPicAndWords = displayPicAndWords + "<tr><td class = 'newsInner'><a class = 'blueToBlack' href = " +arrayLinkJava[0] + " target = '_blank'>"+arrayTitleJava[0]+"</a>"+"&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp"+"Publicated Time: " + arrayDateJava[0] + " </tr>";
						displayPicAndWords = displayPicAndWords + "</table>";
						displayPicAndWords = displayPicAndWords + "<br>";
						document.getElementById("stockNews").innerHTML = displayPicAndWords;
					}
			
			</script>
			
			</div>
		</div>
	
        <?php endif; ?>
        
</center><NOSCRIPT></body></html>