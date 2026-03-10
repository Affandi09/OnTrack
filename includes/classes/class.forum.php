<?php

class Forum extends App
{

    public function addTopic($data)
    {
        global $database;
        $lastid = $database->insert("forum_topics", [
            "category" => $data['category'],
            "title" => $data['title'],
            "content" => $data['content'],
            "peopleid" => $data['peopleid'],
            "created_at" => date("Y-m-d H:i:s"),
            "updated_at" => date("Y-m-d H:i:s"),
            "is_pinned" => 0,
            "is_closed" => 0
        ]);
        if ($lastid == "0") {
            return "11";
        } else {
            if (isset($data['files'])) {
                $file_data = array(
                    'clientid' => 0,
                    'projectid' => 0,
                    'assetid' => 0,
                    'ticketreplyid' => 0,
                    'forumtopicid' => $lastid,
                    'forumreplyid' => 0
                );
                File::upload($file_data, $data['files']);
            }
            logSystem("Forum Topic Added - ID: " . $lastid);
            return "10";
        }
    }

    public function getTopics($category = "")
    {
        global $database;
        if ($category == "") {
            $topics = $database->select("forum_topics", "*", ["ORDER" => ["is_pinned" => "DESC", "updated_at" => "DESC"]]);
        } else {
            $topics = $database->select("forum_topics", "*", ["category" => $category, "ORDER" => ["is_pinned" => "DESC", "updated_at" => "DESC"]]);
        }
        return $topics;
    }

    public function getTopic($id)
    {
        global $database;
        $topic = $database->get("forum_topics", "*", ["id" => $id]);
        return $topic;
    }

    public function addReply($data)
    {
        global $database;
        $lastid = $database->insert("forum_replies", [
            "topic_id" => $data['topic_id'],
            "peopleid" => $data['peopleid'],
            "content" => $data['content'],
            "created_at" => date("Y-m-d H:i:s")
        ]);
        if ($lastid != "0") {
            if (isset($data['files'])) {
                $file_data = array(
                    'clientid' => 0,
                    'projectid' => 0,
                    'assetid' => 0,
                    'ticketreplyid' => 0,
                    'forumtopicid' => $data['topic_id'],
                    'forumreplyid' => $lastid
                );
                File::upload($file_data, $data['files']);
            }
            $database->update("forum_topics", ["updated_at" => date("Y-m-d H:i:s")], ["id" => $data['topic_id']]);
            logSystem("Forum Reply Added - Topic ID: " . $data['topic_id']);
            return "10";
        }
        return "11";
    }

    public function getReplies($topic_id)
    {
        global $database;
        $replies = $database->select("forum_replies", "*", ["topic_id" => $topic_id, "ORDER" => ["created_at" => "ASC"]]);
        return $replies;
    }

    public function getRepliesCount($topic_id)
    {
        global $database;
        return $database->count("forum_replies", ["topic_id" => $topic_id]);
    }

    public function editTopic($data)
    {
        global $database;
        $database->update("forum_topics", [
            "category" => $data['category'],
            "title" => $data['title'],
            "content" => $data['content'],
            "updated_at" => date("Y-m-d H:i:s")
        ], ["id" => $data['id']]);
        logSystem("Forum Topic Edited - ID: " . $data['id']);
        return "20";
    }

    public function deleteTopic($id)
    {
        global $database;
        File::delete_forum_topic_files($id);
        $database->delete("forum_topics", ["id" => $id]);
        $database->delete("forum_replies", ["topic_id" => $id]);
        logSystem("Forum Topic Deleted - ID: " . $id);
        return "30";
    }

    public function togglePin($id, $status)
    {
        global $database;
        $database->update("forum_topics", ["is_pinned" => $status], ["id" => $id]);
        return "20";
    }

    public function toggleClose($id, $status)
    {
        global $database;
        $database->update("forum_topics", ["is_closed" => $status], ["id" => $id]);
        return "20";
    }

}

?>