<?php 

require_once('postRequest.php');

/**
* Copyright 2016, SciFY NPO - http://www.scify.org
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
* This is the function which checks the content
* of the WordPress webpage in order to find
* a reference to a law.
* @param content is the content of the
* Wordpress webpage
*/
public function regexFEK($content){
	/**
	* the pattern that should be looked for in order to identify
	* a reference to the Newspaper of the Government
	*/
	$fekPattern = "(((άρθ[α-ω\\.]{0,} ?\\d+,? [Α-Ωα-ω0-9, ]*παρ[α-ωά\\.]{0,} ?\\d+,? [Α-Ωα-ω0-9, ]*)|(παρ[α-ωά\\.]{0,} ?\\d+,? [Α-Ωα-ω0-9, ]*άρθ[α-ω\\.]{0,4} ?\\d+,? [Α-Ωα-ω0-9, ]*))?((([Νν][όο]μ)[α-ω]{0,3}|[νΝ])\\.?|([Ππ]\\.?[Δδ]\\.?)) ?\\d+\\/\\d+( *\\((ΦΕΚ )?[Α-Ωα-ω]{1,2}[΄`΄'’]? ?\\d+((\\/|, ?)(\\d|\\.|-)*)?\\))?)/i";

	/**
	* the pattern that should be looked for in order to identify
	* a reference to a law of the Newspaper of the Government
	*/
	$lawPattern = "((([Νν][όο]μ)[α-ω]{0,3}|[νΝ])\\.?|([Ππ]\\.?[Δδ]\\.?)) ?\\d+\\/\\d+/i";

	/**
	* method in order to check if any string of the
	* content retrieved corresponds to the pattern
	*/
	preg_match_all($fekPattern,$content,$matchesFek);
	// if a match was found
	if(!empty($matchesFek)){
		foreach ($matchesFek as $v1) {
			if ($v1 == 0){
				foreach ($v1 as $v2) {
					$entityFound = $matchesFek[$v1][$v2];
					// check if there is a match witht the law pattern
					preg_match_all($lawPattern,$entityFound,$matchesLaw);
					/**
					* if no match with the law pattern was found
					* then continue witth the next match with the FEK pattern
					* that was found
					*/
					if(empty($matchesLaw)){
						continue;
					}
					$entityLaw = $matchesLaw[0][0];

					/** Split the law found on the "/" symbol in two parts.
					* For example if this is the law: "ν. 2873/2000 (Α’ 285)",
					* we have two parts "ν. 2873" and "2000 (Α’ 285)"
					*/
					$fekParts = explode('/', $entityFound, 2);
					$entityType = "fek";

					// take the second part of the match, as defined above
					if (strpos($fekParts[1], "(") !== false) {
						/**
						* Check that part for matched accent characters (΄ or ' or ’ or `)
						* and there are  get from this string the FEK issue (e.g. A) and FEK number
						*/
						$FEK_year = trim(substr($fekParts[1], 0, strrpos($fekParts[1], '(')));
						if (strpos($fekParts[1], "΄") !== false){
							$Fek_issue = str_replace("(", "", trim(substr($fekParts[1], strrpos($fekParts[1], '('), strrpos($fekParts[1], '΄') - strrpos($fekParts[1], '('))));
							$Fek_number = str_replace("΄", "", trim(substr($fekParts[1], strrpos($fekParts[1], '΄'), strrpos($fekParts[1], ')') - strrpos($fekParts[1], '΄'))));

						} elseif (strpos($fekParts[1], "'") !== false) {
							$Fek_issue = str_replace("(", "", trim(substr($fekParts[1], strrpos($fekParts[1], '('), strrpos($fekParts[1], "'") - strrpos($fekParts[1], '('))));
							$Fek_number = str_replace("'", "", trim(substr($fekParts[1], strrpos($fekParts[1], "'"), strrpos($fekParts[1], ')') - strrpos($fekParts[1], "'"))));

						} elseif (strpos($fekParts[1], "’") !== false) {
							$Fek_issue = str_replace("(", "", trim(substr($fekParts[1], strrpos($fekParts[1], '('), strrpos($fekParts[1], "’") - strrpos($fekParts[1], '('))));
							$Fek_number = str_replace("’", "", trim(substr($fekParts[1], strrpos($fekParts[1], "’"), strrpos($fekParts[1], ')') - strrpos($fekParts[1], "’"))));

						} elseif (strpos($fekParts[1], "`") !== false) {
							$Fek_issue = str_replace("(", "", trim(substr($fekParts[1], strrpos($fekParts[1], '('), strrpos($fekParts[1], "`") - strrpos($fekParts[1], '('))));
							$Fek_number = str_replace("`", "", trim(substr($fekParts[1], strrpos($fekParts[1], "`"), strrpos($fekParts[1], ')') - strrpos($fekParts[1], "`"))));

						}else {
							/**
							* If none of thes characters was found then check if a space
							* exists or else get the first character
							* as the FEK issue (e.g. A, B etc)
							*/

							$Fek_issue = str_replace(")", "", (str_replace("(", "", trim(substr($fekParts[1], strrpos($fekParts[1], '('), strrpos($fekParts[1], ")") - strrpos($fekParts[1], '('))))));
							if (startsWith($Fek_issue, "ΦΕΚ")){
								$Fek_issue = trim(substr($FEK_issue, strpos("ΦΕΚ") + 3));
							}

							if(strpos(Fek_issue, " ") !== false) {
								$splittedIssue = explode(" ", $Fek_issue);
								$Fek_issue = $splittedIssue[0];
								$Fek_number = $splittedIssue[1];

							} else {
								$Fek_number = substr($FEK_issue, 1);
								$Fek_issue = substr($FEK_issue, 0, 1);
							}
						}
						// split the Fek_number in tokens
						$FEK_num_token = strtok($Fek_number, "(\\/|-| |,)");
						$Fek_number = $FEK_num_token;

						/**
						* Set the corresponding issue checkbox based on the FEK issue retrieved above
						* if the issue found is "B" then select the checkbox reffering to the
						* second issue
						*/
						switch ($Fek_issue) {
							case "Α":
								$checkBoxIssue = "chbIssue_1";
								break;

							case "Β":
								$checkBoxIssue = "chbIssue_2";
								break;

							case "Γ":
								$checkBoxIssue = "chbIssue_3";
								break;

							case "Δ":
								$checkBoxIssue = "chbIssue_4";
								break;

							case "":
								$checkBoxIssue = "";
								break;
						}
					}
					/** save the results, i.e. the match found  and the url
					* referring to this specific lawin a two dimensional array
					*/
					$resultsArray[$index1][$index2] = $entityLaw;
					$resultsArray[$index1][$index2 + 1] = postRequest($FEK_year, $FEK_issue, $FEK_number, $checkBoxIssue);
					$index1 = $index1 + 1;
				}
			}
		}

	}
	return $resultsArray;
}