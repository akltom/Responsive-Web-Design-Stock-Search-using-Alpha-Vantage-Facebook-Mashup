// only the json,///,frontPage work
var http = require('http');
var fs = require('fs');
var request = require('request');
var url = require('url');
var express = require('express');
var app = express();
var Client = require('node-rest-client').Client;

var compression = require('compression');
const https = require('https');


var client = new Client();


app.all('*', function(req, res, next) {   // means regular call of server
	
    res.header("Access-Control-Allow-Origin", "*"); 
    res.header("Access-Control-Allow-Headers", "Origin, X-Requested-With, Content-Type, Accept, datatype");
    res.header("Access-Control-Allow-Methods","PUT,POST,GET,DELETE,OPTIONS");
    res.header("X-Powered-By",' 3.2.1')
    res.header("Content-Type", "application/json;charset=utf-8");
    next();
});





function requestData(url, res) {
    request(url,
	    function(error, response, body) {
	        if (response.statusCode === 200 && !error) {
	            res.send(body);
	        }
	    });
}



app.get('///', function (req, res) {
	res.send('CS571');
})

app.get('/frontPage', function (req, res) {
  res.send('Hello from A!');
})


function getAutoComplete(input, res) { // auto complete    // work!!!!!!!    
    url = "http://dev.markitondemand.com/MODApis/Api/v2/Lookup/json?input=" + input;
    requestData(url, res);
}

app.get('/buquan', function(req, res) { //autocomplete   // remember this is input!    // work!!!!   http://localhost:8888/buquan?input=msft
    var input = req.query.input;
    if (input == null) {
        res.send("empty");
    } else {
		getAutoComplete(input, res);
    }
})


function getIndicator(symbol, idx, res) { // indicator   work!!!!!!!!!!!!!
	url = "https://www.alphavantage.co/query?function=" + idx + "&symbol=" + symbol + "&series_type=open&interval=daily&time_period=10&apikey=IBML9TEYLJVP1G94";
	requestData(url, res);
}

app.get('/sku', function(req, res) {  //  /sku, is the indicator    work!!!!!!!!!!!    http://localhost:8888/sku?stockName=msft&idx=MACD
    var stockName = req.query.stockName;
    var sku = req.query.sku;
    var idx = req.query.idx;
    if (stockName != null) {
        getIndicator(stockName, idx, res);
    } else {
        res.send("empty");
    }
})





function getdowntable(stockName, res) { // news  can not display in web, maybe due to xml file?     http://localhost:8888/down?stockName=msft
	url = "https://seekingalpha.com/api/sa/combined/" + stockName + ".xml";
	requestData(url, res);
}

app.get('/down', function(req, res) { // news
    var stockName = req.query.stockName;
    if (stockName == null) {
        res.send("empty");
    } else {
		getdowntable(stockName, res);
    }
})

//app.get('/down', function(req, res) { //news
//    var stockName = req.query.stockName;
//    if (stockName != null) {
//        getdowntable(stockName, res);
//    } else {
//        res.send("empty");
//    }
//})
//
//function getDowntableUrl(symbol) { // news
//	return "https://seekingalpha.com/api/sa/combined/" + symbol + ".xml";
//}
//function getdowntable(stockName, res) { //news
//	url = getDowntableUrl(stockName);
//	requestData(url, res);
//
//}








app.get('/jsonfile', function (req, res) {   http://localhost:8888/jsonFile?text=aapl
	var symbol = req.query.text;
	var getsymbol = url.parse(req.url, true);
  	// parsed response body as js object 
  	console.log(getsymbol.query.text);
  	// raw response 
  	//console.log(res);
  	client.get("https://www.alphavantage.co/query?function=TIME_SERIES_DAILY&symbol="+symbol+"&apikey=IBML9TEYLJVP1G94", function (data, response) {
	
	// testing link for backend:   then change appl to whatever companies
	// http://localhost:8888/jsonFile?text=aapl
	res.send(data);// or use write......
	  
  })
 
})


function seeStockData(stockName, res) {  // seeStockData = readate
	var url = 'https://www.alphavantage.co/query?function=TIME_SERIES_DAILY&outputsize=full&symbol=';
	url += stockName;
    url += '&apikey=IBML9TEYLJVP1G94';
	requestData(url, res);
};

app.get('/', function(req, res) {  //  work!!!!     http://localhost:8888/?stockName=msft
    var stockName = req.query.stockName;  
    if (stockName == null) {
        res.send("empty");
    } else {
		seeStockData(stockName, res);
    }
})

app.get('/start', function(req, res) { // /start = /init   work!!! display msft  http://localhost:8888/start?stockName=msft
    var stockName = req.query.stockName;
    if (stockName == null) {
        res.send("empty");
    } else {
		res.send(stockName);
    }
})

app.use(compression())

app.listen(8888, function(){
	console.log("launching");
})



//
//var urlMarket = "http://dev.markitondemand.com/MODApis/Api/v2/Lookup/json?input=AAPL";
//var urlStockQuote = "http://www.alphavantage.co/query?funcRon=TIME_SERIES_DAILY&symbol=MSFT&apikey=demo";
//var urlIndicator = "haps://www.alphavantage.co/query?funcRon=SMA&symbol=MSFT&interval=15min&Rme_period=10&series_type=close&apikey=demo";
//var urlNews = "haps://seekingalpha.com/api/sa/combined/SYMBOL.xml";
//
//
//http.createServer(function (req, res) {
//	 var jsonData = '';
//	//var a = url.parse('http://dev.markitondemand.com/MODApis/Api/v2/Lookup/json?input=AAPL');
//	res.on('data', function (d) {
//		jsonData += d;
//		//.console.log(a);
//	});
//	res.on('end',function(){    
//		//获取到的数据
//		//json = JSON.parse(json);
//        console.log('ghghg');
//	});
//	res.on('error', function (e) {
//		console.error(e);
//	});
////	
////	res.get('/init', function(req, res) {
////    	console.log('qweqweqwe');
////	})
//
//    // 发送响应数据 "Hello World"
//    res.end('Hello World\n');
//}).listen(8888);

//http.all('*', function(req, res, next) {   // means regular call of server
//	res.end('asdasdasd\n');
//});






// 终端打印如下信息
//console.log('Server running at http://127.0.0.1:8888/');
//
//
//http.get(urlMarket, function(req, res){
//    var body = '';
//
//    res.on('data', function(chunk){
//        body += chunk;
//    });
//
//    res.on('end', function(){
//        var fbResponse = JSON.parse(body);
//        console.log("Got a response: ", fbResponse.picture);
//    });
//}).on('error', function(e){
//      console.log("Got an error: ", e);
//});




//
//http.get('http://abc.com/api"', function (res) {
//    var json = '';
//    res.on('data', function (d) {
//        json += d;
//    });
//    res.on('end',function(){    
//        //获取到的数据
//        json = JSON.parse(json);
//    });
//}).on('error', function (e) {
//    console.error(e);
//});









//.js is similar to javascript
// return 3 json files in total, one for new and two for charts

//fs.readFile('./intro.txt', function (err, data) {
//if (err) throw err;
//console.log(data);
//});

//
//const http = require('http');
//http.createServer(function (req, res) {
//res.writeHead(200, { 'Content-Type': 'text/plain' });
//res.end('Hello World CSCI571!\n');
//}).listen(9090, '127.0.0.1', function () {
//console.log(Server running at http://localhost:9090/);});




















//var express = require('express');
//var app = express();
//var http = require('http');
//var compression = require('compression')
//
//const https = require('https');
//
//key = "C1LW3J4G6JWPX7W3";

//function readdata(stock_name, res) {
//    url = getMainUrl(stock_name);
//	requestData(url, res);
//};
//function getdowntable(stock_name, res) { //news
//	url = getDowntableUrl(stock_name);
//	requestData(url, res);
//
//}
//function getSpecialOne(symbol, idx, res) { // indicator
//	url = "https://www.alphavantage.co/query?function=" + idx + "&symbol=" + symbol + "&series_type=open&interval=daily&time_period=10&apikey=" + key;
//	requestData(url, res);
//}

//function requestData(url, res) { //useful one
//    request(url,
//	    function(error, response, body) {
//	        if (!error && response.statusCode === 200) {
//	            res.send(body);
//	        }
//	    });
//}
//function getBuquan(input, res) {
//    url = "http://dev.markitondemand.com/MODApis/Api/v2/Lookup/json?input=" + input;
//    requestData(url, res);
//}

//function getMainUrl(stock_name) {
//    var url = 'https://www.alphavantage.co/query?function=TIME_SERIES_DAILY&outputsize=full&symbol=';
//    url += stock_name;
//    url += '&apikey=';
//    url += key;
//    return url;
//}

//function getDowntableUrl(symbol) { // news
//	return "https://seekingalpha.com/api/sa/combined/" + symbol + ".xml";
//}

//app.all('*', function(req, res, next) {   // means regular call of server
//    res.header("Access-Control-Allow-Origin", "*"); 
//    res.header("Access-Control-Allow-Headers", "Origin, X-Requested-With, Content-Type, Accept, datatype");
//    res.header("Access-Control-Allow-Methods","PUT,POST,GET,DELETE,OPTIONS");
//    res.header("X-Powered-By",' 3.2.1')
//    res.header("Content-Type", "application/json;charset=utf-8");
//    next();
//});
//app.get('/', function(req, res) {
//    var stock_name = req.query.stock_name;  // for mine version, use stockName instead of stock_name
//    if (stock_name != null) {
//        readdata(stock_name, res);
//    } else {
//        res.send("empty");
//    }
//})

//app.get('/init', function(req, res) {
//    var stock_name = req.query.stock_name;
//    if (stock_name != null) {
//        res.send(stock_name);
//    } else {
//        res.send("empty");
//    }
//})
//app.get('/down', function(req, res) { //news
//    var stock_name = req.query.stock_name;
//    if (stock_name != null) {
//        getdowntable(stock_name, res);
//    } else {
//        res.send("empty");
//    }
//})
//app.get('/sku', function(req, res) {    // indicator 
//    var stock_name = req.query.stock_name;
//    var sku = req.query.sku;
//    var idx = req.query.idx;
//    if (stock_name != null) {
//        getSpecialOne(stock_name, idx, res);
//    } else {
//        res.send("empty");
//    }
//})
//app.get('/buquan', function(req, res) {
//    var input = req.query.input;
//    if (input != null) {
//        getBuquan(input, res);
//    } else {
//        res.send("empty");
//    }
//})
//app.use(compression())
//var server = app.listen(8081, function() {
//
//})
