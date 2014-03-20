This is a replacement for the stock Cacti graph_xport.php that allows to output in JSON format.

Simply add json=1 to the url.

Example:
```
http://my_cacti_server/graph_xport.php?local_graph_id=1022&rra_id=1&view_type=&graph_start=1395318989&graph_end=1395334156&json=1
```

Sample output:

```
{
	"meta": {
		"start": "1395319020",
		"step": "60",
		"end": "1395334140",
		"rows": "253",
		"columns": "2",
		"legend": {
			"col1": "Inbound",
			"col2": "Outbound"
		},
		"stacked_columns": {
			"col1": 0,
			"col2": 0
		},
		"title_cache": "'Inkas - Dialer (new) - Traffic - eth0'",
		"vertical_label": "'bits per second'",
		"local_graph_id": "1022",
		"host_id": "45"
	},
	"NthPercentile": [
		{
			"format": "|95:bits:0:max:2|",
			"value": "2331386.89"
		}
	],
	"data": {
		"1": {
			"timestamp": "1395319020",
			"col1": "6.2159444444e+03",
			"col2": "1.0835722222e+04"
		},
		"2": {
			"timestamp": "1395319080",
			"col1": "7.2581777778e+03",
			"col2": "1.1839911111e+04"
		},
		"3": {
			"timestamp": "1395319140",
			"col1": "6.4753000000e+03",
			"col2": "1.0003811111e+04"
		},
		"4": {
			"timestamp": "1395319200",
			"col1": "7.0666484517e+03",
			"col2": "1.2242387614e+04"
		},
		"5": {
			"timestamp": "1395319260",
			"col1": "7.2075398722e+03",
			"col2": "1.1883763045e+04"
		},
		"6": {
			"timestamp": "1395319320",
			"col1": "6.8446005650e+03",
			"col2": "1.1799060452e+04"
		},
		"7": {
			"timestamp": "1395319380",
			"col1": "6.4535885246e+03",
			"col2": "1.0984925137e+04"
		},
		"8": {
			"timestamp": "1395319440",
			"col1": "6.7862314754e+03",
			"col2": "1.1807108197e+04"
		},
		"9": {
			"timestamp": "1395319500",
			"col1": "6.9057600000e+03",
			"col2": "1.3062266667e+04"
		},
		"10": {
			"timestamp": "1395319560",
			"col1": "7.9675333333e+03",
			"col2": "3.5353826667e+04"
		},
		"11": {
			"timestamp": "1395319620",
			"col1": "6.3528194350e+03",
			"col2": "1.3631728136e+04"
		},
		"12": {
			"timestamp": "1395319680",
			"col1": "6.9243836251e+03",
			"col2": "1.0513302028e+04"
		},
		"13": {
			"timestamp": "1395319740",
			"col1": "4.8442649625e+03",
			"col2": "5.4117081412e+03"
		},
		"14": {
			"timestamp": "1395319800",
			"col1": "6.9710421413e+03",
			"col2": "6.3640579791e+03"
		},
		"15": {
			"timestamp": "1395319860",
			"col1": "4.5836898361e+03",
			"col2": "4.7347370492e+03"
		},
		"16": {
			"timestamp": "1395319920",
			"col1": "6.0056558418e+04",
			"col2": "6.4142843390e+04"
		},
		"17": {
			"timestamp": "1395319980",
			"col1": "1.0921352005e+05",
			"col2": "1.1412739224e+05"
		},
		"18": {
			"timestamp": "1395320040",
			"col1": "2.4795178820e+05",
			"col2": "8.8800977705e+04"
		}
	}
}
```