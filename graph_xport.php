<?php
/*
 +-------------------------------------------------------------------------+
 | Copyright (C) 2004-2012 The Cacti Group                                 |
 |                                                                         |
 | This program is free software; you can redistribute it and/or           |
 | modify it under the terms of the GNU General Public License             |
 | as published by the Free Software Foundation; either version 2          |
 | of the License, or (at your option) any later version.                  |
 |                                                                         |
 | This program is distributed in the hope that it will be useful,         |
 | but WITHOUT ANY WARRANTY; without even the implied warranty of          |
 | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the           |
 | GNU General Public License for more details.                            |
 +-------------------------------------------------------------------------+
 | Cacti: The Complete RRDTool-based Graphing Solution                     |
 +-------------------------------------------------------------------------+
 | This code is designed, written, and maintained by the Cacti Group. See  |
 | about.php and/or the AUTHORS file for specific developer information.   |
 +-------------------------------------------------------------------------+
 | http://www.cacti.net/                                                   |
 +-------------------------------------------------------------------------+
*/

/* since we'll have additional headers, tell php when to flush them */
ob_start();

$guest_account = true;

include("./include/auth.php");
include_once("./lib/rrd.php");

/* ================= input validation ================= */
input_validate_input_number(get_request_var("graph_start"));
input_validate_input_number(get_request_var("graph_end"));
input_validate_input_number(get_request_var("graph_height"));
input_validate_input_number(get_request_var("graph_width"));
input_validate_input_number(get_request_var("local_graph_id"));
input_validate_input_number(get_request_var("rra_id"));
input_validate_input_number(get_request_var("stdout"));
/* ==================================================== */

/* flush the headers now */
ob_end_clean();

session_write_close();

$graph_data_array = array();

/* override: graph start time (unix time) */
if (!empty($_GET["graph_start"]) && $_GET["graph_start"] < 1600000000) {
	$graph_data_array["graph_start"] = $_GET["graph_start"];
}

/* override: graph end time (unix time) */
if (!empty($_GET["graph_end"]) && $_GET["graph_end"] < 1600000000) {
	$graph_data_array["graph_end"] = $_GET["graph_end"];
}

/* override: graph height (in pixels) */
if (!empty($_GET["graph_height"]) && $_GET["graph_height"] < 3000) {
	$graph_data_array["graph_height"] = $_GET["graph_height"];
}

/* override: graph width (in pixels) */
if (!empty($_GET["graph_width"]) && $_GET["graph_width"] < 3000) {
	$graph_data_array["graph_width"] = $_GET["graph_width"];
}

/* override: skip drawing the legend? */
if (!empty($_GET["graph_nolegend"])) {
	$graph_data_array["graph_nolegend"] = $_GET["graph_nolegend"];
}

/* print RRDTool graph source? */
if (!empty($_GET["show_source"])) {
	$graph_data_array["print_source"] = $_GET["show_source"];
}

$graph_info = db_fetch_row("SELECT * FROM graph_templates_graph WHERE local_graph_id='" . $_REQUEST["local_graph_id"] . "'");

/* for bandwidth, NThPercentile */
$xport_meta = array();

/* JSON array */
$json_array = array();

/* Get graph export */
$xport_array = @rrdtool_function_xport($_GET["local_graph_id"], $_GET["rra_id"], $graph_data_array, $xport_meta);

/* Make graph title the suggested file name */
if (is_array($xport_array["meta"])) {
	$filename = $xport_array["meta"]["title_cache"] . ".csv";
} else {
	$filename = "graph_export.csv";
}

if (!empty($_GET["json"])) {
	header("Content-type: application/json");
}
else {
	header("Content-type: application/vnd.ms-excel");
}

if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') {
	header("Pragma: cache");
}

header("Cache-Control: max-age=15");
if (!isset($_GET["stdout"]) && empty($_GET["json"])) {
	header("Content-Disposition: attachment; filename=\"" . $filename . "\"");
}

if (is_array($xport_array["meta"])) {
	if (!empty($_GET["json"])) {
		$json_array["meta"]=$xport_array["meta"];
		if (isset($xport_meta["NthPercentile"])) {
			$json_array["NthPercentile"]=$xport_meta["NthPercentile"];
		}
		if (isset($xport_meta["Summation"])) {
			$json_array["meta"]=$xport_meta["Summation"];
		}
	}
	else {
		print '"Title:","'          . $xport_array["meta"]["title_cache"]                . '"' . "\n";
    	print '"Vertical Label:","' . $xport_array["meta"]["vertical_label"]             . '"' . "\n";
    	print '"Start Date:","'     . date("Y-m-d H:i:s", $xport_array["meta"]["start"]) . '"' . "\n";
    	print '"End Date:","'       . date("Y-m-d H:i:s", $xport_array["meta"]["end"])   . '"' . "\n";
    	print '"Step:","'           . $xport_array["meta"]["step"]                       . '"' . "\n";
    	print '"Total Rows:","'     . $xport_array["meta"]["rows"]                       . '"' . "\n";
    	print '"Graph ID:","'       . $xport_array["meta"]["local_graph_id"]             . '"' . "\n";
    	print '"Host ID:","'        . $xport_array["meta"]["host_id"]                    . '"' . "\n";

    	if (isset($xport_meta["NthPercentile"])) {
    		foreach($xport_meta["NthPercentile"] as $item) {
    			print '"Nth Percentile:","' . $item["value"] . '","' . $item["format"] . '"' . "\n";
    		}
    	}
    	if (isset($xport_meta["Summation"])) {
    		foreach($xport_meta["Summation"] as $item) {
    			print '"Summation:","' . $item["value"] . '","' . $item["format"] . '"' . "\n";
    		}
    	}

    	print '""' . "\n";

    	$header = '"Date"';
    	for($i=1;$i<=$xport_array["meta"]["columns"];$i++) {
    		$header .= ',"' . $xport_array["meta"]["legend"]["col" . $i] . '"';
    	}
    	print $header . "\n";
	}

}

if (is_array($xport_array["data"])) {
	if (!empty($_GET["json"])) {
		$json_array["data"]=$xport_array["data"];
	}
	else {
		foreach($xport_array["data"] as $row) {
			$data = '"' . date("Y-m-d H:i:s", $row["timestamp"]) . '"';
			for($i=1;$i<=$xport_array["meta"]["columns"];$i++) {
				$data .= ',"' . $row["col" . $i] . '"';
			}
			print $data . "\n";
		}
	}

}

if (!empty($_GET["json"])) {
	echo prettyPrint(json_encode($json_array));
}

/* log the memory usage */
if (read_config_option("log_verbosity") >= POLLER_VERBOSITY_MEDIUM && function_exists('memory_get_peak_usage')) {
	cacti_log("The Peak Graph XPORT Memory Usage was '" . memory_get_peak_usage() . "'", FALSE, "WEBUI");
}

function prettyPrint( $json )
{
    $result = '';
    $level = 0;
    $prev_char = '';
    $in_quotes = false;
    $ends_line_level = NULL;
    $json_length = strlen( $json );

    for( $i = 0; $i < $json_length; $i++ ) {
        $char = $json[$i];
        $new_line_level = NULL;
        $post = "";
        if( $ends_line_level !== NULL ) {
            $new_line_level = $ends_line_level;
            $ends_line_level = NULL;
        }
        if( $char === '"' && $prev_char != '\\' ) {
            $in_quotes = !$in_quotes;
        } else if( ! $in_quotes ) {
            switch( $char ) {
                case '}': case ']':
                    $level--;
                    $ends_line_level = NULL;
                    $new_line_level = $level;
                    break;

                case '{': case '[':
                    $level++;
                case ',':
                    $ends_line_level = $level;
                    break;

                case ':':
                    $post = " ";
                    break;

                case " ": case "\t": case "\n": case "\r":
                    $char = "";
                    $ends_line_level = $new_line_level;
                    $new_line_level = NULL;
                    break;
            }
        }
        if( $new_line_level !== NULL ) {
            $result .= "\n".str_repeat( "\t", $new_line_level );
        }
        $result .= $char.$post;
        $prev_char = $char;
    }

    return $result;
}

?>