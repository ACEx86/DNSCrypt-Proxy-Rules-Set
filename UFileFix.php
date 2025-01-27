<?php
namespace Drag0nPHP {
    /* UFileFix is a rules fixer for dnscrypt-proxy.
     This php script can process fix sort 100 Mega Bytes of text data in seconds.
     The main functions of the script is sort the data, remove some invalid characters, whitespaces, duplicates, etc.*/
	
    $UFileFix = new UFileFix();
    $UFileFix->SendChoice(1, 3);

    final class UFileFix
    {
        private string $UserProfile = '';
        private string $FilePath = '\Desktop\dnsc';
        private string $BlockedDomainName = '\blocked-names.txt';
        private string $BlockedIPName = '\blocked-ips.txt';
        private string $newline = "\n";
        private string $n = "\n";
        private string $r = "\r";
        private string $rn = "\r\n";

        final function __construct()
        {
            $this->UserProfile = '';
            $this->UserProfile = getenv('USERPROFILE') ?: $this->UserProfile = '';
        }

        Final Function __destruct()
        {
            $this->UserProfile = '';
            unset($this->UserProfile);
        }

        final function SendChoice(int $FilePath, int $NewLine): void
        {
            if ($NewLine === 1) {
                $this->newline = $this->n;
            } elseif ($NewLine === 2) {
                $this->newline = $this->r;
            } elseif ($NewLine === 3) {
                $this->newline = $this->rn;
            } elseif ($NewLine === 4) {/* Use current system */
                $this->newline = PHP_EOL;
            }
            if ($FilePath === 1) {
                $this->SortAndFixFile($this->UserProfile . $this->FilePath . $this->BlockedDomainName);
            } elseif ($FilePath === 2) {
                $this->SortAndFixFile($this->UserProfile . $this->FilePath . $this->BlockedIPName);
            }
        }

        private function SortAndFixFile(string $file_path): void
        {
            if (!empty($file_path) and file_exists($file_path) === True and is_readable($file_path) === True) {
                $Data = file_get_contents($file_path) ?: $Data = '';
            } else {
                exit;
            }
            // Check Data
            $rand_num = rand(111111111, 999999999);
            $Removed_Data = '#####' . $this->newline . 'Search for: ' . $rand_num . " to find what we replaced.$this->newline#####$this->newline$this->newline" . preg_replace("/[^a-zA-Z0-9><@.?=:$this->newline\/*#_ -]/", $rand_num, $Data) . "$this->newline$this->newline$this->newline < R > THE DOMAINS WE REMOVED:$this->newline$this->newline" ?: $Removed_Data = '';
            $Data = preg_replace("/[^a-zA-Z0-9><@.?=:$this->newline\/*#_ -]/", '', $Data) ?: $Data = '';
            $DataLength = -1;
            $DataLength = substr_count($Data, $this->newline) ?: $DataLength = -1;
            // Data Array - Saving the data into an array and accessing it with isset and similar functions saves a lot of time instead of doing it with a string and str_contains effectively making the processing of the data faster. From minutes to seconds.
            $Data = preg_split("/$this->newline/", $Data) ?: $Data = '';
            // Here we save all the data that was processed. We need this to check (not add->fix duplicates) and sort the data correctly after a comment or..
            $AllData = [];
            // Data after the last comment. Comments may separate addresses
            $SortedData = [];
            // The processed correct data to output to a file in a string.
            $FixedData = '';
            for ($x = 0; $x <= $DataLength; $x++) {
                $current_line = '';
                if (isset($Data[$x]) === True) {
                    $current_line = $Data[$x] ?: $current_line = '';
                    if (!empty($current_line) === True and strlen($current_line) > 1 and $current_line[0] != '#' and $current_line[0] != $this->newline) {
                        $tmp_currentline = '';
                        $tmp_splitcurrentline = '';
                        if (str_contains($current_line, '#') === True) {
                            $tmp_currentline = explode('#', $current_line)[0];
                            /* Remove characters */
                            $tmp_currentline = preg_replace("/[^a-zA-Z0-9><@.:*_-]/", '', $tmp_currentline);
                            //Don't remove after comment anything
                            $tmp_splitcurrentline = substr($current_line, strlen($tmp_currentline)) ?: $tmp_splitcurrentline = '';
                            $tmp_splitcurrentline = preg_replace("/[^a-zA-Z0-9><@.?=:\/*#_ -]/", '', $tmp_splitcurrentline);
                        } else {
                            $tmp_currentline = $current_line;
                            /* Remove characters */
                            $tmp_currentline = preg_replace("/[^a-zA-Z0-9><@.:*_-]/", '', $tmp_currentline);
                        }
                        if (!empty($tmp_currentline) === True and isset($AllData[$tmp_currentline]) === False) { /* Remove duplicates by not adding the line */
                            $AllData[$tmp_currentline] = $tmp_currentline;
                            $SortedData[$tmp_currentline . $tmp_splitcurrentline] = $tmp_currentline . $tmp_splitcurrentline;
                        } elseif (!empty($tmp_currentline) === True) { /* Add the data that we removed to the log file */
                            $Removed_Data .= $tmp_currentline . $this->newline;
                        } else {
                            $Removed_Data .= 'Remove data: Error processing data.' . $this->newline;
                        }
                    } elseif (!empty($current_line) === True and strlen($current_line) > 0 and $current_line[0] == '#') {
                        if (!empty($SortedData) === True) {
                            sort($SortedData);
                            $FixedData .= implode($this->newline, $SortedData) . $this->newline;
                            echo count($SortedData) . PHP_EOL;
                            $SortedData = []; /* Remove data from the array so we don't mess up the lines from previous comments */
                        }
                        $FixedData .= $current_line;
                        /* Fix the file by adding new line. */
                        if (isset($FixedData[strlen($FixedData) - 1]) === True) {
                            $FixedData .= $this->newline;
                        }
                    } elseif (!empty($current_line) === True) { /* Add the data that we removed to the log file */
                        $Removed_Data .= $current_line . $this->newline; //Check for not adding some data by logic
                    }
                }
            }
            if (isset($this->UserProfile) === True and !empty($this->UserProfile) === True and is_string($this->UserProfile) === True and strlen($this->UserProfile) > 9) {
                // Adds the data after the last comment to the file. It does not mess with the old data if a comment exist (add twice or) because we clear the data that we should add.
                if (!empty($SortedData) === True) {
                    sort($SortedData);
                    $FixedData .= implode($this->newline, $SortedData);
                    echo count($SortedData) . PHP_EOL;
                    $SortedData = []; /* ALSO TAKES PLACE FOR THIS FIX. Remove data from the array so we don't mess up the lines from previous comments */
                }
                file_put_contents($this->UserProfile . '\Desktop\block_rules.txt', $FixedData);
                file_put_contents($this->UserProfile . '\Desktop\block_file_removed.txt', $Removed_Data);
            }
        }
    }
} ?>