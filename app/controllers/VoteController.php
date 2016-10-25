<?php

use Phalcon\Mvc\Controller;

class VoteController extends Controller
{
    public function indexAction()
    {

    }

    public function dummyAction()
    {
        return "true";
    }

    public function voteAction() {

        $postid = $this->request->getPost('postid');
        $userid = $this->request->getPost('userid');
        $upvote = ($this->request->getPost('score') == 1) ? 1 : 0;
        $downvote = ($this->request->getPost('score') == -1) ? 1 : 0;
        
        $cql = "insert into post_user (postid, userid, upvote, downvote, timestamp) values ('$postid', '$userid', $upvote, $downvote, dateOf(now()));";
                
        $result = $this->execute_cql($cql);
        
        if($result['success']) {

            return json_encode(['success' => true]);
        } else {

            return json_encode(['success' => false]);
        }
 
    }

    public function getVoteStatusAction($userid) {
        
        $cql = "select * from user_timestamp where userid = '$userid' limit 100;";
                
        $result = $this->execute_cql($cql);
        
        if($result['success']) {

            $arr = array();
            foreach ($result['data'] as $row) {
                
                array_push($arr, array(
                    'postid' => $row['postid'],
                    'score' => $row['upvote'] + -1 * $row['downvote']
                    ));
            }

            return json_encode(['success' => true, 'result' => $arr]);
        } else {

            return json_encode(['success' => false]);
        }

    }

    public function getVoteCountsAction($postid) {

        $cql = "select sum(downvote) as downvote, sum(upvote) as upvote from post_user where postid = '$postid';";
        
        $result = $this->execute_cql($cql);
                
        if($result['success']) {             

            return json_encode(['success' => true, 'result' => [
                'upvote' => $result['data'][0]['upvote'],
                'downvote' => $result['data'][0]['downvote']
                ]]);
        } else {

            return json_encode(['success' => false]);
        }
    }

    function execute_cql($cql) {
        try {
            $cluster   = Cassandra::cluster()                 // connects to localhost by default
                        ->withContactPoints('10.164.6.78', '10.165.2.217')
                        ->withPort(9042)
                        ->build();
            $keyspace  = 'ninegag';
            $session   = $cluster->connect($keyspace);        // create session, optionally scoped to a keyspace
            $statement = new Cassandra\SimpleStatement($cql);

            $result  = $session->execute($statement);  // fully asynchronous and easy parallel execution

            return ['success' => true, 'data' => $result];

        } catch (Cassandra\Exception $e) {

            Log::error("Caught exception: " .get_class($e));

            return json_encode(['success' => false, 'error' => get_class($e) ]);
        }
    }
}