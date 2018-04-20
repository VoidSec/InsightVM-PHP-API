# InsightVM-PHP-API
Quick and dirty PHP scripts to interact with InsightVM (Rapid 7)
They are not supposed to be pretty, just usefull

## Two files are present:
  - InsightVM STOP API.php: Will interact with the InsightVM instance trough its API to provide a "Panic Button" feature. It can be made avaiable to the night SOC guard in order to stop an active scan that is causing trouble on the network
  
  - InisghtVM Report Parser.php: Will take an InsightVM Scan Export (generated via Nexpose Simple XML Export) as input and it will produce a CSV file as output. CSV file will have the following structure: "IP";"OS";"Vulnerability";"Status Code"
  
### Vulnerabilities Status Code:
| Status Code | Description |
| --- | --- |
| ve | vulnerable, exploited: The check was positive as indicated by asset-specific vulnerability tests. Vulnerabilities with this result appear in the CSV report if the Vulnerabilities found result type was selected in the report configuration.|
| vv | vulnerable, version check: A check was positive because the version of the scanned service or application is associated with known vulnerabilities.|
| vp | vulnerable, potential: The check for a potential vulnerability was positive.|
| ee | excluded, exploited: A check for an exploitable vulnerability was excluded.|
| ev | excluded, version check: A check for a vulnerability that can be identified because the version of the scanned service or application is associated with known vulnerabilities was excluded.|
| ep | excluded, potential: A check for a potential vulnerability was excluded.|
| nv | not vulnerable: Nexpose did not find the target application or service to be vulnerable.|
| uk | unknown: Nexpose was unable to determine whether the scanned service or application is vulnerable.|
| sd | skipped because of DoS settings: Nexpose skipped the check because it involves Denial of Service settings.|
| sv | skipped because of inapplicable version: Nexpose skipped the check because the version of the target service or application is not associated with the given vulnerability.|
| er | error during check: Nexpose encountered an error during the check.|
| ds | skipped, disabled: A check was not performed because it was disabled in the scan template.|
| ov | overridden, version check: A check for a vulnerability that would ordinarily be positive because the version of the target service or application is associated with known vulnerabilities was negative due to information from other checks.|
| nt | no tests: There were no checks to perform.|
