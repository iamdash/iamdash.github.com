<?php

require_once('twitteroauth/twitteroauth.php');

class Twitter {

    private $api_domain = 'https://api.twitter.com';
    private $api_key = 'BOEpUsPXfWkcPoucbbSmA';
    private $api_secret = 'HvFUUhEUEmBIYYpi04Eh6qxNTC8AFtfL9PQESnK6Yc';
    private $oauth_token = '594414034-VX7DDarINyQlBNJmKdSpZnIIOVzl3se2cK0h8AIw';
    private $oauth_token_secret = 'aVo1fHIfoFNnZt528ZDtDLezWB3o34UapsMkGrNGSU';
    private $user_id = '';
    private $username = '_iamdash';
    private $access_token = '';
    private $num_combined_feeds = 1;
    private $feed_type_id = 1;
    private $cache_dir;
    protected $itemcount;
    private $expire_time = 0;

    public function __construct($itemcount) {
 
        $this->itemcount = $itemcount;
        $this->timeline = array();
        //echo "mysql:host=".$MYSQL_HOST.";dbname=".MYSQL_DB, MYSQL_USER, MYSQL_PASS;exit();
        $this->pdo = $this->_PDOConnect();
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $this->cache_dir = $_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . 'cache' . DIRECTORY_SEPARATOR . 'twitter';
        $this->expire_time = $time_expire = time() + 24 * 60 * 60;
        make_cache_path($this->cache_dir);
        $this->conn = new TwitterOAuth($this->api_key, $this->api_secret, $this->oauth_token, $this->oauth_token_secret);
    }

    public function getCombinedFeeds() {

        $tweets = $this->_cacheTweets();

        return $tweets;
    }

    public function getRecentTweets() {
        $content = $this->_getNewTweets();
        /*$content = $this->conn->get("statuses/user_timeline", array(
            'count' => 200,
            'page' => $_GET['page']
        ));
        d($content);*/
        $this->_cacheTweetsToDB($content);

        return $this->_normalizeFeed(json_encode($content), 'tweeted');
    }

    private function _normalizeFeed($response, $action) {
        $r = json_decode($response);

        $feed = array();
        $i = 0;
        foreach ($r as $d) {
            if (isset($d->code) && $d[0]->code == 34) {
                continue;
            } else {
                if (is_object($d)) {
                    $feed[$i]['feed_type_id'] = $this->feed_type_id;
                    $feed[$i]['feed_type'] = 'twitter';
                    $feed[$i]['pub_date'] = $d->pub_date;
                    #$feed[$i]['time_ago'] = $this->_ago($feed[$i]['pub_date']);
                    $feed[$i]['id_str'] = $d->id_str;
                    $feed[$i]['title'] = $d->title;
                    $feed[$i]['content'] = $this->_parseTweet($d);
                    $feed[$i]['hero_image'] = $this->_getTweetMedia($d);
                    $feed[$i]['url'] = 'http://www.twitter.com/' . $this->username . '/status/' . $d->id;
                }
                $i++;
            }
        }
        return $feed;
    }

    private function _getTweetMedia($tweet) {
        if (isset($tweet->entities->media)) {
            return $tweet->entities->media[0]->media_url;
        } else {
            return '';
        }
    }
    private function _parseTweet($tweet) {

        //$t['raw'] = $tweet->text;
        // link URLs
        $t['parsed'] = " " . preg_replace("/(([[:alnum:]]+:\/\/)|www\.)([^[:space:]]*)([[:alnum:]#?\/&=])/i", "<a href=\"\\1\\3\\4\" target=\"_blank\">\\1\\3\\4</a>", $tweet->content);

        // link mailtos
        $t['parsed'] = preg_replace("/(([a-z0-9_]|\\-|\\.)+@([^[:space:]]*)" .
                "([[:alnum:]-]))/i", "<a href=\"mailto:\\1\">\\1</a>", $t['parsed']);

        //link twitter users
        $t['parsed'] = preg_replace("/ +@([a-z0-9_]*) ?/i", " <a href=\"http://twitter.com/\\1\" target=\"_blank\">@\\1</a> ", $t['parsed']);

        //link twitter arguments
        $t['parsed'] = preg_replace("/ +#([a-z0-9_]*) ?/i", " <a href=\"http://twitter.com/search?q=%23\\1\" target=\"_blank\">#\\1</a> ", $t['parsed']);

        // truncates long urls that can cause display problems (optional)
        $t['parsed'] = preg_replace("/>(([[:alnum:]]+:\/\/)|www\.)([^[:space:]]" .
                "{30,40})([^[:space:]]*)([^[:space:]]{10,20})([[:alnum:]#?\/&=])" .
                "</", ">\\3...\\5\\6<", $t['parsed']);

        $t = preg_replace('/%u([a-fA-F0-9]{4})/', '&#x\\1;', $t['parsed']);

        return $t;
    }
    /**
     *
     * @desc    Checks if tweet cache file exists. If not, we write the file, and return it's contents
     * @param type json file contents of cached file
     * @return type
     */
    public function cacheTweets($tweets=false){

        if(!$tweets){
            $this->_cacheTweetsToDB($this->getRecentTweets());
        }else{
            $this->_cacheTweetsToDB($tweets);
        }
    }



    /*     * ****** -----------------------------------------------------------------*** */

    private function _PDOConnect() {
        $opt = array(
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        );
        return new PDO(DB_DSN,DB_USER,DB_PASSWORD, $opt);
    }
    private function _getNewTweets() {

        $statement = $this->pdo->prepare('SELECT * FROM cache WHERE feed_type_id=:feed_type_id ORDER BY pub_date DESC LIMIT 1');
        $statement->execute(array(':feed_type_id' => $this->feed_type_id));
        $row = $statement->fetch();

        $content = $this->conn->get("statuses/user_timeline", array(
            'count' => $this->itemcount,
            'since_id' => $row['id_str']
        ));

        $this->_cacheTweetsToDB($content);
        return $this->_fetchCachedTweets();

    }

    private function _fetchCachedTweets(){

        $statement = $this->pdo->prepare('SELECT * FROM cache WHERE feed_type_id=:feed_type_id ORDER BY pub_date DESC LIMIT '.$this->itemcount);
        $statement->execute(array(':feed_type_id' => $this->feed_type_id));

            $rows = $statement->fetchAll(PDO::FETCH_ASSOC);


        return $rows;

    }

    private function _cacheTweetsToDB($tweets) {
        $tweets = (array)$tweets;
        $tweets = array_reverse($tweets);

        try {

            foreach ($tweets as $key => $t) {
                $insert_vals = array();

                foreach($t as $t_key=>$val){
                    
                    //$insert_vals[$t_key] = is_object($val) ? serialize($val):$val;
                    //debug($t_key);
                }
                
                if(is_object($t)){
                    //debug($t);
                    $insert_vals['created'] = time();
                    $insert_vals['feed_type_id'] = 1;
                    $insert_vals['feed_type'] = "'twitter'";
                    $insert_vals['pub_date'] = strtotime($t->created_at);
                    $insert_vals['title'] = '"'.$t->user->screen_name.'"';
                    $insert_vals['content'] = '"'.$t->text.'"';
                    $insert_vals['id_str'] = '"'.$t->id_str.'"';
                    $insert_vals['url'] = '"http://www.twitter.com/'.$t->user->screen_name.'/statuses/'.$t->id.'"';

                    $sql = "INSERT INTO cache (" . implode(",", array_keys($insert_vals)) . ") VALUES (" . implode(",", $insert_vals).")";

                    $stmt = $this->pdo->prepare($sql);
                    
                    try {
                        $stmt->execute();
                    } catch (PDOException $e) {
                        //echo $e->getMessage();
                    }
                }
               // $conn->commit();
            }
        } catch (PDOException $e) {
            echo 'ERROR: ' . $e->getMessage();
        }
    }

    private function _placeholders($text, $count = 0, $separator = ",") {
        $result = array();
        if ($count > 0) {
            for ($x = 0; $x < $count; $x++) {
                $result[] = $text;
            }
        }

        return implode($separator, $result);
    }

    /*     * ****** -----------------------------------------------------------------*** */
}

?>
