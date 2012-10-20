<?php

class MPD {

    var $link;

    function open() {
        global $MPD_HOST,$MPD_PORT;
        
        $this->link = fsockopen($MPD_HOST,$MPD_PORT,$errno,$errstr,2);

        stream_set_timeout($this->link,2);

        if (!$this->link) {
            
            die("cannot connect to mpd-server at $MPD_HOST:$MPD_PORT");
        }

        fgets($this->link);
        //dump(stream_get_meta_data($this->link));
        //dump(fgets($this->link));

    }

    function close() {
        fwrite($this->link,"close\n");
        //fread($this->link,8192);
        //dump(fgets($this->link));
        fclose($this->link);
        //dump($this->link);
    }
    
    function cmd($cmd) {

        $this->open();

        $q = "";
        if (is_array($cmd)) {
            $q = "command_list_begin\n". implode("\n",$cmd) . "\ncommand_list_end\n";
        }
        else {
            $q = "$cmd\n";
        }
        
        fwrite($this->link,$q);
        
        
        $result = "";
        while (!feof($this->link)) {
            $buf = fgets($this->link,8192);
            
            if (substr($buf,0,2) == "OK") {
                 break;
            }
            else {
                $result .= $buf;
            }
         }
        

        $this->close();
        return $result;

    }

    function status() {
        $this->open();

        fwrite($this->link,"status\n");

        $status = "";
        while (!feof($this->link)) {
            $status .= fread($this->link,8192);
            
        }
        
        $this->close();
        
        return $status;

    }

    
}
?>