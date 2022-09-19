<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\PostModelParent;
use App\Models\ReplyModel;

class ThreadModel extends PostModelParent
{
    use HasFactory;

    protected $table = 'threads';

    protected $primaryKey = 'id';

    public $incrementing = false;

    protected $keyType = 'string';

    function makeThread ($threadBody, $threadTitle, $isLocked, $isInfinite, $allowHtml, $userId) {
        $threadBody = $this->formatBody($threadBody);
    }

    function deleteThread ($threadID) {

    }

    function updateThread ($newBody, $newTitle, $threadId) {
        
    }

    /**
     *  Returns an array of the desired threads 
     *  with all attributes (locked, highlighted, etc.)
     */

    private function getNonPinnedThreadsPerPage($uri, $pagination) { 
        // laravel automatically detects the 'page' input of the current request
        return $self::where('board_uri', $uri)
                    ->where('is_pinned', false)
                    ->orderBy('created_at')
                    ->paginate($pagination)
                    ->get();
    }

    private function getPinnedThreadsPerPage($uri, $pagination) { 
        // laravel automatically detects the 'page' input of the current request
        return $self::where('board_uri', $uri)
                    ->where('is_pinned', true)
                    ->orderBy('last_pinned_updated')
                    ->paginate($pagination)
                    ->get();
    }

    public function getAllThreads($uri, $pagination) {
        $allThreads = getPinnedThreadsPerPage($uri, $pagination) + getNonPinnedThreadsPerPage($uri, $pagination);
        $allThreads = $this->appendReplykeys($allThreads);
        return $allThreads;
    }

    /**
     *   Appends a new 'replies' key to each thread, containing a
     *   list of reply[n] keys to be looped into a thread array.
     */

    private function appendReplykeys($threadArray, $repliesPerThread, ReplyModel $reply) {
        foreach ($threadArray as $thread) {
            $replies = getLastThreadReplies($thread['id'], $repliesPerThread);
            $thread['replies'] = $replies;
        }
        return $threadArray;
    }
}
