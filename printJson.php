<?php
/* $data = json_decode($_POST["data"]); */

$data = get_data("http://search.twitter.com/search.json?q=hello");
header('Content-type: text/javascript');
echo prettyPrint($data);

function get_data($url)
{
    $ch = curl_init();
    $timeout = 5;
    curl_setopt($ch,CURLOPT_URL,$url);
    curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
    curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,$timeout);
    $data = curl_exec($ch);
    curl_close($ch);
    return $data;
}

function prettyPrint($json)
{
    $result = '';
    $level = 0;
    $prev_char = '';
    $in_quotes = false;
    $ends_line_level = NULL;
    $json_length = strlen($json);

    for($i = 0; $i < $json_length; $i++)
    {
        $char = $json[$i];
        $new_line_level = NULL;
        $post = "";
        if($ends_line_level !== NULL)
        {
            $new_line_level = $ends_line_level;
            $ends_line_level = NULL;
        }
        if($char === '"' && $prev_char != '\\')
        {
            $in_quotes = !$in_quotes;
        }
        else if(! $in_quotes)
        {
            switch($char)
            {
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
        if($new_line_level !== NULL)
        {
            $result .= "\n".str_repeat( "\t", $new_line_level );
        }
        $result .= $char.$post;
        $prev_char = $char;
    }

    return $result;
}

?>