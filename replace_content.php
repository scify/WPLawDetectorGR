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

require_once('RegexFEK.php');

/**
* This is the function which retrieves the content
* of the WordPress webpage in order to find
* a reference to a law and then replaces this reference
* with the link pointing to this specific law.
* @param more_link_text
* @param strip_teaser
*/
public function replace_content($more_link_text, $strip_teaser){
	$content = get_the_content( $more_link_text, $strip_teaser );
	$resultsArray = RegexFEK($content);
	foreach ($resultsArray as $v1) {
		foreach($resultsArray as $v2){
			if ($v1 == $v2){
				/**
				* replace the content with the link and
				* the required html tags
				*/
				$content = str_replace($resultsArray[$v1][$v2], '<a href="'.$resultsArray[$v1][$v2 + 1].'">'.$resultsArray[$v1][$v2 ].'</a>',$content);
			}
		}
	}
	// apply the filters to content
	$content = apply_filters( 'replace_content', $content );
	echo $content;
}