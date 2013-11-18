<?php
//This file downloads the data from the url, and parse the html into a multidimensional array.
//Base url
$base = "http://client_html_url";

//Curl gets the base url content. 'file_get_contents' requires 'allow_url_fopen' directives, and so less reliable. 
$curl = curl_init();
curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
curl_setopt($curl, CURLOPT_HEADER, false);
curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($curl, CURLOPT_URL, $base);
curl_setopt($curl, CURLOPT_REFERER, $base);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
$str = curl_exec($curl);
curl_close($curl);


//Creates a dom object
$html = new DOMDocument();
$html->loadHTML($str);

$tables = $html->getElementsByTagName('table');


//Prepares the data array
$data = array();


//Reads all tables from the html (there were 2 in the original)

foreach ($tables as $table)
	{	
	$rows = $table->getElementsByTagName('tr');	
	
		//rows are indexed in foreach clause because first row is used to extract years, while the rest are for data.
		foreach ($rows as $rowindex => $row)
		
		{
			if ($rowindex == 0)
			{
			//reads first row from each table into year keys
				$years = $row->getElementsByTagName('td');
				$year_keys = array();
				foreach ($years as $year)
				{
				$year_keys[] = $year->nodeValue;
				}			
			}
			
			else
			{
				$sum = 0;
				$cells = $row->getElementsByTagName('td');

				//cells are also indexed because first cell supplies financial title, while the rest just gives numbers
				foreach ($cells as $cellindex => $cell)
				{
				if ($cellindex == 0)
					{
					//first cell goes into the title key
					$financial_titles = $cell->nodeValue;				
					}
		
			
				
				else
					{
					
					//if not the first cell, convert the td contents into integer type and save as array values under appropriate keys
					//strtr takes less memory and more reliable than preg_replace
					
					//gets the matching years
					$year_key = $year_keys[$cellindex];						
					
					//fills the value for the array $data
					$data[$financial_titles][$year_key] = (int)strtr($cell->nodeValue, array("("=> "-", ")"=> "", ","=>"", "$"=>"", "-" => ""));
					
					}
				}
				
		    } 
		}
	}

//Now there is the multidimensional array called $data on the buffer. 
	
//It also extracts the financial titles from the data. all 4 steps make use of this information. 
$alltitles = array_keys($data);	
?>