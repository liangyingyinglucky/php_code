<?php

class index{
    public function print_r_reverse($in) {
        $lines = explode("\n", trim($in));
        if (trim($lines[0]) != 'Array') {
            return $in;
        } else {
            if (preg_match("/(\s{5,})\(/", $lines[1], $match)) {
                $spaces = $match[1];
                $spaces_length = strlen($spaces);
                $lines_total = count($lines);
                for ($i = 0; $i < $lines_total; $i++) {
                    if (substr($lines[$i], 0, $spaces_length) == $spaces) {
                        $lines[$i] = substr($lines[$i], $spaces_length);
                    }
                }
            }
            array_shift($lines);
            array_shift($lines);
            array_pop($lines);
            $in = implode("\n", $lines);
            preg_match_all("/^\s{4}\[(.+?)\] \=\> /m", $in, $matches, PREG_OFFSET_CAPTURE | PREG_SET_ORDER);
            $pos = array();
            $previous_key = '';
            $in_length = strlen($in);
            foreach ($matches as $match) {
                $key = $match[1][0];
                $start = $match[0][1] + strlen($match[0][0]);
                $pos[$key] = array($start, $in_length);
                if ($previous_key != '') $pos[$previous_key][1] = $match[0][1] - 1;
                $previous_key = $key;
            }
            $ret = array();
            foreach ($pos as $key => $where) {
                $ret[$key] = $this->print_r_reverse(substr($in, $where[0], $where[1] - $where[0]));
            }
            return $ret;
        }
    }



    public function urlParse($param = ''){
        $url = $_SERVER['REQUEST_URI'];
        if(strpos($url, "?") !== false){
            //有？
            $url = $url.'&page=';
        }else{
            //没有？
            $url = $url.'?page=';
        }
        $url = $_SERVER['REQUEST_URI'] . (strpos($_SERVER['REQUEST_URI'], "?") ? "&" : "?") . $param;
        $parse = parse_url($url);
        if (isset($parse["query"])) {
            parse_str($parse["query"], $params);
            unset($params["page"]);
            $url = $parse["path"] . "?" . http_build_query($params);
        }
        return $url;
    }



}

