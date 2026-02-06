<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Ari extends CI_Controller {

    private $conn;
    private $ariConnection = 'aritest:12345';

    public function __construct() {
        parent::__construct();

        error_reporting(E_ALL);

        require_once "vendor/autoload.php";
        error_reporting(E_ALL);

        $this->conn = new phpari("hello-world", "vendor/greenfieldtech-nirs/phpari/phpari.ini");
    }

    public function config() {
        echo nl2br(file_get_contents('vendor/greenfieldtech-nirs/phpari/phpari.ini'));
    }

    public function getCallOutgoing($uniqueid) {
        sleep(2);
        $res = $this->db->select('*')->from('call_log_outgoing')->where('uniqueid', $uniqueid)->get()->row_array();

        header('Content-Type:text/json');
        echo json_encode($res);
    }

    public function getListChannel() {
        $channels = new channels($this->conn);

        echo json_encode($channels->channel_list());
    }

    public function getRecordingList() {
        $cRecordings = new recordings($this->conn);

        echo json_encode($cRecordings->recording_list());
        exit(0);
    }

    public function downloadRecording() {
        $file_name = $this->input->get('file_name');
        $file_name = str_replace(' ', '+', $file_name);

        //        echo file_get_contents('http://localhost:8088/ari/recordings/stored/' . urlencode($file_name) . '/file?api_key=' . $this->ariConnection);

        if ($this->input->get('play')) {
            $file = file_get_contents('http://10.140.90.7:8088/ari/recordings/stored/' . urlencode($file_name) . '/file?api_key=' . $this->ariConnection);

            $path = 'tmp/';

            if (!is_dir($path)) {
                mkdir($path, 0777);
            }

            $path = 'tmp/' . uniqid();

            file_put_contents($path, $file);

            $this->stream($path, 'audio/wav');

            unlink($path);
        } else {
            header('Cache-Control:no-cache, no-store');
            header('Content-Type:audio/wav');
            header('Content-Disposition: attachment; filename="' . $file_name . '"');

            echo file_get_contents('http://10.140.90.7:8088/ari/recordings/stored/' . urlencode($file_name) . '/file?api_key=' . $this->ariConnection);
        }
    }

    function stream($file, $content_type = 'application/octet-stream') {

        // Make sure the files exists
        if (!file_exists($file)) {
            header("HTTP/1.1 404 Not Found");
            exit;
        }

        // Get file size
        $filesize = sprintf("%u", filesize($file));

        // Handle 'Range' header
        if (isset($_SERVER['HTTP_RANGE'])) {
            $range = $_SERVER['HTTP_RANGE'];
        } elseif ($apache = apache_request_headers()) {
            $headers = array();
            foreach ($apache as $header => $val) {
                $headers[strtolower($header)] = $val;
            }
            if (isset($headers['range'])) {
                $range = $headers['range'];
            } else
                $range = FALSE;
        } else
            $range = FALSE;

        //Is range
        if ($range) {
            $partial = true;
            list($param, $range) = explode('=', $range);
            // Bad request - range unit is not 'bytes'
            if (strtolower(trim($param)) != 'bytes') {
                header("HTTP/1.1 400 Invalid Request");
                exit;
            }
            // Get range values
            $range = explode(',', $range);
            $range = explode('-', $range[0]);
            // Deal with range values
            if ($range[0] === '') {
                $end   = $filesize - 1;
                $start = $end - intval($range[0]);
            } else if ($range[1] === '') {
                $start = intval($range[0]);
                $end   = $filesize - 1;
            } else {
                // Both numbers present, return specific range
                $start   = intval($range[0]);
                $end     = intval($range[1]);
                if ($end >= $filesize || (!$start && (!$end || $end == ($filesize - 1))))
                    $partial = false;
            }
            $length = $end - $start + 1;
        }
        // No range requested
        else {
            $partial = false;
            $length  = $filesize;
        }

        // Send standard headers
        header("Content-Type: $content_type");
        header("Content-Length: $length");
        header('Accept-Ranges: bytes');
        // send extra headers for range handling...
        if ($partial) {
            header('HTTP/1.1 206 Partial Content');
            header("Content-Range: bytes $start-$end/$filesize");
            if (!$fp = fopen($file, 'rb')) { // Error out if we can't read the file
                header("HTTP/1.1 500 Internal Server Error");
                exit;
            }
            if ($start)
                fseek($fp, $start);
            while ($length) {
                set_time_limit(0);
                $read   = ($length > 8192) ? 8192 : $length;
                $length -= $read;
                print(fread($fp, $read));
            }
            fclose($fp);
        }
        //just send the whole file
        else
            readfile($file);
        exit;
    }

    public function spy($channelId) {
        $channels = new channels($this->conn);

//        echo json_encode($channels->channel_list());

        echo json_encode($channels->snoop($channelId, 'both', 'none', 'hello-world'));
    }

    public function coach($channelId) {
        
    }

    public function getSoundList() {
        $sounds = new sounds($this->conn);

        echo json_encode($sounds->sounds_list('en', 'wav'));
        exit(0);
    }

    public function getBridgeList() {
        $bridges = new bridges($this->conn);

//        $ret = $bridges->addChannel('test-bridge', '1562582952.1848');
        $ret = $bridges->bridges_list();

        echo json_encode($ret);
    }

    public function getApplicationList() {
        try {
            $app = new applications($this->conn);

            $result = $app->applications_list();

            if ((!$result) && (count($result)))
                throw new Exception("phpari error occured", 503);

            print_r($result);
            exit(0);
        } catch (Exception $e) {
            echo "Error: " . $conn->lasterror . "\n";
            echo "Trace: " . $conn->lasttrace . "\n";
        }
    }

    public function getEndpoint() {
        $cEndPoints = new endpoints($this->conn);
        $response   = $cEndPoints->endpoints_list();

        echo json_encode($response, JSON_PRETTY_PRINT);
        exit(0);
    }

    public function stasis() {
        include_once __DIR__ . ' / BasicStasisApplication . php';

        $basicAriClient = new BasicStasisApplication("hello-world");

        $basicAriClient->stasisLogger->info("Starting Stasis Program... Waiting for handshake...");
        $basicAriClient->StasisAppEventHandler();

        $basicAriClient->stasisLogger->info("Initializing Handlers... Waiting for handshake...");
        $basicAriClient->StasisAppConnectionHandlers();

        $basicAriClient->stasisLogger->info("Connecting... Waiting for handshake...");
        $basicAriClient->execute();
    }

    public function getMailbox() {
        $mailboxes = new mailboxes($this->conn);

        echo json_encode($mailboxes->mailboxes_list());
    }

}
