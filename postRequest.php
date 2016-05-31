<?php

/**
 * Copyright 2016 , SciFY NPO - http://www.scify.org
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *    http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

/**
 * @link       http://thewildpointers.com/
 * @since      1.0.0
 * @package    WPLawDetectorGR
 * @author     WildPointers <2wildpointers@gmail.com>
 */


/**
* This is the function which returns
* the link pointing to the law found
* in the content of the Wordpress page.
* @param FEK_year is the year when the Newspaper of
* The Government containing the law was published
* @param FEK_issue is the issue of the Newspaper of
* The Government containing the law
* @param FEK_number is the number of the issue of the Newspaper
* of The Government containing the law
* @param FEK_checkBoxIssue is the checkbox reffering to the issue
*/
public function postRequest($FEK_year, $FEK_issue, $FEK_number, $checkBoxIssue) {
	// the url of the "Ethniko Tupografeio", where the form for the law search should be completed
	$url = 'http://wwww.et.gr/idocs-nph/search/fekForm.html';
	// the year that the law was published
	$year = (int)$FEK_year;
	// the array with each reference on a law and the link which points to this law
	$resultsArray = array(array());

	$index1 = 0;
	$index2 = 0;

	if ($year < 30) {
		$FEK_year = (string)(2000 + $year);
	} elseif ($year < 100) {
		$FEK_year = (string)(1900 + $year);
	} else {
		$FEK_year =  (string)($year);
	}

	$fekyear = (int)$FEK_year;
	/**
	* declaration of an array, which contains all the required elements,
	* in order to fill the form available in the url referred above
	*/
	$data = array('year' => $FEK_year,
					$checkBoxIssue => 'on',
					'fekNumberFrom' => $FEK_number,
					'fekNumberTo' => $FEK_number,
					'fekEffectiveDateFrom' => '01.01'.((string)$fekyear),
					'fekEffectiveDateTo' => '31.12',((string)$fekyear),
					'fekReleaseDateFrom' => '01.01'.((string)$fekyear),
					'fekReleaseDateTo' => '31.12'.((string)($fekyear+1)),
					'pageNumber' => '1');

	$options = array(
		'http' => array(
			'header'  => "Accept-language: en\r\n" .
						 "Content-type: application/x-www-form-urlencoded\r\n" .
						 "User-agent: Mozilla/5.0 (Windows NT 6.3; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/43.0.2357.132 Safari/537.36\r\n",
			'method'  => 'POST',
			'content' => http_build_query($data)
		)
	);

	$context  = stream_context_create($options);
	/**
	* save in the variable result the contents retrieved from the results
	* of the search form
	*/
	$result = file_get_contents($url, false, $context);

	/**
	* if a result is found, then return
	* the url pointing to the required law
	*/
	if (strpos($result, 'Το ΦΕΚ σε PDF μορφή') !== false) {
		$urlpdf = 'http://www.et.gr'.substr($results, strpos($results, 'href=') + 6, strpos($results, '\" target=') - (strpos($results, 'href=') + 6));
		}
	return $urlpdf;
}